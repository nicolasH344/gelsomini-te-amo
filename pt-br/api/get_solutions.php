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
    
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Erro de conexão']);
        exit;
    }
    
    // Criar tabela se não existir
    $createTable = "CREATE TABLE IF NOT EXISTS community_solutions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content_type VARCHAR(50) NOT NULL,
        content_id INT NOT NULL,
        user_id INT NOT NULL,
        user_name VARCHAR(100) NOT NULL,
        title VARCHAR(200) NOT NULL,
        code TEXT NOT NULL,
        language VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_content (content_type, content_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn->query($createTable);
    
    // Buscar soluções
    $stmt = $conn->prepare("
        SELECT 
            s.id,
            s.content_type,
            s.content_id,
            s.user_id,
            s.user_name,
            s.title,
            s.code,
            s.language,
            s.created_at,
            (SELECT COUNT(*) FROM solution_likes WHERE solution_id = s.id) as likes
        FROM community_solutions s
        WHERE s.content_type = ? AND s.content_id = ?
        ORDER BY likes DESC, s.created_at DESC
        LIMIT 50
    ");
    
    $stmt->bind_param("si", $content_type, $content_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $solutions = [];
    while ($row = $result->fetch_assoc()) {
        $solutions[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'solutions' => $solutions,
        'total' => count($solutions)
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao carregar soluções', 'error' => $e->getMessage()]);
}

