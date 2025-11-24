<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Database {
    private $host = "localhost";
    private $user = "root";
    private $password = "Home@spSENAI2025!"; // ou "" se estiver sem senha
    private $database = "cursinho";
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

            $this->conn->set_charset("utf8");
            
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
