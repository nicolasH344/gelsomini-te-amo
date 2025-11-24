<?php
namespace App\Models;

class Chat extends BaseModel {
    protected $table = 'chat_messages';
    
    public function getRooms() {
        $stmt = $this->db->prepare("
            SELECT cr.*, COUNT(cm.id) as message_count,
                   MAX(cm.created_at) as last_message_at
            FROM chat_rooms cr
            LEFT JOIN chat_messages cm ON cr.id = cm.room_id
            WHERE cr.is_active = 1
            GROUP BY cr.id
            ORDER BY cr.type, cr.name
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getMessages($roomId, $limit = 50, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT cm.*, u.username, u.first_name, u.last_name, u.avatar
            FROM chat_messages cm
            JOIN users u ON cm.user_id = u.id
            WHERE cm.room_id = ?
            ORDER BY cm.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$roomId, (int)$limit, (int)$offset]);
        $messages = $stmt->fetchAll();
        return array_reverse($messages); // Reverse to show oldest first
    }
    
    public function sendMessage($roomId, $userId, $message) {
        $stmt = $this->db->prepare("
            INSERT INTO chat_messages (room_id, user_id, message)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$roomId, $userId, $message]);
    }
    
    public function getRecentMessages($roomId, $since = null, $limit = 20) {
        $sql = "
            SELECT cm.*, u.username, u.first_name, u.last_name, u.avatar
            FROM chat_messages cm
            JOIN users u ON cm.user_id = u.id
            WHERE cm.room_id = ?
        ";
        $params = [$roomId];
        
        if ($since) {
            $sql .= " AND cm.created_at > ?";
            $params[] = $since;
        }
        
        $sql .= " ORDER BY cm.created_at DESC LIMIT ?";
        $params[] = (int)$limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $messages = $stmt->fetchAll();
        return array_reverse($messages);
    }
    
    public function createExerciseRoom($exerciseId, $exerciseTitle) {
        $stmt = $this->db->prepare("
            INSERT INTO chat_rooms (name, description, type, exercise_id)
            VALUES (?, ?, 'exercise', ?)
        ");
        $name = "Exercício: " . $exerciseTitle;
        $description = "Chat colaborativo para o exercício: " . $exerciseTitle;
        
        if ($stmt->execute([$name, $description, $exerciseId])) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    public function getExerciseRoom($exerciseId) {
        $stmt = $this->db->prepare("
            SELECT * FROM chat_rooms 
            WHERE exercise_id = ? AND type = 'exercise' AND is_active = 1
        ");
        $stmt->execute([$exerciseId]);
        return $stmt->fetch();
    }
    
    public function getOnlineUsers($roomId = null) {
        $sql = "
            SELECT DISTINCT u.id, u.username, u.first_name, u.last_name, u.avatar
            FROM users u
            JOIN user_sessions us ON u.id = us.user_id
            WHERE us.last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
        ";
        
        if ($roomId) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM chat_messages cm 
                WHERE cm.user_id = u.id AND cm.room_id = ? 
                AND cm.created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            )";
            $params = [$roomId];
        } else {
            $params = [];
        }
        
        $sql .= " ORDER BY us.last_activity DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function updateUserActivity($userId, $sessionToken) {
        $stmt = $this->db->prepare("
            INSERT INTO user_sessions (user_id, session_token, last_activity)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE last_activity = NOW()
        ");
        return $stmt->execute([$userId, $sessionToken]);
    }
}