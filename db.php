<?php

class Database {
    private $servername = "mysql-lexisuriel.alwaysdata.net";
    private $username = "439233";
    private $password = "2929*210*18*22Lu";
    private $dbname = "lexisuriel_piel";
    private $conn;

    // Constructor para establecer la conexión
    public function __construct() {
        $this->connect();
    }

    // Método privado para establecer la conexión
    private function connect() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        // Verificar conexión
        if ($this->conn->connect_error) {
            die("No se pudo conectar a la base de datos: " . $this->conn->connect_error);
        }
    }
    // Método para obtener la conexión
    public function getConnection() {
        return $this->conn;
    }   
}
// Uso de la clase
$db = new Database();
$conn = $db->getConnection();
?>
