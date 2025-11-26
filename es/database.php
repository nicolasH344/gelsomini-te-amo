<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $password = "momohiki"; // Contraseña original que estaba funcionando
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
                throw new Exception("Error de conexión: " . $this->conn->connect_error);
            }

            $this->conn->set_charset("utf8mb4");
            
        } catch (Exception $e) {
            die("❌ Fallo en la conexión: " . $e->getMessage() . "\n");
        }
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Clase Database lista para usar