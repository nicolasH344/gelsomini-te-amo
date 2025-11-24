<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class User extends BaseModel {
    protected $table = 'users';
    
    public function authenticate($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND is_active = 1");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $this->updateLastLogin($user['id']);
            return $user;
        }
        return false;
    }
    
    public function register($data) {
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);
        return $this->create($data);
    }
    
    public function updateLastLogin($userId) {
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        return $stmt->execute([$userId]);
    }
    
    public function getProgress($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(CASE WHEN up.status = 'completed' AND up.exercise_id IS NOT NULL THEN 1 END) as exercises_completed,
                COUNT(CASE WHEN up.status = 'completed' AND up.tutorial_id IS NOT NULL THEN 1 END) as tutorials_completed,
                COALESCE(SUM(CASE WHEN up.status = 'completed' THEN up.score ELSE 0 END), 0) as total_points,
                COUNT(ub.id) as badges_earned
            FROM users u
            LEFT JOIN user_progress up ON u.id = up.user_id
            LEFT JOIN user_badges ub ON u.id = ub.user_id
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    public function getBadges($userId) {
        $stmt = $this->db->prepare("
            SELECT b.*, ub.earned_at
            FROM badges b
            JOIN user_badges ub ON b.id = ub.badge_id
            WHERE ub.user_id = ?
            ORDER BY ub.earned_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function checkAndAwardBadges($userId) {
        $progress = $this->getProgress($userId);
        
        $stmt = $this->db->prepare("
            SELECT b.* FROM badges b
            LEFT JOIN user_badges ub ON b.id = ub.badge_id AND ub.user_id = ?
            WHERE ub.id IS NULL AND b.is_active = 1
        ");
        $stmt->execute([$userId]);
        $availableBadges = $stmt->fetchAll();
        
        $newBadges = [];
        foreach ($availableBadges as $badge) {
            $earned = false;
            
            switch ($badge['condition_type']) {
                case 'exercises_completed':
                    $earned = $progress['exercises_completed'] >= $badge['condition_value'];
                    break;
                case 'tutorials_completed':
                    $earned = $progress['tutorials_completed'] >= $badge['condition_value'];
                    break;
                case 'points_earned':
                    $earned = $progress['total_points'] >= $badge['condition_value'];
                    break;
                case 'category_master':
                    $earned = $this->checkCategoryMaster($userId, $badge['condition_category_id']);
                    break;
            }
            
            if ($earned) {
                $this->awardBadge($userId, $badge['id']);
                $newBadges[] = $badge;
            }
        }
        
        return $newBadges;
    }
    
    private function checkCategoryMaster($userId, $categoryId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(e.id) as total_exercises,
                COUNT(CASE WHEN up.status = 'completed' THEN 1 END) as completed_exercises
            FROM exercises e
            LEFT JOIN user_progress up ON e.id = up.exercise_id AND up.user_id = ?
            WHERE e.category_id = ? AND e.is_active = 1
        ");
        $stmt->execute([$userId, $categoryId]);
        $result = $stmt->fetch();
        
        return $result['total_exercises'] > 0 && $result['total_exercises'] == $result['completed_exercises'];
    }
    
    private function awardBadge($userId, $badgeId) {
        $stmt = $this->db->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $badgeId]);
    }
}