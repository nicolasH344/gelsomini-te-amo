<?php
/**
 * API: Adicionar nova discussão
 * Permite usuários autenticados postarem discussões sobre tutoriais/exercícios
 */

session_start();
require_once '../config.php';

header('Content-Type: application/json');

// Verifica autenticação
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

// Captura dados
$content_type = sanitize($_POST['content_type'] ?? '');
$content_id = (int)($_POST['content_id'] ?? 0);
$message = sanitize($_POST['message'] ?? '');

// Validação
if (!$content_type || !$content_id || !$message) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

if (strlen($message) < 10) {
    echo json_encode(['success' => false, 'message' => 'Mensagem muito curta (mínimo 10 caracteres)']);
    exit;
}

try {
    $conn = getDBConnection();
    $user = getCurrentUser();
    
    // Insere discussão
    $stmt = $conn->prepare("
        INSERT INTO discussions (content_type, content_id, user_id, username, message, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $content_type,
        $content_id,
        $user['id'],
        $user['username'],
        $message
    ]);
    
    $discussion_id = $conn->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Discussão publicada com sucesso!',
        'discussion' => [
            'id' => $discussion_id,
            'username' => $user['username'],
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s'),
            'likes' => 0,
            'replies' => 0
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar discussão: ' . $e->getMessage()]);
}
