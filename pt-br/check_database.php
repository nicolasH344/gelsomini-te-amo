<?php
require_once 'config.php';

$conn = getDBConnection();
if (!$conn) {
    die('Erro de conexão com o banco de dados.');
}

// Verificar tabelas existentes
$result = $conn->query("SHOW TABLES");
$existingTables = [];
while ($row = $result->fetch_array()) {
    $existingTables[] = $row[0];
}

// Tabelas necessárias para gamificação
$requiredTables = [
    'user_stats',
    'login_rewards', 
    'badges',
    'user_badges'
];

echo "<h2>Tabelas Existentes no Banco:</h2>";
echo "<ul>";
foreach ($existingTables as $table) {
    echo "<li>$table</li>";
}
echo "</ul>";

echo "<h2>Tabelas Necessárias para Gamificação:</h2>";
echo "<ul>";
foreach ($requiredTables as $table) {
    $exists = in_array($table, $existingTables);
    $status = $exists ? "✅ EXISTE" : "❌ FALTANDO";
    echo "<li>$table - $status</li>";
}
echo "</ul>";

$missingTables = array_diff($requiredTables, $existingTables);
if (!empty($missingTables)) {
    echo "<h2>Tabelas que Precisam ser Criadas:</h2>";
    echo "<ul>";
    foreach ($missingTables as $table) {
        echo "<li style='color: red;'>$table</li>";
    }
    echo "</ul>";
    echo "<p><a href='fix_gamification.php'>Clique aqui para criar as tabelas faltantes</a></p>";
} else {
    echo "<p style='color: green;'>✅ Todas as tabelas necessárias existem!</p>";
}
?>