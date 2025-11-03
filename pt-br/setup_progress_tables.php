<?php
require_once 'config.php';

// Criar tabelas de progresso se não existirem
function setupProgressTables() {
    $conn = getDBConnection();
    if (!$conn) {
        echo "❌ Erro: Não foi possível conectar ao banco de dados.\n";
        return false;
    }

    try {
        // Tabela de exercícios
        $sql = "CREATE TABLE IF NOT EXISTS exercises (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
            category VARCHAR(100) DEFAULT 'html',
            points INT DEFAULT 10,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->exec($sql);

        // Tabela de progresso do usuário
        $sql = "CREATE TABLE IF NOT EXISTS user_progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            exercise_id INT NOT NULL,
            completed BOOLEAN DEFAULT FALSE,
            score INT DEFAULT 0,
            completed_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_exercise (user_id, exercise_id)
        )";
        $conn->exec($sql);

        // Inserir exercícios de exemplo se não existirem
        $check = $conn->query("SELECT COUNT(*) FROM exercises")->fetchColumn();
        if ($check == 0) {
            $exercises = [
                ['HTML Básico', 'Aprenda as tags básicas do HTML', 'beginner', 'html', 10],
                ['CSS Styling', 'Estilize elementos com CSS', 'beginner', 'css', 15],
                ['JavaScript Fundamentos', 'Variáveis e funções em JS', 'intermediate', 'javascript', 20],
                ['Responsive Design', 'Layouts responsivos com CSS', 'intermediate', 'css', 25],
                ['DOM Manipulation', 'Manipule o DOM com JavaScript', 'advanced', 'javascript', 30],
                ['PHP Básico', 'Introdução ao PHP', 'beginner', 'php', 15],
                ['MySQL Queries', 'Consultas básicas em MySQL', 'intermediate', 'database', 20],
                ['AJAX Requests', 'Requisições assíncronas', 'advanced', 'javascript', 35]
            ];

            $stmt = $conn->prepare("INSERT INTO exercises (title, description, difficulty, category, points) VALUES (?, ?, ?, ?, ?)");
            foreach ($exercises as $exercise) {
                $stmt->execute($exercise);
            }
        }

        echo "✅ Tabelas de progresso configuradas com sucesso!\n";
        return true;

    } catch (PDOException $e) {
        echo "❌ Erro ao criar tabelas: " . $e->getMessage() . "\n";
        return false;
    }
}

// Executar se chamado diretamente
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    echo "<h2>Configurando Sistema de Progresso</h2>";
    setupProgressTables();
    echo "<p><a href='progress.php'>Ver Progresso</a> | <a href='index.php'>Voltar</a></p>";
}
?>