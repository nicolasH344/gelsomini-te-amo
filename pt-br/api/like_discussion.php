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
    
    // Verifica se já curtiu
    $stmt = $conn->prepare("
        SELECT id FROM discussion_likes 
        WHERE discussion_id = ? AND user_id = ?
    ");
    $stmt->execute([$discussion_id, $user['id']]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Remove like (descurtir)
        $stmt = $conn->prepare("DELETE FROM discussion_likes WHERE id = ?");
        $stmt->execute([$existing['id']]);
        $action = 'removed';
    } else {
        // Adiciona like
        $stmt = $conn->prepare("
            INSERT INTO discussion_likes (discussion_id, user_id, created_at) 
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$discussion_id, $user['id']]);
        $action = 'added';
    }
    
    // Conta total de likes
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM discussion_likes WHERE discussion_id = ?");
    $stmt->execute([$discussion_id]);
    $total = $stmt->fetch()['total'];
    
    echo json_encode([
        'success' => true,
        'action' => $action,
        'total_likes' => $total
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
