<?php
require_once 'config.php';

// Criar tabela de progresso unificada se não existir
function initProgressTables() {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    // Tabela unificada de progresso
    $conn->query("CREATE TABLE IF NOT EXISTS user_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        content_id INT NOT NULL,
        content_type ENUM('exercise', 'tutorial') NOT NULL,
        status ENUM('started', 'completed') DEFAULT 'started',
        score INT DEFAULT 0,
        progress_percent INT DEFAULT 0,
        time_spent INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_progress (user_id, content_id, content_type),
        INDEX(user_id, content_type, status)
    )");
    
    return true;
}

// Salvar progresso de exercício
function saveExerciseProgress($user_id, $exercise_id, $status = 'started', $score = 0) {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    $stmt = $conn->prepare("INSERT INTO user_progress (user_id, content_id, content_type, status, score) 
                           VALUES (?, ?, 'exercise', ?, ?)
                           ON DUPLICATE KEY UPDATE 
                           status = VALUES(status), 
                           score = VALUES(score),
                           updated_at = NOW()");
    $stmt->bind_param("iisi", $user_id, $exercise_id, $status, $score);
    return $stmt->execute();
}

// Salvar progresso de tutorial
function saveTutorialProgress($user_id, $tutorial_id, $status = 'started', $progress_percent = 0) {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    $stmt = $conn->prepare("INSERT INTO user_progress (user_id, content_id, content_type, status, progress_percent) 
                           VALUES (?, ?, 'tutorial', ?, ?)
                           ON DUPLICATE KEY UPDATE 
                           status = VALUES(status), 
                           progress_percent = VALUES(progress_percent),
                           updated_at = NOW()");
    $stmt->bind_param("iisi", $user_id, $tutorial_id, $status, $progress_percent);
    return $stmt->execute();
}

// Obter progresso de exercício
function getExerciseProgress($user_id, $exercise_id) {
    $conn = getDBConnection();
    if (!$conn) return null;
    
    $stmt = $conn->prepare("SELECT * FROM user_progress WHERE user_id = ? AND content_id = ? AND content_type = 'exercise'");
    $stmt->bind_param("ii", $user_id, $exercise_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Obter progresso de tutorial
function getTutorialProgress($user_id, $tutorial_id) {
    $conn = getDBConnection();
    if (!$conn) return null;
    
    $stmt = $conn->prepare("SELECT * FROM user_progress WHERE user_id = ? AND content_id = ? AND content_type = 'tutorial'");
    $stmt->bind_param("ii", $user_id, $tutorial_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// API para AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
        exit;
    }
    
    initProgressTables();
    $user_id = getCurrentUser()['id'];
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'save_exercise':
            $exercise_id = (int)($_POST['exercise_id'] ?? 0);
            $status = $_POST['status'] ?? 'started';
            $score = (int)($_POST['score'] ?? 0);
            
            $success = saveExerciseProgress($user_id, $exercise_id, $status, $score);
            echo json_encode(['success' => $success]);
            break;
            
        case 'save_tutorial':
            $tutorial_id = (int)($_POST['tutorial_id'] ?? 0);
            $status = $_POST['status'] ?? 'started';
            $progress = (int)($_POST['progress'] ?? 0);
            
            $success = saveTutorialProgress($user_id, $tutorial_id, $status, $progress);
            echo json_encode(['success' => $success]);
            break;
            
        case 'get_exercise':
            $exercise_id = (int)($_POST['exercise_id'] ?? 0);
            $progress = getExerciseProgress($user_id, $exercise_id);
            echo json_encode(['success' => true, 'progress' => $progress]);
            break;
            
        case 'get_tutorial':
            $tutorial_id = (int)($_POST['tutorial_id'] ?? 0);
            $progress = getTutorialProgress($user_id, $tutorial_id);
            echo json_encode(['success' => true, 'progress' => $progress]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
    }
    exit;
}

// Inicializar tabelas
initProgressTables();
?>