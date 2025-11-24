<?php
// Incluir config primeiro para evitar problemas de sessão
if (file_exists('config.php')) {
    require_once 'config.php';
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Varredura Completa do Site</h1>";
echo "<p>Verificando todos os arquivos e conexões...</p>";

// 1. TESTE DE CONEXÃO PRINCIPAL
echo "<h2>1. 🔌 Teste de Conexão</h2>";
try {
    $conn = new mysqli("localhost", "root", "Home@spSENAI2025!", "cursinho");
    
    if ($conn->connect_error) {
        echo "<div style='color: red;'>❌ ERRO DE CONEXÃO: " . $conn->connect_error . "</div>";
        echo "<p><strong>Possíveis causas:</strong></p>";
        echo "<ul>";
        echo "<li>XAMPP não está rodando</li>";
        echo "<li>MySQL não iniciado</li>";
        echo "<li>Senha incorreta</li>";
        echo "<li>Banco 'cursinho' não existe</li>";
        echo "</ul>";
    } else {
        echo "<div style='color: green;'>✅ Conexão OK</div>";
        echo "<p>Servidor: " . $conn->server_info . "</p>";
        
        // Verificar banco
        $result = $conn->query("SELECT DATABASE()");
        if ($result) {
            $row = $result->fetch_row();
            echo "<p>Banco atual: <strong>" . $row[0] . "</strong></p>";
        }
        
        // Listar tabelas
        $result = $conn->query("SHOW TABLES");
        if ($result && $result->num_rows > 0) {
            echo "<p>Tabelas encontradas: ";
            $tables = [];
            while ($row = $result->fetch_row()) {
                $tables[] = $row[0];
            }
            echo implode(', ', $tables) . "</p>";
        } else {
            echo "<div style='color: orange;'>⚠️ Nenhuma tabela encontrada</div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ ERRO: " . $e->getMessage() . "</div>";
}

// 2. VERIFICAR ARQUIVOS CRÍTICOS
echo "<h2>2. 📁 Arquivos Críticos</h2>";
$critical_files = [
    'config.php' => 'Configurações principais',
    'database.php' => 'Classe de conexão',
    'header.php' => 'Cabeçalho do site',
    'footer.php' => 'Rodapé do site',
    'index.php' => 'Página inicial',
    'login.php' => 'Sistema de login',
    'exercises_index.php' => 'Lista de exercícios',
    'tutorials_index.php' => 'Lista de tutoriais'
];

foreach ($critical_files as $file => $desc) {
    if (file_exists($file)) {
        echo "<div style='color: green;'>✅ $file - $desc</div>";
        
        // Verificar erros de sintaxe
        $output = [];
        $return_var = 0;
        exec("php -l $file 2>&1", $output, $return_var);
        
        if ($return_var !== 0) {
            echo "<div style='color: red; margin-left: 20px;'>❌ Erro de sintaxe: " . implode(' ', $output) . "</div>";
        }
    } else {
        echo "<div style='color: red;'>❌ $file - ARQUIVO NÃO ENCONTRADO</div>";
    }
}

// 3. TESTAR PÁGINAS PRINCIPAIS
echo "<h2>3. 🌐 Teste de Páginas</h2>";
$pages = [
    'index.php' => 'Página inicial',
    'login.php' => 'Login',
    'simple_exercises.php' => 'Exercícios (versão simples)',
    'simple_tutorials.php' => 'Tutoriais (versão simples)'
];

foreach ($pages as $page => $desc) {
    if (file_exists($page)) {
        echo "<div style='color: green;'>✅ $page - $desc</div>";
        echo "<div style='margin-left: 20px;'>";
        echo "<a href='$page' target='_blank'>Testar página</a>";
        echo "</div>";
    } else {
        echo "<div style='color: red;'>❌ $page - NÃO ENCONTRADO</div>";
    }
}

// 4. VERIFICAR CONFIGURAÇÕES
echo "<h2>4. ⚙️ Configurações</h2>";

// Config.php já foi carregado no início
echo "<div style='color: green;'>✅ config.php carregado</div>";
    
// Testar funções principais
if (function_exists('getDBConnection')) {
    echo "<div style='color: green;'>✅ Função getDBConnection() existe</div>";
    
    $test_conn = getDBConnection();
    if ($test_conn) {
        echo "<div style='color: green;'>✅ getDBConnection() funciona</div>";
    } else {
        echo "<div style='color: red;'>❌ getDBConnection() retorna null</div>";
    }
} else {
    echo "<div style='color: red;'>❌ Função getDBConnection() não existe</div>";
}

if (function_exists('processLogin')) {
    echo "<div style='color: green;'>✅ Função processLogin() existe</div>";
} else {
    echo "<div style='color: red;'>❌ Função processLogin() não existe</div>";
}

// 5. CRIAR/VERIFICAR ESTRUTURA DO BANCO
echo "<h2>5. 🗄️ Estrutura do Banco</h2>";

if (isset($conn) && $conn && !$conn->connect_error) {
    // Criar banco se não existir
    $conn->query("CREATE DATABASE IF NOT EXISTS cursinho CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->select_db("cursinho");
    
    // Criar tabela users
    $sql_users = "CREATE TABLE IF NOT EXISTS users (
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
    
    if ($conn->query($sql_users)) {
        echo "<div style='color: green;'>✅ Tabela 'users' OK</div>";
    } else {
        echo "<div style='color: red;'>❌ Erro ao criar tabela users: " . $conn->error . "</div>";
    }
    
    // Verificar se existem usuários
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['count'] == 0) {
            echo "<div style='color: orange;'>⚠️ Nenhum usuário encontrado. Criando usuários de teste...</div>";
            
            // Criar usuário admin
            $stmt = $conn->prepare("INSERT IGNORE INTO users (username, email, password_hash, first_name, last_name, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
            $admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $username = 'admin';
            $email = 'admin@cursinho.com';
            $first_name = 'Admin';
            $last_name = 'Sistema';
            $is_admin = 1;
            $stmt->bind_param("sssssi", $username, $email, $admin_hash, $first_name, $last_name, $is_admin);
            
            if ($stmt->execute()) {
                echo "<div style='color: green;'>✅ Usuário admin criado</div>";
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
                echo "<div style='color: green;'>✅ Usuário teste criado</div>";
            }
        } else {
            echo "<div style='color: green;'>✅ " . $row['count'] . " usuário(s) encontrado(s)</div>";
        }
    }
}

// 6. RESUMO E AÇÕES
echo "<h2>6. 📋 Resumo e Próximas Ações</h2>";

echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>";
echo "<h3>Credenciais de Teste:</h3>";
echo "<ul>";
echo "<li><strong>Admin:</strong> admin / admin123</li>";
echo "<li><strong>Usuário:</strong> usuario / 123456</li>";
echo "</ul>";

echo "<h3>Links para Teste:</h3>";
echo "<ul>";
echo "<li><a href='index.php' target='_blank'>Página Inicial</a></li>";
echo "<li><a href='login.php' target='_blank'>Fazer Login</a></li>";
echo "<li><a href='simple_exercises.php' target='_blank'>Exercícios</a></li>";
echo "<li><a href='simple_tutorials.php' target='_blank'>Tutoriais</a></li>";
echo "</ul>";

echo "<h3>Se ainda houver problemas:</h3>";
echo "<ol>";
echo "<li>Verifique se o XAMPP está rodando</li>";
echo "<li>Inicie o MySQL no painel do XAMPP</li>";
echo "<li>Verifique se a senha do MySQL está correta</li>";
echo "<li>Execute este scanner novamente</li>";
echo "</ol>";
echo "</div>";

if (isset($conn)) {
    $conn->close();
}
?>