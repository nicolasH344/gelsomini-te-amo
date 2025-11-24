<?php
/**
 * API: Adicionar Resposta a Discussão
 * Endpoint para criar respostas em discussões da comunidade
 */

session_start();
require_once '../config.php';

header('Content-Type: application/json');

// Verificar autenticação
if (!isLoggedIn()) {
    echo json_encode([
        'success' => false,
        'message' => 'Usuário não autenticado'
    ]);
    exit;
}

// Obter dados do POST
$discussion_id = (int)($_POST['discussion_id'] ?? 0);
$message = trim($_POST['message'] ?? '');

// Validações
if ($discussion_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de discussão inválido'
    ]);
    exit;
}

if (strlen($message) < 5) {
    echo json_encode([
        'success' => false,
        'message' => 'A resposta deve ter pelo menos 5 caracteres'
    ]);
    exit;
}

try {
    $conn = getDBConnection();
    $user = getCurrentUser();
    
    if (!$conn || !$user) {
        echo json_encode(['success' => false, 'message' => 'Erro de conexão ou sessão']);
        exit;
    }
    
    $user_id = $user['id'];
    $user_name = $user['username'] ?? $user['name'] ?? 'Usuário';
    
    // Inserir resposta
    $stmt = $conn->prepare("
        INSERT INTO discussion_replies (discussion_id, user_id, user_name, message, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    $stmt->bind_param("iiss", $discussion_id, $user_id, $user_name, $message);
    $stmt->execute();
    
    $reply_id = $stmt->insert_id;
    
    echo json_encode([
        'success' => true,
        'message' => 'Resposta publicada com sucesso',
        'reply' => [
            'id' => $reply_id,
            'discussion_id' => $discussion_id,
            'user_name' => $user_name,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao salvar resposta: ' . $e->getMessage()
    ]);
}

