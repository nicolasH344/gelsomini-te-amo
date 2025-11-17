<?php
require_once 'config.php';

function getForumPosts($category = '', $search = '', $page = 1, $perPage = 10) {
    // Usar mysqli em vez de PDO
    try {
        require_once 'database.php';
        $db = new Database();
        $conn = $db->conn;
        
        $sql = "SELECT fp.id, fp.title, fp.content, fp.created_at, fp.views,
                       COALESCE(u.username, 'Usuário Anônimo') as author,
                       COALESCE(fc.name, 'Geral') as category,
                       COALESCE(fp.is_solved, 0) as is_solved,
                       (SELECT COUNT(*) FROM forum_comments WHERE post_id = fp.id) as replies
                FROM forum_posts fp 
                LEFT JOIN users u ON fp.user_id = u.id
                LEFT JOIN forum_categories fc ON fp.category_id = fc.id
                ORDER BY fp.created_at DESC
                LIMIT ?
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $perPage);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'content' => $row['content'],
                'created_at' => $row['created_at'],
                'views' => $row['views'] ?? 0,
                'author' => $row['author'],
                'category' => $row['category'],
                'is_solved' => (bool)$row['is_solved'],
                'replies' => $row['replies']
            ];
        }
        
        $db->closeConnection();
        return $posts;
        
    } catch (Exception $e) {
        // Retornar dados de exemplo se houver erro
        return [
            [
                'id' => 1,
                'title' => 'duvida css',
                'content' => 'eu estou tendo duvidas de classe...',
                'created_at' => date('Y-m-d H:i:s'),
                'views' => 0,
                'author' => 'Usuário',
                'category' => 'CSS',
                'is_solved' => false,
                'replies' => 0
            ]
        ];
    }
}

function getForumCategories() {
    try {
        require_once 'database.php';
        $db = new Database();
        $conn = $db->conn;
        
        $result = $conn->query("SELECT id, name, color FROM forum_categories ORDER BY name");
        $categories = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'color' => $row['color'] ?? 'secondary'
                ];
            }
        }
        
        $db->closeConnection();
        
        // Se não houver categorias, retornar padrões
        if (empty($categories)) {
            return [
                ['id' => 1, 'name' => 'HTML', 'color' => 'danger'],
                ['id' => 2, 'name' => 'CSS', 'color' => 'primary'],
                ['id' => 3, 'name' => 'JavaScript', 'color' => 'warning'],
                ['id' => 4, 'name' => 'PHP', 'color' => 'info']
            ];
        }
        
        return $categories;
        
    } catch (Exception $e) {
        return [
            ['id' => 1, 'name' => 'HTML', 'color' => 'danger'],
            ['id' => 2, 'name' => 'CSS', 'color' => 'primary'],
            ['id' => 3, 'name' => 'JavaScript', 'color' => 'warning'],
            ['id' => 4, 'name' => 'PHP', 'color' => 'info']
        ];
    }
}

function createForumPost($title, $content, $category_id, $user_id) {
    try {
        require_once 'database.php';
        $db = new Database();
        $conn = $db->conn;
        
        $stmt = $conn->prepare("INSERT INTO forum_posts (title, content, category_id, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $title, $content, $category_id, $user_id);
        $result = $stmt->execute();
        
        $db->closeConnection();
        return $result;
        
    } catch (Exception $e) {
        return false;
    }
}

function getForumPost($id) {
    $conn = getDBConnection();
    if (!$conn) return null;
    
    $stmt = $conn->prepare("SELECT fp.*, u.username, fc.name as category_name 
                           FROM forum_posts fp 
                           LEFT JOIN users u ON fp.user_id = u.id
                           LEFT JOIN forum_categories fc ON fp.category_id = fc.id
                           WHERE fp.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getForumComments($post_id) {
    $conn = getDBConnection();
    if (!$conn) return [];
    
    $stmt = $conn->prepare("SELECT fc.*, u.username 
                           FROM forum_comments fc 
                           LEFT JOIN users u ON fc.user_id = u.id
                           WHERE fc.post_id = ? 
                           ORDER BY fc.created_at ASC");
    $stmt->execute([$post_id]);
    return $stmt->fetchAll();
}
?>