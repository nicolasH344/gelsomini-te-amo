<?php
require_once 'config.php';
require_once 'feedback_system.php';

// Conectar páginas ao banco 'cursinho'
class DatabaseConnector {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
        $this->migrateToNewStructure();
    }
    
    private function migrateToNewStructure() {
        if (!$this->conn) return;
        
        try {
            // Verificar se tabelas novas existem, se não, criar
            $tables = [
                'categories' => "CREATE TABLE IF NOT EXISTS categories (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(50) NOT NULL,
                    slug VARCHAR(50) UNIQUE NOT NULL,
                    description TEXT NULL,
                    icon VARCHAR(50) NULL,
                    color VARCHAR(7) DEFAULT '#6f42c1',
                    sort_order INT DEFAULT 0,
                    is_active BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )",
                
                'exercises' => "CREATE TABLE IF NOT EXISTS exercises (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    slug VARCHAR(255) UNIQUE NULL,
                    description TEXT NOT NULL,
                    content LONGTEXT NULL,
                    difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
                    category_id INT DEFAULT 1,
                    points INT DEFAULT 10,
                    estimated_time INT DEFAULT 30,
                    instructions LONGTEXT NULL,
                    view_count INT DEFAULT 0,
                    completion_count INT DEFAULT 0,
                    created_by INT DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )",
                
                'user_progress' => "CREATE TABLE IF NOT EXISTS user_progress (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    exercise_id INT NOT NULL,
                    status ENUM('not_started', 'in_progress', 'completed', 'reviewed') DEFAULT 'not_started',
                    score INT DEFAULT 0,
                    max_score INT DEFAULT 100,
                    attempts INT DEFAULT 0,
                    time_spent INT DEFAULT 0,
                    user_code LONGTEXT NULL,
                    feedback TEXT NULL,
                    started_at TIMESTAMP NULL,
                    completed_at TIMESTAMP NULL,
                    last_attempt_at TIMESTAMP NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_user_exercise (user_id, exercise_id)
                )",
                
                'badges' => "CREATE TABLE IF NOT EXISTS badges (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    description TEXT NOT NULL,
                    icon VARCHAR(50) NOT NULL,
                    color VARCHAR(7) DEFAULT '#ffc107',
                    criteria JSON NOT NULL,
                    points INT DEFAULT 0,
                    is_active BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )",
                
                'user_badges' => "CREATE TABLE IF NOT EXISTS user_badges (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    badge_id INT NOT NULL,
                    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_user_badge (user_id, badge_id)
                )",
                
                'exercise_feedback' => "CREATE TABLE IF NOT EXISTS exercise_feedback (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    exercise_id INT NOT NULL,
                    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
                    comment TEXT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_user_exercise_feedback (user_id, exercise_id)
                )"
            ];
            
            foreach ($tables as $name => $sql) {
                $this->conn->exec($sql);
            }
            
            // Inserir dados básicos se não existirem
            $this->insertBasicData();
            
        } catch (PDOException $e) {
            error_log("Erro na migração: " . $e->getMessage());
        }
    }
    
    private function insertBasicData() {
        // Categorias
        $stmt = $this->conn->query("SELECT COUNT(*) FROM categories");
        if ($stmt->fetchColumn() == 0) {
            $categories = [
                ['HTML', 'html', 'Linguagem de marcação', 'fab fa-html5', '#e34f26'],
                ['CSS', 'css', 'Linguagem de estilo', 'fab fa-css3-alt', '#1572b6'],
                ['JavaScript', 'javascript', 'Linguagem de programação', 'fab fa-js-square', '#f7df1e'],
                ['PHP', 'php', 'Linguagem server-side', 'fab fa-php', '#777bb4']
            ];
            
            $stmt = $this->conn->prepare("INSERT INTO categories (name, slug, description, icon, color) VALUES (?, ?, ?, ?, ?)");
            foreach ($categories as $cat) {
                $stmt->execute($cat);
            }
        }
        
        // Exercícios
        $stmt = $this->conn->query("SELECT COUNT(*) FROM exercises");
        if ($stmt->fetchColumn() == 0) {
            $exercises = [
                ['HTML Básico', 'html-basico', 'Aprenda as tags básicas do HTML', 'beginner', 1, 10],
                ['CSS Styling', 'css-styling', 'Estilize elementos com CSS', 'beginner', 2, 15],
                ['JavaScript Fundamentos', 'js-fundamentos', 'Variáveis e funções em JS', 'intermediate', 3, 20],
                ['Responsive Design', 'responsive-design', 'Layouts responsivos', 'intermediate', 2, 25],
                ['DOM Manipulation', 'dom-manipulation', 'Manipule o DOM com JS', 'advanced', 3, 30],
                ['PHP Básico', 'php-basico', 'Introdução ao PHP', 'beginner', 4, 15],
                ['MySQL Queries', 'mysql-queries', 'Consultas em MySQL', 'intermediate', 4, 20],
                ['AJAX Requests', 'ajax-requests', 'Requisições assíncronas', 'advanced', 3, 35]
            ];
            
            $stmt = $this->conn->prepare("INSERT INTO exercises (title, slug, description, difficulty, category_id, points) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($exercises as $ex) {
                $stmt->execute($ex);
            }
        }
        
        // Badges
        $stmt = $this->conn->query("SELECT COUNT(*) FROM badges");
        if ($stmt->fetchColumn() == 0) {
            $badges = [
                ['Primeiro Passo', 'Completou o primeiro exercício', 'fas fa-baby', '#28a745', '{"type": "exercise_count", "value": 1}', 10],
                ['Dedicado', 'Completou 5 exercícios', 'fas fa-medal', '#ffc107', '{"type": "exercise_count", "value": 5}', 50],
                ['Especialista', 'Completou 10 exercícios', 'fas fa-trophy', '#dc3545', '{"type": "exercise_count", "value": 10}', 100],
                ['Sequência de Fogo', 'Estudou por 3 dias consecutivos', 'fas fa-fire', '#fd7e14', '{"type": "streak_days", "value": 3}', 75],
                ['Avaliador', 'Avaliou 5 exercícios', 'fas fa-star', '#17a2b8', '{"type": "feedback_count", "value": 5}', 30]
            ];
            
            $stmt = $this->conn->prepare("INSERT INTO badges (name, description, icon, color, criteria, points) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($badges as $badge) {
                $stmt->execute($badge);
            }
        }
    }
    
    public function getExercises($category = '', $difficulty = '', $search = '', $page = 1, $perPage = 9) {
        if (!$this->conn) return [];
        
        $offset = ($page - 1) * $perPage;
        $where = ["1=1"];
        $params = [];
        
        if ($category) {
            $where[] = "c.slug = ?";
            $params[] = strtolower($category);
        }
        
        if ($difficulty) {
            $where[] = "e.difficulty = ?";
            $params[] = strtolower($difficulty);
        }
        
        if ($search) {
            $where[] = "(e.title LIKE ? OR e.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql = "SELECT e.*, c.name as category_name, c.color as category_color 
                FROM exercises e 
                LEFT JOIN categories c ON e.category_id = c.id 
                WHERE " . implode(' AND ', $where) . "
                ORDER BY e.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = (int)$perPage;
        $params[] = (int)$offset;
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getUserProgress($user_id) {
        if (!$this->conn) return [];
        
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(*) as total_exercises,
                COUNT(CASE WHEN up.status = 'completed' THEN 1 END) as completed_exercises,
                SUM(CASE WHEN up.status = 'completed' THEN COALESCE(e.points, 10) ELSE 0 END) as total_points,
                AVG(CASE WHEN up.status = 'completed' THEN up.score ELSE NULL END) as avg_score
            FROM exercises e 
            LEFT JOIN user_progress up ON e.id = up.exercise_id AND up.user_id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    }
    
    public function completeExercise($user_id, $exercise_id, $score = 10) {
        global $feedbackSystem;
        return $feedbackSystem->completeExercise($user_id, $exercise_id, $score);
    }
    
    public function submitFeedback($user_id, $exercise_id, $rating, $comment = '') {
        global $feedbackSystem;
        return $feedbackSystem->submitFeedback($user_id, $exercise_id, $rating, $comment);
    }
    
    public function getUserBadges($user_id) {
        global $feedbackSystem;
        return $feedbackSystem->getUserBadges($user_id);
    }
    
    public function getAllBadges() {
        global $feedbackSystem;
        return $feedbackSystem->getAllBadges();
    }
}

// Instância global
$dbConnector = new DatabaseConnector();
?>