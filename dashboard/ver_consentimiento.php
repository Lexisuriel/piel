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

// Verificar si la tabla consentimientos_pdf existe
$table_check = $conn->query("SHOW TABLES LIKE 'consentimientos_pdf'");
$table_exists = $table_check->num_rows > 0;

$total_consentimientos = 0;
$result = null;

if ($table_exists) {
    // Obtener consentimientos generados
    $sql = "SELECT 
                cp.id,
                cp.fecha_creacion,
                cp.nombre_archivo,
                cp.ruta_archivo,
                e.nombre AS nombre_especialista,
                e.especialidad,
                cit.fecha AS fecha_cita,
                cit.tipo_cita
            FROM consentimientos_pdf cp
            INNER JOIN especialistas e ON cp.id_especialista = e.id
            INNER JOIN citas cit ON cp.id_cita = cit.id
            WHERE cp.id_paciente = ?
            ORDER BY cp.fecha_creacion DESC";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $total_consentimientos = $result->num_rows;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Consentimientos - Pro-Piel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="icon" href="../ico/logo.ico">
    
    <style>
        :root {
            --primary-color: #2a9d8f;
            --primary-hover: #21867a;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 1200px;
            margin-top: 20px;
            margin-bottom: 20px;
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
            text-align: center;
        }
        
        .table td {
            vertical-align: middle;
            padding: 12px !important;
            text-align: center;
        }
        
        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            font-weight: 500;
        }
        
        .btn-primary-custom:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        
        .btn-volver {
            background: #5a6d75;
            color: white;
            font-weight: 500;
        }
        
        .btn-volver:hover {
            background: #465559;
            color: white;
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }
        
        .badge-count {
            background-color: var(--primary-color);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .file-icon {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-right: 8px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 40px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
            border-radius: 10px;
            padding: 20px;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(42, 157, 143, 0.1);
            transition: background-color 0.3s ease;
        }
        
        .badge-especialidad {
            font-size: 0.8rem;
            padding: 6px 12px;
        }
        
        .btn-group .btn {
            margin: 0 2px;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
                margin-top: 10px;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
            
            .btn-group {
                display: flex;
                flex-direction: column;
            }
            
            .btn-group .btn {
                margin: 2px 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar Superior -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: var(--primary-color);">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-user-md"></i> Pro-Piel
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white">
                    <i class="fas fa-user"></i> <?= htmlspecialchars($nombre_usuario) ?>
                </span>
            </div>
        </div>
    </nav>

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

        <!-- Mensaje si la tabla no existe -->
        <?php if (!$table_exists): ?>
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle"></i> Configuración requerida</h5>
                <p>La tabla de consentimientos PDF no existe en la base de datos. Esto es normal si es la primera vez que accedes a esta sección.</p>
                <p>Los consentimientos aparecerán aquí automáticamente después de que generes tu primer consentimiento desde la sección "Mis Citas".</p>
                <div class="mt-3">
                    <a href="ver_citas.php" class="btn btn-primary-custom">
                        <i class="fas fa-calendar-check"></i> Ir a Mis Citas
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lista de Consentimientos -->
        <?php if ($table_exists && $result && $result->num_rows > 0): ?>
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
                                    <span class="badge bg-info badge-especialidad"><?= htmlspecialchars($row['especialidad']) ?></span>
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
                                        <?php 
                                        $ruta_completa = '../consentimientos/' . basename($row['ruta_archivo']);
                                        if (file_exists($ruta_completa)): 
                                        ?>
                                            <a href="<?= $ruta_completa ?>" 
                                               class="btn btn-outline-primary btn-sm" 
                                               target="_blank"
                                               title="Ver PDF">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                            <a href="<?= $ruta_completa ?>" 
                                               class="btn btn-outline-success btn-sm"
                                               download="<?= $row['nombre_archivo'] ?>"
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
        <?php elseif ($table_exists): ?>
            <div class="empty-state">
                <i class="fas fa-file-contract"></i>
                <h4>No hay consentimientos generados</h4>
                <p class="text-muted">Aún no has generado ningún consentimiento informado.</p>
                <p class="text-muted">
                    Los consentimientos aparecerán aquí después de que completes el proceso 
                    de firma en tus citas. Ve a la sección "Mis Citas" para generar tu primer consentimiento.
                </p>
                <a href="ver_citas.php" class="btn btn-primary-custom mt-3">
                    <i class="fas fa-calendar-check"></i> Ver Mis Citas
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">&copy; 2025 Pro-Piel. Todos los derechos reservados.</p>
        </div>
    </footer>

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

            // Animación para los botones
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>

<?php
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>