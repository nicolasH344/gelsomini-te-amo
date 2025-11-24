<?php
// ============================================================
// DATABASE_CONNECTOR.PHP - Conector de Banco de Dados
// ============================================================
// Este arquivo gerencia a estrutura do banco de dados e
// fornece funções para manipular exercícios, progresso e badges
// ============================================================

require_once 'config.php';          // Importa configurações
require_once 'feedback_system.php'; // Importa sistema de feedback

/**
 * Classe DatabaseConnector
 * Responsável por:
 * - Criar/migrar estrutura do banco de dados
 * - Gerenciar exercícios e categorias
 * - Rastrear progresso dos usuários
 * - Interface com sistema de feedback/badges
 */
class DatabaseConnector {
    private $conn; // Conexão MySQLi com o banco de dados
    
    /**
     * Construtor - Inicializa conexão e cria estrutura do banco
     */
    public function __construct() {
        // Obtém conexão MySQLi do banco
        $this->conn = getDBConnection();
        // Migra/cria estrutura de tabelas necessárias
        $this->migrateToNewStructure();
    }
    
    /**
     * Migra/cria a estrutura de tabelas do banco de dados
     * Cria todas as tabelas necessárias se não existirem:
     * - categories: Categorias de exercícios (HTML, CSS, JS, PHP)
     * - exercises: Exercícios disponíveis
     * - user_progress: Progresso dos usuários nos exercícios
     * - badges: Conquistas disponíveis
     * - user_badges: Conquistas dos usuários
     * - exercise_feedback: Avaliações dos exercícios
     */
    private function migrateToNewStructure() {
        // Verifica se há conexão, senão retorna
        if (!$this->conn) return;
        
        try {
            // Define todas as tabelas a serem criadas
            // Cada entrada é: 'nome_tabela' => 'SQL CREATE TABLE'
            $tables = [
                // ============================================================
                // TABELA: categories - Categorias de conteúdo
                // ============================================================
                'categories' => "CREATE TABLE IF NOT EXISTS categories (
                    id INT AUTO_INCREMENT PRIMARY KEY,          -- ID único
                    name VARCHAR(50) NOT NULL,                  -- Nome (HTML, CSS, etc)
                    slug VARCHAR(50) UNIQUE NOT NULL,           -- URL amigável (html, css)
                    description TEXT NULL,                      -- Descrição da categoria
                    icon VARCHAR(50) NULL,                      -- Classe do ícone
                    color VARCHAR(7) DEFAULT '#6f42c1',         -- Cor hexadecimal
                    sort_order INT DEFAULT 0,                   -- Ordem de exibição
                    is_active BOOLEAN DEFAULT TRUE,             -- Se está ativo
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Data de criação
                )",
                
                // ============================================================
                // TABELA: exercises - Exercícios do sistema
                // ============================================================
                'exercises' => "CREATE TABLE IF NOT EXISTS exercises (
                    id INT AUTO_INCREMENT PRIMARY KEY,          -- ID único
                    title VARCHAR(255) NOT NULL,                -- Título do exercício
                    slug VARCHAR(255) UNIQUE NULL,              -- URL amigável
                    description TEXT NOT NULL,                  -- Descrição breve
                    content LONGTEXT NULL,                      -- Conteúdo completo
                    difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner', -- Nível
                    category_id INT DEFAULT 1,                  -- Categoria (FK)
                    points INT DEFAULT 10,                      -- Pontos ao completar
                    estimated_time INT DEFAULT 30,              -- Tempo estimado (min)
                    instructions LONGTEXT NULL,                 -- Instruções detalhadas
                    view_count INT DEFAULT 0,                   -- Contador de visualizações
                    completion_count INT DEFAULT 0,             -- Contador de conclusões
                    created_by INT DEFAULT 1,                   -- ID do criador
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Data de criação
                )",
                
                // ============================================================
                // TABELA: user_progress - Progresso dos usuários
                // ============================================================
                'user_progress' => "CREATE TABLE IF NOT EXISTS user_progress (
                    id INT AUTO_INCREMENT PRIMARY KEY,          -- ID único
                    user_id INT NOT NULL,                       -- ID do usuário
                    exercise_id INT NOT NULL,                   -- ID do exercício
                    status ENUM('not_started', 'in_progress', 'completed', 'reviewed') DEFAULT 'not_started', -- Status
                    score INT DEFAULT 0,                        -- Pontuação obtida
                    max_score INT DEFAULT 100,                  -- Pontuação máxima
                    attempts INT DEFAULT 0,                     -- Número de tentativas
                    time_spent INT DEFAULT 0,                   -- Tempo gasto (min)
                    user_code LONGTEXT NULL,                    -- Código do usuário
                    feedback TEXT NULL,                         -- Feedback recebido
                    started_at TIMESTAMP NULL,                  -- Quando começou
                    completed_at TIMESTAMP NULL,                -- Quando completou
                    last_attempt_at TIMESTAMP NULL,             -- Última tentativa
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Data criação
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Atualização
                    UNIQUE KEY unique_user_exercise (user_id, exercise_id) -- Impede duplicatas
                )",
                
                'badges' => "CREATE TABLE IF NOT EXISTS badges (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    description TEXT NOT NULL,
                    icon VARCHAR(50) NOT NULL,
                    color VARCHAR(7) DEFAULT '#ffc107',
                    criteria TEXT NOT NULL,
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
            
            // ============================================================
            // EXECUÇÃO DAS QUERIES
            // ============================================================
            // Percorre cada tabela e executa o CREATE TABLE
            foreach ($tables as $name => $sql) {
                $this->conn->query($sql); // Executa a query (MySQLi)
            }
            
            // Insere dados básicos (categorias, exercícios exemplo, badges)
            $this->insertBasicData();
            
        } catch (Exception $e) {
            // Em caso de erro, registra no log do servidor
            error_log("Erro na migração: " . $e->getMessage());
        }
    }
    
    /**
     * Insere dados básicos no banco de dados
     * - Categorias padrão (HTML, CSS, JavaScript, PHP)
     * - Exercícios de exemplo
     * - Badges padrão
     * Só insere se as tabelas estiverem vazias
     */
    private function insertBasicData() {
        // ============================================================
        // INSERIR CATEGORIAS PADRÃO
        // ============================================================
        // Verifica se já existem categorias
        $result = $this->conn->query("SELECT COUNT(*) as count FROM categories");
        $row = $result->fetch_assoc();
        
        // Se não existir nenhuma categoria, insere as padrão
        if ($row['count'] == 0) {
            // Array com categorias padrão
            // Formato: [nome, slug, descrição, ícone Font Awesome, cor]
            $categories = [
                ['HTML', 'html', 'Linguagem de marcação', 'fab fa-html5', '#e34f26'],
                ['CSS', 'css', 'Linguagem de estilo', 'fab fa-css3-alt', '#1572b6'],
                ['JavaScript', 'javascript', 'Linguagem de programação', 'fab fa-js-square', '#f7df1e'],
                ['PHP', 'php', 'Linguagem server-side', 'fab fa-php', '#777bb4']
            ];
            
            // Prepara a query de inserção
            $stmt = $this->conn->prepare("INSERT INTO categories (name, slug, description, icon, color) VALUES (?, ?, ?, ?, ?)");
            
            // Insere cada categoria
            foreach ($categories as $cat) {
                // Vincula parâmetros (s = string)
                $stmt->bind_param("sssss", $cat[0], $cat[1], $cat[2], $cat[3], $cat[4]);
                $stmt->execute(); // Executa a inserção
            }
        }
        
        // ============================================================
        // INSERIR EXERCÍCIOS DE EXEMPLO
        // ============================================================
        // Verifica se já existem exercícios
        $result = $this->conn->query("SELECT COUNT(*) as count FROM exercises");
        $row = $result->fetch_assoc();
        
        // Se não existir nenhum exercício, insere os de exemplo
        if ($row['count'] == 0) {
            // Array com exercícios de exemplo
            // Formato: [título, slug, descrição, dificuldade, categoria_id, pontos]
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
            
            // Prepara query de inserção
            $stmt = $this->conn->prepare("INSERT INTO exercises (title, slug, description, difficulty, category_id, points) VALUES (?, ?, ?, ?, ?, ?)");
            
            // Insere cada exercício
            foreach ($exercises as $ex) {
                // Vincula parâmetros (s=string, i=integer)
                $stmt->bind_param("ssssii", $ex[0], $ex[1], $ex[2], $ex[3], $ex[4], $ex[5]);
                $stmt->execute();
            }
        }
        
        // ============================================================
        // INSERIR BADGES PADRÃO
        // ============================================================
        // Verifica se já existem badges
        $result = $this->conn->query("SELECT COUNT(*) as count FROM badges");
        $row = $result->fetch_assoc();
        
        // Se não existir nenhum badge, insere os padrão
        if ($row['count'] == 0) {
            // Array com badges padrão
            // Formato: [nome, descrição, ícone, cor, critérios JSON, pontos]
            $badges = [
                ['Primeiro Passo', 'Completou o primeiro exercício', 'fas fa-baby', '#28a745', '{"type": "exercise_count", "value": 1}', 10],
                ['Dedicado', 'Completou 5 exercícios', 'fas fa-medal', '#ffc107', '{"type": "exercise_count", "value": 5}', 50],
                ['Especialista', 'Completou 10 exercícios', 'fas fa-trophy', '#dc3545', '{"type": "exercise_count", "value": 10}', 100],
                ['Sequência de Fogo', 'Estudou por 3 dias consecutivos', 'fas fa-fire', '#fd7e14', '{"type": "streak_days", "value": 3}', 75],
                ['Avaliador', 'Avaliou 5 exercícios', 'fas fa-star', '#17a2b8', '{"type": "feedback_count", "value": 5}', 30]
            ];
            
            // Prepara query de inserção
            $stmt = $this->conn->prepare("INSERT INTO badges (name, description, icon, color, criteria, points) VALUES (?, ?, ?, ?, ?, ?)");
            
            // Insere cada badge
            foreach ($badges as $badge) {
                // Vincula parâmetros (s=string, i=integer)
                $stmt->bind_param("sssssi", $badge[0], $badge[1], $badge[2], $badge[3], $badge[4], $badge[5]);
                $stmt->execute();
            }
        }
    }
    
    /**
     * Busca exercícios com filtros opcionais
     * @param string $category Slug da categoria (ex: 'html', 'css')
     * @param string $difficulty Nível de dificuldade ('beginner', 'intermediate', 'advanced')
     * @param string $search Termo de busca (pesquisa em título e descrição)
     * @param int $page Número da página (para paginação)
     * @param int $perPage Itens por página
     * @return array Lista de exercícios encontrados
     */
    public function getExercises($category = '', $difficulty = '', $search = '', $page = 1, $perPage = 9) {
        // Verifica se há conexão
        if (!$this->conn) return [];
        
        // Calcula o offset para paginação
        // Ex: página 2 com 9 por página = offset 9 (pula os primeiros 9)
        $offset = ($page - 1) * $perPage;
        
        // Inicializa condições WHERE e parâmetros
        $where = ["1=1"]; // Condição sempre verdadeira (base)
        $params = []; // Array de parâmetros para bind_param
        
        // Adiciona filtro de categoria se fornecido
        if ($category) {
            $where[] = "c.slug = ?"; // Adiciona condição WHERE
            $params[] = strtolower($category); // Adiciona parâmetro
        }
        
        // Adiciona filtro de dificuldade se fornecido
        if ($difficulty) {
            $where[] = "e.difficulty = ?";
            $params[] = strtolower($difficulty);
        }
        
        // Adiciona filtro de busca se fornecido
        if ($search) {
            $where[] = "(e.title LIKE ? OR e.description LIKE ?)"; // Busca em título OU descrição
            $params[] = "%$search%"; // % = coringa SQL (qualquer caractere)
            $params[] = "%$search%";
        }
        
        // Monta a query SQL completa
        $sql = "SELECT e.*, c.name as category_name, c.color as category_color 
                FROM exercises e 
                LEFT JOIN categories c ON e.category_id = c.id 
                WHERE " . implode(' AND ', $where) . "
                ORDER BY e.created_at DESC 
                LIMIT ? OFFSET ?";
        
        // Adiciona parâmetros de paginação
        $params[] = (int)$perPage;
        $params[] = (int)$offset;
        
        // Prepara a query
        $stmt = $this->conn->prepare($sql);
        
        // Vincula parâmetros dinamicamente
        if (!empty($params)) {
            // Cria string de tipos (i=integer, s=string)
            $types = '';
            foreach ($params as $param) {
                $types .= is_int($param) ? 'i' : 's';
            }
            // Vincula todos os parâmetros de uma vez usando spread operator (...)
            $stmt->bind_param($types, ...$params);
        }
        
        // Executa a query
        $stmt->execute();
        // Obtém os resultados
        $result = $stmt->get_result();
        // Retorna como array associativo
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Obtém estatísticas de progresso do usuário
     * @param int $user_id ID do usuário
     * @return array Estatísticas (total de exercícios, completados, pontos, média)
     */
    public function getUserProgress($user_id) {
        // Verifica se há conexão
        if (!$this->conn) return [];
        
        // Query que calcula estatísticas do usuário
        // COUNT(*) = total de exercícios
        // COUNT(CASE...) = contagem condicional (apenas completados)
        // SUM(CASE...) = soma condicional (soma pontos apenas se completado)
        // AVG(...) = média das pontuações
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(*) as total_exercises,
                COUNT(CASE WHEN up.status = 'completed' THEN 1 END) as completed_exercises,
                SUM(CASE WHEN up.status = 'completed' THEN COALESCE(e.points, 10) ELSE 0 END) as total_points,
                AVG(CASE WHEN up.status = 'completed' THEN up.score ELSE NULL END) as avg_score
            FROM exercises e 
            LEFT JOIN user_progress up ON e.id = up.exercise_id AND up.user_id = ?
        ");
        $stmt->bind_param("i", $user_id); // Vincula o ID do usuário
        $stmt->execute(); // Executa
        $result = $stmt->get_result(); // Obtém resultado
        return $result->fetch_assoc(); // Retorna como array associativo
    }
    
    // ============================================================
    // MÉTODOS QUE DELEGAM PARA O FEEDBACK SYSTEM
    // ============================================================
    // Estes métodos são atalhos que chamam o sistema de feedback
    
    /**
     * Marca exercício como completo (delega para FeedbackSystem)
     */
    public function completeExercise($user_id, $exercise_id, $score = 10) {
        global $feedbackSystem; // Acessa instância global
        return $feedbackSystem->completeExercise($user_id, $exercise_id, $score);
    }
    
    /**
     * Submete feedback sobre exercício (delega para FeedbackSystem)
     */
    public function submitFeedback($user_id, $exercise_id, $rating, $comment = '') {
        global $feedbackSystem;
        return $feedbackSystem->submitFeedback($user_id, $exercise_id, $rating, $comment);
    }
    
    /**
     * Obtém badges do usuário (delega para FeedbackSystem)
     */
    public function getUserBadges($user_id) {
        global $feedbackSystem;
        return $feedbackSystem->getUserBadges($user_id);
    }
    
    /**
     * Obtém todos os badges disponíveis (delega para FeedbackSystem)
     */
    public function getAllBadges() {
        global $feedbackSystem;
        return $feedbackSystem->getAllBadges();
    }
}

// ============================================================
// INSTÂNCIA GLOBAL DO CONECTOR
// ============================================================
// Cria instância global acessível em todo o sistema
$dbConnector = new DatabaseConnector();
?>