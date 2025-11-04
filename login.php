<?php
session_start();
require_once 'db.php';

// Si el usuario ya ha iniciado sesión, redirigirlo
if (isset($_SESSION['id'])) {
    header('Location: dashboard/dashboard.php');
    exit();
}

$error = ""; // Variable para manejar mensajes de error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener y limpiar los datos del formulario
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    
    if (!empty($email) && !empty($password)) {
        // Acceso a la conexión desde db.php
        $db = new Database();
        $conn = $db->getConnection();

        // Preparar la consulta SQL para obtener el usuario y sus datos
        $sql = "SELECT id, nombre_completo, email, password FROM usuario WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            
            // Verificar si se encontró el usuario
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $nombre_completo, $emailDB, $hashedPassword);
                $stmt->fetch();
                
                // Verificar la contraseña
                if (password_verify($password, $hashedPassword)) {
                    // Autenticación exitosa, iniciar sesión
                    $_SESSION['id'] = $id;
                    $_SESSION['nombre_completo'] = $nombre_completo;
                    $_SESSION['email'] = $email;

                    // Redirigir a la página de inicio
                    header('Location: dashboard/dashboard.php');
                    exit();
                } else {
                    // Contraseña incorrecta
                    $error = 'Contraseña incorrecta';
                }
            } else {
                // No se encontró el usuario
                $error = 'No se encontró ninguna cuenta con ese correo electrónico';
            }
            $stmt->close();
        } else {
            // Error en la preparación de la consulta SQL
            $error = 'Error en la consulta SQL';
        }
        $conn->close();
    } else {
        // Campos incompletos
        $error = 'Por favor completa todos los campos';
    }
}

// Si hay errores, mostrarlos en la página de login
if (!empty($error)) {
    echo "<script>alert('" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "'); window.history.back();</script>";
}
?>
