<?php
/**
 * API: Obter Respostas de Discussão
 * Endpoint para buscar todas as respostas de uma discussão
 */

require_once '../config.php';
require_once '../database_connector.php';

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
    
    // Buscar respostas ordenadas por data
    $stmt = $conn->prepare("
        SELECT 
            id,
            user_id,
            username,
            message,
            created_at
        FROM discussion_replies
        WHERE discussion_id = ?
        ORDER BY created_at ASC
        LIMIT 50
    ");
    
    $stmt->execute([$discussion_id]);
    $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'replies' => $replies
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao carregar respostas: ' . $e->getMessage(),
        'replies' => []
    ]);
}
