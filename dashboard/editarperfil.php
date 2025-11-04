<?php
    session_start();
    if (!isset($_SESSION['id'])) {
        header('Location: ../index.php');
        exit();
    }

    include_once("../db.php");

    $db = new Database();
    $conn = $db->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nuevoNombre = $conn->real_escape_string($_POST['nombre']);
        $nuevoEmail = $conn->real_escape_string($_POST['email']);
        $nuevoTelefono = $conn->real_escape_string($_POST['telefono']);

        $updateSQL = "UPDATE usuario SET nombre_completo = ?, email = ?, tell = ? WHERE id = ?";
        if ($stmt = $conn->prepare($updateSQL)) {
            $stmt->bind_param("sssi", $nuevoNombre, $nuevoEmail, $nuevoTelefono, $_SESSION['id']);
            $stmt->execute();
            $stmt->close();

            $_SESSION['nombre_completo'] = $nuevoNombre;
            $_SESSION['email'] = $nuevoEmail;

            echo "<script>alert('Perfil actualizado exitosamente.'); window.location.href = 'editarperfil.php';</script>";
            exit();
        }
    }

    $nombre = $_SESSION['nombre_completo'];
    $email = $_SESSION['email'];

    $sql = "SELECT tell, fecha_nacimiento FROM usuario WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('i', $_SESSION['id']);
        $stmt->execute();
        $stmt->bind_result($telefono, $fecha_nacimiento);
        $stmt->fetch();
        $stmt->close();
    }
    $conn->close();

    $edad = "Desconocida";
    if (!empty($fecha_nacimiento)) {
        $fecha_nac = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nac)->y;
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Pro-Piel</title>
    <link rel="icon" href="../ico/logo.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
            padding: 40px;
        }

        .dashboard-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h3 {
            color: #2a9d8f;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-top: 5px;
        }

        button {
            margin-top: 25px;
            padding: 10px 20px;
            background-color: #2a9d8f;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        button:hover {
            background-color: #21867a;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            background-color: #6c757d;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Regresar </a>

        <h3>Editar Perfil</h3>
        <form action="editarperfil.php" method="post">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($nombre); ?>">

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>">

            <label for="telefono">Teléfono:</label>
            <input type="tel" name="telefono" id="telefono" value="<?php echo htmlspecialchars($telefono); ?>">

            <label for="edad">Edad:</label>
            <input type="text" name="edad" id="edad" value="<?php echo htmlspecialchars($edad); ?>" disabled>

            <button type="submit">Guardar</button>
        </form>
    </div>
</body>
</html>
