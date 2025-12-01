<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Database {
    private $host = "localhost";
    private $user = "root";
    private $password = "Home@spSENAI2025!";
    private $database = "cursinho";
    public $conn;

    public function __construct() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        try {
            // Primeiro conectar sem banco para criar se necessário
            $this->conn = new mysqli($this->host, $this->user, $this->password);
            $this->conn->set_charset("utf8mb4");
            
            // Criar banco se não existir
            $this->conn->query("CREATE DATABASE IF NOT EXISTS `{$this->database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Selecionar banco
            $this->conn->select_db($this->database);
            
        } catch (mysqli_sql_exception $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Erro de conexão com o banco de dados");
        }
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Classe Database pronta para uso
