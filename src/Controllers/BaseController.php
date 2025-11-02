<?php
namespace App\Controllers;

class BaseController {
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    protected function getCurrentUser() {
        if (!$this->isLoggedIn()) return null;
        return [
            'id' => $_SESSION['user_id'] ?? 0,
            'username' => $_SESSION['username'] ?? '',
            'first_name' => $_SESSION['first_name'] ?? '',
            'last_name' => $_SESSION['last_name'] ?? '',
            'is_admin' => $_SESSION['is_admin'] ?? false
        ];
    }
    
    protected function setSuccess($message) {
        $_SESSION['success'] = $message;
    }
    
    protected function setError($message) {
        $_SESSION['error'] = $message;
    }
    
    protected function renderView($view, $data = []) {
        extract($data);
        include "src/Views/{$view}.php";
    }
}
?>