<?php
// Script para configurar o sistema de conquistas
require_once 'config.php';
require_once 'achievements_system.php';

try {
    require_once 'database.php';
    $db = new Database();
    
    // Criar tabelas necessárias se não existirem
    
    // Tabela user_progress (se não existir)
    $db->conn->query("
        CREATE TABLE IF NOT EXISTS user_progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            exercise_id INT NOT NULL,
            status ENUM('started', 'completed') DEFAULT 'started',
            score INT DEFAULT 0,
            completed_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_exercise (user_id, exercise_id)
        )
    ");
    
    // Tabela tutorial_progress (se não existir)
    $db->conn->query("
        CREATE TABLE IF NOT EXISTS tutorial_progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            tutorial_id INT NOT NULL,
            status ENUM('started', 'completed') DEFAULT 'started',
            completed_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_tutorial (user_id, tutorial_id)
        )
    ");
    
    // Inicializar sistema de conquistas
    $achievementsSystem = new AchievementsSystem($db);
    
    echo "✅ Sistema de conquistas configurado com sucesso!\n";
    echo "📊 Tabelas criadas:\n";
    echo "   - achievements\n";
    echo "   - user_achievements\n";
    echo "   - user_coins\n";
    echo "   - user_progress\n";
    echo "   - tutorial_progress\n";
    
    $db->closeConnection();
    
} catch (Exception $e) {
    echo "❌ Erro ao configurar sistema: " . $e->getMessage() . "\n";
}
?>