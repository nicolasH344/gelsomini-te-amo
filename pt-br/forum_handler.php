<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

$conn = getDBConnection();
$user_id = getCurrentUser()['id'];
$action = $_POST['action'] ?? '';

// Criar tabelas se não existirem
$conn->query("CREATE TABLE IF NOT EXISTS forum_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS forum_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS forum_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

switch ($action) {
    case 'create_post':
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        
        if (empty($title) || empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Título e conteúdo são obrigatórios']);
            break;
        }
        
        $stmt = $conn->prepare("INSERT INTO forum_posts (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $content);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'post_id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao criar post']);
        }
        break;
        
    case 'create_comment':
        $post_id = (int)($_POST['post_id'] ?? 0);
        $content = $_POST['content'] ?? '';
        
        if (!$post_id || empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Post ID e conteúdo são obrigatórios']);
            break;
        }
        
        $stmt = $conn->prepare("INSERT INTO forum_comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user_id, $content);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'comment_id' => $conn->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao criar comentário']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}
?>