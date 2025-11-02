<?php
namespace App\Models;

class Forum extends BaseModel {
    protected $table = 'forum_posts';
    
    public function getPostsWithDetails($category = '', $search = '', $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT fp.*, u.username, fc.name as category_name,
                (SELECT COUNT(*) FROM forum_comments WHERE post_id = fp.id) as replies
                FROM forum_posts fp 
                LEFT JOIN users u ON fp.user_id = u.id
                LEFT JOIN forum_categories fc ON fp.category_id = fc.id
                WHERE 1=1";
        $params = [];
        
        if ($category) {
            $sql .= " AND fc.name = ?";
            $params[] = $category;
        }
        
        if ($search) {
            $sql .= " AND (fp.title LIKE ? OR fp.content LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql .= " ORDER BY fp.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getPostWithDetails($id) {
        $stmt = $this->db->prepare("SELECT fp.*, u.username, fc.name as category_name 
                                   FROM forum_posts fp 
                                   LEFT JOIN users u ON fp.user_id = u.id
                                   LEFT JOIN forum_categories fc ON fp.category_id = fc.id
                                   WHERE fp.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getComments($postId) {
        $stmt = $this->db->prepare("SELECT fc.*, u.username 
                                   FROM forum_comments fc 
                                   LEFT JOIN users u ON fc.user_id = u.id
                                   WHERE fc.post_id = ? 
                                   ORDER BY fc.created_at ASC");
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }
    
    public function addComment($postId, $userId, $content) {
        $stmt = $this->db->prepare("INSERT INTO forum_comments (post_id, user_id, content) VALUES (?, ?, ?)");
        return $stmt->execute([$postId, $userId, $content]);
    }
}
?>