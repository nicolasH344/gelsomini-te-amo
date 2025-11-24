<?php
require_once 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Configurando Tabelas do Fórum</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #667eea; }
        .success { color: #28a745; padding: 10px; background: #d4edda; border-radius: 5px; margin: 10px 0; }
        .error { color: #dc3545; padding: 10px; background: #f8d7da; border-radius: 5px; margin: 10px 0; }
        .info { color: #0c5460; padding: 10px; background: #d1ecf1; border-radius: 5px; margin: 10px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🔧 Configuração das Tabelas do Fórum</h1>";

try {
    $conn = getDBConnection();
    
    if (!$conn) {
        throw new Exception("Não foi possível conectar ao banco de dados");
    }
    
    echo "<div class='info'>✅ Conectado ao banco de dados 'cursinho'</div>";
    
    // 1. Criar tabela de categorias do fórum
    echo "<h2>1. Criando tabela forum_categories</h2>";
    $sql = "CREATE TABLE IF NOT EXISTS forum_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        color VARCHAR(50) DEFAULT 'secondary',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ Tabela forum_categories criada/verificada com sucesso</div>";
    } else {
        throw new Exception("Erro ao criar forum_categories: " . $conn->error);
    }
    
    // Inserir categorias padrão
    $result = $conn->query("SELECT COUNT(*) as count FROM forum_categories");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        echo "<h3>Inserindo categorias padrão...</h3>";
        $categories = [
            ['HTML & CSS', 'Questões sobre HTML5 e CSS3', 'danger'],
            ['JavaScript', 'Dúvidas sobre JavaScript e frameworks', 'warning'],
            ['PHP & MySQL', 'Backend, PHP e bancos de dados', 'info'],
            ['Dúvidas Gerais', 'Perguntas gerais sobre programação', 'secondary'],
            ['Projetos', 'Compartilhe seus projetos e peça feedback', 'success'],
            ['Carreira', 'Discussões sobre carreira em desenvolvimento', 'primary']
        ];
        
        foreach ($categories as $cat) {
            $stmt = $conn->prepare("INSERT INTO forum_categories (name, description, color) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $cat[0], $cat[1], $cat[2]);
            if ($stmt->execute()) {
                echo "<div class='success'>✅ Categoria '{$cat[0]}' inserida</div>";
            }
        }
    } else {
        echo "<div class='info'>ℹ️ Categorias já existem ({$row['count']} categorias)</div>";
    }
    
    // 2. Criar tabela de posts do fórum
    echo "<h2>2. Criando tabela forum_posts</h2>";
    $sql = "CREATE TABLE IF NOT EXISTS forum_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        category_id INT,
        user_id INT NOT NULL,
        views INT DEFAULT 0,
        is_solved TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES forum_categories(id) ON DELETE SET NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_category (category_id),
        INDEX idx_user (user_id),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ Tabela forum_posts criada/verificada com sucesso</div>";
    } else {
        throw new Exception("Erro ao criar forum_posts: " . $conn->error);
    }
    
    // 3. Criar tabela de comentários do fórum
    echo "<h2>3. Criando tabela forum_comments</h2>";
    $sql = "CREATE TABLE IF NOT EXISTS forum_comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES forum_posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_post (post_id),
        INDEX idx_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ Tabela forum_comments criada/verificada com sucesso</div>";
    } else {
        throw new Exception("Erro ao criar forum_comments: " . $conn->error);
    }
    
    // 4. Verificar estrutura das tabelas
    echo "<h2>4. Verificando estrutura das tabelas</h2>";
    
    $tables = ['forum_categories', 'forum_posts', 'forum_comments'];
    foreach ($tables as $table) {
        $result = $conn->query("DESCRIBE $table");
        echo "<h3>Estrutura da tabela: $table</h3>";
        echo "<pre>";
        while ($row = $result->fetch_assoc()) {
            echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
        }
        echo "</pre>";
    }
    
    // 5. Mostrar categorias existentes
    echo "<h2>5. Categorias do Fórum</h2>";
    $result = $conn->query("SELECT * FROM forum_categories ORDER BY name");
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']} | Nome: {$row['name']} | Cor: {$row['color']}\n";
    }
    echo "</pre>";
    
    // 6. Contar posts
    $result = $conn->query("SELECT COUNT(*) as count FROM forum_posts");
    $row = $result->fetch_assoc();
    echo "<div class='info'>📊 Total de posts no fórum: {$row['count']}</div>";
    
    echo "<div class='success' style='margin-top: 30px;'>
        <h2>✅ Configuração concluída com sucesso!</h2>
        <p>Todas as tabelas do fórum foram criadas e estão prontas para uso.</p>
        <p><a href='forum_index.php' style='color: #667eea; font-weight: bold;'>← Voltar para o Fórum</a></p>
    </div>";
    
} catch (Exception $e) {
    echo "<div class='error'><strong>❌ Erro:</strong> " . $e->getMessage() . "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</div></body></html>";
?>
