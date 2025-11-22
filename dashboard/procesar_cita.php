<?php
session_start();
require_once("../db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ID del paciente desde la sesión
    if (!isset($_SESSION["id"])) {
        echo "<script>alert('Sesión expirada.'); window.location.href='../index.php';</script>";
        exit();
    }

    $id_paciente    = $_SESSION["id"];
    $id_especialista = $_POST["id_especialista"];
    $tipo_cita      = $_POST["tipo"];
    $fecha          = $_POST["fecha"];
    $hora           = $_POST["hora"];
    $motivo = $_POST["motivo"];


    // Validación básica
    if (empty($id_especialista) || empty($tipo_cita) || empty($fecha) || empty($hora)) {
        echo "<script>alert('Faltan datos para registrar la cita.'); window.history.back();</script>";
        exit();
    }

    // Conexión BD
    $db = new Database();
    $conn = $db->getConnection();

    // INSERT CORRECTO A LA TABLA REAL
   $sql = "INSERT INTO citas (id_paciente, id_especialista, tipo_cita, fecha, hora, motivo, estado)
        VALUES (?, ?, ?, ?, ?, ?, 'Pendiente')";


    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissss",
    $id_paciente,
    $id_especialista,
    $tipo_cita,
    $fecha,
    $hora,
    $motivo
);


    if ($stmt->execute()) {
        echo "<script>alert('Cita agendada exitosamente.'); window.location.href='ver_citas.php';</script>";
    } else {
        echo "<script>alert('Error al agendar cita: {$conn->error}'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
