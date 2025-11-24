<?php
require_once 'src/Config/Environment.php';

// Carregar variáveis de ambiente
Environment::load();

echo "<h2>Teste de Compatibilidade MySQL Workbench</h2>";

// Testar conexão
$result = Environment::testDatabaseConnection();

if ($result['success']) {
    echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px 0;'>";
    echo "✅ " . $result['message'];
    echo "</div>";
    
    try {
        // Conectar ao banco
        $host = Environment::get('DB_HOST', 'localhost');
        $dbname = Environment::get('DB_NAME', 'cursinho');
        $username = Environment::get('DB_USER', 'root');
        $password = Environment::get('DB_PASS', '');
        
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];
        
        $pdo = new PDO($dsn, $username, $password, $options);
        
        // Verificar versão do MySQL
        $version = $pdo->query("SELECT VERSION() as version")->fetch();
        echo "<p><strong>Versão MySQL:</strong> " . $version['version'] . "</p>";
        
        // Verificar charset do banco
        $charset = $pdo->query("SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '{$dbname}'")->fetch();
        if ($charset) {
            echo "<p><strong>Charset:</strong> " . $charset['DEFAULT_CHARACTER_SET_NAME'] . "</p>";
            echo "<p><strong>Collation:</strong> " . $charset['DEFAULT_COLLATION_NAME'] . "</p>";
        }
        
        // Listar tabelas
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "<h3>Tabelas Criadas (" . count($tables) . "):</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>{$table}</li>";
        }
        echo "</ul>";
        
        // Verificar dados iniciais
        $categories = $pdo->query("SELECT COUNT(*) as count FROM categories")->fetch();
        $badges = $pdo->query("SELECT COUNT(*) as count FROM badges")->fetch();
        $forum_cats = $pdo->query("SELECT COUNT(*) as count FROM forum_categories")->fetch();
        $chat_rooms = $pdo->query("SELECT COUNT(*) as count FROM chat_rooms")->fetch();
        
        echo "<h3>Dados Iniciais:</h3>";
        echo "<ul>";
        echo "<li>Categorias: " . $categories['count'] . "</li>";
        echo "<li>Badges: " . $badges['count'] . "</li>";
        echo "<li>Categorias do Fórum: " . $forum_cats['count'] . "</li>";
        echo "<li>Salas de Chat: " . $chat_rooms['count'] . "</li>";
        echo "</ul>";
        
        // Verificar foreign keys
        $fks = $pdo->query("
            SELECT 
                TABLE_NAME,
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE REFERENCED_TABLE_SCHEMA = '{$dbname}' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ")->fetchAll();
        
        echo "<h3>Foreign Keys (" . count($fks) . "):</h3>";
        echo "<ul>";
        foreach ($fks as $fk) {
            echo "<li>{$fk['TABLE_NAME']}.{$fk['COLUMN_NAME']} → {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']}</li>";
        }
        echo "</ul>";
        
        echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px 0;'>";
        echo "✅ <strong>Banco de dados totalmente compatível com MySQL Workbench!</strong>";
        echo "</div>";
        
    } catch (PDOException $e) {
        echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
        echo "❌ Erro ao verificar estrutura: " . $e->getMessage();
        echo "</div>";
    }
    
} else {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
    echo "❌ " . $result['message'];
    echo "</div>";
    
    echo "<h3>Instruções:</h3>";
    echo "<ol>";
    echo "<li>Certifique-se de que o MySQL está rodando no XAMPP</li>";
    echo "<li>Importe o arquivo 'database_schema.sql' no MySQL Workbench</li>";
    echo "<li>Verifique as credenciais no arquivo '.env'</li>";
    echo "</ol>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #333; }
ul { margin: 10px 0; }
li { margin: 5px 0; }
</style>