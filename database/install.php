<?php
/**
 * Script de instalação do banco de dados
 * Executa o schema e dados iniciais
 */

// Configurações do banco
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'cursinho_db';

try {
    // Conecta ao MySQL sem especificar database
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Instalando banco de dados...</h2>";
    
    // Lê e executa o schema
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $statements = explode(';', $schema);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "<p>✓ Schema criado com sucesso</p>";
    
    // Lê e executa os dados iniciais
    $seedData = file_get_contents(__DIR__ . '/seed_data.sql');
    $statements = explode(';', $seedData);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "<p>✓ Dados iniciais inseridos com sucesso</p>";
    echo "<p><strong>Banco de dados instalado com sucesso!</strong></p>";
    echo "<p>Usuário admin criado:</p>";
    echo "<ul>";
    echo "<li>Username: admin</li>";
    echo "<li>Email: admin@cursinho.local</li>";
    echo "<li>Senha: password</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red'>Erro: " . $e->getMessage() . "</p>";
}
?>