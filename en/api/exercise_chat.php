<?php
header('Content-Type: application/json');
require_once '../config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão']);
    exit;
}

// Criar tabela se não existir
$conn->exec("CREATE TABLE IF NOT EXISTS exercise_chat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exercise_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $exerciseId = (int)($input['exercise_id'] ?? 0);
    $message = sanitize($input['message'] ?? '');
    $userId = getCurrentUser()['id'];
    
    if ($exerciseId && $message) {
        $stmt = $conn->prepare("INSERT INTO exercise_chat (exercise_id, user_id, content) VALUES (?, ?, ?)");
        if ($stmt->execute([$exerciseId, $userId, $message])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao enviar mensagem']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    }
} else {
    $exerciseId = (int)($_GET['exercise_id'] ?? 0);
    
    if ($exerciseId) {
        $stmt = $conn->prepare("
            SELECT ec.*, u.username, u.first_name 
            FROM exercise_chat ec 
            JOIN users u ON ec.user_id = u.id 
            WHERE ec.exercise_id = ? 
            ORDER BY ec.created_at ASC 
            LIMIT 50
        ");
        $stmt->execute([$exerciseId]);
        $messages = $stmt->fetchAll();
        
        echo json_encode($messages);
    } else {
        echo json_encode([]);
    }
}
?>