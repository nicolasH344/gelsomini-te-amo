<?php
require_once 'config.php';

echo "<h2>🔧 Criando Usuários de Teste</h2>";

$conn = getDBConnection();
if (!$conn) {
    die("❌ Erro de conexão");
}

// Criar usuário admin
$admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name, is_admin) 
                       VALUES (?, ?, ?, ?, ?, ?) 
                       ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), is_admin = VALUES(is_admin)");
$username = 'admin';
$email = 'admin@cursinho.com';
$first_name = 'Admin';
$last_name = 'Sistema';
$is_admin = 1;
$stmt->bind_param("sssssi", $username, $email, $admin_hash, $first_name, $last_name, $is_admin);

if ($stmt->execute()) {
    echo "✅ <strong>Admin criado/atualizado:</strong> admin / admin123<br>";
} else {
    echo "❌ Erro ao criar admin<br>";
}

// Criar usuário normal
$user_hash = password_hash('123456', PASSWORD_DEFAULT);
$username = 'usuario';
$email = 'usuario@cursinho.com';
$first_name = 'Usuário';
$last_name = 'Teste';
$is_admin = 0;
$stmt->bind_param("sssssi", $username, $email, $user_hash, $first_name, $last_name, $is_admin);

if ($stmt->execute()) {
    echo "✅ <strong>Usuário criado/atualizado:</strong> usuario / 123456<br>";
} else {
    echo "❌ Erro ao criar usuário<br>";
}

// Verificar usuários criados
echo "<h3>📋 Usuários no Sistema:</h3>";
$result = $conn->query("SELECT username, first_name, last_name, is_admin, created_at FROM users ORDER BY is_admin DESC");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Username</th><th>Nome</th><th>Admin</th><th>Criado em</th><th>Ação</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $admin_badge = $row['is_admin'] ? '👑 Admin' : '👤 Usuário';
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($row['username']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
        echo "<td>$admin_badge</td>";
        echo "<td>" . date('d/m/Y H:i', strtotime($row['created_at'])) . "</td>";
        echo "<td><a href='login.php' target='_blank'>Fazer Login</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<h3>🔗 Links Úteis:</h3>";
echo "<ul>";
echo "<li><a href='login.php' target='_blank'>🔐 Página de Login</a></li>";
echo "<li><a href='index.php' target='_blank'>🏠 Página Inicial</a></li>";
echo "<li><a href='exercises_index.php' target='_blank'>💪 Exercícios</a></li>";
echo "<li><a href='conquistas.php' target='_blank'>🏆 Conquistas</a></li>";
echo "<li><a href='test_progress.php' target='_blank'>🧪 Teste de Progresso</a></li>";
echo "</ul>";

echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
echo "<h4>📝 Credenciais de Teste:</h4>";
echo "<p><strong>👑 Admin:</strong> <code>admin</code> / <code>admin123</code></p>";
echo "<p><strong>👤 Usuário:</strong> <code>usuario</code> / <code>123456</code></p>";
echo "</div>";

$conn->close();
?>