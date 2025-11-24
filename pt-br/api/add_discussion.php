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
    
    if (!$conn || !$user) {
        echo json_encode(['success' => false, 'message' => 'Erro de conexão ou sessão']);
        exit;
    }
    
    $user_id = $user['id'];
    $user_name = $user['username'] ?? $user['name'] ?? 'Usuário';
    
    // Insere discussão
    $stmt = $conn->prepare("
        INSERT INTO discussions (content_type, content_id, user_id, user_name, message, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->bind_param("siiss", $content_type, $content_id, $user_id, $user_name, $message);
    $stmt->execute();
    
    $discussion_id = $stmt->insert_id;
    
    echo json_encode([
        'success' => true,
        'message' => 'Discussão publicada com sucesso!',
        'discussion' => [
            'id' => $discussion_id,
            'user_name' => $user_name,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s'),
            'likes' => 0,
            'replies_count' => 0
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar discussão: ' . $e->getMessage()]);
}

