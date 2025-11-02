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
$conn->exec("CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Enviar mensagem
    $input = json_decode(file_get_contents('php://input'), true);
    $message = sanitize($input['message'] ?? '');
    $userId = getCurrentUser()['id'];
    
    if ($message) {
        $stmt = $conn->prepare("INSERT INTO chat_messages (user_id, content) VALUES (?, ?)");
        if ($stmt->execute([$userId, $message])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao enviar mensagem']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Mensagem vazia']);
    }
} else {
    // Buscar mensagens
    $stmt = $conn->prepare("
        SELECT cm.*, u.username, u.first_name, u.last_name 
        FROM chat_messages cm 
        JOIN users u ON cm.user_id = u.id 
        ORDER BY cm.created_at DESC 
        LIMIT 50
    ");
    $stmt->execute();
    $messages = array_reverse($stmt->fetchAll());
    
    echo json_encode($messages);
}
?>