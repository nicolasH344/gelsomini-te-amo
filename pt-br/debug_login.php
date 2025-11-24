<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug do Sistema de Login</h2>";

try {
    // 1. Testar conexão
    echo "<h3>1. Testando Conexão</h3>";
    $conn = new mysqli("localhost", "root", "Home@spSENAI2025!", "cursinho");
    
    if ($conn->connect_error) {
        echo "<p>❌ Erro de conexão: " . $conn->connect_error . "</p>";
        exit;
    }
    echo "<p>✅ Conexão OK</p>";
    
    // 2. Verificar se tabela users existe
    echo "<h3>2. Verificando Tabela Users</h3>";
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows == 0) {
        echo "<p>❌ Tabela 'users' não existe</p>";
        
        // Criar tabela users
        $sql = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            is_admin TINYINT(1) DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($conn->query($sql)) {
            echo "<p>✅ Tabela 'users' criada</p>";
        } else {
            echo "<p>❌ Erro ao criar tabela: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>✅ Tabela 'users' existe</p>";
    }
    
    // 3. Verificar usuários existentes
    echo "<h3>3. Usuários Existentes</h3>";
    $result = $conn->query("SELECT id, username, email, first_name, last_name, is_admin FROM users");
    
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Nome</th><th>Admin</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['first_name'] . " " . $row['last_name'] . "</td>";
            echo "<td>" . ($row['is_admin'] ? 'Sim' : 'Não') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ Nenhum usuário encontrado</p>";
        
        // 4. Criar usuários de teste
        echo "<h3>4. Criando Usuários de Teste</h3>";
        
        // Admin
        $username = 'admin';
        $email = 'admin@cursinho.com';
        $password = 'admin123';
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
        $first_name = 'Admin';
        $last_name = 'Sistema';
        $is_admin = 1;
        $stmt->bind_param("sssssi", $username, $email, $password_hash, $first_name, $last_name, $is_admin);
        
        if ($stmt->execute()) {
            echo "<p>✅ Usuário admin criado</p>";
        } else {
            echo "<p>❌ Erro ao criar admin: " . $conn->error . "</p>";
        }
        
        // Usuário normal
        $username = 'usuario';
        $email = 'usuario@cursinho.com';
        $password = '123456';
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
        $first_name = 'Usuário';
        $last_name = 'Teste';
        $is_admin = 0;
        $stmt->bind_param("sssssi", $username, $email, $password_hash, $first_name, $last_name, $is_admin);
        
        if ($stmt->execute()) {
            echo "<p>✅ Usuário teste criado</p>";
        } else {
            echo "<p>❌ Erro ao criar usuário: " . $conn->error . "</p>";
        }
    }
    
    // 5. Testar login
    echo "<h3>5. Testando Login</h3>";
    
    $test_users = [
        ['admin', 'admin123'],
        ['usuario', '123456']
    ];
    
    foreach ($test_users as $test_user) {
        $username = $test_user[0];
        $password = $test_user[1];
        
        echo "<h4>Testando: $username / $password</h4>";
        
        // Buscar usuário
        $stmt = $conn->prepare("SELECT id, username, password_hash, first_name, last_name, is_admin FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            echo "<p>✅ Usuário encontrado: " . $user['username'] . "</p>";
            
            // Verificar senha
            if (password_verify($password, $user['password_hash'])) {
                echo "<p>✅ Senha correta</p>";
                echo "<p><strong>Login funcionaria!</strong></p>";
            } else {
                echo "<p>❌ Senha incorreta</p>";
                echo "<p>Hash no banco: " . substr($user['password_hash'], 0, 20) . "...</p>";
                
                // Recriar hash
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $update_stmt->bind_param("si", $new_hash, $user['id']);
                if ($update_stmt->execute()) {
                    echo "<p>✅ Hash da senha atualizado</p>";
                }
            }
        } else {
            echo "<p>❌ Usuário não encontrado</p>";
        }
        echo "<hr>";
    }
    
    echo "<h3>✅ Diagnóstico Concluído</h3>";
    echo "<p><strong>Credenciais para teste:</strong></p>";
    echo "<ul>";
    echo "<li>Admin: <code>admin</code> / <code>admin123</code></li>";
    echo "<li>Usuário: <code>usuario</code> / <code>123456</code></li>";
    echo "</ul>";
    echo "<p><a href='login.php'>Testar Login</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}
?>