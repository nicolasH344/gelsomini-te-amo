<?php
header('Content-Type: application/json');
require_once '../config.php';

// Verificar se o usuário está logado
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

// Obter dados da requisição
$data = json_decode(file_get_contents('php://input'), true);
$type = $data['type'] ?? '';
$id = (int)($data['id'] ?? 0);

if (!$type || !$id) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$userId = getCurrentUser()['id'];
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados']);
    exit;
}

try {
    if ($type === 'tutorial') {
        // Verificar se já existe registro de progresso
        $stmt = $conn->prepare("SELECT id FROM tutorial_progress WHERE user_id = ? AND tutorial_id = ?");
        $stmt->execute([$userId, $id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Atualizar progresso existente
            $stmt = $conn->prepare("UPDATE tutorial_progress SET progress = 100, completed_at = NOW() WHERE user_id = ? AND tutorial_id = ?");
            $stmt->execute([$userId, $id]);
        } else {
            // Criar novo registro de progresso
            $stmt = $conn->prepare("INSERT INTO tutorial_progress (user_id, tutorial_id, progress, completed_at, created_at) VALUES (?, ?, 100, NOW(), NOW())");
            $stmt->execute([$userId, $id]);
        }
    } elseif ($type === 'exercise') {
        // Verificar se já existe registro de progresso
        $stmt = $conn->prepare("SELECT id FROM user_progress WHERE user_id = ? AND exercise_id = ?");
        $stmt->execute([$userId, $id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Atualizar progresso existente
            $stmt = $conn->prepare("UPDATE user_progress SET completed = 1, completed_at = NOW() WHERE user_id = ? AND exercise_id = ?");
            $stmt->execute([$userId, $id]);
        } else {
            // Criar novo registro de progresso
            $stmt = $conn->prepare("INSERT INTO user_progress (user_id, exercise_id, completed, completed_at) VALUES (?, ?, 1, NOW())");
            $stmt->execute([$userId, $id]);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Progresso salvo com sucesso'
    ]);
    
} catch (PDOException $e) {
    error_log('Erro ao salvar progresso: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao salvar progresso: ' . $e->getMessage()
    ]);
}
?>
