<?php
require_once 'config.php';

function getExercises($category = '', $difficulty = '', $search = '', $page = 1, $perPage = 9) {
    $conn = getDBConnection();
    if (!$conn) {
        return [
            ['id' => 1, 'title' => 'Estrutura Básica HTML', 'description' => 'Aprenda a criar a estrutura básica de uma página HTML', 'difficulty_level' => 'beginner', 'category_name' => 'HTML'],
            ['id' => 2, 'title' => 'Estilização com CSS', 'description' => 'Pratique estilização básica com CSS', 'difficulty_level' => 'beginner', 'category_name' => 'CSS'],
            ['id' => 3, 'title' => 'Interatividade com JavaScript', 'description' => 'Adicione interatividade às suas páginas', 'difficulty_level' => 'intermediate', 'category_name' => 'JavaScript']
        ];
    }
    
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
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getExercise($id) {
    $conn = getDBConnection();
    if (!$conn) return null;
    
    $stmt = $conn->prepare("SELECT e.*, ec.name as category_name 
                           FROM exercises e 
                           LEFT JOIN exercise_categories ec ON e.category_id = ec.id 
                           WHERE e.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function countExercises($category = '', $difficulty = '', $search = '') {
    $conn = getDBConnection();
    if (!$conn) return 6;
    
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
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}
?>