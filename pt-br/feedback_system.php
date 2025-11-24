<?php
// ============================================================
// FEEDBACK_SYSTEM.PHP - Sistema de Feedback e Conquistas
// ============================================================
// Este arquivo gerencia o sistema de badges (conquistas) e
// feedback dos usuários sobre os exercícios
// ============================================================

require_once 'config.php'; // Importa configurações do sistema

// Classe principal do sistema de feedback e conquistas
class FeedbackSystem {
    private $conn; // Armazena a conexão MySQLi com o banco de dados
    
    /**
     * Construtor - Inicializa a conexão e cria tabelas necessárias
     */
    public function __construct() {
        // Obtém conexão MySQLi do banco de dados
        $this->conn = getDBConnection();
        // Configura/cria as tabelas necessárias se não existirem
        $this->setupTables();
    }
    
    /**
     * Cria as tabelas necessárias no banco de dados
     * - badges: Define as conquistas disponíveis
     * - user_badges: Relaciona usuários com badges conquistados
     * - exercise_feedback: Armazena avaliações dos exercícios
     */
    private function setupTables() {
        // Verifica se há conexão ativa, senão retorna sem fazer nada
        if (!$this->conn) return;
        
        // ============================================================
        // CRIAÇÃO DE TABELAS
        // ============================================================
        
        // Tabela de badges (conquistas disponíveis no sistema)
        $this->conn->query("CREATE TABLE IF NOT EXISTS badges (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            icon VARCHAR(50) NOT NULL DEFAULT 'fas fa-trophy',
            color VARCHAR(7) NOT NULL DEFAULT '#ffc107',
            condition_type ENUM('exercises_completed', 'tutorials_completed', 'points_earned', 'streak_days', 'category_master') NOT NULL,
            condition_value INT NOT NULL,
            condition_category_id INT NULL DEFAULT NULL,
            points_reward INT NOT NULL DEFAULT 0,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Tabela de badges dos usuários (relaciona usuários com badges conquistados)
        $this->conn->query("CREATE TABLE IF NOT EXISTS user_badges (
            id INT AUTO_INCREMENT PRIMARY KEY,              -- ID único do relacionamento
            user_id INT NOT NULL,                           -- ID do usuário
            badge_id INT NOT NULL,                          -- ID do badge conquistado
            earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Data que ganhou o badge
            UNIQUE KEY unique_user_badge (user_id, badge_id) -- Impede duplicatas
        )");
        
        // Tabela de feedback (avaliações de exercícios pelos usuários)
        $this->conn->query("CREATE TABLE IF NOT EXISTS exercise_feedback (
            id INT AUTO_INCREMENT PRIMARY KEY,              -- ID único do feedback
            user_id INT NOT NULL,                           -- ID do usuário que avaliou
            exercise_id INT NOT NULL,                       -- ID do exercício avaliado
            rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5), -- Nota de 1 a 5
            comment TEXT NULL,                              -- Comentário opcional
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Data da avaliação
        )");
        
        // Insere os badges padrão do sistema se não existirem
        $this->insertDefaultBadges();
    }
    
    /**
     * Insere os badges padrão do sistema
     * Cada badge tem: nome, descrição, ícone, cor, critérios (JSON), pontos
     */
    private function insertDefaultBadges() {
        // Array com os badges padrão do sistema
        $badges = [
            ['Primeiro Passo', 'Completou o primeiro exercício', 'fas fa-baby', '#28a745', 'exercises_completed', 1, 10],
            ['Dedicado', 'Completou 5 exercícios', 'fas fa-medal', '#ffc107', 'exercises_completed', 5, 50],
            ['Especialista', 'Completou 10 exercícios', 'fas fa-trophy', '#dc3545', 'exercises_completed', 10, 100],
            ['Mestre', 'Completou 20 exercícios', 'fas fa-crown', '#6f42c1', 'exercises_completed', 20, 200],
            ['Sequência de Fogo', 'Estudou por 3 dias consecutivos', 'fas fa-fire', '#fd7e14', 'streak_days', 3, 75]
        ];
        
        foreach ($badges as $badge) {
            $stmt = $this->conn->prepare("INSERT IGNORE INTO badges (name, description, icon, color, condition_type, condition_value, points_reward) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssii", $badge[0], $badge[1], $badge[2], $badge[3], $badge[4], $badge[5], $badge[6]);
            $stmt->execute();
        }
    }
    
    /**
     * Marca um exercício como completo para o usuário
     * @param int $user_id ID do usuário
     * @param int $exercise_id ID do exercício
     * @param int $score Pontuação obtida (padrão: 10)
     * @return bool true se sucesso, false se erro
     */
    public function completeExercise($user_id, $exercise_id, $score = 10) {
        // Verifica se há conexão com o banco
        if (!$this->conn) return false;
        
        try {
            // Insere ou atualiza o progresso do usuário
            // Se o registro já existe (mesma combinação user_id + exercise_id),
            // atualiza mantendo a maior pontuação entre a antiga e a nova
            $stmt = $this->conn->prepare("
                INSERT INTO user_progress (user_id, exercise_id, status, score, completed_at) 
                VALUES (?, ?, 'completed', ?, NOW())
                ON DUPLICATE KEY UPDATE 
                status = 'completed', score = GREATEST(score, VALUES(score)), completed_at = NOW()
            ");
            // Vincula os parâmetros (i=integer): user_id, exercise_id, score
            $stmt->bind_param("iii", $user_id, $exercise_id, $score);
            // Executa a query
            $stmt->execute();
            
            // Verifica se o usuário conquistou novos badges após completar o exercício
            $this->checkBadges($user_id);
            
            return true; // Retorna sucesso
        } catch (Exception $e) {
            // Em caso de erro, retorna false
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
            $stmt->bind_param("iiis", $user_id, $exercise_id, $rating, $comment);
            $stmt->execute();
            
            // Verificar badge de avaliador
            $this->checkBadges($user_id);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Verifica se o usuário conquistou novos badges
     * @param int $user_id ID do usuário
     */
    public function checkBadges($user_id) {
        // Verifica se há conexão com o banco
        if (!$this->conn) return;
        
        $stmt = $this->conn->prepare("
            SELECT b.* FROM badges b 
            WHERE b.is_active = 1 
            AND b.id NOT IN (SELECT badge_id FROM user_badges WHERE user_id = ?)
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $badges = $result->fetch_all(MYSQLI_ASSOC);
        
        foreach ($badges as $badge) {
            if ($this->checkCriteria($user_id, $badge['condition_type'], $badge['condition_value'])) {
                $this->awardBadge($user_id, $badge['id']);
            }
        }
    }
    
    private function checkCriteria($user_id, $condition_type, $condition_value) {
        switch ($condition_type) {
            case 'exercises_completed':
                $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM user_progress WHERE user_id = ? AND status = 'completed'");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                return $result['count'] >= $condition_value;
                
            case 'streak_days':
                $stmt = $this->conn->prepare("
                    SELECT COUNT(DISTINCT DATE(completed_at)) as count
                    FROM user_progress 
                    WHERE user_id = ? AND status = 'completed' AND completed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                ");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                return $result['count'] >= $condition_value;
        }
        
        return false;
    }
    
    private function awardBadge($user_id, $badge_id) {
        try {
            $stmt = $this->conn->prepare("INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $badge_id);
            $stmt->execute();
            
            // Retornar info do badge para notificação
            $stmt = $this->conn->prepare("SELECT name, description, icon FROM badges WHERE id = ?");
            $stmt->bind_param("i", $badge_id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (Exception $e) {
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
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getAllBadges() {
        if (!$this->conn) return [];
        
        $result = $this->conn->query("SELECT * FROM badges WHERE is_active = 1 ORDER BY points ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
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
        $stmt->bind_param("i", $exercise_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

// ============================================================
// INSTÂNCIA GLOBAL DO SISTEMA DE FEEDBACK
// ============================================================
// Cria uma instância global para ser usada em todo o sistema
// Esta instância é acessível através da variável $feedbackSystem
$feedbackSystem = new FeedbackSystem();
?>