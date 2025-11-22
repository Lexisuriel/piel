<?php
session_start();
require_once("../db.php");

// Redirigir si no hay sesión
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

$id_usuario = $_SESSION['id'];
$nombre_usuario = $_SESSION['nombre_completo'];

// Conexión
$db = new Database();
$conn = $db->getConnection();

// Obtener consentimientos generados - CONSULTA CORREGIDA
$sql = "SELECT 
            c.id,
            c.fecha_creacion,
            c.nombre_archivo,
            c.ruta_archivo,
            e.nombre AS nombre_especialista,
            e.especialidad,
            cit.fecha AS fecha_cita,
            cit.tipo_cita
        FROM consentimientos c
        INNER JOIN especialistas e ON c.id_especialista = e.id
        INNER JOIN citas cit ON c.id_cita = cit.id
        WHERE c.id_paciente = ?
        ORDER BY c.fecha_creacion DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

// Contar consentimientos
$total_consentimientos = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Consentimientos - Pro-Piel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="icon" href="../ico/logo.ico">
    
    <style>
        :root {
            --primary-color: #2a9d8f;
            --primary-hover: #21867a;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 1200px;
            margin-top: 20px;
        }
        
        h3 {
            color: var(--primary-color);
            margin-bottom: 25px;
            font-weight: 700;
        }
        
        .table th {
            background-color: var(--primary-color) !important;
            color: white;
            font-weight: 600;
            padding: 12px;
        }
        
        .table td {
            vertical-align: middle;
            padding: 12px !important;
        }
        
        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary-custom:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        .btn-volver {
            background: #5a6d75;
            color: white;
        }
        
        .btn-volver:hover {
            background: #465559;
        }
        
        .badge-count {
            background-color: var(--primary-color);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .file-icon {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-right: 8px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="dashboard.php" class="btn btn-volver mb-2">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
            <h3 class="mb-0">
                <i class="fas fa-file-contract"></i> Mis Consentimientos
            </h3>
            <small class="text-muted">Lista de consentimientos informados generados</small>
        </div>
        <div class="text-end">
            <span class="badge-count">
                <i class="fas fa-file-pdf"></i> <?= $total_consentimientos ?> consentimiento(s)
            </span>
        </div>
    </div>

    <!-- Lista de Consentimientos -->
    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Fecha de Generación</th>
                        <th>Especialista</th>
                        <th>Especialidad</th>
                        <th>Fecha de Cita</th>
                        <th>Tipo de Cita</th>
                        <th>Archivo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <i class="fas fa-calendar-alt text-muted"></i>
                                <?= date('d/m/Y H:i', strtotime($row['fecha_creacion'])) ?>
                            </td>
                            <td><?= htmlspecialchars($row['nombre_especialista']) ?></td>
                            <td>
                                <span class="badge bg-info"><?= htmlspecialchars($row['especialidad']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($row['fecha_cita']) ?></td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($row['tipo_cita']) ?></span>
                            </td>
                            <td>
                                <i class="fas fa-file-pdf file-icon"></i>
                                <?= htmlspecialchars($row['nombre_archivo']) ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <?php if (file_exists($row['ruta_archivo'])): ?>
                                        <a href="<?= $row['ruta_archivo'] ?>" 
                                           class="btn btn-outline-primary btn-sm" 
                                           target="_blank"
                                           title="Ver PDF">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <a href="<?= $row['ruta_archivo'] ?>" 
                                           class="btn btn-outline-success btn-sm"
                                           download
                                           title="Descargar PDF">
                                            <i class="fas fa-download"></i> Descargar
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-outline-danger btn-sm" disabled
                                                title="Archivo no encontrado">
                                            <i class="fas fa-exclamation-triangle"></i> No disponible
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-file-contract"></i>
            <h4>No hay consentimientos generados</h4>
            <p class="text-muted">Aún no has generado ningún consentimiento informado.</p>
            <p class="text-muted">
                Los consentimientos aparecerán aquí después de que completes el proceso 
                de firma en tus citas.
            </p>
            <a href="ver_citas.php" class="btn btn-primary-custom mt-3">
                <i class="fas fa-calendar-check"></i> Ver Mis Citas
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Confirmación antes de descargar
    document.addEventListener('DOMContentLoaded', function() {
        const downloadButtons = document.querySelectorAll('a[download]');
        downloadButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const fileName = this.closest('tr').querySelector('.file-icon').nextSibling.textContent.trim();
                if (!confirm(`¿Descargar el archivo "${fileName}"?`)) {
                    e.preventDefault();
                }
            });
        });
    });
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>