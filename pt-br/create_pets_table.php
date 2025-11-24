<?php
require_once 'config.php';

$conn = getDBConnection();
if (!$conn) {
    die('Erro de conexão com o banco de dados.');
}

// Criar tabela pets
$conn->query("CREATE TABLE IF NOT EXISTS pets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(50) NOT NULL,
    price INT DEFAULT 0,
    rarity ENUM('common', 'rare', 'epic', 'legendary') DEFAULT 'common',
    is_default BOOLEAN DEFAULT FALSE
)");

// Criar tabela user_pets
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
('Dragão Código', 'fas fa-dragon', 500, 'rare', FALSE),
('Foguete AI', 'fas fa-rocket', 750, 'epic', FALSE),
('Unicórnio Dev', 'fas fa-horse', 1000, 'legendary', FALSE)");

// Dar mascote padrão para usuários existentes
$conn->query("INSERT IGNORE INTO user_pets (user_id, pet_id, is_active)
SELECT u.id, p.id, TRUE 
FROM users u 
CROSS JOIN pets p 
WHERE p.is_default = TRUE");

echo "Tabelas de mascotes criadas com sucesso!<br>";
echo "<a href='badges.php'>Ver página de conquistas</a>";
?>