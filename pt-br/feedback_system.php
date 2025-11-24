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
            id INT AUTO_INCREMENT PRIMARY KEY,              -- ID único do badge
            name VARCHAR(100) NOT NULL,                     -- Nome do badge (ex: 'Iniciante')
            description TEXT NOT NULL,                      -- Descrição do que é necessário
            icon VARCHAR(50) NOT NULL,                      -- Classe do ícone (Font Awesome)
            color VARCHAR(7) DEFAULT '#ffc107',             -- Cor em hexadecimal
            criteria TEXT NOT NULL,                         -- JSON com critérios (tipo e valor)
            points INT DEFAULT 0,                           -- Pontos ganhos ao conquistar
            is_active BOOLEAN DEFAULT TRUE,                 -- Se o badge está ativo
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Data de criação
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
        // Formato: [nome, descrição, ícone Font Awesome, cor hexadecimal, critérios JSON, pontos]
        $badges = [
            // Badge de primeiro exercício (fácil de conquistar)
            ['Primeiro Passo', 'Completou o primeiro exercício', 'fas fa-baby', '#28a745', '{"type": "exercise_count", "value": 1}', 10],
            // Badge de 5 exercícios (dedicação inicial)
            ['Dedicado', 'Completou 5 exercícios', 'fas fa-medal', '#ffc107', '{"type": "exercise_count", "value": 5}', 50],
            // Badge de 10 exercícios (nível intermediário)
            ['Especialista', 'Completou 10 exercícios', 'fas fa-trophy', '#dc3545', '{"type": "exercise_count", "value": 10}', 100],
            // Badge de 20 exercícios (mestre)
            ['Mestre', 'Completou 20 exercícios', 'fas fa-crown', '#6f42c1', '{"type": "exercise_count", "value": 20}', 200],
            // Badge de sequência (estudar vários dias seguidos)
            ['Sequência de Fogo', 'Estudou por 3 dias consecutivos', 'fas fa-fire', '#fd7e14', '{"type": "streak_days", "value": 3}', 75],
            // Badge de feedback (avaliar exercícios)
            ['Avaliador', 'Avaliou 5 exercícios', 'fas fa-star', '#17a2b8', '{"type": "feedback_count", "value": 5}', 30],
            // Badge de perfeccionismo (notas máximas)
            ['Perfeccionista', 'Obteve nota máxima em 5 exercícios', 'fas fa-gem', '#e83e8c', '{"type": "perfect_scores", "value": 5}', 150]
        ];
        
        // Percorre cada badge e insere no banco (IGNORE evita duplicatas)
        foreach ($badges as $badge) {
            // Prepara a query de inserção
            $stmt = $this->conn->prepare("INSERT IGNORE INTO badges (name, description, icon, color, criteria, points) VALUES (?, ?, ?, ?, ?, ?)");
            // Vincula os parâmetros (s=string, i=integer)
            $stmt->bind_param("sssssi", $badge[0], $badge[1], $badge[2], $badge[3], $badge[4], $badge[5]);
            // Executa a inserção
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
        
        // Busca todos os badges ativos que o usuário AINDA NÃO conquistou
        // Usando subquery para excluir os badges que o usuário já tem
        $stmt = $this->conn->prepare("
            SELECT b.* FROM badges b 
            WHERE b.is_active = 1 
            AND b.id NOT IN (SELECT badge_id FROM user_badges WHERE user_id = ?)
        ");
        $stmt->bind_param("i", $user_id); // Vincula o ID do usuário
        $stmt->execute(); // Executa a query
        $result = $stmt->get_result(); // Obtém o resultado
        $badges = $result->fetch_all(MYSQLI_ASSOC); // Converte para array associativo
        
        // Percorre cada badge não conquistado
        foreach ($badges as $badge) {
            // Decodifica o JSON de critérios para array PHP
            $criteria = json_decode($badge['criteria'], true);
            
            // Verifica se o usuário atende aos critérios deste badge
            if ($this->checkCriteria($user_id, $criteria)) {
                // Se atende, concede o badge ao usuário
                $this->awardBadge($user_id, $badge['id']);
            }
        }
    }
    
    /**
     * Verifica se o usuário atende aos critérios de um badge
     * @param int $user_id ID do usuário
     * @param array $criteria Critérios do badge (tipo e valor necessário)
     * @return bool true se atende, false caso contrário
     */
    private function checkCriteria($user_id, $criteria) {
        // Switch baseado no tipo de critério
        switch ($criteria['type']) {
            
            // Critério: Número de exercícios completados
            case 'exercise_count':
                $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM user_progress WHERE user_id = ? AND status = 'completed'");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                // Retorna true se contagem >= valor necessário
                return $result['count'] >= $criteria['value'];
                
            // Critério: Número de feedbacks/avaliações feitas
            case 'feedback_count':
                $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM exercise_feedback WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                return $result['count'] >= $criteria['value'];
                
            // Critério: Número de notas perfeitas (score >= 100)
            case 'perfect_scores':
                $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM user_progress WHERE user_id = ? AND score >= 100");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                return $result['count'] >= $criteria['value'];
                
            // Critério: Dias consecutivos de estudo (streak)
            case 'streak_days':
                // Conta quantos dias DISTINTOS o usuário completou exercícios
                // nos últimos 7 dias
                $stmt = $this->conn->prepare("
                    SELECT COUNT(DISTINCT DATE(completed_at)) as count
                    FROM user_progress 
                    WHERE user_id = ? AND status = 'completed' AND completed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                ");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                return $result['count'] >= $criteria['value'];
        }
        
        // Se o tipo não foi reconhecido, retorna false
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