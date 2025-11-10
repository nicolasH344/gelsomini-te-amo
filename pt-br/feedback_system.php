<?php
require_once 'config.php';

// Sistema de feedback e conquistas
class FeedbackSystem {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
        $this->setupTables();
    }
    
    private function setupTables() {
        if (!$this->conn) return;
        
        // Tabela de badges
        $this->conn->exec("CREATE TABLE IF NOT EXISTS badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            icon VARCHAR(50) NOT NULL,
            color VARCHAR(7) DEFAULT '#ffc107',
            criteria JSON NOT NULL,
            points INT DEFAULT 0,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Tabela de badges dos usuários
        $this->conn->exec("CREATE TABLE IF NOT EXISTS user_badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            badge_id INT NOT NULL,
            earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_badge (user_id, badge_id)
        )");
        
        // Tabela de feedback
        $this->conn->exec("CREATE TABLE IF NOT EXISTS exercise_feedback (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            exercise_id INT NOT NULL,
            rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            comment TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Inserir badges padrão
        $this->insertDefaultBadges();
    }
    
    private function insertDefaultBadges() {
        $badges = [
            ['Primeiro Passo', 'Completou o primeiro exercício', 'fas fa-baby', '#28a745', '{"type": "exercise_count", "value": 1}', 10],
            ['Dedicado', 'Completou 5 exercícios', 'fas fa-medal', '#ffc107', '{"type": "exercise_count", "value": 5}', 50],
            ['Especialista', 'Completou 10 exercícios', 'fas fa-trophy', '#dc3545', '{"type": "exercise_count", "value": 10}', 100],
            ['Mestre', 'Completou 20 exercícios', 'fas fa-crown', '#6f42c1', '{"type": "exercise_count", "value": 20}', 200],
            ['Sequência de Fogo', 'Estudou por 3 dias consecutivos', 'fas fa-fire', '#fd7e14', '{"type": "streak_days", "value": 3}', 75],
            ['Avaliador', 'Avaliou 5 exercícios', 'fas fa-star', '#17a2b8', '{"type": "feedback_count", "value": 5}', 30],
            ['Perfeccionista', 'Obteve nota máxima em 5 exercícios', 'fas fa-gem', '#e83e8c', '{"type": "perfect_scores", "value": 5}', 150]
        ];
        
        foreach ($badges as $badge) {
            $stmt = $this->conn->prepare("INSERT IGNORE INTO badges (name, description, icon, color, criteria, points) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute($badge);
        }
    }
    
    public function completeExercise($user_id, $exercise_id, $score = 10) {
        if (!$this->conn) return false;
        
        try {
            // Marcar exercício como completo
            $stmt = $this->conn->prepare("
                INSERT INTO user_progress (user_id, exercise_id, status, score, completed_at) 
                VALUES (?, ?, 'completed', ?, NOW())
                ON DUPLICATE KEY UPDATE 
                status = 'completed', score = GREATEST(score, VALUES(score)), completed_at = NOW()
            ");
            $stmt->execute([$user_id, $exercise_id, $score]);
            
            // Verificar conquistas
            $this->checkBadges($user_id);
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function submitFeedback($user_id, $exercise_id, $rating, $comment = '') {
        if (!$this->conn) return false;
        
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO exercise_feedback (user_id, exercise_id, rating, comment) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)
            ");
            $stmt->execute([$user_id, $exercise_id, $rating, $comment]);
            
            // Verificar badge de avaliador
            $this->checkBadges($user_id);
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function checkBadges($user_id) {
        if (!$this->conn) return;
        
        // Buscar badges não conquistados
        $stmt = $this->conn->prepare("
            SELECT b.* FROM badges b 
            WHERE b.is_active = 1 
            AND b.id NOT IN (SELECT badge_id FROM user_badges WHERE user_id = ?)
        ");
        $stmt->execute([$user_id]);
        $badges = $stmt->fetchAll();
        
        foreach ($badges as $badge) {
            $criteria = json_decode($badge['criteria'], true);
            
            if ($this->checkCriteria($user_id, $criteria)) {
                $this->awardBadge($user_id, $badge['id']);
            }
        }
    }
    
    private function checkCriteria($user_id, $criteria) {
        switch ($criteria['type']) {
            case 'exercise_count':
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM user_progress WHERE user_id = ? AND status = 'completed'");
                $stmt->execute([$user_id]);
                return $stmt->fetchColumn() >= $criteria['value'];
                
            case 'feedback_count':
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM exercise_feedback WHERE user_id = ?");
                $stmt->execute([$user_id]);
                return $stmt->fetchColumn() >= $criteria['value'];
                
            case 'perfect_scores':
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM user_progress WHERE user_id = ? AND score >= 100");
                $stmt->execute([$user_id]);
                return $stmt->fetchColumn() >= $criteria['value'];
                
            case 'streak_days':
                // Simplificado: verificar se completou exercícios em dias diferentes
                $stmt = $this->conn->prepare("
                    SELECT COUNT(DISTINCT DATE(completed_at)) 
                    FROM user_progress 
                    WHERE user_id = ? AND status = 'completed' AND completed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                ");
                $stmt->execute([$user_id]);
                return $stmt->fetchColumn() >= $criteria['value'];
        }
        
        return false;
    }
    
    private function awardBadge($user_id, $badge_id) {
        try {
            $stmt = $this->conn->prepare("INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $badge_id]);
            
            // Retornar info do badge para notificação
            $stmt = $this->conn->prepare("SELECT name, description, icon FROM badges WHERE id = ?");
            $stmt->execute([$badge_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getUserBadges($user_id) {
        if (!$this->conn) return [];
        
        $stmt = $this->conn->prepare("
            SELECT b.*, ub.earned_at 
            FROM user_badges ub 
            JOIN badges b ON ub.badge_id = b.id 
            WHERE ub.user_id = ? 
            ORDER BY ub.earned_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
    
    public function getAllBadges() {
        if (!$this->conn) return [];
        
        $stmt = $this->conn->query("SELECT * FROM badges WHERE is_active = 1 ORDER BY points ASC");
        return $stmt->fetchAll();
    }
    
    public function getExerciseFeedback($exercise_id) {
        if (!$this->conn) return [];
        
        $stmt = $this->conn->prepare("
            SELECT ef.*, u.first_name, u.last_name 
            FROM exercise_feedback ef 
            JOIN users u ON ef.user_id = u.id 
            WHERE ef.exercise_id = ? 
            ORDER BY ef.created_at DESC 
            LIMIT 10
        ");
        $stmt->execute([$exercise_id]);
        return $stmt->fetchAll();
    }
}

// Instância global
$feedbackSystem = new FeedbackSystem();
?>