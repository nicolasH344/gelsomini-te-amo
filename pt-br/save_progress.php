<?php
require_once 'config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Não logado']);
    exit;
}

$user_id = getCurrentUser()['id'];
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão']);
    exit;
}

// Criar tabelas se não existirem
$conn->query("CREATE TABLE IF NOT EXISTS exercise_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    exercise_id INT NOT NULL,
    status ENUM('started', 'completed') DEFAULT 'started',
    score INT DEFAULT 0,
    completed_at TIMESTAMP NULL,
    UNIQUE KEY unique_progress (user_id, exercise_id)
)");

$conn->query("CREATE TABLE IF NOT EXISTS tutorial_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tutorial_id INT NOT NULL,
    status ENUM('started', 'completed') DEFAULT 'started',
    progress_percent INT DEFAULT 0,
    completed_at TIMESTAMP NULL,
    UNIQUE KEY unique_progress (user_id, tutorial_id)
)");

$type = $_POST['type'] ?? '';
$item_id = (int)($_POST['item_id'] ?? 0);
$status = $_POST['status'] ?? 'started';

if ($type === 'exercise') {
    $score = (int)($_POST['score'] ?? 10);
    $completed_at = $status === 'completed' ? 'NOW()' : 'NULL';
    
    $stmt = $conn->prepare("INSERT INTO exercise_progress (user_id, exercise_id, status, score, completed_at) 
                           VALUES (?, ?, ?, ?, $completed_at)
                           ON DUPLICATE KEY UPDATE 
                           status = VALUES(status), 
                           score = VALUES(score),
                           completed_at = VALUES(completed_at)");
    $stmt->bind_param("iisi", $user_id, $item_id, $status, $score);
    
} elseif ($type === 'tutorial') {
    $progress = (int)($_POST['progress'] ?? 0);
    $completed_at = $status === 'completed' ? 'NOW()' : 'NULL';
    
    $stmt = $conn->prepare("INSERT INTO tutorial_progress (user_id, tutorial_id, status, progress_percent, completed_at) 
                           VALUES (?, ?, ?, ?, $completed_at)
                           ON DUPLICATE KEY UPDATE 
                           status = VALUES(status), 
                           progress_percent = VALUES(progress_percent),
                           completed_at = VALUES(completed_at)");
    $stmt->bind_param("iisi", $user_id, $item_id, $status, $progress);
    
} else {
    echo json_encode(['success' => false, 'message' => 'Tipo inválido']);
    exit;
}

if ($stmt->execute()) {
    // Adicionar notificação interna em vez de usar notificações do navegador
    if ($status === 'completed') {
        $title = $type === 'exercise' ? 'Exercício Concluído!' : 'Tutorial Concluído!';
        $message = $type === 'exercise' ? 'Parabéns! Você completou um exercício.' : 'Parabéns! Você completou um tutorial.';
        
        // Adicionar à nossa base de notificações internas
        $stmt2 = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'success')");
        $stmt2->bind_param("iss", $user_id, $title, $message);
        $stmt2->execute();
    }
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar']);
}
?>