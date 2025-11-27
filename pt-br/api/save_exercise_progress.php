<?php
header('Content-Type: application/json');
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login necessário']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['exercise_id']) || !isset($input['code'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$exerciseId = (int)$input['exercise_id'];
$code = $input['code'];
$userId = getCurrentUser()['id'];

try {
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Erro de conexão com banco de dados');
    }
    
    // Verificar se já existe progresso para este exercício
    $stmt = $conn->prepare("SELECT id FROM exercise_progress WHERE user_id = ? AND exercise_id = ?");
    $stmt->bind_param("ii", $userId, $exerciseId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Atualizar progresso existente
        $stmt = $conn->prepare("UPDATE exercise_progress SET code = ?, updated_at = NOW() WHERE user_id = ? AND exercise_id = ?");
        $stmt->bind_param("sii", $code, $userId, $exerciseId);
    } else {
        // Criar novo progresso
        $stmt = $conn->prepare("INSERT INTO exercise_progress (user_id, exercise_id, code, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("iis", $userId, $exerciseId, $code);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Progresso salvo com sucesso']);
    } else {
        throw new Exception('Erro ao salvar progresso');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>