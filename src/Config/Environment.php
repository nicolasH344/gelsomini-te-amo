<?php
class Environment {
    private static $loaded = false;
    
    public static function load($path = null) {
        if (self::$loaded) return;
        
        $envFile = $path ?: dirname(__DIR__, 2) . '/.env';
        
        // Validar caminho para prevenir path traversal
        $realPath = realpath($envFile);
        $basePath = realpath(dirname(__DIR__, 2));
        
        if (!$realPath || !$basePath || strpos($realPath, $basePath) !== 0) {
            return;
        }
        
        if (!file_exists($realPath)) return;
        
        $lines = file($realPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && $line[0] !== '#') {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
        self::$loaded = true;
    }
    
    public static function get($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
    
    // Método para testar conexão MySQL
    public static function testDatabaseConnection() {
        try {
            $host = self::get('DB_HOST', 'localhost');
            $dbname = self::get('DB_NAME', 'cursinho');
            $username = self::get('DB_USER', 'root');
            $password = self::get('DB_PASS', '');
            
            $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $pdo = new PDO($dsn, $username, $password, $options);
            return ['success' => true, 'message' => 'Conexão estabelecida com sucesso'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro de conexão: ' . $e->getMessage()];
        }
    }
}