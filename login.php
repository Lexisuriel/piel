<?php
session_start();
require_once 'db.php';

$error = "";

// Si ya inició sesión, redirigir
if (isset($_SESSION['id'])) {
    if ($_SESSION['rol'] === 'admin') {
        header('Location: dashboard/admin_dashboard.php');
    } elseif ($_SESSION['rol'] === 'paciente') {
        header('Location: dashboard/dashboard.php');
    } elseif ($_SESSION['rol'] === 'especialista') {
        // Redirigir según especialidad
        if ($_SESSION['especialidad'] === 'Dermatologo') {
            header('Location: especialistas/dermatologo.php');
        } elseif ($_SESSION['especialidad'] === 'Podologo') {
            header('Location: especialistas/podologo.php');
        } elseif ($_SESSION['especialidad'] === 'Tamizologo' || $_SESSION['especialidad'] === 'Tamiz') {
            header('Location: especialistas/tamiz.php');
        }
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $db = new Database();
        $conn = $db->getConnection();

        /* ---------------------------------------------------------
            1) BUSCAR EN TABLA USUARIO (paciente / admin)
        ----------------------------------------------------------*/
        $sql = "SELECT id, nombre_completo, email, password, rol FROM usuario WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $row = $resultado->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                // Guardar sesión
                session_regenerate_id(true);
                $_SESSION['id'] = $row['id'];
                $_SESSION['nombre_completo'] = $row['nombre_completo'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['rol'] = $row['rol'];

                if ($row['rol'] === 'admin') {
                    header('Location: dashboard/admin_dashboard.php');
                } elseif ($row['rol'] === 'paciente') {
                    header('Location: dashboard/dashboard.php');
                } else {
                    $error = "Rol no permitido.";
                }
                exit();
            }
        }

        /* ---------------------------------------------------------
            2) BUSCAR EN TABLA ESPECIALISTAS
        ----------------------------------------------------------*/
        $sql2 = "SELECT id, nombre, correo, password, especialidad FROM especialistas WHERE correo = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param('s', $email);
        $stmt2->execute();
        $resultado2 = $stmt2->get_result();

        if ($resultado2->num_rows === 1) {
            $esp = $resultado2->fetch_assoc();

            if (password_verify($password, $esp['password'])) {
                session_regenerate_id(true);

                $_SESSION['id_especialista'] = $esp['id'];
                $_SESSION['nombre_especialista'] = $esp['nombre'];
                $_SESSION['especialidad'] = $esp['especialidad'];
                $_SESSION['rol'] = 'especialista';

                // Redirección según especialidad
                if ($esp['especialidad'] === 'Dermatologo') {
                    header('Location: especialistas/dermatologo.php');
                } elseif ($esp['especialidad'] === 'Podologo') {
                    header('Location: especialistas/podologo.php');
                } elseif ($esp['especialidad'] === 'Tamizologo' || $esp['especialidad'] === 'Tamiz') {
                    header('Location: especialistas/tamiz.php');
                } else {
                    $error = "Especialidad no reconocida.";
                }
                exit();
            } else {
                $error = "Contraseña incorrecta.";
            }
        }

        $stmt->close();
        $stmt2->close();
        $conn->close();

        if (empty($error)) {
            $error = "No se encontró ninguna cuenta con ese correo electrónico";
        }

    } else {
        $error = "Por favor completa todos los campos";
    }
}

if (!empty($error)) {
    echo "<script>
        alert('" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "');
        window.history.back();
    </script>";
}
?>
