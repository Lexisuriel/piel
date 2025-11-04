<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: ../index.php');
    exit();
}

require_once("../db.php");

$nombre = $_SESSION['nombre_completo'];
$email = $_SESSION['email'];
$id_usuario = $_SESSION['id'];

$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT tell, fecha_nacimiento FROM usuario WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->bind_result($telefono, $fecha_nacimiento);
    $stmt->fetch();
    $stmt->close();
}

$edad = "Desconocida";
if (!empty($fecha_nacimiento)) {
    $fecha_nac = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nac)->y;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Pacientes - Pro-Piel</title>
    <link rel="icon" href="../ico/logo.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <style>
        :root {
            --primary-color: #2a9d8f;
            --primary-hover: #21867a;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f9fc;
            min-height: 100vh;
        }
        
        /* Sidebar styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--primary-color);
            color: #fff;
            height: 100vh;
            position: fixed;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-header h2 {
            margin: 0;
            white-space: nowrap;
        }
        
        .sidebar-nav {
            padding: 20px 0;
        }
        
        .sidebar-nav a {
            display: flex;
            align-items: center;
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            margin: 5px 0;
            font-weight: 500;
            transition: all 0.3s;
            white-space: nowrap;
        }
        
        .sidebar-nav a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background-color: var(--primary-hover);
            padding-left: 25px;
        }
        
        .sidebar-nav a:hover i, .sidebar-nav a.active i {
            color: #fff;
        }
        
        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s;
        }
        
        /* Dashboard cards */
        .dashboard-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            padding: 20px;
            transition: transform 0.3s;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .dashboard-card h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        .dashboard-card h3 i {
            margin-right: 10px;
        }
        
        /* Header */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .dashboard-header h1 {
            color: var(--primary-color);
            margin: 0;
            font-size: 1.8rem;
        }
        
        .dashboard-header .subtitle {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Buttons */
        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary-custom:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        /* Toggle button for sidebar */
        .sidebar-toggle {
            display: none;
            background: var(--primary-color);
            color: white;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            margin-right: 15px;
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .sidebar {
                left: calc(-1 * var(--sidebar-width));
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar-toggle {
                display: block;
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .dashboard-header h1 {
                margin-bottom: 10px;
            }
        }
        
        /* Profile info */
        .profile-info p {
            margin-bottom: 10px;
        }
        
        .profile-info strong {
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-user-md"></i> Pro-Piel</h2>
        </div>
        <div class="sidebar-nav">
            <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Inicio</a>
            <a href="agendar_cita.php"><i class="fas fa-calendar-check"></i> Mis Citas</a>
            <a href="ver_consentimiento.php"><i class="fas fa-file-alt"></i> Consentimiento</a>
            <a href="#historial"><i class="fas fa-notes-medical"></i> Historial</a>
            <a href="editarperfil.php"><i class="fas fa-user"></i> Perfil</a>
            <a href="#ayuda"><i class="fas fa-question-circle"></i> Ayuda</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="dashboard-header">
                <div>
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Bienvenido, <?php echo htmlspecialchars($nombre); ?></h1>
                    <span class="subtitle">Panel de Paciente</span>
                </div>
            </div>
            
            <div class="row">
                <!-- Mis Citas Card -->
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <h3><i class="fas fa-calendar-check"></i> Próximas Citas</h3>
                        <a href="ver_citas.php" class="btn btn-primary-custom mb-3">
                            <i class="fas fa-calendar-check"></i> Ver citas agendadas
                        </a>
                        <p>Aquí aparecerán tus próximas citas.</p>
                    </div>
                </div>
                
                <!-- Historial Card -->
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <h3><i class="fas fa-file-medical-alt"></i> Historial de Tratamientos</h3>
                        <p>Consulta tu historial médico aquí.</p>
                    </div>
                </div>
                
                <!-- Perfil Card -->
                <div class="col-12">
                    <div class="dashboard-card profile-info">
                        <h3><i class="fas fa-user-circle"></i> Perfil</h3>
                        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($telefono ?? "No registrado"); ?></p>
                        <p><strong>Edad:</strong> <?php echo htmlspecialchars($edad); ?> años</p>
                        <a href="editarperfil.php" class="btn btn-primary-custom">
                            <i class="fas fa-edit"></i> Editar Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('main-content').classList.toggle('sidebar-active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            if (window.innerWidth <= 992 && 
                !sidebar.contains(event.target) && 
                event.target !== sidebarToggle && 
                !sidebarToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });
        
        // Adjust sidebar on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                document.getElementById('sidebar').classList.remove('active');
            }
        });
    </script>
</body>
</html>