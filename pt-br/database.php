<?php
class Database {
    private $host = "sh-pro66.hostgator.com.br";
    private $user = "devgom44_aims-sub2";
    private $password = "aims-sub2@1234!";
    private $database = "devgom44_aims-sub2";
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
                throw new Exception("Erro de conexão: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8mb4");
            
        } catch (Exception $e) {
            die("❌ Falha na conexão: " . $e->getMessage() . "\n");
        }
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Classe Database pronta para uso
