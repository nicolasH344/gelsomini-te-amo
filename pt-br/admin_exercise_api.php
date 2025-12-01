<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$conn = getDBConnection();
$action = $_GET['action'] ?? '';

if ($action === 'get') {
    $id = (int)($_GET['id'] ?? 0);
    
    $stmt = $conn->prepare("SELECT * FROM exercises WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($exercise = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'exercise' => $exercise]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Exercício não encontrado']);
    }
}
?>