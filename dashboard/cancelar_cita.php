<?php
session_start();
require_once("../db.php");

if (!isset($_SESSION['id']) || !isset($_GET['id'])) {
    header("Location: ver_citas.php");
    exit();
}

$id_usuario = $_SESSION['id'];
$id_cita = $_GET['id'];

$db = new Database();
$conn = $db->getConnection();

// Verificar que la cita pertenezca al usuario
$check = $conn->prepare("SELECT id FROM citas WHERE id = ? AND id_paciente = ?");
$check->bind_param("ii", $id_cita, $id_usuario);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    // Eliminar la cita de la base de datos
    $delete = $conn->prepare("DELETE FROM citas WHERE id = ?");
    $delete->bind_param("i", $id_cita);
    $delete->execute();
    $delete->close();

    echo "<script>alert('Cita cancelada y eliminada correctamente.'); window.location.href = 'ver_citas.php';</script>";
} else {
    echo "<script>alert('No puedes cancelar esta cita.'); window.location.href = 'ver_citas.php';</script>";
}

$check->close();
$conn->close();
?>
