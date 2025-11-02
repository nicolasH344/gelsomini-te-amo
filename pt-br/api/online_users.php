<?php
header('Content-Type: application/json');
require_once '../config.php';

if (!isLoggedIn()) {
    echo json_encode([]);
    exit;
}

$conn = getDBConnection();
if (!$conn) {
    echo json_encode([]);
    exit;
}

// Criar tabela se não existir
$conn->exec("CREATE TABLE IF NOT EXISTS user_sessions (
    user_id INT PRIMARY KEY,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// Atualizar atividade do usuário atual
$userId = getCurrentUser()['id'];
$conn->prepare("INSERT INTO user_sessions (user_id) VALUES (?) ON DUPLICATE KEY UPDATE last_activity = NOW()")
     ->execute([$userId]);

// Buscar usuários online (ativos nos últimos 5 minutos)
$stmt = $conn->prepare("
    SELECT u.id, u.first_name, u.last_name 
    FROM users u 
    JOIN user_sessions us ON u.id = us.user_id 
    WHERE us.last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
    ORDER BY us.last_activity DESC
");
$stmt->execute();
$users = $stmt->fetchAll();

$onlineUsers = array_map(function($user) {
    return [
        'id' => $user['id'],
        'name' => $user['first_name'] . ' ' . $user['last_name']
    ];
}, $users);

echo json_encode($onlineUsers);
?>