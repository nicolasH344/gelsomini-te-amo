<?php
// Teste de conexão e diagnóstico completo
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Teste de Conexão e Diagnóstico</h2>";

// 1. Testar conexão direta
echo "<h3>1. Teste de Conexão Direta</h3>";
try {
    $conn = new mysqli("localhost", "root", "Home@spSENAI2025!", "cursinho");
    
    if ($conn->connect_error) {
        echo "<p>❌ Erro de conexão: " . $conn->connect_error . "</p>";
    } else {
        echo "<p>✅ Conexão direta funcionando</p>";
        echo "<p>Servidor: " . $conn->server_info . "</p>";
        
        // Testar se banco existe
        $result = $conn->query("SELECT DATABASE()");
        if ($result) {
            $row = $result->fetch_row();
            echo "<p>✅ Banco atual: " . $row[0] . "</p>";
        }
        
        // Listar tabelas
        $result = $conn->query("SHOW TABLES");
        if ($result && $result->num_rows > 0) {
            echo "<p>✅ Tabelas encontradas:</p><ul>";
            while ($row = $result->fetch_row()) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>❌ Nenhuma tabela encontrada</p>";
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p>❌ Erro na conexão: " . $e->getMessage() . "</p>";
}

// 2. Testar classe Database
echo "<h3>2. Teste da Classe Database</h3>";
try {
    require_once 'database.php';
    $db = new Database();
    echo "<p>✅ Classe Database carregada</p>";
    
    if ($db->conn) {
        echo "<p>✅ Conexão via classe funcionando</p>";
    } else {
        echo "<p>❌ Conexão via classe falhou</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro na classe Database: " . $e->getMessage() . "</p>";
}

// 3. Testar config.php
echo "<h3>3. Teste do Config.php</h3>";
try {
    require_once 'config.php';
    echo "<p>✅ Config.php carregado</p>";
    
    $conn = getDBConnection();
    if ($conn) {
        echo "<p>✅ getDBConnection() funcionando</p>";
    } else {
        echo "<p>❌ getDBConnection() retornou null</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro no config.php: " . $e->getMessage() . "</p>";
}

// 4. Testar se usuários existem
echo "<h3>4. Teste de Usuários</h3>";
try {
    $conn = getDBConnection();
    if ($conn) {
        $result = $conn->query("SELECT COUNT(*) as total FROM users");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>✅ Total de usuários: " . $row['total'] . "</p>";
            
            if ($row['total'] == 0) {
                echo "<p>⚠️ Nenhum usuário encontrado. Execute create_test_user.php</p>";
            }
        }
    }
} catch (Exception $e) {
    echo "<p>❌ Erro ao verificar usuários: " . $e->getMessage() . "</p>";
}

// 5. Verificar arquivos importantes
echo "<h3>5. Verificação de Arquivos</h3>";
$files = [
    'config.php',
    'database.php',
    'header.php',
    'footer.php',
    'index.php',
    'login.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p>✅ $file existe</p>";
    } else {
        echo "<p>❌ $file não encontrado</p>";
    }
}

echo "<h3>Próximos Passos:</h3>";
echo "<ol>";
echo "<li><a href='fix_database.php'>Corrigir/Criar Tabelas</a></li>";
echo "<li><a href='create_test_user.php'>Criar Usuários de Teste</a></li>";
echo "<li><a href='index.php'>Testar Página Principal</a></li>";
echo "<li><a href='login.php'>Testar Login</a></li>";
echo "</ol>";
?>