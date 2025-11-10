<?php
require_once 'config.php';

echo "<h2>Corrigindo Estrutura do Banco</h2>";

$conn = getDBConnection();
if (!$conn) {
    echo "❌ Erro de conexão<br>";
    exit;
}

try {
    // Verificar se coluna is_published existe
    $stmt = $conn->query("DESCRIBE exercises");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('is_published', $columns)) {
        echo "➕ Adicionando coluna is_published...<br>";
        $conn->exec("ALTER TABLE exercises ADD COLUMN is_published BOOLEAN DEFAULT TRUE");
        echo "✅ Coluna is_published adicionada<br>";
    } else {
        echo "✅ Coluna is_published já existe<br>";
    }
    
    // Verificar se coluna category_name existe na view
    echo "🔧 Verificando estrutura das categorias...<br>";
    $stmt = $conn->query("SELECT COUNT(*) FROM categories");
    $catCount = $stmt->fetchColumn();
    echo "✅ Categorias encontradas: $catCount<br>";
    
    // Testar query de exercícios
    echo "🧪 Testando query de exercícios...<br>";
    $stmt = $conn->query("SELECT e.*, c.name as category_name FROM exercises e LEFT JOIN categories c ON e.category_id = c.id LIMIT 1");
    $test = $stmt->fetch();
    
    if ($test) {
        echo "✅ Query funcionando: " . $test['title'] . "<br>";
    } else {
        echo "⚠️ Nenhum exercício encontrado<br>";
    }
    
    echo "<br><a href='exercises_index.php' class='btn btn-primary'>Testar Exercícios</a>";
    echo " <a href='index.php' class='btn btn-secondary'>Voltar</a>";
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}
?>