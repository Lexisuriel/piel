<?php
session_start();
require_once("../db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION["id"];
    $especialidad = $_POST["especialidad"];
    $tipo = $_POST["tipo"];
    $fecha = $_POST["fecha"];
    $hora = $_POST["hora"];

    $db = new Database();
    $conn = $db->getConnection();

    $sql = "INSERT INTO citas (usuario_id, especialidad, tipo_cita, fecha, hora) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $id_usuario, $especialidad, $tipo, $fecha, $hora);

    if ($stmt->execute()) {
        echo "<script>alert('Cita agendada exitosamente.'); window.location.href='ver_citas.php';</script>";
    } else {
        echo "<script>alert('Error al agendar cita.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
