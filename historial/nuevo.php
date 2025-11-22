<?php
require_once '../db.php';

$id_especialista = $_GET['id_especialista'] ?? null;
if (!$id_especialista) {
    die("Falta el ID del especialista.");
}

// Obtener pacientes
$pacientes = [];
$result = $conn->query("SELECT id, nombre_completo AS nombre FROM usuario WHERE rol='paciente' ORDER BY nombre ASC");
while ($row = $result->fetch_assoc()) {
    $pacientes[] = $row;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_paciente = $_POST['id_paciente'] ?? '';
    $id_especialista = $_POST['id_especialista'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $diagnostico = trim($_POST['diagnostico'] ?? '');
    $tratamiento = trim($_POST['tratamiento'] ?? '');
    $observaciones = $_POST['observaciones'] ?? '';

    if ($id_paciente && $id_especialista && $fecha && $diagnostico && $tratamiento) {
        $sql = "INSERT INTO historial_medico (id_paciente, id_especialista, fecha, diagnostico, tratamiento, observaciones) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissss", $id_paciente, $id_especialista, $fecha, $diagnostico, $tratamiento, $observaciones);
        if ($stmt->execute()) {
            $mensaje = "Histoairal guardado correctamente ";
        } else {
            $mensaje = "Error al guardadar el historial ";
            $stmt->error;
        }
    } else {
        $mensaje = "Todos los campos excepto Observaciones son obligatorios";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Historial Médico</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-4 ">
        <h2>Registrar Nuevo Historial Médico</h2>
        <?php if (isset($mensaje)) : ?>
            <div class="alert alert-info"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <form method="POST" class="border p-4 bg-light">
            <input type="hidden" name="id_especialista" value="<?php echo htmlspecialchars($id_especialista); ?>">
            <div class="form-group">
                <label for="id_paciente">Paciente</label>
                <select name="id_paciente" class="form-control" required>
                    <option value="">Seleccione un paciente</option>

                    <?php foreach ($pacientes as $p) : ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" name="fecha" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="diagnostico">Diagnóstico:</label>
                <textarea name="diagnostico" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label for="tratamiento">Tratamiento:</label>
                <textarea name="tratamiento" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label for="observaciones">Observaciones (Opcional)</label>
                <textarea name="observaciones" class="form-control"></textarea>
            </div>
            <button type="submit"class="btn btn-success">Guardar Historial</button>
           <a href="ver.php?id=<?= $id_especialista ?>" 
   class="btn btn-secondary">Volver</a>

        </form>


</body>

</html>