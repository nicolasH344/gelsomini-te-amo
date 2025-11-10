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
}