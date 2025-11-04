<?php
session_start();
require_once("../db.php");

// Redirigir si no hay sesión
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

$id_usuario = $_SESSION['id'];
$db = new Database();
$conn = $db->getConnection();

// Obtener citas activas del usuario
$sql = "SELECT id, especialidad, tipo_cita, fecha, hora, estado 
        FROM citas 
        WHERE usuario_id = ? 
        ORDER BY fecha, hora";

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
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
            padding: 30px;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 800px;
        }
        .btn-volver {
            margin-bottom: 20px;
            background-color: #6c757d;
            color: white;
        }
        .btn-volver:hover {
            background-color: #5a6268;
        }
        h3 {
            color: #2a9d8f;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" class="btn btn-volver"><i class="fas fa-arrow-left"></i> Volver al inicio</a>
    <h3>Mis Citas Agendadas</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Especialidad</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['especialidad']) ?></td>
                        <td><?= htmlspecialchars($row['tipo_cita']) ?></td>
                        <td><?= htmlspecialchars($row['fecha']) ?></td>
                        <td><?= htmlspecialchars($row['hora']) ?></td>
                        <td><?= htmlspecialchars($row['estado']) ?></td>
                        <td>
                            <?php if ($row['estado'] === 'Activa'): ?>
                                <a href="reprogramar_cita.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Reprogramar</a>
                                <a href="cancelar_cita.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas cancelar esta cita?')">Cancelar</a>
                            <?php else: ?>
                                <span class="text-muted">No disponible</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No tienes citas agendadas.</td></tr>
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
