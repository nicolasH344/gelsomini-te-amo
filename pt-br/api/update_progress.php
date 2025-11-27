<?php
header('Content-Type: application/json');
require_once '../config.php';
require_once '../gamification_functions.php';

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

if (!$input || !isset($input['exercise_id']) || !isset($input['score'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$exerciseId = (int)$input['exercise_id'];
$score = (int)$input['score'];
$status = $input['status'] ?? 'in_progress';
$userId = getCurrentUser()['id'];

try {
    $conn = getDBConnection();
    if (!$conn) {
        throw new Exception('Erro de conexão com banco de dados');
    }
    
    // Atualizar ou inserir progresso
    $stmt = $conn->prepare("
        INSERT INTO user_progress (user_id, exercise_id, status, score, updated_at) 
        VALUES (?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
        status = VALUES(status), 
        score = GREATEST(score, VALUES(score)), 
        updated_at = NOW()
    ");
    $stmt->bind_param("iisi", $userId, $exerciseId, $status, $score);
    
    if (!$stmt->execute()) {
        throw new Exception('Erro ao atualizar progresso');
    }
    
    // Verificar conquistas
    $newAchievements = [];
    if (function_exists('checkAchievements')) {
        $newAchievements = checkAchievements($userId);
    }
    
    // Adicionar moedas se completou
    $coinsEarned = 0;
    if ($status === 'completed') {
        $coinsEarned = max(10, $score); // Mínimo 10 moedas
        if (function_exists('addCoins')) {
            addCoins($userId, $coinsEarned);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Progresso atualizado com sucesso',
        'new_achievements' => $newAchievements,
        'coins_earned' => $coinsEarned
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>