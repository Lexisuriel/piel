<?php
session_start();
require_once("../db.php");

// Redirigir si no hay sesión
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

$id_usuario = $_SESSION['id'];

// Conexión
$db = new Database();
$conn = $db->getConnection();

// CONSULTA COMPLETA CON JOIN + MOTIVO + OBSERVACIONES
$sql = "SELECT 
            c.id,
            c.id_especialista,
            c.tipo_cita,
            c.fecha,
            c.hora,
            c.estado,
            c.motivo,
            c.observaciones,
            e.especialidad,
            e.nombre AS nombre_especialista
        FROM citas c
        INNER JOIN especialistas e ON c.id_especialista = e.id
        WHERE c.id_paciente = ?
        ORDER BY c.fecha, c.hora";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Citas - Pro-Piel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../ico/logo.ico">
    <style>
        .container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 1150px;
        }
        h3 {
            color: #2a9d8f;
            margin-bottom: 25px;
            font-weight: 700;
        }
        .table th {
            background-color: #1f7a6a !important;
            color: white;
            text-align: center;
            font-weight: 600;
            padding: 12px;
        }
        .table td {
            vertical-align: middle;
            padding: 10px 12px !important;
            font-size: 15px;
        }
        .btn-sm {
            padding: 4px 10px;
            font-size: 13px;
        }
        .table-bordered td, .table-bordered th {
            border-color: #c7d7d7 !important;
        }
        .table-striped tbody tr:nth-child(odd) {
            background-color: #f8fbfb;
        }
        .table-striped tbody tr:nth-child(even) {
            background-color: #eef7f7;
        }
        .btn-volver {
            background: #5a6d75;
            color: white;
        }
        .btn-volver:hover {
            background: #465559;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" class="btn btn-volver">← Volver al inicio</a>
    <h3>Mis Citas Agendadas</h3>

    <table class="table table-striped table-bordered text-center">
        <thead>
            <tr>
                <th>Especialidad</th>
                <th>Especialista</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
                <th>Motivo</th>
                <th>Observaciones</th>
                <th>Historial</th>
                <th>Consentimiento</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['especialidad']) ?></td>
                        <td><?= htmlspecialchars($row['nombre_especialista']) ?></td>
                        <td><?= htmlspecialchars($row['tipo_cita']) ?></td>
                        <td><?= htmlspecialchars($row['fecha']) ?></td>
                        <td><?= htmlspecialchars($row['motivo']) ?></td>
                        <td><?= htmlspecialchars($row['estado']) ?></td>
                        <td><?= htmlspecialchars($row['motivo']) ?></td>
                        <td><?= htmlspecialchars($row['observaciones'] ?: 'N/A') ?></td>

                        <!-- HISTORIAL EN PDF -->
                        <td>
                            <a href="../historial/historial_pdf.php?id_paciente=<?= $id_usuario ?>&id_especialista=<?= $row['id_especialista'] ?>"
                               class="btn btn-outline-primary btn-sm" target="_blank">
                                PDF
                            </a>
                        </td>

                        <!-- CONSENTIMIENTO INFORMADO -->
                        <td>
                            <a href="../consentimientos/consentimiento.php?id_cita=<?= $row['id'] ?>"
                               class="btn btn-outline-info btn-sm">
                                Ver
                            </a>
                        </td>

                        <!-- REPROGRAMAR / CANCELAR -->
                        <td>
                            <?php if ($row['estado'] === 'Pendiente'): ?>
                                <a href="reprogramar_cita.php?id=<?= $row['id'] ?>" 
                                   class="btn btn-warning btn-sm">Reprogramar</a>

                                <a href="cancelar_cita.php?id=<?= $row['id'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('¿Seguro que deseas cancelar esta cita?')">
                                   Cancelar
                                </a>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center">No tienes citas agendadas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
