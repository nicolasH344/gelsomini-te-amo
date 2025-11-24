<?php
namespace App\Models;

class Exercise extends BaseModel {
    protected $table = 'exercises';
    
    public function getByCategory($categoryId = null, $difficulty = null, $limit = null) {
        $sql = "
            SELECT e.*, c.name as category_name, c.color as category_color
            FROM exercises e
            JOIN categories c ON e.category_id = c.id
            WHERE e.is_active = 1
        ";
        $params = [];
        
        if ($categoryId) {
            $sql .= " AND e.category_id = ?";
            $params[] = $categoryId;
        }
        
        if ($difficulty) {
            $sql .= " AND e.difficulty = ?";
            $params[] = $difficulty;
        }
        
        $sql .= " ORDER BY e.difficulty, e.created_at";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = (int)$limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getUserProgress($exerciseId, $userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM user_progress 
            WHERE exercise_id = ? AND user_id = ?
        ");
        $stmt->execute([$exerciseId, $userId]);
        return $stmt->fetch();
    }
    
    public function updateProgress($userId, $exerciseId, $status, $score = 0, $timeSpent = 0) {
        $existing = $this->getUserProgress($exerciseId, $userId);
        
        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE user_progress 
                SET status = ?, score = GREATEST(score, ?), attempts = attempts + 1, 
                    time_spent = time_spent + ?, completed_at = CASE WHEN ? = 'completed' THEN NOW() ELSE completed_at END,
                    updated_at = NOW()
                WHERE exercise_id = ? AND user_id = ?
            ");
            $result = $stmt->execute([$status, $score, $timeSpent, $status, $exerciseId, $userId]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO user_progress (user_id, exercise_id, status, score, time_spent, completed_at)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $completedAt = $status === 'completed' ? date('Y-m-d H:i:s') : null;
            $result = $stmt->execute([$userId, $exerciseId, $status, $score, $timeSpent, $completedAt]);
        }
        
        // Check for new badges
        if ($result && $status === 'completed') {
            $userModel = new User();
            $userModel->checkAndAwardBadges($userId);
        }
        
        return $result;
    }
    
    public function getWithProgress($userId, $categoryId = null, $difficulty = null) {
        $sql = "
            SELECT e.*, c.name as category_name, c.color as category_color,
                   up.status as user_status, up.score as user_score, up.completed_at
            FROM exercises e
            JOIN categories c ON e.category_id = c.id
            LEFT JOIN user_progress up ON e.id = up.exercise_id AND up.user_id = ?
            WHERE e.is_active = 1
        ";
        $params = [$userId];
        
        if ($categoryId) {
            $sql .= " AND e.category_id = ?";
            $params[] = $categoryId;
        }
        
        if ($difficulty) {
            $sql .= " AND e.difficulty = ?";
            $params[] = $difficulty;
        }
        
        $sql .= " ORDER BY e.difficulty, e.created_at";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getStats() {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total_exercises,
                COUNT(CASE WHEN difficulty = 'iniciante' THEN 1 END) as beginner_count,
                COUNT(CASE WHEN difficulty = 'intermediario' THEN 1 END) as intermediate_count,
                COUNT(CASE WHEN difficulty = 'avancado' THEN 1 END) as advanced_count
            FROM exercises WHERE is_active = 1
        ");
        return $stmt->fetch();
    }
}