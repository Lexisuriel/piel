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

// Obtener cita actual
$sql = "SELECT fecha FROM citas WHERE id = ? AND id_paciente = ? AND estado = 'Pendiente'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_cita, $id_usuario);
$stmt->execute();
$stmt->bind_result($fechaActual);
$stmt->fetch();
$stmt->close();

// Separar fecha y hora para los inputs del formulario
$fechaInput = date('Y-m-d', strtotime($fechaActual));
$horaInput = date('H:i', strtotime($fechaActual));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reprogramar Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f4f8; padding: 30px; font-family: 'Segoe UI', sans-serif; }
        .container { max-width: 600px; margin: auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h3 { color: #2a9d8f; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h3>Reprogramar Cita</h3>
    <form action="reprogramar_cita.php?id=<?= $id_cita ?>" method="POST">
        <div class="mb-3">
            <label for="fecha" class="form-label">Nueva fecha:</label>
            <input type="date" name="fecha" id="fecha" class="form-control" value="<?= $fechaInput ?>" required>
        </div>
        <div class="mb-3">
            <label for="hora" class="form-label">Nueva hora:</label>
            <input type="time" name="hora" id="hora" class="form-control" value="<?= $horaInput ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
        <a href="ver_citas.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>

<?php
// Procesar reprogramación
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevaFecha = $_POST['fecha'];
    $nuevaHora = $_POST['hora'];
    $fechaCompleta = $nuevaFecha . " " . $nuevaHora . ":00";

    $update = $conn->prepare("UPDATE citas SET fecha = ? WHERE id = ? AND id_paciente = ?");
    $update->bind_param("sii", $fechaCompleta, $id_cita, $id_usuario);
    $update->execute();
    $update->close();

    echo "<script>alert('Cita reprogramada exitosamente.'); window.location.href = 'ver_citas.php';</script>";
}
$conn->close();
?>
