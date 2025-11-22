<?php
session_start();
if (!isset($_SESSION['id_especialista']) || $_SESSION['especialidad'] !== 'Dermatologo') {
    header("Location: ../index.php");
    exit();
}
$nombre = $_SESSION['nombre_especialista'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Dermatólogo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f8fa;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .card {
            transition: transform 0.2s ease;
        } 
        .card:hover {
            transform: scale(1.02);
        }
        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }
        .header-box {
            display: flex;
            align-items: center;
            background-color: #007bff;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body class="container py-5">
    <div class="header-box">
        <img src="../img/doctor.jpeg" alt="Foto" class="avatar">
        <div>
            <h3 class="mb-0">Bienvenido <?= htmlspecialchars($nombre) ?></h3>
            <p class="mb-0">Especialidad: Dermatología</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Ver Citas</h5>
                    <p class="card-text">Revisa tus próximas citas agendadas.</p>
                    <a href="ver_citas.php" class="btn btn-primary">Acceder</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Historial Médico</h5>
                    <p class="card-text">Consulta o registra el historial del paciente.</p>
                  <a href="../historial/ver.php?id=<?= $_SESSION['id_especialista'] ?>" 
   class="btn btn-success">
   Ver historial
</a>

                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Cerrar Sesión</h5>
                    <p class="card-text">Finalizar tu sesión actual.</p>
                    <a href="../logout.php" class="btn btn-danger">Salir</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
