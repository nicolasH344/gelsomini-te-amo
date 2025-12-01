<?php
// API para AJAX - deve vir antes de qualquer output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    require_once 'config.php';
    
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Não autorizado']);
        exit;
    }
    
    $user_id = getCurrentUser()['id'];
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'get':
            $notifications = getNotifications($user_id);
            echo json_encode(['success' => true, 'notifications' => $notifications]);
            break;
            
        case 'mark_read':
            $id = (int)($_POST['id'] ?? 0);
            $success = markAsRead($id, $user_id);
            echo json_encode(['success' => $success]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
    }
    exit;
}

require_once 'config.php';

// Criar tabela de notificações se não existir
$conn = getDBConnection();
if ($conn) {
    $conn->query("CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX(user_id, is_read)
    )");
}

// Função para adicionar notificação
function addNotification($user_id, $title, $message, $type = 'info') {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $title, $message, $type);
    return $stmt->execute();
}

// Função para buscar notificações
function getNotifications($user_id, $unread_only = false) {
    $conn = getDBConnection();
    if (!$conn) return [];
    
    $where = "user_id = ?";
    if ($unread_only) $where .= " AND is_read = 0";
    
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE $where ORDER BY created_at DESC LIMIT 20");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Função para marcar como lida
function markAsRead($notification_id, $user_id) {
    $conn = getDBConnection();
    if (!$conn) return false;
    
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notification_id, $user_id);
    return $stmt->execute();
}

// Contar notificações não lidas
function getUnreadCount($user_id) {
    $conn = getDBConnection();
    if (!$conn) return 0;
    
    $stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_row()[0];
}
?>