<?php
require_once 'config.php';

echo "<h2>Corrigindo tabela badges...</h2>";

try {
    $conn = getDBConnection();
    
    if (!$conn) {
        throw new Exception("Não foi possível conectar ao banco de dados");
    }
    
    // Dropar tabela badges se existir
    $conn->query("DROP TABLE IF EXISTS badges");
    echo "<p>✅ Tabela badges removida</p>";
    
    // Dropar tabela user_badges se existir
    $conn->query("DROP TABLE IF EXISTS user_badges");
    echo "<p>✅ Tabela user_badges removida</p>";
    
    // Recriar tabela badges com estrutura correta
    $sql = "CREATE TABLE badges (
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
    )";
    
    if ($conn->query($sql)) {
        echo "<p>✅ Tabela badges criada com estrutura correta</p>";
    } else {
        echo "<p>❌ Erro ao criar tabela badges: " . $conn->error . "</p>";
    }
    
    // Recriar tabela user_badges
    $sql = "CREATE TABLE user_badges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        badge_id INT NOT NULL,
        earned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_badge (user_id, badge_id)
    )";
    
    if ($conn->query($sql)) {
        echo "<p>✅ Tabela user_badges criada</p>";
    } else {
        echo "<p>❌ Erro ao criar tabela user_badges: " . $conn->error . "</p>";
    }
    
    echo "<h3>✅ Correção concluída!</h3>";
    echo "<p><a href='index.php'>Voltar para o site</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}
?>