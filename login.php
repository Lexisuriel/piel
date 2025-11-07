<?php
session_start();
require_once 'db.php';

// Si el usuario ya inició sesión, redirigir según su rol
if (isset($_SESSION['id'])) {
    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
        header('Location: dashboard/admin_dashboard.php');
    } else {
        header('Location: dashboard/dashboard.php');
    }
    exit();
}

$error = ""; // Variable para manejar mensajes de error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiar y validar datos
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    
    if (!empty($email) && !empty($password)) {
        // Conexión a la base de datos
        $db = new Database();
        $conn = $db->getConnection();

        // Buscar al usuario
        $sql = "SELECT id, nombre_completo, email, password, rol FROM usuario WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows === 1) {
                $stmt->bind_result($id, $nombre_completo, $emailDB, $hashedPassword, $rol);
                $stmt->fetch();
                
                if (password_verify($password, $hashedPassword)) {
                    // Evitar fijación de sesión
                    session_regenerate_id(true);

                    // Guardar datos de sesión
                    $_SESSION['id'] = $id;
                    $_SESSION['nombre_completo'] = $nombre_completo;
                    $_SESSION['email'] = $emailDB;
                    $_SESSION['rol'] = $rol;

                    // Redirigir según el rol
                    if ($rol === 'admin') {
                        header('Location: dashboard/admin_dashboard.php');
                    } else {
                        header('Location: dashboard/dashboard.php');
                    }
                    exit();
                } else {
                    $error = 'Contraseña incorrecta';
                }
            } else {
                $error = 'No se encontró ninguna cuenta con ese correo electrónico';
            }
            $stmt->close();
        } else {
            $error = 'Error en la consulta SQL';
        }
        $conn->close();
    } else {
        $error = 'Por favor completa todos los campos';
    }
}

// Mostrar errores (y volver atrás)
if (!empty($error)) {
    echo "<script>
        alert('" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "');
        window.history.back();
    </script>";
}
?>
