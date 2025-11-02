<?php
namespace App\Models;

class Badge extends BaseModel {
    protected $table = 'badges';
    
    public function getUserBadges($userId) {
        $stmt = $this->db->prepare("
            SELECT b.*, ub.earned_at 
            FROM user_badges ub 
            JOIN badges b ON ub.badge_id = b.id 
            WHERE ub.user_id = ? 
            ORDER BY ub.earned_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function checkAndAwardBadges($userId) {
        $badges = [
            ['id' => 1, 'condition' => 'first_exercise', 'check' => $this->checkFirstExercise($userId)],
            ['id' => 2, 'condition' => 'exercise_master', 'check' => $this->checkExerciseMaster($userId)],
            ['id' => 3, 'condition' => 'forum_contributor', 'check' => $this->checkForumContributor($userId)],
            ['id' => 4, 'condition' => 'streak_7', 'check' => $this->checkStreak($userId, 7)],
            ['id' => 5, 'condition' => 'perfect_score', 'check' => $this->checkPerfectScore($userId)]
        ];
        
        foreach ($badges as $badge) {
            if ($badge['check'] && !$this->userHasBadge($userId, $badge['id'])) {
                $this->awardBadge($userId, $badge['id']);
            }
        }
    }
    
    private function checkFirstExercise($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM user_progress WHERE user_id = ? AND completed = 1");
        $stmt->execute([$userId]);
        return $stmt->fetch()['count'] >= 1;
    }
    
    private function checkExerciseMaster($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM user_progress WHERE user_id = ? AND completed = 1");
        $stmt->execute([$userId]);
        return $stmt->fetch()['count'] >= 10;
    }
    
    private function checkForumContributor($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM forum_posts WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch()['count'] >= 5;
    }
    
    private function checkStreak($userId, $days) {
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT DATE(completed_at)) as streak 
            FROM user_progress 
            WHERE user_id = ? AND completed = 1 
            AND completed_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        $stmt->execute([$userId, $days]);
        return $stmt->fetch()['streak'] >= $days;
    }
    
    private function checkPerfectScore($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM user_progress WHERE user_id = ? AND score = 100");
        $stmt->execute([$userId]);
        return $stmt->fetch()['count'] >= 5;
    }
    
    private function userHasBadge($userId, $badgeId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM user_badges WHERE user_id = ? AND badge_id = ?");
        $stmt->execute([$userId, $badgeId]);
        return $stmt->fetch()['count'] > 0;
    }
    
    private function awardBadge($userId, $badgeId) {
        $stmt = $this->db->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $badgeId]);
    }
}
?>