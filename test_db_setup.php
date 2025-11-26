<?php
// Script simples para testar e criar banco de dados
echo "Testando conexão com banco de dados...\n";

try {
    // Tentar conectar sem especificar database
    $conn = new mysqli("localhost", "root", "momohiki");
    
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error . "\n");
    }
    
    echo "✓ Conectado ao MySQL com sucesso!\n";
    
    // Criar banco se não existir
    $sql = "CREATE DATABASE IF NOT EXISTS `Aims-sub2` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql) === TRUE) {
        echo "✓ Banco de dados 'Aims-sub2' criado/verificado com sucesso!\n";
    } else {
        echo "Erro ao criar banco: " . $conn->error . "\n";
    }
    
    // Selecionar o banco
    $conn->select_db("Aims-sub2");
    
    // Criar tabela users se não existir
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        is_admin BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        is_active BOOLEAN DEFAULT TRUE,
        email_verified BOOLEAN DEFAULT FALSE,
        profile_picture VARCHAR(255) NULL,
        bio TEXT NULL,
        INDEX idx_username (username),
        INDEX idx_email (email),
        INDEX idx_active (is_active)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ Tabela 'users' criada/verificada com sucesso!\n";
    } else {
        echo "Erro ao criar tabela users: " . $conn->error . "\n";
    }
    
    // Inserir usuários de teste se não existirem
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $user_password = password_hash('123456', PASSWORD_DEFAULT);
    
    // Verificar se admin já existe
    $result = $conn->query("SELECT id FROM users WHERE username = 'admin'");
    if ($result->num_rows == 0) {
        $sql = "INSERT INTO users (first_name, last_name, username, email, password_hash, is_admin) VALUES ('Admin', 'Sistema', 'admin', 'admin@cursinho.local', '$admin_password', 1)";
        
        if ($conn->query($sql) === TRUE) {
            echo "✓ Usuário admin criado com sucesso!\n";
        } else {
            echo "Erro ao criar usuário admin: " . $conn->error . "\n";
        }
    } else {
        echo "✓ Usuário admin já existe!\n";
    }
    
    // Verificar se usuário teste já existe
    $result = $conn->query("SELECT id FROM users WHERE username = 'usuario'");
    if ($result->num_rows == 0) {
        $sql = "INSERT INTO users (first_name, last_name, username, email, password_hash, is_admin) VALUES ('Usuario', 'Teste', 'usuario', 'usuario@cursinho.local', '$user_password', 0)";
        
        if ($conn->query($sql) === TRUE) {
            echo "✓ Usuário teste criado com sucesso!\n";
        } else {
            echo "Erro ao criar usuário teste: " . $conn->error . "\n";
        }
    } else {
        echo "✓ Usuário teste já existe!\n";
    }
    
    echo "\n=== CONFIGURAÇÃO CONCLUÍDA ===\n";
    echo "Banco: Aims-sub2\n";
    echo "Usuários de teste:\n";
    echo "- Admin: admin / admin123\n";
    echo "- Usuário: usuario / 123456\n";
    echo "\nO sistema de login está pronto para uso!\n";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>