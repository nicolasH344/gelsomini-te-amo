<?php
require_once 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Verificar Usuários</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #667eea; }
        .success { color: #28a745; padding: 10px; background: #d4edda; border-radius: 5px; margin: 10px 0; }
        .error { color: #dc3545; padding: 10px; background: #f8d7da; border-radius: 5px; margin: 10px 0; }
        .info { color: #0c5460; padding: 10px; background: #d1ecf1; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        .btn { display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>👤 Verificação de Usuários</h1>";

try {
    $conn = getDBConnection();
    
    if (!$conn) {
        throw new Exception("Não foi possível conectar ao banco de dados");
    }
    
    // Verificar sessão atual
    echo "<h2>Sessão Atual</h2>";
    if (isLoggedIn()) {
        $user = getCurrentUser();
        echo "<div class='success'>";
        echo "✅ Você está logado como: <strong>{$user['username']}</strong><br>";
        echo "ID: {$user['id']}<br>";
        echo "Nome: {$user['first_name']} {$user['last_name']}<br>";
        echo "Admin: " . ($user['is_admin'] ? 'Sim' : 'Não');
        echo "</div>";
    } else {
        echo "<div class='error'>❌ Você NÃO está logado</div>";
        echo "<a href='login.php' class='btn'>Fazer Login</a>";
    }
    
    // Listar todos os usuários
    echo "<h2>Usuários Cadastrados</h2>";
    $result = $conn->query("SELECT id, username, email, first_name, last_name, is_admin, created_at FROM users ORDER BY id");
    
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Username</th><th>Nome</th><th>Email</th><th>Admin</th><th>Criado em</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td><strong>{$row['username']}</strong></td>";
            echo "<td>{$row['first_name']} {$row['last_name']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>" . ($row['is_admin'] ? '✅' : '❌') . "</td>";
            echo "<td>" . date('d/m/Y H:i', strtotime($row['created_at'])) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<div class='info'>Total: {$result->num_rows} usuário(s)</div>";
    } else {
        echo "<div class='error'>❌ Nenhum usuário cadastrado</div>";
        echo "<a href='register.php' class='btn'>Criar Conta</a>";
    }
    
    echo "<div style='margin-top: 30px;'>";
    echo "<a href='forum_index.php' class='btn'>← Voltar para o Fórum</a>";
    echo "<a href='login.php' class='btn'>Login</a>";
    echo "<a href='register.php' class='btn'>Registrar</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'><strong>❌ Erro:</strong> " . $e->getMessage() . "</div>";
}

echo "</div></body></html>";
?>
