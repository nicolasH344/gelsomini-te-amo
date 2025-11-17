<?php
/**
 * API: Adicionar Resposta a Discussão
 * Endpoint para criar respostas em discussões da comunidade
 */

session_start();
require_once '../config.php';
require_once '../database_connector.php';

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
    
    // Inserir resposta
    $stmt = $conn->prepare("
        INSERT INTO discussion_replies (discussion_id, user_id, username, message, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $discussion_id,
        $user['id'],
        $user['username'],
        $message
    ]);
    
    $reply_id = $conn->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Resposta publicada com sucesso',
        'reply' => [
            'id' => $reply_id,
            'discussion_id' => $discussion_id,
            'username' => $user['username'],
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao salvar resposta: ' . $e->getMessage()
    ]);
}
