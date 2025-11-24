<?php
require_once 'config.php';

echo "<h2>Criando usuário de teste...</h2>";

try {
    $conn = getDBConnection();
    
    if (!$conn) {
        throw new Exception("Não foi possível conectar ao banco de dados");
    }
    
    // Verificar se tabela users existe
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows == 0) {
        echo "<p>❌ Tabela 'users' não existe. Execute fix_database.php primeiro.</p>";
        exit;
    }
    
    // Verificar se usuário admin já existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $username = 'admin';
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<p>✅ Usuário 'admin' já existe</p>";
    } else {
        // Criar usuário admin
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password_hash, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
        $first_name = 'Admin';
        $last_name = 'Sistema';
        $username = 'admin';
        $email = 'admin@cursinho.com';
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $is_admin = 1;
        
        $stmt->bind_param("sssssi", $first_name, $last_name, $username, $email, $password_hash, $is_admin);
        
        if ($stmt->execute()) {
            echo "<p>✅ Usuário admin criado com sucesso</p>";
            echo "<p><strong>Login:</strong> admin</p>";
            echo "<p><strong>Senha:</strong> admin123</p>";
        } else {
            echo "<p>❌ Erro ao criar usuário admin: " . $conn->error . "</p>";
        }
    }
    
    // Verificar se usuário teste já existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $username = 'usuario';
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<p>✅ Usuário 'usuario' já existe</p>";
    } else {
        // Criar usuário teste
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password_hash, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
        $first_name = 'Usuário';
        $last_name = 'Teste';
        $username = 'usuario';
        $email = 'usuario@cursinho.com';
        $password_hash = password_hash('123456', PASSWORD_DEFAULT);
        $is_admin = 0;
        
        $stmt->bind_param("sssssi", $first_name, $last_name, $username, $email, $password_hash, $is_admin);
        
        if ($stmt->execute()) {
            echo "<p>✅ Usuário teste criado com sucesso</p>";
            echo "<p><strong>Login:</strong> usuario</p>";
            echo "<p><strong>Senha:</strong> 123456</p>";
        } else {
            echo "<p>❌ Erro ao criar usuário teste: " . $conn->error . "</p>";
        }
    }
    
    echo "<h3>✅ Usuários de teste prontos!</h3>";
    echo "<p><a href='login.php'>Testar Login</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}
?>