<?php
namespace App\Models;

class Tutorial extends BaseModel {
    protected $table = 'tutorials';
    
    public function getByCategory($categoryId = null, $difficulty = null, $limit = null) {
        $sql = "
            SELECT t.*, c.name as category_name, c.color as category_color
            FROM tutorials t
            JOIN categories c ON t.category_id = c.id
            WHERE t.is_active = 1
        ";
        $params = [];
        
        if ($categoryId) {
            $sql .= " AND t.category_id = ?";
            $params[] = $categoryId;
        }
        
        if ($difficulty) {
            $sql .= " AND t.difficulty = ?";
            $params[] = $difficulty;
        }
        
        $sql .= " ORDER BY t.difficulty, t.created_at";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = (int)$limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function markAsRead($userId, $tutorialId) {
        $stmt = $this->db->prepare("
            INSERT INTO user_progress (user_id, tutorial_id, status, completed_at)
            VALUES (?, ?, 'completed', NOW())
            ON DUPLICATE KEY UPDATE 
            status = 'completed', 
            completed_at = NOW(),
            updated_at = NOW()
        ");
        $result = $stmt->execute([$userId, $tutorialId]);
        
        // Check for new badges
        if ($result) {
            $userModel = new User();
            $userModel->checkAndAwardBadges($userId);
        }
        
        return $result;
    }
    
    public function getUserProgress($tutorialId, $userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM user_progress 
            WHERE tutorial_id = ? AND user_id = ?
        ");
        $stmt->execute([$tutorialId, $userId]);
        return $stmt->fetch();
    }
    
    public function getWithProgress($userId, $categoryId = null, $difficulty = null) {
        $sql = "
            SELECT t.*, c.name as category_name, c.color as category_color,
                   up.status as user_status, up.completed_at
            FROM tutorials t
            JOIN categories c ON t.category_id = c.id
            LEFT JOIN user_progress up ON t.id = up.tutorial_id AND up.user_id = ?
            WHERE t.is_active = 1
        ";
        $params = [$userId];
        
        if ($categoryId) {
            $sql .= " AND t.category_id = ?";
            $params[] = $categoryId;
        }
        
        if ($difficulty) {
            $sql .= " AND t.difficulty = ?";
            $params[] = $difficulty;
        }
        
        $sql .= " ORDER BY t.difficulty, t.created_at";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getStats() {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total_tutorials,
                COUNT(CASE WHEN difficulty = 'iniciante' THEN 1 END) as beginner_count,
                COUNT(CASE WHEN difficulty = 'intermediario' THEN 1 END) as intermediate_count,
                COUNT(CASE WHEN difficulty = 'avancado' THEN 1 END) as advanced_count,
                AVG(reading_time) as avg_reading_time
            FROM tutorials WHERE is_active = 1
        ");
        return $stmt->fetch();
    }
}