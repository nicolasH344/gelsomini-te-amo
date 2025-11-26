<?php
// Iniciar sessão PRIMEIRO para evitar headers already sent
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Configurações do Site
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'WebLearn - Developer Journey');
}

if (!defined('SITE_URL')) {
    define('SITE_URL', 'http://localhost');
}

if (!defined('BASE_PATH')) {
    define('BASE_PATH', '');
}

// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'Aims-sub2');
define('DB_USER', 'root');
define('DB_PASS', 'momohiki');
define('DB_CHARSET', 'utf8mb4');

// Configurações de debug
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    ini_set('display_errors', 0);
}

// Timezone
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('America/Sao_Paulo');
}

// Configurações padrão de sessão
if (!isset($_SESSION['theme'])) { $_SESSION['theme'] = 'purple'; }
if (!isset($_SESSION['language'])) { $_SESSION['language'] = 'en'; }
if (!isset($_SESSION['accessibility_mode'])) { $_SESSION['accessibility_mode'] = false; }

// Detectar idioma pela URL
$current_path = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($current_path, '/en/') !== false) {
    $_SESSION['language'] = 'en';
} elseif (strpos($current_path, '/es/') !== false) {
    $_SESSION['language'] = 'es';
} elseif (strpos($current_path, '/pt-br/') !== false) {
    $_SESSION['language'] = 'pt-br';
}

/**
 * Conexão com o Banco de Dados usando mysqli
 */
if (!function_exists('getDBConnection')) {
    function getDBConnection() {
        static $connection = null;
        
        if ($connection === null) {
            try {
                $connection = new mysqli("localhost", "root", "momohiki", "Aims-sub2");
                
                if ($connection->connect_error) {
                    if (DEBUG_MODE) {
                        error_log("Connection error: " . $connection->connect_error);
                    }
                    return null;
                }
                
                $connection->set_charset("utf8mb4");
            } catch (Exception $e) {
                if (DEBUG_MODE) {
                    error_log("Database connection error: " . $e->getMessage());
                }
                return null;
            }
        }
        
        return $connection;
    }
}

/**
 * Função para sanitizar dados
 */
if (!function_exists('sanitize')) {
    function sanitize($data) {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('validateCSRFToken')) {
    function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

// Funções de Autenticação e Usuário
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() { return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']); }
}
if (!function_exists('isAdmin')) {
    function isAdmin() { return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true; }
}
if (!function_exists('getCurrentUser')) {
    function getCurrentUser() {
        if (!isLoggedIn()) { return null; }
        return [
            'id' => $_SESSION['user_id'] ?? 0,
            'username' => $_SESSION['username'] ?? '',
            'first_name' => $_SESSION['first_name'] ?? '',
            'last_name' => $_SESSION['last_name'] ?? '',
            'is_admin' => $_SESSION['is_admin'] ?? false
        ];
    }
}

// Demais funções do sistema
if (!function_exists('processLogin')) {
    function processLogin($username, $password) {
        try {
            // Conexão direta para evitar problemas
            $conn = new mysqli("localhost", "root", "momohiki", "Aims-sub2");
            
            if ($conn->connect_error) {
                return ['success' => false, 'message' => 'Connection error: ' . $conn->connect_error];
            }
            
            // Buscar usuário no banco
            $stmt = $conn->prepare("SELECT id, first_name, last_name, username, password_hash, is_admin FROM users WHERE username = ? OR email = ?");
            if (!$stmt) {
                return ['success' => false, 'message' => 'Query preparation error: ' . $conn->error];
            }
            
            $stmt->bind_param("ss", $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Verificar senha
                if (password_verify($password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['is_admin'] = (bool)$user['is_admin'];
                    
                    $conn->close();
                    return ['success' => true, 'message' => 'Login successful!'];
                } else {
                    $conn->close();
                    return ['success' => false, 'message' => 'Incorrect password.'];
                }
            } else {
                $conn->close();
                return ['success' => false, 'message' => 'User not found.'];
            }
            
        } catch(Exception $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}

if (!function_exists('processLogout')) {
    function processLogout() {
        $theme = $_SESSION['theme'] ?? 'purple';
        $language = $_SESSION['language'] ?? 'en';
        $accessibility = $_SESSION['accessibility_mode'] ?? false;
        $_SESSION = [];
        $_SESSION['theme'] = $theme;
        $_SESSION['language'] = $language;
        $_SESSION['accessibility_mode'] = $accessibility;
        return ['success' => true, 'message' => 'Logout successful!'];
    }
}

if (!function_exists('processRegister')) {
    function processRegister($data) {
        // Validações básicas
        if (empty($data['first_name']) || empty($data['last_name']) || 
            empty($data['username']) || empty($data['email']) || 
            empty($data['password']) || empty($data['confirm_password'])) {
            return ['success' => false, 'message' => 'Please fill in all required fields'];
        }
        
        // Verificar consentimento LGPD
        if (!isset($data['lgpd_consent']) || $data['lgpd_consent'] !== 'on') {
            return ['success' => false, 'message' => 'You must accept the Privacy Policy'];
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email'];
        }
        
        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        if ($data['password'] !== $data['confirm_password']) {
            return ['success' => false, 'message' => 'Passwords do not match'];
        }
        
        if (!isset($data['terms']) || $data['terms'] !== 'on') {
            return ['success' => false, 'message' => 'You must accept the terms of use'];
        }
        
        // Tentar conectar com banco de dados usando mysqli
        try {
            require_once 'database.php';
            $db = new Database();
            $conn = $db->conn;
            
            // Verificar se username já existe
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->bind_param("s", $data['username']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $db->closeConnection();
                return ['success' => false, 'message' => 'Username is already taken'];
            }
            
            // Verificar se email já existe
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $data['email']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $db->closeConnection();
                return ['success' => false, 'message' => 'Email is already registered'];
            }
            
            // Inserir novo usuário
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password_hash) VALUES (?, ?, ?, ?, ?)");
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->bind_param("sssss", $data['first_name'], $data['last_name'], $data['username'], $data['email'], $password_hash);
            
            if ($stmt->execute()) {
                $db->closeConnection();
                return ['success' => true, 'message' => 'Account created successfully! Please login to continue.'];
            } else {
                $db->closeConnection();
                return ['success' => false, 'message' => 'Error creating account: ' . $conn->error];
            }
            
        } catch(Exception $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}

if (!function_exists('getStats')) {
    function getStats() {
        return ['total_users' => 1250, 'total_exercises' => 85, 'total_tutorials' => 42, 'total_forum_posts' => 3680];
    }
}

// Processar mudanças de configuração
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $configChanged = false;

    if (isset($_POST['change_theme']) && isset($_POST['theme'])) {
        $_SESSION['theme'] = sanitize($_POST['theme']);
        $configChanged = true;
    }
    
    if (isset($_POST['change_language']) && isset($_POST['language'])) {
        $_SESSION['language'] = sanitize($_POST['language']);
        $configChanged = true;
    }
    
    if (isset($_POST['toggle_accessibility'])) {
        $_SESSION['accessibility_mode'] = !$_SESSION['accessibility_mode'];
        $configChanged = true;
    }

    if ($configChanged) {
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}
?>