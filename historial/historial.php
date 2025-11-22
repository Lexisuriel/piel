<?php 
require_once '../db.php';

$id_paciente = $_GET['id_paciente'] ?? null;
$id_especialista = $_GET['id_especialista'] ?? null;

if (!$id_paciente || !$id_especialista){
    die("Faltan parámetros necesarios");
}

// OBTENER NOMBRE DEL PACIENTE
$stmt = $conn->prepare("SELECT nombre_completo FROM usuario WHERE id = ?");
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$paciente = $stmt->get_result()->fetch_assoc();

// OBTENER DATOS DEL ESPECIALISTA
$stmt = $conn->prepare("SELECT nombre, especialidad FROM especialistas WHERE id = ?");
$stmt->bind_param("i", $id_especialista);
$stmt->execute();
$especialista = $stmt->get_result()->fetch_assoc();

// OBTENER HISTORIAL (TABLA historial_medico)
$stmt = $conn->prepare("
    SELECT fecha, diagnostico, tratamiento, observaciones
    FROM historial_medico
    WHERE id_paciente = ? AND id_especialista = ?
    ORDER BY fecha DESC
");
$stmt->bind_param("ii", $id_paciente, $id_especialista);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial Médico</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>

<body class="container mt-4">

<h3>
    Historial Clínico de: 
    <?= htmlspecialchars($paciente['nombre_completo'] ?? 'Paciente desconocido') ?> 
    atendido por 
    <?= htmlspecialchars($especialista['nombre']) ?>
    (<?= htmlspecialchars($especialista['especialidad']) ?>)
</h3>

<!-- BOTÓN PARA AGREGAR NUEVO HISTORIAL -->
<a href="nuevo.php?id_especialista=<?= $id_especialista ?>&id_paciente=<?= $id_paciente ?>"
   class="btn btn-success my-3">
    + Nuevo Historial Médico
</a>

<!-- BOTÓN VOLVER -->
<a href="javascript:history.back()" class="btn btn-secondary my-3">⬅ Volver</a>

<?php if($resultado->num_rows > 0) : ?>
    <table class="table table-bordered mt-3 text-center">
        <thead class="thead-dark">
            <tr>
                <th>Fecha</th>
                <th>Diagnóstico</th>
                <th>Tratamiento</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['fecha']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['diagnostico'])) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['tratamiento'])) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['observaciones'])) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

<?php else: ?>
    <div class="alert alert-warning">No hay historial médico registrado para este paciente.</div>
<?php endif; ?>

</body>
</html>
