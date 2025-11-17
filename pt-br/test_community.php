<?php
/**
 * TESTE DO SISTEMA DE COMUNIDADE
 * Arquivo para testar se as APIs e tabelas estão funcionando
 */

session_start();
require_once 'config.php';
require_once 'database_connector.php';

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Teste - Sistema de Comunidade</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
</head>
<body>
    <div class='container mt-5'>
        <h1 class='mb-4'><i class='fas fa-flask'></i> Teste do Sistema de Comunidade</h1>
        
        <div class='alert alert-info'>
            <strong>Status da Sessão:</strong> " . (isLoggedIn() ? 'Usuário Logado' : 'Não Logado') . "
        </div>";

// Teste 1: Verificar tabelas
echo "<div class='card mb-3'>
    <div class='card-header bg-primary text-white'>
        <h5 class='mb-0'>1. Verificação de Tabelas do Banco de Dados</h5>
    </div>
    <div class='card-body'>";

try {
    $conn = getDBConnection();
    
    $tables = ['discussions', 'discussion_likes', 'discussion_replies', 'community_solutions', 'solution_likes'];
    
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->fetch() !== false;
        
        if ($exists) {
            // Contar registros
            $count = $conn->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "<div class='alert alert-success'>
                <i class='fas fa-check-circle'></i> Tabela <strong>$table</strong> existe - $count registros
            </div>";
        } else {
            echo "<div class='alert alert-danger'>
                <i class='fas fa-times-circle'></i> Tabela <strong>$table</strong> NÃO existe
            </div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>
        <i class='fas fa-exclamation-triangle'></i> Erro: " . $e->getMessage() . "
    </div>";
}

echo "</div></div>";

// Teste 2: Verificar APIs
echo "<div class='card mb-3'>
    <div class='card-header bg-success text-white'>
        <h5 class='mb-0'>2. Verificação de Arquivos de API</h5>
    </div>
    <div class='card-body'>";

$apis = [
    'api/get_discussions.php',
    'api/add_discussion.php',
    'api/like_discussion.php',
    'api/get_solutions.php',
    'api/add_solution.php',
    'api/add_reply.php',
    'api/get_replies.php'
];

foreach ($apis as $api) {
    $path = __DIR__ . '/' . $api;
    if (file_exists($path)) {
        echo "<div class='alert alert-success'>
            <i class='fas fa-check-circle'></i> <strong>$api</strong> existe
        </div>";
    } else {
        echo "<div class='alert alert-danger'>
            <i class='fas fa-times-circle'></i> <strong>$api</strong> NÃO existe
        </div>";
    }
}

echo "</div></div>";

// Teste 3: Testar API de GET
echo "<div class='card mb-3'>
    <div class='card-header bg-info text-white'>
        <h5 class='mb-0'>3. Teste de API - GET Discussions</h5>
    </div>
    <div class='card-body'>";

$testUrl = 'api/get_discussions.php?content_type=tutorial&content_id=1';
echo "<p>Testando: <code>$testUrl</code></p>";

try {
    $response = file_get_contents(__DIR__ . '/' . $testUrl);
    $data = json_decode($response, true);
    
    echo "<pre class='bg-light p-3'>";
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Erro: " . $e->getMessage() . "</div>";
}

echo "</div></div>";

// Instruções
echo "<div class='card mb-3'>
    <div class='card-header bg-warning'>
        <h5 class='mb-0'><i class='fas fa-lightbulb'></i> Próximos Passos</h5>
    </div>
    <div class='card-body'>
        <ol>
            <li>Se alguma tabela não existe, execute: <a href='create_community_tables.php' class='btn btn-sm btn-primary'>create_community_tables.php</a></li>
            <li>Se alguma API não existe, verifique se os arquivos foram criados na pasta <code>api/</code></li>
            <li>Teste a página real: <a href='show.php?type=tutorial&id=1' class='btn btn-sm btn-success'>show.php?type=tutorial&id=1</a></li>
            <li>Abra a aba 'Comunidade' e verifique o console do navegador (F12) para erros JavaScript</li>
        </ol>
    </div>
</div>";

echo "<div class='card'>
    <div class='card-header bg-dark text-white'>
        <h5 class='mb-0'><i class='fas fa-code'></i> Debug Console</h5>
    </div>
    <div class='card-body'>
        <p>Abra o Console do Navegador (F12) e execute:</p>
        <pre class='bg-light p-3'><code>// Teste manual das funções JavaScript
console.log('Content Type:', contentType);
console.log('Content ID:', contentId);
console.log('Is Logged In:', isLoggedIn);

// Carregar discussões manualmente
loadDiscussions();

// Carregar soluções manualmente
loadSolutions();</code></pre>
    </div>
</div>

    </div>
</body>
</html>";
?>
