<?php
require_once 'config.php';

function setupExerciseTables() {
    try {
        $conn = getDBConnection();
        if (!$conn) {
            throw new Exception('Erro de conexão com o banco de dados');
        }
        
        // Tabela para progresso dos exercícios
        $sql1 = "CREATE TABLE IF NOT EXISTS exercise_progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            exercise_id INT NOT NULL,
            code TEXT,
            status ENUM('in_progress', 'completed') DEFAULT 'in_progress',
            score INT DEFAULT 0,
            attempts INT DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_exercise (user_id, exercise_id),
            INDEX idx_user_id (user_id),
            INDEX idx_exercise_id (exercise_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        // Tabela para validações de exercícios
        $sql2 = "CREATE TABLE IF NOT EXISTS exercise_validations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            exercise_id INT NOT NULL,
            code TEXT NOT NULL,
            validation_result JSON,
            score INT DEFAULT 0,
            passed BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_exercise (user_id, exercise_id),
            INDEX idx_passed (passed)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        // Tabela para estatísticas de exercícios
        $sql3 = "CREATE TABLE IF NOT EXISTS exercise_stats (
            id INT AUTO_INCREMENT PRIMARY KEY,
            exercise_id INT NOT NULL,
            total_attempts INT DEFAULT 0,
            total_completions INT DEFAULT 0,
            avg_score DECIMAL(5,2) DEFAULT 0.00,
            avg_time_minutes INT DEFAULT 0,
            difficulty_rating DECIMAL(3,2) DEFAULT 0.00,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_exercise (exercise_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        // Tabela para feedback dos exercícios
        $sql4 = "CREATE TABLE IF NOT EXISTS exercise_feedback (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            exercise_id INT NOT NULL,
            rating INT CHECK (rating >= 1 AND rating <= 5),
            comment TEXT,
            difficulty_rating INT CHECK (difficulty_rating >= 1 AND difficulty_rating <= 5),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_exercise_rating (exercise_id, rating)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        // Executar queries
        $queries = [$sql1, $sql2, $sql3, $sql4];
        $results = [];
        
        foreach ($queries as $i => $sql) {
            if ($conn->query($sql)) {
                $results[] = "Tabela " . ($i + 1) . " criada com sucesso";
            } else {
                $results[] = "Erro na tabela " . ($i + 1) . ": " . $conn->error;
            }
        }
        
        // Inserir dados iniciais nas estatísticas
        $exercises = getExercisesData();
        foreach ($exercises as $exercise) {
            $exerciseId = $exercise['id'];
            $checkSql = "SELECT id FROM exercise_stats WHERE exercise_id = ?";
            $stmt = $conn->prepare($checkSql);
            $stmt->bind_param("i", $exerciseId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                $insertSql = "INSERT INTO exercise_stats (exercise_id) VALUES (?)";
                $stmt2 = $conn->prepare($insertSql);
                $stmt2->bind_param("i", $exerciseId);
                $stmt2->execute();
            }
        }
        
        $results[] = "Dados iniciais inseridos nas estatísticas";
        
        return $results;
        
    } catch (Exception $e) {
        return ["Erro: " . $e->getMessage()];
    }
}

// Se executado diretamente
if (basename($_SERVER['PHP_SELF']) === 'setup_exercise_tables.php') {
    require_once 'exercise_functions.php';
    
    echo "<h2>Configuração das Tabelas de Exercícios</h2>";
    echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 5px;'>";
    
    $results = setupExerciseTables();
    foreach ($results as $result) {
        echo "<p>" . htmlspecialchars($result) . "</p>";
    }
    
    echo "</div>";
    echo "<p><a href='interactive_exercises.php'>Ir para Exercícios Interativos</a></p>";
    echo "<p><a href='manage_exercises.php'>Gerenciar Exercícios (Admin)</a></p>";
}
?>