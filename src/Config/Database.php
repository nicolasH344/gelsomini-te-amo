<?php
namespace App\Config;

use PDO;
use PDOException;

require_once __DIR__ . '/Environment.php';

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        \Environment::load();
        try {
            $host = \Environment::get('DB_HOST', 'localhost');
            $dbname = \Environment::get('DB_NAME', 'weblearn');
            $username = \Environment::get('DB_USER', 'root');
            $password = \Environment::get('DB_PASS', '');
            
            $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
            $this->connection = new PDO($dsn, $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new \Exception("Database connection failed");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}
?>