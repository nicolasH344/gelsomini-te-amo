<?php
namespace App\Controllers;

use App\Models\Forum;

class ForumController extends BaseController {
    private $forumModel;
    
    public function __construct() {
        $this->forumModel = new Forum();
    }
    
    public function index() {
        // Processar criação de post
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post']) && $this->isLoggedIn()) {
            $title = $this->sanitize($_POST['title']);
            $content = $this->sanitize($_POST['content']);
            $categoryId = (int)$_POST['category_id'];
            $userId = $this->getCurrentUser()['id'];
            
            if ($this->forumModel->create([
                'title' => $title,
                'content' => $content,
                'category_id' => $categoryId,
                'user_id' => $userId
            ])) {
                $this->setSuccess('Post criado com sucesso!');
                $this->redirect('forum_index.php');
            }
        }
        
        // Parâmetros de filtro
        $category = $this->sanitize($_GET['category'] ?? '');
        $search = $this->sanitize($_GET['search'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        
        // Buscar dados
        $posts = $this->forumModel->getPostsWithDetails($category, $search, $page);
        $categories = $this->getCategories();
        
        return [
            'posts' => $posts,
            'categories' => $categories,
            'category' => $category,
            'search' => $search,
            'page' => $page
        ];
    }
    
    public function show($id) {
        $post = $this->forumModel->getPostWithDetails($id);
        if (!$post) {
            $this->redirect('forum_index.php');
        }
        
        // Processar novo comentário
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment']) && $this->isLoggedIn()) {
            $content = $this->sanitize($_POST['content']);
            $userId = $this->getCurrentUser()['id'];
            
            if ($this->forumModel->addComment($id, $userId, $content)) {
                $this->setSuccess('Comentário adicionado!');
                $this->redirect("forum_post.php?id=$id");
            }
        }
        
        $comments = $this->forumModel->getComments($id);
        
        return [
            'post' => $post,
            'comments' => $comments
        ];
    }
    
    private function getCategories() {
        return [
            ['id' => 1, 'name' => 'HTML'],
            ['id' => 2, 'name' => 'CSS'],
            ['id' => 3, 'name' => 'JavaScript'],
            ['id' => 4, 'name' => 'PHP']
        ];
    }
}
?>