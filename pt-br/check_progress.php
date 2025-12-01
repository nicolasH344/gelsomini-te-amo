<?php
require_once 'config.php';

if (!isLoggedIn()) {
    echo "Não logado";
    exit;
}

$user_id = getCurrentUser()['id'];
$type = $_GET['type'] ?? '';
$id = (int)($_GET['id'] ?? 0);
$conn = getDBConnection();

if ($type === 'exercise') {
    $stmt = $conn->prepare("SELECT * FROM exercise_progress WHERE user_id = ? AND exercise_id = ?");
    $stmt->bind_param("ii", $user_id, $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result) {
        echo "Status: " . $result['status'] . ", Score: " . $result['score'];
    } else {
        echo "Nenhum progresso encontrado";
    }
    
} elseif ($type === 'tutorial') {
    $stmt = $conn->prepare("SELECT * FROM tutorial_progress WHERE user_id = ? AND tutorial_id = ?");
    $stmt->bind_param("ii", $user_id, $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result) {
        echo "Status: " . $result['status'] . ", Progresso: " . $result['progress_percent'] . "%";
    } else {
        echo "Nenhum progresso encontrado";
    }
}
?>