<?php
echo "<h2>🔍 Diagnóstico MySQL</h2>";

// Testar diferentes combinações de credenciais
$credentials = [
    ['root', ''],
    ['root', 'root'],
    ['root', 'Home@spSENAI2025!'],
    ['', ''],
];

foreach ($credentials as $cred) {
    $user = $cred[0];
    $pass = $cred[1];
    
    echo "<p>Testando: user='$user', password='" . ($pass ? str_repeat('*', strlen($pass)) : 'vazio') . "'</p>";
    
    try {
        $conn = new mysqli('localhost', $user, $pass);
        if ($conn->connect_error) {
            echo "❌ Erro: " . $conn->connect_error . "<br>";
        } else {
            echo "✅ <strong>SUCESSO!</strong> Credenciais corretas: user='$user', password='" . ($pass ?: 'vazio') . "'<br>";
            
            // Testar se banco cursinho existe
            $result = $conn->query("SHOW DATABASES LIKE 'cursinho'");
            if ($result && $result->num_rows > 0) {
                echo "✅ Banco 'cursinho' existe<br>";
            } else {
                echo "⚠️ Banco 'cursinho' não existe - será criado<br>";
                $conn->query("CREATE DATABASE cursinho");
            }
            
            $conn->close();
            break;
        }
    } catch (Exception $e) {
        echo "❌ Exceção: " . $e->getMessage() . "<br>";
    }
    echo "<hr>";
}
?>