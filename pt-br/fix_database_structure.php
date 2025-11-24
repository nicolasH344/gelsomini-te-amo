<?php
require_once 'config.php';

$conn = getDBConnection();
if (!$conn) {
    die('Erro de conexão com o banco de dados.');
}

echo "<h2>Corrigindo estrutura do banco de dados...</h2>";

// Verificar e corrigir tabela user_badges
$result = $conn->query("SHOW COLUMNS FROM user_badges LIKE 'reward_claimed'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE user_badges ADD COLUMN reward_claimed BOOLEAN DEFAULT FALSE");
    echo "✅ Coluna reward_claimed adicionada à tabela user_badges<br>";
} else {
    echo "✅ Coluna reward_claimed já existe<br>";
}

// Verificar estrutura completa das tabelas
$tables = [
    'user_stats' => "CREATE TABLE IF NOT EXISTS user_stats (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        level INT DEFAULT 1,
        xp INT DEFAULT 0,
        coins INT DEFAULT 50,
        streak_days INT DEFAULT 0,
        last_login DATE,
        login_streak INT DEFAULT 0,
        total_logins INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user (user_id)
    )",
    
    'login_rewards' => "CREATE TABLE IF NOT EXISTS login_rewards (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        reward_date DATE NOT NULL,
        xp_gained INT DEFAULT 0,
        coins_gained INT DEFAULT 0,
        streak_day INT DEFAULT 1,
        UNIQUE KEY unique_daily_reward (user_id, reward_date)
    )",
    
    'badges' => "CREATE TABLE IF NOT EXISTS badges (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        icon VARCHAR(50) DEFAULT 'fas fa-trophy',
        xp_reward INT DEFAULT 0,
        coin_reward INT DEFAULT 0,
        badge_type ENUM('login', 'exercise', 'tutorial', 'forum', 'special') DEFAULT 'exercise',
        requirement_value INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    'user_badges' => "CREATE TABLE IF NOT EXISTS user_badges (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        badge_id INT NOT NULL,
        earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        reward_claimed BOOLEAN DEFAULT FALSE,
        UNIQUE KEY unique_user_badge (user_id, badge_id)
    )",
    
    'pets' => "CREATE TABLE IF NOT EXISTS pets (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(50) NOT NULL,
        icon VARCHAR(50) NOT NULL,
        price INT DEFAULT 0,
        rarity ENUM('common', 'rare', 'epic', 'legendary') DEFAULT 'common',
        is_default BOOLEAN DEFAULT FALSE
    )",
    
    'user_pets' => "CREATE TABLE IF NOT EXISTS user_pets (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        pet_id INT NOT NULL,
        is_active BOOLEAN DEFAULT FALSE,
        purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_pet (user_id, pet_id)
    )"
];

foreach ($tables as $tableName => $sql) {
    $conn->query($sql);
    echo "✅ Tabela $tableName verificada/criada<br>";
}

echo "<br><h3>✅ Estrutura do banco corrigida!</h3>";
echo "<a href='badges.php' class='btn btn-success'>Testar Conquistas</a>";
?>