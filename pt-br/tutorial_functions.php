<?php
require_once 'config.php';

function getTutorials($category = '', $difficulty = '', $search = '', $page = 1, $perPage = 6) {
    try {
        require_once 'database.php';
        $db = new Database();
        $conn = $db->conn;
        
        $where = ["t.status = 'Publicado'"];
        $params = [];
        $types = "";
        
        if ($category) {
            $where[] = "tc.slug = ?";
            $params[] = $category;
            $types .= "s";
        }
        
        if ($difficulty) {
            $where[] = "t.difficulty_level = ?";
            $params[] = $difficulty;
            $types .= "s";
        }
        
        if ($search) {
            $where[] = "(t.title LIKE ? OR t.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $types .= "ss";
        }
        
        $offset = ($page - 1) * $perPage;
        $params[] = $perPage;
        $params[] = $offset;
        $types .= "ii";
        
        $sql = "SELECT t.*, tc.name as category_name, tc.color as category_color,
                       u.username as author_name
                FROM tutorials t
                LEFT JOIN tutorial_categories tc ON t.category = tc.name
                LEFT JOIN users u ON t.author_id = u.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY t.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $tutorials = [];
        while ($row = $result->fetch_assoc()) {
            $tutorials[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'slug' => $row['slug'],
                'description' => $row['description'] ?? substr(strip_tags($row['content']), 0, 150),
                'category' => $row['category'],
                'category_name' => $row['category_name'] ?? $row['category'],
                'category_color' => $row['category_color'] ?? 'secondary',
                'level' => $row['difficulty_level'],
                'duration' => $row['duration'] ?? '30 min',
                'views' => $row['views'],
                'likes' => $row['likes'] ?? 0,
                'status' => $row['status'] ?? 'Publicado',
                'author' => $row['author_name'] ?? 'Admin',
                'created_at' => $row['created_at']
            ];
        }
        
        $db->closeConnection();
        return $tutorials;
        
    } catch (Exception $e) {
        return [];
    }
}

function getTutorial($id) {
    try {
        require_once 'database.php';
        $db = new Database();
        $conn = $db->conn;
        
        $stmt = $conn->prepare("SELECT t.*, tc.name as category_name, tc.color as category_color,
                                       u.username as author_name
                                FROM tutorials t
                                LEFT JOIN tutorial_categories tc ON t.category = tc.name
                                LEFT JOIN users u ON t.author_id = u.id
                                WHERE t.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Incrementar visualizações
            $conn->query("UPDATE tutorials SET views = views + 1 WHERE id = $id");
            
            $tutorial = [
                'id' => $row['id'],
                'title' => $row['title'],
                'slug' => $row['slug'],
                'description' => $row['description'] ?? substr(strip_tags($row['content']), 0, 150),
                'content' => $row['content'],
                'category' => $row['category'],
                'category_name' => $row['category_name'] ?? $row['category'],
                'category_color' => $row['category_color'] ?? 'secondary',
                'level' => $row['difficulty_level'],
                'duration' => $row['duration'] ?? '30 min',
                'views' => $row['views'] + 1,
                'likes' => $row['likes'] ?? 0,
                'status' => $row['status'] ?? 'Publicado',
                'author' => $row['author_name'] ?? 'Admin',
                'created_at' => $row['created_at']
            ];
            
            $db->closeConnection();
            return $tutorial;
        }
        
        $db->closeConnection();
        return null;
        
    } catch (Exception $e) {
        return null;
    }
}

function getTutorialCategories() {
    try {
        require_once 'database.php';
        $db = new Database();
        $conn = $db->conn;
        
        $result = $conn->query("SELECT * FROM tutorial_categories WHERE is_active = 1 ORDER BY sort_order, name");
        $categories = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'slug' => $row['slug'],
                    'description' => $row['description'],
                    'icon' => $row['icon'],
                    'color' => $row['color']
                ];
            }
        }
        
        $db->closeConnection();
        
        // Fallback para categorias padrão
        if (empty($categories)) {
            return [
                ['name' => 'HTML', 'slug' => 'html', 'color' => 'danger', 'icon' => 'fab fa-html5'],
                ['name' => 'CSS', 'slug' => 'css', 'color' => 'primary', 'icon' => 'fab fa-css3-alt'],
                ['name' => 'JavaScript', 'slug' => 'javascript', 'color' => 'warning', 'icon' => 'fab fa-js-square'],
                ['name' => 'PHP', 'slug' => 'php', 'color' => 'info', 'icon' => 'fab fa-php']
            ];
        }
        
        return $categories;
        
    } catch (Exception $e) {
        return [
            ['name' => 'HTML', 'slug' => 'html', 'color' => 'danger', 'icon' => 'fab fa-html5'],
            ['name' => 'CSS', 'slug' => 'css', 'color' => 'primary', 'icon' => 'fab fa-css3-alt'],
            ['name' => 'JavaScript', 'slug' => 'javascript', 'color' => 'warning', 'icon' => 'fab fa-js-square'],
            ['name' => 'PHP', 'slug' => 'php', 'color' => 'info', 'icon' => 'fab fa-php']
        ];
    }
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