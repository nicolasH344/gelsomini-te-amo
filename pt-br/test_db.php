<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de Conexão com Banco de Dados</h1>";
echo "<style>body { font-family: Arial; padding: 20px; } .error { color: red; } .success { color: green; } .info { color: blue; }</style>";

require_once 'config.php';

echo "<h2>1. Testando getDBConnection()</h2>";
$conn = getDBConnection();

if ($conn) {
    echo "<p class='success'>✓ Conexão estabelecida com sucesso!</p>";
    
    echo "<h2>2. Verificando tabela 'users'</h2>";
    try {
        $result = $conn->query("SHOW TABLES LIKE 'users'");
        if ($result->rowCount() > 0) {
            echo "<p class='success'>✓ Tabela 'users' existe</p>";
            
            echo "<h2>3. Verificando colunas da tabela 'users'</h2>";
            $columns = $conn->query("SHOW COLUMNS FROM users")->fetchAll();
            echo "<ul>";
            foreach ($columns as $col) {
                echo "<li>{$col['Field']} - {$col['Type']}</li>";
            }
            echo "</ul>";
            
            $required_columns = ['avatar', 'bio', 'website'];
            foreach ($required_columns as $col) {
                $exists = false;
                foreach ($columns as $column) {
                    if ($column['Field'] === $col) {
                        $exists = true;
                        break;
                    }
                }
                if ($exists) {
                    echo "<p class='success'>✓ Coluna '$col' existe</p>";
                } else {
                    echo "<p class='error'>✗ Coluna '$col' NÃO existe - Precisa criar!</p>";
                }
            }
            
        } else {
            echo "<p class='error'>✗ Tabela 'users' NÃO existe!</p>";
            echo "<p class='info'>Execute o script de setup primeiro.</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='error'>✗ Erro ao verificar tabela: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>4. Verificando outras tabelas</h2>";
    $tables = ['user_progress', 'tutorial_progress', 'badges', 'user_badges', 'forum_posts', 'forum_comments'];
    foreach ($tables as $table) {
        try {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result->rowCount() > 0) {
                $count = $conn->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                echo "<p class='success'>✓ Tabela '$table' existe ($count registros)</p>";
            } else {
                echo "<p class='error'>✗ Tabela '$table' NÃO existe</p>";
            }
        } catch (PDOException $e) {
            echo "<p class='error'>✗ Erro ao verificar '$table': " . $e->getMessage() . "</p>";
        }
    }
    
} else {
    echo "<p class='error'>✗ Falha na conexão com banco de dados!</p>";
    echo "<p class='info'>Verifique as configurações em config.php:</p>";
    echo "<ul>";
    echo "<li>DB_HOST: " . DB_HOST . "</li>";
    echo "<li>DB_NAME: " . DB_NAME . "</li>";
    echo "<li>DB_USER: " . DB_USER . "</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<h2>Ações Recomendadas:</h2>";
echo "<ol>";
echo "<li><a href='setup_profile_tables.php'>Executar Setup de Tabelas</a></li>";
echo "<li><a href='profile.php'>Testar Página de Perfil</a></li>";
echo "</ol>";
?>
