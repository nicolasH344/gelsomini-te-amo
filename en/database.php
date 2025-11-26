<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $password = "momohiki"; // Senha original que estava funcionando
    private $database = "Aims-sub2";
    public $conn;

    public function __construct() {
        try {
            $this->conn = new mysqli(
                $this->host,
                $this->user,
                $this->password,
                $this->database
            );

            if ($this->conn->connect_error) {
                throw new Exception("Connection error: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8mb4");
            
        } catch (Exception $e) {
            die("❌ Connection failed: " . $e->getMessage() . "\n");
        }
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Database class ready for use