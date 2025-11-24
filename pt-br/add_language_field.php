<?php
/**
 * Script para adicionar o campo 'language' na tabela exercises
 * e popular com base no category_name
 */

require_once 'config.php';

$conn = getDBConnection();
if (!$conn) {
    die("Erro: Não foi possível conectar ao banco de dados.");
}

echo "<h2>Adicionando campo 'language' na tabela exercises...</h2>";

try {
    // Verificar se a coluna já existe
    $stmt = $conn->query("SHOW COLUMNS FROM exercises LIKE 'language'");
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        echo "<p>1. Adicionando coluna 'language'...</p>";
        $conn->exec("ALTER TABLE exercises ADD COLUMN language VARCHAR(20) DEFAULT 'javascript' AFTER category_id");
        echo "<p style='color: green;'>✓ Coluna 'language' adicionada com sucesso!</p>";
    } else {
        echo "<p style='color: blue;'>ℹ Coluna 'language' já existe.</p>";
    }
    
    // Atualizar os valores com base no category_name
    echo "<p>2. Atualizando valores de 'language' com base nas categorias...</p>";
    
    // Buscar todas as categorias
    $stmt = $conn->query("SELECT id, name FROM exercise_categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($categories as $category) {
        $categoryName = strtolower($category['name']);
        $language = 'javascript'; // padrão
        
        // Mapear categoria para linguagem
        if (strpos($categoryName, 'html') !== false) {
            $language = 'html';
        } elseif (strpos($categoryName, 'css') !== false) {
            $language = 'css';
        } elseif (strpos($categoryName, 'javascript') !== false || strpos($categoryName, 'js') !== false) {
            $language = 'javascript';
        } elseif (strpos($categoryName, 'php') !== false) {
            $language = 'php';
        } elseif (strpos($categoryName, 'python') !== false) {
            $language = 'python';
        } elseif (strpos($categoryName, 'java') !== false && strpos($categoryName, 'javascript') === false) {
            $language = 'java';
        }
        
        // Atualizar exercícios desta categoria
        $updateStmt = $conn->prepare("UPDATE exercises SET language = ? WHERE category_id = ?");
        $updateStmt->execute([$language, $category['id']]);
        $affected = $updateStmt->rowCount();
        
        echo "<p>  - Categoria '<strong>{$category['name']}</strong>' → language '<strong>$language</strong>' ({$affected} exercícios atualizados)</p>";
    }
    
    echo "<p style='color: green; font-weight: bold; margin-top: 20px;'>✓ Processo concluído com sucesso!</p>";
    echo "<p>Você pode agora voltar para a página de exercícios.</p>";
    echo "<p><a href='exercises_index.php' class='btn btn-primary'>Voltar para Exercícios</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #f5f5f5;
}
h2 {
    color: #333;
    border-bottom: 2px solid #6f42c1;
    padding-bottom: 10px;
}
p {
    line-height: 1.6;
    margin: 10px 0;
}
.btn {
    display: inline-block;
    padding: 10px 20px;
    background: #6f42c1;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    margin-top: 20px;
}
.btn:hover {
    background: #5a32a3;
}
</style>
