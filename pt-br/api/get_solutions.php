<?php
/**
 * API: Buscar soluções da comunidade
 * Retorna soluções compartilhadas
 */

require_once '../config.php';

header('Content-Type: application/json');

$content_type = sanitize($_GET['content_type'] ?? '');
$content_id = (int)($_GET['content_id'] ?? 0);

if (!$content_type || !$content_id) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit;
}

try {
    $conn = getDBConnection();
    
    // Busca soluções com contagem de likes
    $stmt = $conn->prepare("
        SELECT 
            s.*,
            COUNT(sl.id) as likes
        FROM community_solutions s
        LEFT JOIN solution_likes sl ON s.id = sl.solution_id
        WHERE s.content_type = ? AND s.content_id = ?
        GROUP BY s.id
        ORDER BY likes DESC, s.created_at DESC
        LIMIT 10
    ");
    
    $stmt->execute([$content_type, $content_id]);
    $solutions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'solutions' => $solutions
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
