<?php
session_start();
require_once("../db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_paciente = $_POST["id_paciente"];
    $id_especialista = $_POST["id_especialista"];
    $motivo = $_POST["motivo"];
    $fecha = $_POST["fecha"];
    $hora = $_POST["hora"];

   
    $fecha_hora = $fecha . " " . $hora . ":00";

    $db = new Database();
    $conn = $db->getConnection();

    $sql = "INSERT INTO citas (id_paciente, id_especialista, motivo, fecha, estado, observaciones)
            VALUES (?, ?, ?, ?, 'Pendiente', '')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $id_paciente, $id_especialista, $motivo, $fecha_hora);

    if ($stmt->execute()) {
        echo "<script>alert('Cita agendada exitosamente.'); window.location.href='ver_citas.php';</script>";
    } else {
        echo "<script>alert('Error al agendar cita: " . $conn->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
