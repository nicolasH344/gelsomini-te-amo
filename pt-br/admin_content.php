<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$conn = getDBConnection();
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $type = $_GET['type'] ?? '';
        $items = [];
        
        if ($type === 'exercises') {
            $result = $conn->query("SELECT * FROM exercises ORDER BY created_at DESC");
            if ($result) {
                $items = $result->fetch_all(MYSQLI_ASSOC);
            }
        } elseif ($type === 'tutorials') {
            $result = $conn->query("SELECT * FROM tutorials ORDER BY created_at DESC");
            if ($result) {
                $items = $result->fetch_all(MYSQLI_ASSOC);
            }
        }
        
        echo json_encode(['success' => true, 'items' => $items]);
        break;
        
    case 'save':
        $type = $input['type'] ?? '';
        $id = $input['id'] ?? '';
        $title = $input['title'] ?? '';
        $description = $input['description'] ?? '';
        $difficulty = $input['difficulty'] ?? 'beginner';
        
        if ($type === 'exercise') {
            if ($id) {
                // Atualizar
                $stmt = $conn->prepare("UPDATE exercises SET title = ?, description = ?, difficulty = ? WHERE id = ?");
                $stmt->bind_param("sssi", $title, $description, $difficulty, $id);
            } else {
                // Criar novo
                $stmt = $conn->prepare("INSERT INTO exercises (title, description, difficulty, category_id, content) VALUES (?, ?, ?, 1, 'Conteúdo do exercício')");
                $stmt->bind_param("sss", $title, $description, $difficulty);
            }
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao salvar']);
            }
        } elseif ($type === 'tutorial') {
            if ($id) {
                // Atualizar
                $stmt = $conn->prepare("UPDATE tutorials SET title = ?, description = ?, difficulty = ? WHERE id = ?");
                $stmt->bind_param("sssi", $title, $description, $difficulty, $id);
            } else {
                // Criar novo
                $stmt = $conn->prepare("INSERT INTO tutorials (title, description, difficulty, category_id, content) VALUES (?, ?, ?, 1, 'Conteúdo do tutorial')");
                $stmt->bind_param("sss", $title, $description, $difficulty);
            }
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao salvar']);
            }
        }
        break;
        
    case 'delete':
        $type = $input['type'] ?? '';
        $id = (int)($input['id'] ?? 0);
        
        if ($type === 'exercise') {
            // Deletar progresso relacionado primeiro
            $conn->query("DELETE FROM exercise_progress WHERE exercise_id = $id");
            $stmt = $conn->prepare("DELETE FROM exercises WHERE id = ?");
            $stmt->bind_param("i", $id);
        } elseif ($type === 'tutorial') {
            // Deletar progresso relacionado primeiro
            $conn->query("DELETE FROM tutorial_progress WHERE tutorial_id = $id");
            $stmt = $conn->prepare("DELETE FROM tutorials WHERE id = ?");
            $stmt->bind_param("i", $id);
        } elseif ($type === 'forum_post') {
            // Deletar comentários relacionados primeiro
            $conn->query("DELETE FROM forum_comments WHERE post_id = $id");
            $stmt = $conn->prepare("DELETE FROM forum_posts WHERE id = ?");
            $stmt->bind_param("i", $id);
        } elseif ($type === 'achievement') {
            $stmt = $conn->prepare("DELETE FROM user_achievements WHERE id = ?");
            $stmt->bind_param("i", $id);
        }
        
        if (isset($stmt) && $stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao deletar']);
        }
        break;
        
    case 'reset_achievements':
        // Limpar conquistas existentes
        $conn->query("DELETE FROM user_achievements");
        
        // Recalcular conquistas baseadas em exercícios completados
        $result = $conn->query("SELECT user_id, COUNT(*) as completed FROM exercise_progress WHERE status = 'completed' GROUP BY user_id");
        
        $achievement_thresholds = [1 => 'Primeiro Exercício', 5 => 'Praticante', 10 => 'Dedicado', 20 => 'Expert'];
        $count = 0;
        
        if ($result) {
            while ($user = $result->fetch_assoc()) {
                $user_id = $user['user_id'];
                $completed = $user['completed'];
                
                foreach ($achievement_thresholds as $threshold => $name) {
                    if ($completed >= $threshold) {
                        $stmt = $conn->prepare("INSERT INTO user_achievements (user_id, achievement_type, achievement_data) VALUES (?, ?, ?)");
                        $data = json_encode(['exercises_completed' => $completed]);
                        $stmt->bind_param("iss", $user_id, $name, $data);
                        if ($stmt->execute()) $count++;
                    }
                }
            }
        }
        
        echo json_encode(['success' => true, 'message' => "$count conquistas recalculadas"]);
        break;
        
    case 'create_forum_category':
        $name = $input['name'] ?? '';
        $description = $input['description'] ?? '';
        
        if ($name) {
            $stmt = $conn->prepare("INSERT INTO forum_categories (name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $description);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao criar categoria']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Nome obrigatório']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}
?>