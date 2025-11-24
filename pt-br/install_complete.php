<?php
require_once 'config.php';

$conn = getDBConnection();
if (!$conn) {
    die('Erro de conexão com o banco de dados.');
}

// Criar todas as tabelas necessárias
$tables = [
    "CREATE TABLE IF NOT EXISTS user_stats (
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
    
    "CREATE TABLE IF NOT EXISTS login_rewards (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        reward_date DATE NOT NULL,
        xp_gained INT DEFAULT 0,
        coins_gained INT DEFAULT 0,
        streak_day INT DEFAULT 1,
        UNIQUE KEY unique_daily_reward (user_id, reward_date)
    )",
    
    "CREATE TABLE IF NOT EXISTS badges (
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
    
    "CREATE TABLE IF NOT EXISTS user_badges (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        badge_id INT NOT NULL,
        earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        reward_claimed BOOLEAN DEFAULT FALSE,
        UNIQUE KEY unique_user_badge (user_id, badge_id)
    )",
    
    "CREATE TABLE IF NOT EXISTS pets (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(50) NOT NULL,
        icon VARCHAR(50) NOT NULL,
        price INT DEFAULT 0,
        rarity ENUM('common', 'rare', 'epic', 'legendary') DEFAULT 'common',
        is_default BOOLEAN DEFAULT FALSE
    )",
    
    "CREATE TABLE IF NOT EXISTS user_pets (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        pet_id INT NOT NULL,
        is_active BOOLEAN DEFAULT FALSE,
        purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_pet (user_id, pet_id)
    )"
];

$success = 0;
foreach ($tables as $sql) {
    if ($conn->query($sql)) {
        $success++;
    }
}

// Inserir badges iniciais
$conn->query("INSERT IGNORE INTO badges (name, description, icon, xp_reward, coin_reward, badge_type, requirement_value) VALUES
('Primeiro Login', 'Faça seu primeiro login', 'fas fa-sign-in-alt', 25, 10, 'login', 1),
('Dedicado', 'Faça login por 3 dias consecutivos', 'fas fa-calendar-check', 50, 25, 'login', 3),
('Fiel', 'Faça login por 7 dias consecutivos', 'fas fa-fire', 100, 50, 'login', 7),
('Primeiro Passo', 'Complete seu primeiro exercício', 'fas fa-baby', 50, 15, 'exercise', 1),
('Estudioso', 'Complete 10 exercícios', 'fas fa-graduation-cap', 200, 75, 'exercise', 10)");

// Inserir mascotes
$conn->query("INSERT IGNORE INTO pets (name, icon, price, rarity, is_default) VALUES
('CodeBot', 'fas fa-robot', 0, 'common', TRUE),
('Gato Programador', 'fas fa-cat', 200, 'common', FALSE),
('Dragão Código', 'fas fa-dragon', 500, 'rare', FALSE),
('Foguete AI', 'fas fa-rocket', 750, 'epic', FALSE),
('Unicórnio Dev', 'fas fa-horse', 1000, 'legendary', FALSE)");

// Inicializar dados para usuários existentes
$conn->query("INSERT IGNORE INTO user_stats (user_id, level, xp, coins, last_login, total_logins)
SELECT id, 1, 0, 50, CURDATE(), 1 FROM users");

// Dar mascote padrão para todos os usuários
$conn->query("INSERT IGNORE INTO user_pets (user_id, pet_id, is_active)
SELECT u.id, p.id, TRUE 
FROM users u 
CROSS JOIN pets p 
WHERE p.is_default = TRUE");

echo "Sistema de gamificação instalado com sucesso! $success tabelas criadas.<br>";
echo "<a href='badges.php'>Ver página de conquistas</a>";
?>