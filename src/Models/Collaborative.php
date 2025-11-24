<?php
namespace App\Models;

class Collaborative extends BaseModel {
    protected $table = 'collaborative_sessions';
    
    public function createSession($exerciseId, $creatorId) {
        $sessionCode = $this->generateSessionCode();
        
        $stmt = $this->db->prepare("
            INSERT INTO collaborative_sessions (exercise_id, creator_id, session_code)
            VALUES (?, ?, ?)
        ");
        
        if ($stmt->execute([$exerciseId, $creatorId, $sessionCode])) {
            $sessionId = $this->db->lastInsertId();
            $this->joinSession($sessionId, $creatorId);
            return $sessionCode;
        }
        
        return false;
    }
    
    public function getSession($sessionCode) {
        $stmt = $this->db->prepare("
            SELECT cs.*, e.title as exercise_title, e.content as exercise_content,
                   u.username as creator_username
            FROM collaborative_sessions cs
            JOIN exercises e ON cs.exercise_id = e.id
            JOIN users u ON cs.creator_id = u.id
            WHERE cs.session_code = ? AND cs.is_active = 1
        ");
        $stmt->execute([$sessionCode]);
        return $stmt->fetch();
    }
    
    public function joinSession($sessionId, $userId) {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO collaborative_participants (session_id, user_id)
            VALUES (?, ?)
        ");
        return $stmt->execute([$sessionId, $userId]);
    }
    
    public function getParticipants($sessionId) {
        $stmt = $this->db->prepare("
            SELECT u.id, u.username, u.first_name, u.last_name, u.avatar, cp.joined_at
            FROM collaborative_participants cp
            JOIN users u ON cp.user_id = u.id
            WHERE cp.session_id = ?
            ORDER BY cp.joined_at
        ");
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll();
    }
    
    public function updateCode($sessionCode, $code) {
        $stmt = $this->db->prepare("
            UPDATE collaborative_sessions 
            SET current_code = ?, updated_at = NOW()
            WHERE session_code = ? AND is_active = 1
        ");
        return $stmt->execute([$code, $sessionCode]);
    }
    
    public function getCode($sessionCode) {
        $stmt = $this->db->prepare("
            SELECT current_code FROM collaborative_sessions 
            WHERE session_code = ? AND is_active = 1
        ");
        $stmt->execute([$sessionCode]);
        $result = $stmt->fetch();
        return $result ? $result['current_code'] : '';
    }
    
    public function endSession($sessionCode, $userId) {
        $stmt = $this->db->prepare("
            UPDATE collaborative_sessions 
            SET is_active = 0 
            WHERE session_code = ? AND creator_id = ?
        ");
        return $stmt->execute([$sessionCode, $userId]);
    }
    
    public function getUserSessions($userId, $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT cs.*, e.title as exercise_title
            FROM collaborative_sessions cs
            JOIN exercises e ON cs.exercise_id = e.id
            WHERE cs.creator_id = ?
            ORDER BY cs.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, (int)$limit]);
        return $stmt->fetchAll();
    }
    
    private function generateSessionCode() {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // Check if code already exists
        $stmt = $this->db->prepare("SELECT id FROM collaborative_sessions WHERE session_code = ?");
        $stmt->execute([$code]);
        
        if ($stmt->fetch()) {
            return $this->generateSessionCode(); // Recursive call if code exists
        }
        
        return $code;
    }
}