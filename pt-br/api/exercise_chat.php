<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$user = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $exerciseId = (int)($input['exercise_id'] ?? 0);
    $message = trim($input['message'] ?? '');
    
    if (!$exerciseId || !$message) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO exercise_chat (exercise_id, user_id, message, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        
        $stmt->execute([$exerciseId, $user['id'], $message]);
        
        echo json_encode(['success' => true, 'message' => 'Mensagem enviada']);
    } catch (PDOException $e) {
        error_log("Erro ao enviar mensagem do chat: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $exerciseId = (int)($_GET['exercise_id'] ?? 0);
    
    if (!$exerciseId) {
        echo json_encode([]);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT ec.message as content, ec.created_at, u.first_name as username
            FROM exercise_chat ec
            JOIN users u ON ec.user_id = u.id
            WHERE ec.exercise_id = ?
            ORDER BY ec.created_at ASC
            LIMIT 50
        ");
        
        $stmt->execute([$exerciseId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($messages);
    } catch (PDOException $e) {
        error_log("Erro ao carregar chat do exercício: " . $e->getMessage());
        echo json_encode([]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>