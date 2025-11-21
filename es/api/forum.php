<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../src/autoload.php';

use App\Models\Forum;

session_start();

$method = $_SERVER['REQUEST_METHOD'];
$forumModel = new Forum();

try {
    if ($method === 'GET') {
        $action = $_GET['action'] ?? 'posts';
        
        switch ($action) {
            case 'posts':
                $categoryId = $_GET['category'] ?? null;
                $search = $_GET['search'] ?? null;
                $page = max(1, (int)($_GET['page'] ?? 1));
                $limit = 20;
                $offset = ($page - 1) * $limit;
                
                $posts = $forumModel->getPosts($categoryId, $search, $limit, $offset);
                echo json_encode(['success' => true, 'posts' => $posts]);
                break;
                
            case 'post':
                $postId = $_GET['id'] ?? null;
                if (!$postId) throw new Exception('ID do post não fornecido');
                
                $post = $forumModel->getPost($postId);
                $comments = $forumModel->getComments($postId);
                $forumModel->incrementViews($postId);
                
                echo json_encode([
                    'success' => true,
                    'post' => $post,
                    'comments' => $comments
                ]);
                break;
                
            case 'categories':
                $categories = $forumModel->getCategories();
                echo json_encode(['success' => true, 'categories' => $categories]);
                break;
        }
        
    } elseif ($method === 'POST') {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('Usuário não autenticado');
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
        
        switch ($action) {
            case 'create_post':
                $result = $forumModel->createPost([
                    'title' => $input['title'],
                    'content' => $input['content'],
                    'user_id' => $_SESSION['user_id'],
                    'category_id' => $input['category_id']
                ]);
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Post criado com sucesso']);
                } else {
                    throw new Exception('Erro ao criar post');
                }
                break;
                
            case 'create_comment':
                $result = $forumModel->createComment(
                    $input['post_id'],
                    $_SESSION['user_id'],
                    $input['content'],
                    $input['is_solution'] ?? false
                );
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Comentário adicionado']);
                } else {
                    throw new Exception('Erro ao adicionar comentário');
                }
                break;
        }
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}