<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['id_especialista']) || !isset($_POST['guardar'])) {
    header("Location: ver_citas.php");
    exit();
}

$id_cita = $_POST['guardar'];
$estado = $_POST['estado_' . $id_cita] ?? '';
$observaciones = $_POST['observaciones_' . $id_cita] ?? '';

// Validar estado permitido
$estados_validos = ['Pendiente', 'Confirmada', 'Rechazada', 'Reprogramada'];
if (!in_array($estado, $estados_validos)) {
    $_SESSION['error'] = "Estado inválido.";
    header("Location: ver_citas.php");
    exit();
}

// Actualizar en la base de datos
$stmt = $conn->prepare("UPDATE citas SET estado = ?, observaciones = ? WHERE id = ?");
$stmt->bind_param("ssi", $estado, $observaciones, $id_cita);

if ($stmt->execute()) {
    $_SESSION['mensaje'] = "Cita actualizada correctamente.";
} else {
    $_SESSION['error'] = "Error al actualizar la cita.";
}

header("Location: ver_citas.php");
exit();
