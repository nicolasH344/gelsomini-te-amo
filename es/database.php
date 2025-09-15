<?php
$host = "localhost"; 
$dbname = "web_learning_platform"; 
$username = "root"; 
$password = ""; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexão bem-sucedida!";
} catch (PDOException $e) {
    echo "❌ Erro na conexão: " . $e->getMessage();
}
