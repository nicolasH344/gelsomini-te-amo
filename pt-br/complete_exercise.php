<?php
require_once 'config.php';
require_once 'database_connector.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Não logado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exercise_id = (int)($_POST['exercise_id'] ?? 0);
    $score = (int)($_POST['score'] ?? 10);
    $user_id = getCurrentUser()['id'];
    
    if ($exercise_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID inválido']);
        exit;
    }
    
    if ($dbConnector->completeExercise($user_id, $exercise_id, $score)) {
        echo json_encode(['success' => true, 'message' => 'Exercício completado!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao completar exercício']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
}
?>