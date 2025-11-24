<?php
/**
 * API: Obter Respostas de Discussão
 * Endpoint para buscar todas as respostas de uma discussão
 */

require_once '../config.php';

header('Content-Type: application/json');

$discussion_id = (int)($_GET['discussion_id'] ?? 0);

if ($discussion_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de discussão inválido',
        'replies' => []
    ]);
    exit;
}

try {
    $conn = getDBConnection();
    
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Erro de conexão', 'replies' => []]);
        exit;
    }
    
    // Buscar respostas ordenadas por data
    $stmt = $conn->prepare("
        SELECT 
            id,
            user_id,
            user_name,
            message,
            created_at
        FROM discussion_replies
        WHERE discussion_id = ?
        ORDER BY created_at ASC
        LIMIT 50
    ");
    
    $stmt->bind_param("i", $discussion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $replies = [];
    while ($row = $result->fetch_assoc()) {
        $replies[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'replies' => $replies,
        'total' => count($replies)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao carregar respostas: ' . $e->getMessage(),
        'replies' => []
    ]);
}

