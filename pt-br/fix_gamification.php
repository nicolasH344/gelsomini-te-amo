<?php
require_once 'config.php';

$conn = getDBConnection();
if (!$conn) {
    die('Erro de conexão com o banco de dados.');
}

// Criar tabelas necessárias
$conn->query("CREATE TABLE IF NOT EXISTS user_stats (
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
)");

$conn->query("CREATE TABLE IF NOT EXISTS login_rewards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    reward_date DATE NOT NULL,
    xp_gained INT DEFAULT 0,
    coins_gained INT DEFAULT 0,
    streak_day INT DEFAULT 1,
    UNIQUE KEY unique_daily_reward (user_id, reward_date)
)");

$conn->query("CREATE TABLE IF NOT EXISTS badges (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fas fa-trophy',
    xp_reward INT DEFAULT 0,
    coin_reward INT DEFAULT 0,
    badge_type ENUM('login', 'exercise', 'tutorial', 'forum', 'special') DEFAULT 'exercise',
    requirement_value INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS user_badges (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    badge_id INT NOT NULL,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reward_claimed BOOLEAN DEFAULT FALSE,
    UNIQUE KEY unique_user_badge (user_id, badge_id)
)");

$conn->query("CREATE TABLE IF NOT EXISTS pets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(50) NOT NULL,
    price INT DEFAULT 0,
    rarity ENUM('common', 'rare', 'epic', 'legendary') DEFAULT 'common',
    is_default BOOLEAN DEFAULT FALSE
)");

$conn->query("CREATE TABLE IF NOT EXISTS user_pets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    pet_id INT NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_pet (user_id, pet_id)
)");

// Inserir mascotes padrão
$conn->query("INSERT IGNORE INTO pets (name, icon, price, rarity, is_default) VALUES
('CodeBot', 'fas fa-robot', 0, 'common', TRUE),
('Gato Programador', 'fas fa-cat', 200, 'common', FALSE),
('Dragão Código', 'fas fa-dragon', 500, 'rare', FALSE)");

// Inicializar dados para usuários existentes
$conn->query("INSERT IGNORE INTO user_stats (user_id, level, xp, coins, last_login, total_logins)
SELECT id, 1, 0, 50, CURDATE(), 1 FROM users");

echo "Tabelas criadas com sucesso! <a href='badges.php'>Ir para Conquistas</a>";
?>