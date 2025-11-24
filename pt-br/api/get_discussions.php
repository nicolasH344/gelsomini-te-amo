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
    
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Erro de conexão']);
        exit;
    }
    
    // Criar tabelas se não existirem
    $createDiscussions = "CREATE TABLE IF NOT EXISTS discussions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content_type VARCHAR(50) NOT NULL,
        content_id INT NOT NULL,
        user_id INT NOT NULL,
        user_name VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_content (content_type, content_id),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $createLikes = "CREATE TABLE IF NOT EXISTS discussion_likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        discussion_id INT NOT NULL,
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_like (discussion_id, user_id),
        FOREIGN KEY (discussion_id) REFERENCES discussions(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $createReplies = "CREATE TABLE IF NOT EXISTS discussion_replies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        discussion_id INT NOT NULL,
        user_id INT NOT NULL,
        user_name VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (discussion_id) REFERENCES discussions(id) ON DELETE CASCADE,
        INDEX idx_discussion (discussion_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn->query($createDiscussions);
    $conn->query($createLikes);
    $conn->query($createReplies);
    
    // Buscar discussões com contagens
    $stmt = $conn->prepare("
        SELECT 
            d.id,
            d.content_type,
            d.content_id,
            d.user_id,
            d.user_name,
            d.message,
            d.created_at,
            (SELECT COUNT(*) FROM discussion_likes WHERE discussion_id = d.id) as likes,
            (SELECT COUNT(*) FROM discussion_replies WHERE discussion_id = d.id) as replies_count
        FROM discussions d
        WHERE d.content_type = ? AND d.content_id = ?
        ORDER BY d.created_at DESC
        LIMIT 50
    ");
    
    $stmt->bind_param("si", $content_type, $content_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $discussions = [];
    while ($row = $result->fetch_assoc()) {
        $discussions[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'discussions' => $discussions,
        'total' => count($discussions)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao carregar discussões',
        'error' => $e->getMessage()
    ]);
}

