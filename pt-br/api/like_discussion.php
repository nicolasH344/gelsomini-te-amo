<?php
/**
 * API: Curtir/Descurtir discussão
 * Sistema de likes para discussões
 */

session_start();
require_once '../config.php';

header('Content-Type: application/json');

// Verifica autenticação
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

$discussion_id = (int)($_POST['discussion_id'] ?? 0);

if (!$discussion_id) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
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
    
    // Verifica se já curtiu
    $stmt = $conn->prepare("
        SELECT id FROM discussion_likes 
        WHERE discussion_id = ? AND user_id = ?
    ");
    $stmt->bind_param("ii", $discussion_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing = $result->fetch_assoc();
    
    if ($existing) {
        // Remove like (descurtir)
        $stmt = $conn->prepare("DELETE FROM discussion_likes WHERE id = ?");
        $stmt->bind_param("i", $existing['id']);
        $stmt->execute();
        $action = 'removed';
    } else {
        // Adiciona like
        $stmt = $conn->prepare("
            INSERT INTO discussion_likes (discussion_id, user_id, created_at) 
            VALUES (?, ?, NOW())
        ");
        $stmt->bind_param("ii", $discussion_id, $user_id);
        $stmt->execute();
        $action = 'added';
    }
    
    // Conta total de likes
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM discussion_likes WHERE discussion_id = ?");
    $stmt->bind_param("i", $discussion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'];
    
    echo json_encode([
        'success' => true,
        'action' => $action,
        'total_likes' => $total
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}

