<?php
namespace App\Models;

class Forum extends BaseModel {
    protected $table = 'forum_posts';
    
    public function getPosts($categoryId = null, $search = null, $limit = 20, $offset = 0) {
        $sql = "
            SELECT fp.*, u.username, u.first_name, u.last_name, u.avatar,
                   fc.name as category_name, fc.color as category_color,
                   COUNT(fcom.id) as replies_count
            FROM forum_posts fp
            JOIN users u ON fp.user_id = u.id
            JOIN forum_categories fc ON fp.category_id = fc.id
            LEFT JOIN forum_comments fcom ON fp.id = fcom.post_id
            WHERE fc.is_active = 1
        ";
        $params = [];
        
        if ($categoryId) {
            $sql .= " AND fp.category_id = ?";
            $params[] = $categoryId;
        }
        
        if ($search) {
            $sql .= " AND (fp.title LIKE ? OR fp.content LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " GROUP BY fp.id ORDER BY fp.is_pinned DESC, fp.created_at DESC LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getPost($id) {
        $stmt = $this->db->prepare("
            SELECT fp.*, u.username, u.first_name, u.last_name, u.avatar,
                   fc.name as category_name, fc.color as category_color
            FROM forum_posts fp
            JOIN users u ON fp.user_id = u.id
            JOIN forum_categories fc ON fp.category_id = fc.id
            WHERE fp.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getComments($postId) {
        $stmt = $this->db->prepare("
            SELECT fc.*, u.username, u.first_name, u.last_name, u.avatar
            FROM forum_comments fc
            JOIN users u ON fc.user_id = u.id
            WHERE fc.post_id = ?
            ORDER BY fc.is_solution DESC, fc.created_at ASC
        ");
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }
    
    public function createPost($data) {
        return $this->create($data);
    }
    
    public function createComment($postId, $userId, $content, $isSolution = false) {
        $stmt = $this->db->prepare("
            INSERT INTO forum_comments (post_id, user_id, content, is_solution)
            VALUES (?, ?, ?, ?)
        ");
        $result = $stmt->execute([$postId, $userId, $content, $isSolution]);
        
        if ($result && $isSolution) {
            $this->markAsSolved($postId);
        }
        
        return $result;
    }
    
    public function markAsSolved($postId) {
        $stmt = $this->db->prepare("UPDATE forum_posts SET is_solved = 1 WHERE id = ?");
        return $stmt->execute([$postId]);
    }
    
    public function incrementViews($postId) {
        $stmt = $this->db->prepare("UPDATE forum_posts SET views = views + 1 WHERE id = ?");
        return $stmt->execute([$postId]);
    }
    
    public function getCategories() {
        $stmt = $this->db->prepare("
            SELECT fc.*, COUNT(fp.id) as posts_count
            FROM forum_categories fc
            LEFT JOIN forum_posts fp ON fc.id = fp.category_id
            WHERE fc.is_active = 1
            GROUP BY fc.id
            ORDER BY fc.sort_order, fc.name
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getRecentPosts($limit = 5) {
        $stmt = $this->db->prepare("
            SELECT fp.id, fp.title, fp.created_at, u.username
            FROM forum_posts fp
            JOIN users u ON fp.user_id = u.id
            ORDER BY fp.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([(int)$limit]);
        return $stmt->fetchAll();
    }
}