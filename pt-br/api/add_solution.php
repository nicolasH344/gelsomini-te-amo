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
    echo json_encode(['success' => false, 'message' => 'Código muito curto']);
    exit;
}

try {
    $conn = getDBConnection();
    $user = getCurrentUser();
    
    // Insere solução
    $stmt = $conn->prepare("
        INSERT INTO community_solutions 
        (content_type, content_id, user_id, username, title, code, language, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $content_type,
        $content_id,
        $user['id'],
        $user['username'],
        $title,
        $code,
        $language
    ]);
    
    $solution_id = $conn->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Solução compartilhada com sucesso!',
        'solution' => [
            'id' => $solution_id,
            'username' => $user['username'],
            'title' => $title,
            'code' => $code,
            'language' => $language,
            'created_at' => date('Y-m-d H:i:s'),
            'likes' => 0
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar solução: ' . $e->getMessage()]);
}
