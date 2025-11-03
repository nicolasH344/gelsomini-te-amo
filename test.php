<?php
// Teste básico para verificar se o PHP está funcionando
echo "<h1>✅ PHP está funcionando!</h1>";
echo "<p>Versão do PHP: " . phpversion() . "</p>";
echo "<p>Data/Hora atual: " . date('Y-m-d H:i:s') . "</p>";

// Teste de conexão com MySQL
echo "<h2>Teste de Conexão MySQL:</h2>";

$host = "localhost";
$user = "root";
$password = "Home@spSENAI2025!";
$database = "cursinho";

try {
    $conn = new mysqli($host, $user, $password, $database);
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>❌ Erro de conexão: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Conexão com MySQL realizada com sucesso!</p>";
        echo "<p>Versão do MySQL: " . $conn->server_info . "</p>";
        
        // Testar se o banco existe
        $result = $conn->query("SHOW DATABASES LIKE 'cursinho'");
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Banco de dados 'cursinho' encontrado!</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Banco de dados 'cursinho' não encontrado!</p>";
        }
    }
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

// Teste de arquivos importantes
echo "<h2>Verificação de Arquivos:</h2>";

$files_to_check = [
    'index.php',
    'pt-br/index.php',
    'pt-br/config.php',
    '.env',
    'src/Config/Environment.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $file existe</p>";
    } else {
        echo "<p style='color: red;'>❌ $file não encontrado</p>";
    }
}

echo "<h2>Informações do Servidor:</h2>";
echo "<p>Servidor: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script atual: " . $_SERVER['SCRIPT_NAME'] . "</p>";
?>