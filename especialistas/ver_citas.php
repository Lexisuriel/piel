<?php
session_start();
if (!isset($_SESSION['id_especialista'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../db.php';

$id_especialista = $_SESSION['id_especialista'];
$nombre = $_SESSION['nombre_especialista'];
$especialidad = $_SESSION['especialidad'];

// Obtener citas
$sql = "SELECT c.*, u.nombre_completo AS paciente 
        FROM citas c 
        JOIN usuario u ON c.id_paciente = u.id 
        WHERE c.id_especialista = ?
        ORDER BY c.fecha DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_especialista);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Citas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h3 class="mb-4">Citas de <?= htmlspecialchars($nombre) ?> (<?= $especialidad ?>)</h3>

    <?php if ($resultado && $resultado->num_rows > 0): ?>
    <form action="guardar_cambios.php" method="post">
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>Paciente</th>
                        <th>Fecha</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Observaciones (de la primera cita)</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($cita = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($cita['paciente']) ?></td>
                        <td><?= htmlspecialchars($cita['fecha']) ?></td>
                        <td><?= htmlspecialchars($cita['motivo']) ?></td>
                        <td>
                            <select name="estado_<?= $cita['id'] ?>" class="form-select">
                                <?php
                                $estados = ['Pendiente', 'Confirmada', 'Rechazada', 'Reprogramada'];
                                foreach ($estados as $estado) {
                                    $selected = $cita['estado'] === $estado ? 'selected' : '';
                                    echo "<option value='$estado' $selected>$estado</option>";
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <textarea name="observaciones_<?= $cita['id'] ?>" class="form-control" rows="2"><?= htmlspecialchars($cita['observaciones']) ?></textarea>
                        </td>
                        <td>
                            <button type="submit" name="guardar" value="<?= $cita['id'] ?>" class="btn btn-primary">Guardar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        </div> <!-- cierre de table-responsive -->

<div class="text-center mt-4">
    <a href="<?= strtolower($especialidad) ?>.php" class="btn btn-secondary">Regresar al Panel</a>
</div>

    </form>
    <?php else: ?>
        <div class="alert alert-warning text-center">No hay citas disponibles.</div>
    <?php endif; ?>
</body>
</html>
