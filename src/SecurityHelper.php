<?php
class SecurityHelper {
    public static function sanitizeOutput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeOutput'], $data);
        }
        return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function setSecureCookie($name, $value, $expire = 0) {
        setcookie($name, $value, [
            'expires' => $expire,
            'path' => '/',
            'secure' => false, // localhost não usa HTTPS
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    
    public static function validateFileUpload($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']) {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }
        
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return in_array($ext, $allowedTypes) && $file['size'] <= 5000000;
    }
}