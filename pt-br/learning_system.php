<?php
// Sistema de Aprendizado Progressivo Adaptativo
class LearningSystem {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
        $this->initializeTables();
    }
    
    private function initializeTables() {
        // Tabela de trilhas de aprendizado
        $this->db->conn->query("
            CREATE TABLE IF NOT EXISTS learning_paths (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                difficulty_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
                estimated_hours INT DEFAULT 10,
                prerequisites TEXT,
                learning_objectives TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Tabela de exercícios com metodologia
        $this->db->conn->query("
            CREATE TABLE IF NOT EXISTS exercise_methodology (
                id INT AUTO_INCREMENT PRIMARY KEY,
                exercise_id INT NOT NULL,
                learning_type ENUM('guided', 'challenge', 'project', 'quiz', 'debug') DEFAULT 'guided',
                hint_system JSON,
                step_by_step JSON,
                common_mistakes JSON,
                success_criteria JSON,
                adaptive_difficulty BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Tabela de progresso adaptativo
        $this->db->conn->query("
            CREATE TABLE IF NOT EXISTS adaptive_progress (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                exercise_id INT NOT NULL,
                attempts INT DEFAULT 0,
                hints_used INT DEFAULT 0,
                time_spent INT DEFAULT 0,
                difficulty_adjustment FLOAT DEFAULT 1.0,
                learning_style ENUM('visual', 'kinesthetic', 'auditory', 'reading') DEFAULT 'visual',
                mastery_level FLOAT DEFAULT 0.0,
                last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_user_exercise (user_id, exercise_id)
            )
        ");
        
        $this->seedLearningData();
    }
    
    private function seedLearningData() {
        // Criar trilhas de aprendizado
        $paths = [
            [
                'name' => 'Fundamentos Web',
                'description' => 'Aprenda HTML, CSS e JavaScript do zero',
                'difficulty_level' => 'beginner',
                'estimated_hours' => 20,
                'prerequisites' => 'Nenhum conhecimento prévio necessário',
                'learning_objectives' => 'Criar páginas web básicas, estilizar com CSS, adicionar interatividade com JavaScript'
            ],
            [
                'name' => 'Desenvolvimento Frontend',
                'description' => 'Domine técnicas avançadas de frontend',
                'difficulty_level' => 'intermediate',
                'estimated_hours' => 30,
                'prerequisites' => 'HTML, CSS e JavaScript básicos',
                'learning_objectives' => 'Criar interfaces responsivas, usar frameworks, otimizar performance'
            ],
            [
                'name' => 'Programação Backend',
                'description' => 'Desenvolva aplicações server-side com PHP',
                'difficulty_level' => 'intermediate',
                'estimated_hours' => 25,
                'prerequisites' => 'Lógica de programação básica',
                'learning_objectives' => 'Criar APIs, trabalhar com bancos de dados, implementar autenticação'
            ]
        ];
        
        foreach ($paths as $path) {
            $stmt = $this->db->conn->prepare("
                INSERT IGNORE INTO learning_paths (name, description, difficulty_level, estimated_hours, prerequisites, learning_objectives)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssiss", 
                $path['name'],
                $path['description'],
                $path['difficulty_level'],
                $path['estimated_hours'],
                $path['prerequisites'],
                $path['learning_objectives']
            );
            $stmt->execute();
            $stmt->close();
        }
    }
    
    public function getPersonalizedExercises($user_id, $limit = 6) {
        // Analisar padrão de aprendizado do usuário
        $learningProfile = $this->analyzeLearningProfile($user_id);
        
        // Buscar exercícios adaptados ao perfil
        $stmt = $this->db->conn->prepare("
            SELECT e.*, em.learning_type, em.adaptive_difficulty,
                   COALESCE(ap.mastery_level, 0) as mastery_level,
                   COALESCE(ap.attempts, 0) as attempts
            FROM exercises e
            LEFT JOIN exercise_methodology em ON e.id = em.exercise_id
            LEFT JOIN adaptive_progress ap ON e.id = ap.exercise_id AND ap.user_id = ?
            WHERE COALESCE(ap.mastery_level, 0) < 0.8
            ORDER BY 
                CASE 
                    WHEN ap.mastery_level IS NULL THEN 1
                    WHEN ap.mastery_level < 0.3 THEN 2
                    WHEN ap.mastery_level < 0.6 THEN 3
                    ELSE 4
                END,
                e.difficulty_level,
                RAND()
            LIMIT ?
        ");
        
        if (!$stmt) {
            return [];
        }
        
        $stmt->bind_param("ii", $user_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $exercises = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $exercises[] = $row;
            }
        }
        
        $stmt->close();
        return $exercises;
    }
    
    public function getExerciseWithMethodology($exercise_id, $user_id = null) {
        $stmt = $this->db->conn->prepare("
            SELECT e.*, em.learning_type, em.hint_system, em.step_by_step, 
                   em.common_mistakes, em.success_criteria, em.adaptive_difficulty
            FROM exercises e
            LEFT JOIN exercise_methodology em ON e.id = em.exercise_id
            WHERE e.id = ?
        ");
        
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param("i", $exercise_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $exercise = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        
        if ($exercise && $user_id) {
            // Buscar progresso adaptativo
            $stmt = $this->db->conn->prepare("
                SELECT * FROM adaptive_progress WHERE user_id = ? AND exercise_id = ?
            ");
            
            if ($stmt) {
                $stmt->bind_param("ii", $user_id, $exercise_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $progress = $result ? $result->fetch_assoc() : null;
                $stmt->close();
            } else {
                $progress = null;
            }
            
            $exercise['adaptive_progress'] = $progress;
            
            // Adaptar dificuldade baseado no progresso
            if ($progress && $exercise['adaptive_difficulty']) {
                $exercise = $this->adaptExerciseDifficulty($exercise, $progress);
            }
        }
        
        return $exercise;
    }
    
    private function analyzeLearningProfile($user_id) {
        $stmt = $this->db->conn->prepare("
            SELECT 
                AVG(attempts) as avg_attempts,
                AVG(hints_used) as avg_hints,
                AVG(time_spent) as avg_time,
                AVG(mastery_level) as avg_mastery,
                learning_style,
                COUNT(*) as total_exercises
            FROM adaptive_progress 
            WHERE user_id = ?
            GROUP BY learning_style
            ORDER BY COUNT(*) DESC
            LIMIT 1
        ");
        
        if (!$stmt) {
            return [
                'avg_attempts' => 1,
                'avg_hints' => 0,
                'avg_time' => 300,
                'avg_mastery' => 0,
                'learning_style' => 'visual',
                'total_exercises' => 0
            ];
        }
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $profile = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        
        return $profile ?: [
            'avg_attempts' => 1,
            'avg_hints' => 0,
            'avg_time' => 300,
            'avg_mastery' => 0,
            'learning_style' => 'visual',
            'total_exercises' => 0
        ];
    }
    
    private function adaptExerciseDifficulty($exercise, $progress) {
        $difficulty_multiplier = $progress['difficulty_adjustment'];
        
        // Adaptar baseado no número de tentativas
        if ($progress['attempts'] > 3) {
            $difficulty_multiplier *= 0.8; // Reduzir dificuldade
        } elseif ($progress['attempts'] == 1 && $progress['mastery_level'] > 0.9) {
            $difficulty_multiplier *= 1.2; // Aumentar dificuldade
        }
        
        $exercise['adapted_difficulty'] = $difficulty_multiplier;
        return $exercise;
    }
    
    public function updateProgress($user_id, $exercise_id, $success, $time_spent, $hints_used = 0) {
        // Calcular nível de maestria baseado no desempenho
        $mastery_score = $this->calculateMasteryScore($success, $time_spent, $hints_used);
        
        $stmt = $this->db->conn->prepare("
            INSERT INTO adaptive_progress (user_id, exercise_id, attempts, hints_used, time_spent, mastery_level, last_attempt)
            VALUES (?, ?, 1, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
            attempts = attempts + 1,
            hints_used = hints_used + ?,
            time_spent = time_spent + ?,
            mastery_level = GREATEST(mastery_level, ?),
            last_attempt = NOW()
        ");
        $stmt->bind_param("iiiidid", $user_id, $exercise_id, $hints_used, $time_spent, $mastery_score, $hints_used, $time_spent, $mastery_score);
        $stmt->execute();
        $stmt->close();
        
        return $mastery_score;
    }
    
    private function calculateMasteryScore($success, $time_spent, $hints_used) {
        $base_score = $success ? 1.0 : 0.3;
        
        // Penalizar por tempo excessivo (mais de 10 minutos)
        if ($time_spent > 600) {
            $base_score *= 0.8;
        }
        
        // Penalizar por uso excessivo de dicas
        if ($hints_used > 2) {
            $base_score *= 0.7;
        }
        
        // Bonificar por eficiência
        if ($success && $time_spent < 180 && $hints_used == 0) {
            $base_score = min(1.0, $base_score * 1.2);
        }
        
        return round($base_score, 2);
    }
    
    public function getLearningPaths() {
        $result = $this->db->conn->query("SELECT * FROM learning_paths ORDER BY difficulty_level, estimated_hours");
        $paths = [];
        
        while ($row = $result->fetch_assoc()) {
            $paths[] = $row;
        }
        
        return $paths;
    }
    
    public function getNextRecommendation($user_id) {
        // Analisar pontos fracos e recomendar próximo exercício
        $stmt = $this->db->conn->prepare("
            SELECT e.*, ap.mastery_level, ap.attempts
            FROM exercises e
            LEFT JOIN adaptive_progress ap ON e.id = ap.exercise_id AND ap.user_id = ?
            WHERE COALESCE(ap.mastery_level, 0) < 0.7
            ORDER BY 
                CASE WHEN ap.mastery_level IS NULL THEN 1 ELSE 2 END,
                ap.mastery_level ASC,
                e.difficulty_level ASC
            LIMIT 1
        ");
        
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $recommendation = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        
        return $recommendation;
    }
}
?>