<?php
/**
 * API: Adicionar solução da comunidade
 * Permite compartilhar soluções de código
 */

session_start();
require_once '../config.php';

header('Content-Type: application/json');

// Verifica autenticação
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

$content_type = sanitize($_POST['content_type'] ?? '');
$content_id = (int)($_POST['content_id'] ?? 0);
$title = sanitize($_POST['title'] ?? '');
$code = $_POST['code'] ?? ''; // Não sanitiza código
$language = sanitize($_POST['language'] ?? 'javascript');

// Validação
if (!$content_type || !$content_id || !$title || !$code) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

if (strlen($code) < 20) {
    echo json_encode(['success' => false, 'message' => 'Código muito curto (mínimo 20 caracteres)']);
    exit;
}

try {
    $conn = getDBConnection();
    $user = getCurrentUser();
    
    if (!$conn || !$user) {
        echo json_encode(['success' => false, 'message' => 'Erro de conexão ou sessão']);
        exit;
    }
    
    $user_id = $user['id'];
    $user_name = $user['username'] ?? $user['name'] ?? 'Usuário';
    
    // Criar tabela solution_likes se não existir
    $createLikesTable = "CREATE TABLE IF NOT EXISTS solution_likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        solution_id INT NOT NULL,
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_like (solution_id, user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn->query($createLikesTable);
    
    // Insere solução
    $stmt = $conn->prepare("
        INSERT INTO community_solutions 
        (content_type, content_id, user_id, user_name, title, code, language, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->bind_param("siissss", $content_type, $content_id, $user_id, $user_name, $title, $code, $language);
    $stmt->execute();
    
    $solution_id = $stmt->insert_id;
    
    echo json_encode([
        'success' => true,
        'message' => 'Solução compartilhada com sucesso!',
        'solution' => [
            'id' => $solution_id,
            'user_name' => $user_name,
            'title' => $title,
            'code' => $code,
            'language' => $language,
            'created_at' => date('Y-m-d H:i:s'),
            'likes' => 0
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar solução: ' . $e->getMessage()]);
}

