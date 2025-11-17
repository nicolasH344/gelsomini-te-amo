<?php
/**
 * API: Buscar discussões
 * Retorna discussões de um tutorial/exercício específico
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
    
    // Busca discussões com contagem de likes
    $stmt = $conn->prepare("
        SELECT 
            d.*,
            COUNT(DISTINCT dl.id) as likes,
            COUNT(DISTINCT dr.id) as replies
        FROM discussions d
        LEFT JOIN discussion_likes dl ON d.id = dl.discussion_id
        LEFT JOIN discussion_replies dr ON d.id = dr.discussion_id
        WHERE d.content_type = ? AND d.content_id = ?
        GROUP BY d.id
        ORDER BY d.created_at DESC
        LIMIT 20
    ");
    
    $stmt->execute([$content_type, $content_id]);
    $discussions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'discussions' => $discussions
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
