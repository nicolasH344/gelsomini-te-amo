<?php
require_once '../config.php';
require_once '../tutorial_functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$type = $input['type'] ?? '';
$id = (int)($input['id'] ?? 0);
$user_id = getCurrentUser()['id'];

if (!$type || !$id) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
    exit;
}

if ($type === 'tutorial') {
    $result = updateTutorialProgress($user_id, $id, 100, 'completed');
    
    if ($result) {
        echo json_encode([
            'success' => true, 
            'message' => 'Tutorial marcado como concluído!',
            'redirect' => 'progress.php'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar progresso']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Tipo de conteúdo não suportado']);
}
?>