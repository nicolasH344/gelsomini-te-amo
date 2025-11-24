<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

$user = getCurrentUser();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

$conn = getDBConnection();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro de conexão com banco']);
    exit;
}

switch ($action) {
    case 'check_achievements':
        checkAndAwardAchievements($user['id'], $conn);
        break;
        
    case 'claim_reward':
        claimBadgeReward($_POST['badge_id'], $user['id'], $conn);
        break;
        
    case 'get_progress':
        getUserProgress($user['id'], $conn);
        break;
        
    case 'update_progress':
        updateUserProgress($_POST['type'], $_POST['value'], $user['id'], $conn);
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Ação inválida']);
}

function checkAndAwardAchievements($userId, $conn) {
    // Buscar estatísticas do usuário
    $stats = getUserStats($userId, $conn);
    
    // Buscar badges não conquistados
    $stmt = $conn->prepare("
        SELECT b.* FROM badges b 
        WHERE b.id NOT IN (
            SELECT badge_id FROM user_badges WHERE user_id = ?
        )
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $availableBadges = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $newBadges = [];
    
    foreach ($availableBadges as $badge) {
        $requirements = json_decode($badge['requirements'], true);
        
        if (checkRequirement($requirements, $stats, $userId, $conn)) {
            // Conceder badge
            $stmt = $conn->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $userId, $badge['id']);
            $stmt->execute();
            
            // Adicionar XP e moedas
            addXPAndCoins($userId, $badge['xp_reward'], $badge['coin_reward'], $conn);
            
            $newBadges[] = $badge;
        }
    }
    
    echo json_encode([
        'success' => true,
        'new_badges' => $newBadges,
        'message' => count($newBadges) > 0 ? 'Novas conquistas desbloqueadas!' : 'Nenhuma nova conquista'
    ]);
}

function checkRequirement($requirements, $stats, $userId, $conn) {
    switch ($requirements['type']) {
        case 'exercises_completed':
            return $stats['exercises_completed'] >= $requirements['value'];
            
        case 'tutorials_read':
            return $stats['tutorials_read'] >= $requirements['value'];
            
        case 'forum_posts':
            return $stats['forum_posts'] >= $requirements['value'];
            
        case 'streak_days':
            return $stats['streak_days'] >= $requirements['value'];
            
        case 'category_exercises':
            $stmt = $conn->prepare("
                SELECT COUNT(*) FROM user_progress up 
                JOIN exercises e ON up.exercise_id = e.id 
                JOIN categories c ON e.category_id = c.id 
                WHERE up.user_id = ? AND c.name = ? AND up.status = 'completed'
            ");
            $stmt->bind_param("is", $userId, $requirements['category']);
            $stmt->execute();
            $count = $stmt->get_result()->fetch_row()[0];
            return $count >= $requirements['value'];
            
        default:
            return false;
    }
}

function getUserStats($userId, $conn) {
    // Exercícios completados
    $stmt = $conn->prepare("SELECT COUNT(*) FROM user_progress WHERE user_id = ? AND status = 'completed'");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $exercisesCompleted = $stmt->get_result()->fetch_row()[0];
    
    // Tutoriais lidos
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tutorial_progress WHERE user_id = ? AND status = 'completed'");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $tutorialsRead = $stmt->get_result()->fetch_row()[0];
    
    // Posts no fórum
    $stmt = $conn->prepare("SELECT COUNT(*) FROM forum_posts WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $forumPosts = $stmt->get_result()->fetch_row()[0];
    
    // Sequência de dias
    $stmt = $conn->prepare("SELECT streak_days FROM user_stats WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_row();
    $streakDays = $result ? $result[0] : 0;
    
    return [
        'exercises_completed' => $exercisesCompleted,
        'tutorials_read' => $tutorialsRead,
        'forum_posts' => $forumPosts,
        'streak_days' => $streakDays
    ];
}

function addXPAndCoins($userId, $xp, $coins, $conn) {
    // Verificar se usuário existe na tabela user_stats
    $stmt = $conn->prepare("SELECT id FROM user_stats WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_row();
    
    if (!$exists) {
        // Criar registro inicial
        $stmt = $conn->prepare("INSERT INTO user_stats (user_id, level, xp, coins) VALUES (?, 1, ?, ?)");
        $stmt->bind_param("iii", $userId, $xp, $coins);
        $stmt->execute();
    } else {
        // Atualizar XP e moedas
        $stmt = $conn->prepare("UPDATE user_stats SET xp = xp + ?, coins = coins + ? WHERE user_id = ?");
        $stmt->bind_param("iii", $xp, $coins, $userId);
        $stmt->execute();
        
        // Verificar se subiu de nível
        $stmt = $conn->prepare("SELECT level, xp FROM user_stats WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        $newLevel = floor($result['xp'] / 100) + 1;
        if ($newLevel > $result['level']) {
            $stmt = $conn->prepare("UPDATE user_stats SET level = ? WHERE user_id = ?");
            $stmt->bind_param("ii", $newLevel, $userId);
            $stmt->execute();
        }
    }
}

function claimBadgeReward($badgeId, $userId, $conn) {
    $stmt = $conn->prepare("UPDATE user_badges SET reward_claimed = TRUE WHERE badge_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $badgeId, $userId);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Recompensa coletada!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao coletar recompensa']);
    }
}

function getUserProgress($userId, $conn) {
    $stmt = $conn->prepare("SELECT * FROM user_stats WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    
    if (!$stats) {
        $stats = ['level' => 1, 'xp' => 0, 'coins' => 50, 'streak_days' => 0];
    }
    
    echo json_encode(['success' => true, 'stats' => $stats]);
}

function updateUserProgress($type, $value, $userId, $conn) {
    switch ($type) {
        case 'exercise_completed':
            // Atualizar sequência de dias se necessário
            updateStreakDays($userId, $conn);
            break;
            
        case 'tutorial_read':
            updateStreakDays($userId, $conn);
            break;
            
        case 'forum_post':
            // Lógica específica para posts do fórum
            break;
    }
    
    // Verificar conquistas após atualizar progresso
    checkAndAwardAchievements($userId, $conn);
}

function updateStreakDays($userId, $conn) {
    $today = date('Y-m-d');
    
    $stmt = $conn->prepare("SELECT last_activity, streak_days FROM user_stats WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if (!$result) {
        // Criar registro inicial
        $stmt = $conn->prepare("INSERT INTO user_stats (user_id, last_activity, streak_days) VALUES (?, ?, 1)");
        $stmt->bind_param("is", $userId, $today);
        $stmt->execute();
        return;
    }
    
    $lastActivity = $result['last_activity'];
    $currentStreak = $result['streak_days'];
    
    if ($lastActivity === $today) {
        // Já estudou hoje, não fazer nada
        return;
    }
    
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    if ($lastActivity === $yesterday) {
        // Continuou a sequência
        $newStreak = $currentStreak + 1;
    } else {
        // Quebrou a sequência
        $newStreak = 1;
    }
    
    $stmt = $conn->prepare("UPDATE user_stats SET last_activity = ?, streak_days = ? WHERE user_id = ?");
    $stmt->bind_param("sii", $today, $newStreak, $userId);
    $stmt->execute();
}
?>