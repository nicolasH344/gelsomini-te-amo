<?php
require_once 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Debug - Criar Post</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #667eea; }
        .success { color: #28a745; padding: 15px; background: #d4edda; border-radius: 5px; margin: 10px 0; }
        .error { color: #dc3545; padding: 15px; background: #f8d7da; border-radius: 5px; margin: 10px 0; }
        .info { color: #0c5460; padding: 15px; background: #d1ecf1; border-radius: 5px; margin: 10px 0; }
        .warning { color: #856404; padding: 15px; background: #fff3cd; border-radius: 5px; margin: 10px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .btn { display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; font-weight: bold; }
        .section { margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 8px; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🔍 Debug - Sistema de Posts do Fórum</h1>";

try {
    $conn = getDBConnection();
    
    if (!$conn) {
        throw new Exception("Não foi possível conectar ao banco de dados");
    }
    
    echo "<div class='success'>✅ Conexão com banco de dados estabelecida</div>";
    
    // 1. Verificar sessão
    echo "<div class='section'>";
    echo "<h2>1️⃣ Status da Sessão</h2>";
    if (isLoggedIn()) {
        $user = getCurrentUser();
        echo "<div class='success'>";
        echo "✅ <strong>Usuário logado:</strong><br>";
        echo "ID: {$user['id']}<br>";
        echo "Username: {$user['username']}<br>";
        echo "Nome: {$user['first_name']} {$user['last_name']}";
        echo "</div>";
        $can_post = true;
    } else {
        echo "<div class='error'>";
        echo "❌ <strong>Nenhum usuário logado</strong><br>";
        echo "Você precisa estar logado para criar posts.<br>";
        echo "<a href='login.php' class='btn'>Fazer Login</a>";
        echo "</div>";
        $can_post = false;
    }
    echo "</div>";
    
    // 2. Verificar tabelas
    echo "<div class='section'>";
    echo "<h2>2️⃣ Verificação das Tabelas</h2>";
    
    $tables = ['forum_categories', 'forum_posts', 'forum_comments'];
    $all_tables_ok = true;
    
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            $count_result = $conn->query("SELECT COUNT(*) as count FROM $table");
            $count = $count_result->fetch_assoc()['count'];
            echo "<div class='success'>✅ Tabela '$table' existe ({$count} registros)</div>";
        } else {
            echo "<div class='error'>❌ Tabela '$table' NÃO existe</div>";
            $all_tables_ok = false;
        }
    }
    
    if (!$all_tables_ok) {
        echo "<div class='warning'>";
        echo "⚠️ Execute o script de configuração:<br>";
        echo "<a href='setup_forum_tables.php' class='btn'>Configurar Tabelas</a>";
        echo "</div>";
    }
    echo "</div>";
    
    // 3. Listar categorias
    echo "<div class='section'>";
    echo "<h2>3️⃣ Categorias Disponíveis</h2>";
    $result = $conn->query("SELECT * FROM forum_categories ORDER BY name");
    if ($result->num_rows > 0) {
        echo "<pre>";
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['id']} | Nome: {$row['name']} | Cor: {$row['color']}\n";
        }
        echo "</pre>";
    } else {
        echo "<div class='warning'>⚠️ Nenhuma categoria encontrada</div>";
    }
    echo "</div>";
    
    // 4. Listar posts existentes
    echo "<div class='section'>";
    echo "<h2>4️⃣ Posts Existentes</h2>";
    $result = $conn->query("SELECT fp.*, u.username, fc.name as category_name 
                           FROM forum_posts fp 
                           LEFT JOIN users u ON fp.user_id = u.id
                           LEFT JOIN forum_categories fc ON fp.category_id = fc.id
                           ORDER BY fp.created_at DESC LIMIT 10");
    
    if ($result->num_rows > 0) {
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        echo "<tr style='background: #667eea; color: white;'>
                <th style='padding: 10px; border: 1px solid #ddd;'>ID</th>
                <th style='padding: 10px; border: 1px solid #ddd;'>Título</th>
                <th style='padding: 10px; border: 1px solid #ddd;'>Categoria</th>
                <th style='padding: 10px; border: 1px solid #ddd;'>Autor</th>
                <th style='padding: 10px; border: 1px solid #ddd;'>Data</th>
              </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'>{$row['id']}</td>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'><strong>{$row['title']}</strong></td>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'>{$row['category_name']}</td>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'>{$row['username']}</td>";
            echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . date('d/m/Y H:i', strtotime($row['created_at'])) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<div class='info'>Total: {$result->num_rows} post(s)</div>";
    } else {
        echo "<div class='info'>ℹ️ Nenhum post cadastrado ainda</div>";
    }
    echo "</div>";
    
    // 5. Teste de criação (se logado)
    if ($can_post && $all_tables_ok) {
        echo "<div class='section'>";
        echo "<h2>5️⃣ Teste de Criação de Post</h2>";
        echo "<div class='info'>";
        echo "<p>Tudo pronto para criar posts! Use o formulário no fórum.</p>";
        echo "<a href='forum_index.php' class='btn'>Ir para o Fórum</a>";
        echo "</div>";
        echo "</div>";
    }
    
    // Status geral
    echo "<div class='section' style='background: " . ($can_post && $all_tables_ok ? "#d4edda" : "#fff3cd") . ";'>";
    echo "<h2>📊 Status Geral do Sistema</h2>";
    
    $issues = [];
    if (!$can_post) $issues[] = "Nenhum usuário logado";
    if (!$all_tables_ok) $issues[] = "Tabelas não configuradas";
    
    if (empty($issues)) {
        echo "<div class='success'>";
        echo "<h3>✅ Sistema 100% Funcional!</h3>";
        echo "<p>Todas as verificações passaram. O sistema de posts está pronto para uso.</p>";
        echo "<a href='forum_index.php' class='btn'>Acessar Fórum</a>";
        echo "</div>";
    } else {
        echo "<div class='warning'>";
        echo "<h3>⚠️ Problemas Encontrados:</h3>";
        echo "<ul>";
        foreach ($issues as $issue) {
            echo "<li>$issue</li>";
        }
        echo "</ul>";
        echo "<p><strong>Ações recomendadas:</strong></p>";
        if (!$can_post) echo "<a href='login.php' class='btn'>Fazer Login</a>";
        if (!$all_tables_ok) echo "<a href='setup_forum_tables.php' class='btn'>Configurar Tabelas</a>";
        echo "</div>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'><strong>❌ Erro:</strong> " . $e->getMessage() . "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</div></body></html>";
?>
