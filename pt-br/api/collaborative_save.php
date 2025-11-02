<?php
header('Content-Type: application/json');
require_once '../config.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão']);
    exit;
}

// Criar tabela se não existir
$conn->exec("CREATE TABLE IF NOT EXISTS collaborative_code (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exercise_id INT NOT NULL,
    user_id INT NOT NULL,
    html_code TEXT,
    css_code TEXT,
    js_code TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_exercise (user_id, exercise_id),
    FOREIGN KEY (user_id) REFERENCES users(id)
)");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $exerciseId = (int)($input['exercise_id'] ?? 0);
    $html = $input['html'] ?? '';
    $css = $input['css'] ?? '';
    $js = $input['js'] ?? '';
    $userId = getCurrentUser()['id'];
    
    if ($exerciseId) {
        $stmt = $conn->prepare("
            INSERT INTO collaborative_code (exercise_id, user_id, html_code, css_code, js_code) 
            VALUES (?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            html_code = VALUES(html_code), 
            css_code = VALUES(css_code), 
            js_code = VALUES(js_code)
        ");
        
        if ($stmt->execute([$exerciseId, $userId, $html, $css, $js])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID do exercício inválido']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>