<?php
require_once '../db.php';

// Validar ID del especialista
$id_especialista = $_GET['id'] ?? null;
if (!$id_especialista) {
    die("Falta el ID del especialista.");
}

// Obtener datos del especialista
$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT nombre, especialidad FROM especialistas WHERE id = ?");
$stmt->bind_param("i", $id_especialista);
$stmt->execute();
$esp = $stmt->get_result()->fetch_assoc();

if (!$esp) {
    die("Especialista no encontrado.");
}

// Obtener pacientes del especialista (tabla usuario)
$sql = "
    SELECT 
        u.id AS id_paciente,
        u.nombre_completo AS nombre_paciente,
        c.motivo,
        c.estado,
        c.observaciones
    FROM citas c
    JOIN usuario u ON c.id_paciente = u.id
    WHERE c.id_especialista = ?
    ORDER BY c.fecha DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_especialista);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pacientes del Especialista</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    
    <a href="../especialistas/<?= strtolower($esp['especialidad']) ?>.php" 
   class="btn btn-secondary mb-3">
    ← Regresar al panel
</a>


<h3>Pacientes atendidos por: <?= htmlspecialchars($esp['nombre']) ?> (<?= $esp['especialidad'] ?>)</h3>

<?php if ($result->num_rows > 0): ?>
<table class="table table-bordered mt-4 text-center">
    <thead class="thead-dark">
        <tr>
            <th>Paciente</th>
            <th>Motivo</th>
            <th>Estado</th>
            <th>Observaciones</th>
            <th>Historial Médico</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['nombre_paciente']) ?></td>
            <td><?= htmlspecialchars($row['motivo']) ?></td>
            <td><?= htmlspecialchars($row['estado']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['observaciones'] ?? 'N/A')) ?></td>
            <td>
                <a href="historial.php?id_paciente=<?= $row['id_paciente'] ?>&id_especialista=<?= $id_especialista ?>" 
                   class="btn btn-primary btn-sm">Ver historial</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
    <div class="alert alert-warning mt-4">No hay pacientes registrados para este especialista.</div>
<?php endif; ?>

</body>
</html>
