<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h1>Conexión exitosa!</h1>";
    echo "<p>Versión de MySQL: ".$db->getAttribute(PDO::ATTR_SERVER_VERSION)."</p>";
    
    // Probando consulta a la tabla usuario
    $stmt = $db->query("SELECT COUNT(*) as total FROM usuario");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total de pacientes: ".$result['total']."</p>";
} catch (Exception $e) {
    die("<h1>Error de conexión:</h1><p>".$e->getMessage()."</p>");
}