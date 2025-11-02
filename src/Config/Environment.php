<?php
class Environment {
    private static $loaded = false;
    
    public static function load($path = null) {
        if (self::$loaded) return;
        
        $envFile = $path ?: dirname(__DIR__, 2) . '/.env';
        if (!file_exists($envFile)) return;
        
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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