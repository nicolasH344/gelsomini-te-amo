<?php
namespace App\Models;

class Exercise extends BaseModel {
    protected $table = 'exercises';
    
    public function getExercisesWithCategory($category = '', $difficulty = '', $search = '', $page = 1, $perPage = 9) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT e.*, ec.name as category_name 
                FROM exercises e 
                LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                WHERE 1=1";
        $params = [];
        
        if ($category) {
            $sql .= " AND ec.name = ?";
            $params[] = $category;
        }
        
        if ($difficulty) {
            $difficulty_map = ['Iniciante' => 'beginner', 'Intermediário' => 'intermediate', 'Avançado' => 'advanced'];
            if (isset($difficulty_map[$difficulty])) {
                $sql .= " AND e.difficulty_level = ?";
                $params[] = $difficulty_map[$difficulty];
            }
        }
        
        if ($search) {
            $sql .= " AND (e.title LIKE ? OR e.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql .= " ORDER BY e.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getExerciseWithCategory($id) {
        $stmt = $this->db->prepare("SELECT e.*, ec.name as category_name 
                                   FROM exercises e 
                                   LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                                   WHERE e.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function countExercises($category = '', $difficulty = '', $search = '') {
        $sql = "SELECT COUNT(*) as total FROM exercises e 
                LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                WHERE 1=1";
        $params = [];
        
        if ($category) {
            $sql .= " AND ec.name = ?";
            $params[] = $category;
        }
        
        if ($difficulty) {
            $difficulty_map = ['Iniciante' => 'beginner', 'Intermediário' => 'intermediate', 'Avançado' => 'advanced'];
            if (isset($difficulty_map[$difficulty])) {
                $sql .= " AND e.difficulty_level = ?";
                $params[] = $difficulty_map[$difficulty];
            }
        }
        
        if ($search) {
            $sql .= " AND (e.title LIKE ? OR e.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
?>