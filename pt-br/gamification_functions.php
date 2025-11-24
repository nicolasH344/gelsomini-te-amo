<?php
require_once 'config.php';

// Função para dar recompensa de login diário
function giveLoginReward($userId) {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    // Verificar se as tabelas existem
    $tables = ['login_rewards', 'user_stats'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows == 0) {
            return false; // Tabela não existe
        }
    }
    
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    // Verificar se já ganhou recompensa hoje
    $stmt = $conn->prepare("SELECT id FROM login_rewards WHERE user_id = ? AND reward_date = ?");
    $stmt->bind_param("is", $userId, $today);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return false; // Já ganhou hoje
    }
    
    // Buscar estatísticas do usuário
    $stmt = $conn->prepare("SELECT * FROM user_stats WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    
    if (!$stats) {
        // Criar registro inicial
        $stmt = $conn->prepare("INSERT INTO user_stats (user_id, last_login, total_logins, login_streak) VALUES (?, ?, 1, 1)");
        $stmt->bind_param("is", $userId, $today);
        $stmt->execute();
        $loginStreak = 1;
    } else {
        // Calcular streak
        if ($stats['last_login'] === $yesterday) {
            $loginStreak = $stats['login_streak'] + 1;
        } else if ($stats['last_login'] === $today) {
            $loginStreak = $stats['login_streak'];
        } else {
            $loginStreak = 1; // Quebrou a sequência
        }
        
        // Atualizar estatísticas
        $stmt = $conn->prepare("UPDATE user_stats SET last_login = ?, total_logins = total_logins + 1, login_streak = ? WHERE user_id = ?");
        $stmt->bind_param("sii", $today, $loginStreak, $userId);
        $stmt->execute();
    }
    
    // Calcular recompensas baseadas no streak
    $baseXP = 10;
    $baseCoins = 5;
    $streakBonus = min($loginStreak - 1, 10); // Máximo 10 de bônus
    
    $xpReward = $baseXP + ($streakBonus * 2);
    $coinReward = $baseCoins + $streakBonus;
    
    // Dar recompensa
    $stmt = $conn->prepare("INSERT INTO login_rewards (user_id, reward_date, xp_gained, coins_gained, streak_day) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isiii", $userId, $today, $xpReward, $coinReward, $loginStreak);
    $stmt->execute();
    
    // Atualizar XP e moedas
    $stmt = $conn->prepare("UPDATE user_stats SET xp = xp + ?, coins = coins + ? WHERE user_id = ?");
    $stmt->bind_param("iii", $xpReward, $coinReward, $userId);
    $stmt->execute();
    
    // Verificar se subiu de nível
    updateUserLevel($userId);
    
    // Verificar badges de login
    checkLoginBadges($userId, $loginStreak);
    
    return [
        'xp' => $xpReward,
        'coins' => $coinReward,
        'streak' => $loginStreak
    ];
}

// Função para atualizar nível do usuário
function updateUserLevel($userId) {
    $conn = getDBConnection();
    if (!$conn) return;
    
    $stmt = $conn->prepare("SELECT xp, level FROM user_stats WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    
    if ($stats) {
        $newLevel = floor($stats['xp'] / 100) + 1;
        if ($newLevel > $stats['level']) {
            $stmt = $conn->prepare("UPDATE user_stats SET level = ? WHERE user_id = ?");
            $stmt->bind_param("ii", $newLevel, $userId);
            $stmt->execute();
            
            // Dar recompensa por subir de nível
            $levelReward = $newLevel * 25;
            $stmt = $conn->prepare("UPDATE user_stats SET coins = coins + ? WHERE user_id = ?");
            $stmt->bind_param("ii", $levelReward, $userId);
            $stmt->execute();
        }
    }
}

// Função para verificar badges de login
function checkLoginBadges($userId, $loginStreak) {
    $conn = getDBConnection();
    if (!$conn) return;
    
    // Buscar badges de login não conquistados
    $stmt = $conn->prepare("
        SELECT b.* FROM badges b 
        WHERE b.badge_type = 'login' 
        AND b.requirement_value <= ? 
        AND b.id NOT IN (SELECT badge_id FROM user_badges WHERE user_id = ?)
    ");
    $stmt->bind_param("ii", $loginStreak, $userId);
    $stmt->execute();
    $badges = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    foreach ($badges as $badge) {
        // Conceder badge
        $stmt = $conn->prepare("INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $badge['id']);
        $stmt->execute();
        
        // Dar recompensas do badge
        $stmt = $conn->prepare("UPDATE user_stats SET xp = xp + ?, coins = coins + ? WHERE user_id = ?");
        $stmt->bind_param("iii", $badge['xp_reward'], $badge['coin_reward'], $userId);
        $stmt->execute();
    }
}

// Função para buscar estatísticas do usuário
function getUserStats($userId) {
    $conn = getDBConnection();
    if (!$conn) return null;
    
    // Verificar se a tabela existe
    $result = $conn->query("SHOW TABLES LIKE 'user_stats'");
    if ($result->num_rows == 0) {
        return [
            'level' => 1,
            'xp' => 0,
            'coins' => 50,
            'login_streak' => 0,
            'total_logins' => 0
        ];
    }
    
    $stmt = $conn->prepare("SELECT * FROM user_stats WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if (!$result) {
        // Criar registro inicial
        $stmt = $conn->prepare("INSERT INTO user_stats (user_id) VALUES (?)");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        return [
            'level' => 1,
            'xp' => 0,
            'coins' => 50,
            'login_streak' => 0,
            'total_logins' => 0
        ];
    }
    
    return $result;
}

// Função para buscar badges do usuário
function getUserBadges($userId) {
    $conn = getDBConnection();
    if (!$conn) return [];
    
    // Verificar se as tabelas existem
    $result = $conn->query("SHOW TABLES LIKE 'user_badges'");
    if ($result->num_rows == 0) {
        return [];
    }
    
    // Verificar se a coluna reward_claimed existe
    $columnCheck = $conn->query("SHOW COLUMNS FROM user_badges LIKE 'reward_claimed'");
    $hasRewardClaimed = $columnCheck->num_rows > 0;
    
    if ($hasRewardClaimed) {
        $stmt = $conn->prepare("
            SELECT b.*, ub.earned_at, ub.reward_claimed
            FROM user_badges ub 
            JOIN badges b ON ub.badge_id = b.id 
            WHERE ub.user_id = ? 
            ORDER BY ub.earned_at DESC
        ");
    } else {
        $stmt = $conn->prepare("
            SELECT b.*, ub.earned_at, FALSE as reward_claimed
            FROM user_badges ub 
            JOIN badges b ON ub.badge_id = b.id 
            WHERE ub.user_id = ? 
            ORDER BY ub.earned_at DESC
        ");
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Função para buscar mascote ativo
function getActivePet($userId) {
    $conn = getDBConnection();
    if (!$conn) return ['name' => 'CodeBot', 'icon' => 'fas fa-robot'];
    
    // Verificar se as tabelas existem
    $result = $conn->query("SHOW TABLES LIKE 'user_pets'");
    if ($result->num_rows == 0) {
        return ['name' => 'CodeBot', 'icon' => 'fas fa-robot'];
    }
    
    $stmt = $conn->prepare("
        SELECT p.name, p.icon 
        FROM user_pets up 
        JOIN pets p ON up.pet_id = p.id 
        WHERE up.user_id = ? AND up.is_active = TRUE
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    return $result ?: ['name' => 'CodeBot', 'icon' => 'fas fa-robot'];
}

// Função para dar XP por atividade
function giveActivityXP($userId, $activity, $amount = 1) {
    $conn = getDBConnection();
    if (!$conn) return;
    
    // Verificar se a tabela existe
    $result = $conn->query("SHOW TABLES LIKE 'user_stats'");
    if ($result->num_rows == 0) {
        return;
    }
    
    $xpRewards = [
        'exercise_completed' => 25,
        'tutorial_read' => 15,
        'forum_post' => 10
    ];
    
    $xp = ($xpRewards[$activity] ?? 10) * $amount;
    
    $stmt = $conn->prepare("UPDATE user_stats SET xp = xp + ? WHERE user_id = ?");
    $stmt->bind_param("ii", $xp, $userId);
    $stmt->execute();
    
    updateUserLevel($userId);
    checkActivityBadges($userId, $activity);
}

// Função para verificar badges de atividade
function checkActivityBadges($userId, $activity) {
    $conn = getDBConnection();
    if (!$conn) return;
    
    $badgeTypes = [
        'exercise_completed' => 'exercise',
        'tutorial_read' => 'tutorial',
        'forum_post' => 'forum'
    ];
    
    $badgeType = $badgeTypes[$activity] ?? 'special';
    
    // Contar atividades do usuário
    $counts = [
        'exercise' => "SELECT COUNT(*) FROM user_progress WHERE user_id = ? AND status = 'completed'",
        'tutorial' => "SELECT COUNT(*) FROM tutorial_progress WHERE user_id = ? AND status = 'completed'",
        'forum' => "SELECT COUNT(*) FROM forum_posts WHERE user_id = ?"
    ];
    
    if (isset($counts[$badgeType])) {
        $stmt = $conn->prepare($counts[$badgeType]);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_row()[0];
        
        // Verificar badges disponíveis
        $stmt = $conn->prepare("
            SELECT b.* FROM badges b 
            WHERE b.badge_type = ? 
            AND b.requirement_value <= ? 
            AND b.id NOT IN (SELECT badge_id FROM user_badges WHERE user_id = ?)
        ");
        $stmt->bind_param("sii", $badgeType, $count, $userId);
        $stmt->execute();
        $badges = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        foreach ($badges as $badge) {
            // Conceder badge
            $stmt = $conn->prepare("INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $userId, $badge['id']);
            $stmt->execute();
            
            // Dar recompensas
            $stmt = $conn->prepare("UPDATE user_stats SET xp = xp + ?, coins = coins + ? WHERE user_id = ?");
            $stmt->bind_param("iii", $badge['xp_reward'], $badge['coin_reward'], $userId);
            $stmt->execute();
        }
    }
}
?>