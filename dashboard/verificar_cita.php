<?php
require_once("../db.php");

$db = new Database();
$conn = $db->getConnection();

if (isset($_POST['fecha'], $_POST['hora'], $_POST['id_especialista'])) {
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $id_especialista = (int)$_POST['id_especialista'];

    // Como 'fecha' guarda fecha y hora juntas, combinamos ambos valores
    $fechaHora = $fecha . ' ' . $hora . ':00';

    $sql = "SELECT COUNT(*) AS total 
            FROM citas 
            WHERE fecha = ? 
            AND id_especialista = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $fechaHora, $id_especialista);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['total'] > 0) {
        echo "ocupado";
    } else {
        echo "disponible";
    }
} else {
    echo "error";
}
