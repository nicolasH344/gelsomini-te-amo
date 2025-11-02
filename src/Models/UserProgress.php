<?php
namespace App\Models;

class UserProgress extends BaseModel {
    protected $table = 'user_progress';
    
    public function markExerciseCompleted($userId, $exerciseId, $userCode = '', $score = 100) {
        // Verificar se já existe progresso
        $existing = $this->getExerciseProgress($userId, $exerciseId);
        
        if ($existing) {
            // Atualizar progresso existente
            return $this->update($existing['id'], [
                'completed' => 1,
                'user_code' => $userCode,
                'score' => $score,
                'completed_at' => date('Y-m-d H:i:s'),
                'attempts' => $existing['attempts'] + 1
            ]);
        } else {
            // Criar novo progresso
            return $this->create([
                'user_id' => $userId,
                'exercise_id' => $exerciseId,
                'completed' => 1,
                'user_code' => $userCode,
                'score' => $score,
                'completed_at' => date('Y-m-d H:i:s'),
                'attempts' => 1
            ]);
        }
    }
    
    public function getExerciseProgress($userId, $exerciseId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ? AND exercise_id = ?");
        $stmt->execute([$userId, $exerciseId]);
        return $stmt->fetch();
    }
    
    public function getUserStats($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_exercises,
                SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_exercises,
                AVG(CASE WHEN completed = 1 THEN score ELSE NULL END) as avg_score,
                SUM(attempts) as total_attempts
            FROM {$this->table} 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        // Buscar total de exercícios disponíveis
        $totalAvailable = $this->db->query("SELECT COUNT(*) as total FROM exercises")->fetch()['total'];
        
        return [
            'total_available' => $totalAvailable,
            'total_started' => $result['total_exercises'] ?? 0,
            'completed' => $result['completed_exercises'] ?? 0,
            'avg_score' => round($result['avg_score'] ?? 0, 1),
            'total_attempts' => $result['total_attempts'] ?? 0,
            'completion_rate' => $totalAvailable > 0 ? round(($result['completed_exercises'] ?? 0) / $totalAvailable * 100, 1) : 0
        ];
    }
    
    public function getCompletedExerciseIds($userId) {
        $stmt = $this->db->prepare("SELECT exercise_id FROM {$this->table} WHERE user_id = ? AND completed = 1");
        $stmt->execute([$userId]);
        return array_column($stmt->fetchAll(), 'exercise_id');
    }
    
    public function getCategoryProgress($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                ec.name as category_name,
                COUNT(e.id) as total_exercises,
                SUM(CASE WHEN up.completed = 1 THEN 1 ELSE 0 END) as completed_exercises
            FROM exercise_categories ec
            LEFT JOIN exercises e ON ec.id = e.category_id
            LEFT JOIN {$this->table} up ON e.id = up.exercise_id AND up.user_id = ?
            GROUP BY ec.id, ec.name
            ORDER BY ec.name
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
?>