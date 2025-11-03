<?php
require_once 'config.php';

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
    
    $conn = getDBConnection();
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Erro de conexão']);
        exit;
    }
    
    try {
        // Inserir ou atualizar progresso
        $stmt = $conn->prepare("
            INSERT INTO user_progress (user_id, exercise_id, completed, score, completed_at) 
            VALUES (?, ?, 1, ?, NOW())
            ON DUPLICATE KEY UPDATE 
            completed = 1, score = GREATEST(score, VALUES(score)), completed_at = NOW()
        ");
        
        $stmt->execute([$user_id, $exercise_id, $score]);
        
        echo json_encode(['success' => true, 'message' => 'Exercício completado!']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
}
?>