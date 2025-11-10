<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$exerciseId = (int)($input['exercise_id'] ?? 0);
$html = $input['html'] ?? '';
$css = $input['css'] ?? '';
$js = $input['js'] ?? '';
$userId = getCurrentUser()['id'];

if (!$exerciseId) {
    echo json_encode(['success' => false, 'message' => 'ID do exercício inválido']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO collaborative_progress (exercise_id, user_id, html_content, css_content, js_content, updated_at)
        VALUES (?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
        html_content = VALUES(html_content),
        css_content = VALUES(css_content),
        js_content = VALUES(js_content),
        updated_at = NOW()
    ");
    
    $stmt->execute([$exerciseId, $userId, $html, $css, $js]);
    
    echo json_encode(['success' => true, 'message' => 'Progresso salvo com sucesso']);
} catch (PDOException $e) {
    error_log("Erro ao salvar progresso colaborativo: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>