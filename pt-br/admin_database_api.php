<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

$conn = getDBConnection();
$action = $_POST['action'] ?? '';

if ($action === 'optimize') {
    try {
        $tables = ['users', 'exercises', 'user_progress', 'user_achievements', 'notifications'];
        
        foreach ($tables as $table) {
            $conn->query("OPTIMIZE TABLE $table");
        }
        
        echo json_encode(['success' => true, 'message' => 'Banco otimizado com sucesso']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>