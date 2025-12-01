<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

$user_id = getCurrentUser()['id'];
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão']);
    exit;
}

// Criar tabelas se não existirem
$conn->query("CREATE TABLE IF NOT EXISTS user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content_id INT NOT NULL,
    content_type ENUM('exercise', 'tutorial') NOT NULL,
    status ENUM('started', 'completed') DEFAULT 'started',
    score INT DEFAULT 0,
    progress_percent INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_progress (user_id, content_id, content_type)
)");

$conn->query("CREATE TABLE IF NOT EXISTS user_achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    achievement_type VARCHAR(50) NOT NULL,
    achievement_data JSON,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$action = $_POST['action'] ?? '';
$exercise_id = (int)($_POST['exercise_id'] ?? 0);

switch ($action) {
    case 'start':
        // Marcar exercício como iniciado
        $stmt = $conn->prepare("INSERT IGNORE INTO user_progress (user_id, content_id, content_type, status) VALUES (?, ?, 'exercise', 'started')");
        $stmt->bind_param("ii", $user_id, $exercise_id);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;
        
    case 'complete':
        $score = (int)($_POST['score'] ?? 10);
        
        // Salvar progresso
        $stmt = $conn->prepare("INSERT INTO user_progress (user_id, content_id, content_type, status, score, updated_at) 
                               VALUES (?, ?, 'exercise', 'completed', ?, NOW())
                               ON DUPLICATE KEY UPDATE 
                               status = 'completed', score = ?, updated_at = NOW()");
        $stmt->bind_param("iii", $user_id, $exercise_id, $score, $score);
        $stmt->execute();
        
        // Verificar conquistas
        $achievements = [];
        
        // Contar exercícios completados
        $stmt = $conn->prepare("SELECT COUNT(*) FROM user_progress WHERE user_id = ? AND content_type = 'exercise' AND status = 'completed'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $completed_count = $stmt->get_result()->fetch_row()[0];
        
        // Conquistas baseadas em quantidade
        $achievement_thresholds = [1 => 'Primeiro Exercício', 5 => 'Praticante', 10 => 'Dedicado', 20 => 'Expert'];
        
        foreach ($achievement_thresholds as $threshold => $name) {
            if ($completed_count == $threshold) {
                // Verificar se já tem essa conquista
                $stmt = $conn->prepare("SELECT id FROM user_achievements WHERE user_id = ? AND achievement_type = ?");
                $stmt->bind_param("is", $user_id, $name);
                $stmt->execute();
                
                if ($stmt->get_result()->num_rows == 0) {
                    // Adicionar conquista
                    $stmt = $conn->prepare("INSERT INTO user_achievements (user_id, achievement_type, achievement_data) VALUES (?, ?, ?)");
                    $data = json_encode(['exercises_completed' => $completed_count]);
                    $stmt->bind_param("iss", $user_id, $name, $data);
                    $stmt->execute();
                    
                    $achievements[] = $name;
                    
                    // Adicionar notificação
                    $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'success')");
                    $title = "Nova Conquista!";
                    $message = "Você desbloqueou: $name";
                    $stmt->bind_param("iss", $user_id, $title, $message);
                    $stmt->execute();
                }
            }
        }
        
        // Adicionar notificação de progresso
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'success')");
        $title = "Exercício Concluído!";
        $message = "Parabéns! Você completou um exercício e ganhou $score pontos.";
        $stmt->bind_param("iss", $user_id, $title, $message);
        $stmt->execute();
        
        echo json_encode([
            'success' => true, 
            'score' => $score,
            'achievements' => $achievements,
            'total_completed' => $completed_count
        ]);
        break;
        
    case 'get_progress':
        $stmt = $conn->prepare("SELECT * FROM user_progress WHERE user_id = ? AND content_id = ? AND content_type = 'exercise'");
        $stmt->bind_param("ii", $user_id, $exercise_id);
        $stmt->execute();
        $progress = $stmt->get_result()->fetch_assoc();
        
        echo json_encode(['success' => true, 'progress' => $progress]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}
?>