<?php
require_once 'config.php';
require_once 'data/tutorials.php';

function getTutorialCategories() {
    return [
        ['id' => 1, 'name' => 'HTML', 'slug' => 'html', 'description' => 'Linguagem de marcação', 'icon' => 'fab fa-html5', 'color' => 'danger', 'is_active' => 1],
        ['id' => 2, 'name' => 'CSS', 'slug' => 'css', 'description' => 'Estilos e layout', 'icon' => 'fab fa-css3-alt', 'color' => 'primary', 'is_active' => 1],
        ['id' => 3, 'name' => 'JavaScript', 'slug' => 'javascript', 'description' => 'Interatividade', 'icon' => 'fab fa-js-square', 'color' => 'warning', 'is_active' => 1],
        ['id' => 4, 'name' => 'PHP', 'slug' => 'php', 'description' => 'Backend', 'icon' => 'fab fa-php', 'color' => 'info', 'is_active' => 1]
    ];
}

function updateTutorialProgress($user_id, $tutorial_id, $progress = 0, $status = 'reading') {
    if (!$user_id) return false;
    
    try {
        require_once 'database.php';
        $db = new Database();
        $conn = $db->conn;
        
        // Verificar se já existe progresso
        $stmt = $conn->prepare("SELECT id FROM tutorial_progress WHERE user_id = ? AND tutorial_id = ?");
        $stmt->bind_param("ii", $user_id, $tutorial_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Atualizar existente
            $stmt = $conn->prepare("UPDATE tutorial_progress SET status = ?, progress_percentage = ?, completed_at = ? WHERE user_id = ? AND tutorial_id = ?");
            $completed_at = ($status === 'completed') ? date('Y-m-d H:i:s') : null;
            $stmt->bind_param("sisii", $status, $progress, $completed_at, $user_id, $tutorial_id);
        } else {
            // Inserir novo
            $stmt = $conn->prepare("INSERT INTO tutorial_progress (user_id, tutorial_id, status, progress_percentage, completed_at) VALUES (?, ?, ?, ?, ?)");
            $completed_at = ($status === 'completed') ? date('Y-m-d H:i:s') : null;
            $stmt->bind_param("iisis", $user_id, $tutorial_id, $status, $progress, $completed_at);
        }
        
        $success = $stmt->execute();
        $db->closeConnection();
        return $success;
        
    } catch (Exception $e) {
        return false;
    }
}
?>