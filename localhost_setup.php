<?php
// Configuração específica para localhost
// Este arquivo configura o ambiente para desenvolvimento local

// Configurações de erro para desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔧 Configuração do Localhost</h1>";

// Verificar se o XAMPP está rodando
echo "<h2>1. Verificando Serviços:</h2>";

// Testar Apache
if (function_exists('apache_get_version')) {
    echo "<p style='color: green;'>✅ Apache está rodando: " . apache_get_version() . "</p>";
} else {
    echo "<p style='color: green;'>✅ Servidor web está rodando</p>";
}

// Testar MySQL
$mysql_running = false;
try {
    $conn = new mysqli("localhost", "root", "");
    if (!$conn->connect_error) {
        $mysql_running = true;
        echo "<p style='color: green;'>✅ MySQL está rodando (sem senha)</p>";
        $conn->close();
    }
} catch (Exception $e) {
    try {
        $conn = new mysqli("localhost", "root", "Home@spSENAI2025!");
        if (!$conn->connect_error) {
            $mysql_running = true;
            echo "<p style='color: green;'>✅ MySQL está rodando (com senha)</p>";
            $conn->close();
        }
    } catch (Exception $e2) {
        echo "<p style='color: red;'>❌ MySQL não está rodando ou credenciais incorretas</p>";
    }
}

// Criar banco de dados se não existir
if ($mysql_running) {
    echo "<h2>2. Configurando Banco de Dados:</h2>";
    
    try {
        // Tentar com senha primeiro
        $conn = new mysqli("localhost", "root", "Home@spSENAI2025!");
        if ($conn->connect_error) {
            // Se falhar, tentar sem senha
            $conn = new mysqli("localhost", "root", "");
        }
        
        if (!$conn->connect_error) {
            // Criar banco se não existir
            $sql = "CREATE DATABASE IF NOT EXISTS cursinho CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            if ($conn->query($sql)) {
                echo "<p style='color: green;'>✅ Banco de dados 'cursinho' criado/verificado</p>";
            }
            
            // Selecionar banco
            $conn->select_db("cursinho");
            
            // Criar tabela de usuários básica
            $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                first_name VARCHAR(50) NOT NULL,
                last_name VARCHAR(50) NOT NULL,
                is_admin BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            if ($conn->query($sql)) {
                echo "<p style='color: green;'>✅ Tabela 'users' criada/verificada</p>";
            }
            
            // Inserir usuário admin padrão se não existir
            $check_admin = $conn->query("SELECT id FROM users WHERE username = 'admin'");
            if ($check_admin->num_rows == 0) {
                $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (username, email, password_hash, first_name, last_name, is_admin) 
                        VALUES ('admin', 'admin@localhost.com', '$password_hash', 'Administrador', 'Sistema', TRUE)";
                if ($conn->query($sql)) {
                    echo "<p style='color: green;'>✅ Usuário admin criado (admin/admin123)</p>";
                }
            } else {
                echo "<p style='color: blue;'>ℹ️ Usuário admin já existe</p>";
            }
            
            $conn->close();
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erro ao configurar banco: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>3. Verificando Arquivos:</h2>";

// Verificar permissões de arquivos
$files = [
    '.env' => 'Arquivo de configuração',
    'index.php' => 'Página principal',
    'pt-br/index.php' => 'Página em português',
    'pt-br/config.php' => 'Configurações PHP'
];

foreach ($files as $file => $desc) {
    if (file_exists($file)) {
        if (is_readable($file)) {
            echo "<p style='color: green;'>✅ $desc ($file) - OK</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ $desc ($file) - Sem permissão de leitura</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ $desc ($file) - Não encontrado</p>";
    }
}

echo "<h2>4. URLs de Teste:</h2>";
$base_url = "http://localhost/gelsomini-te-amo";
echo "<ul>";
echo "<li><a href='$base_url/test.php' target='_blank'>Teste básico PHP/MySQL</a></li>";
echo "<li><a href='$base_url/' target='_blank'>Página principal (redirecionamento)</a></li>";
echo "<li><a href='$base_url/pt-br/' target='_blank'>Página em português</a></li>";
echo "<li><a href='$base_url/pt-br/login.php' target='_blank'>Página de login</a></li>";
echo "</ul>";

echo "<h2>5. Próximos Passos:</h2>";
echo "<ol>";
echo "<li>Certifique-se de que o XAMPP está rodando (Apache + MySQL)</li>";
echo "<li>Acesse: <strong>http://localhost/gelsomini-te-amo/</strong></li>";
echo "<li>Se houver erro, acesse primeiro: <strong>http://localhost/gelsomini-te-amo/test.php</strong></li>";
echo "<li>Para login use: <strong>admin / admin123</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p><em>Arquivo gerado em: " . date('Y-m-d H:i:s') . "</em></p>";
?>