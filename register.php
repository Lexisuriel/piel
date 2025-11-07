<?php
require_once("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();

    // Conexión a la base de datos
    $db = new Database();
    $conn = $db->getConnection();

    // Sanitizar datos
    $nombre_completo = htmlspecialchars(trim($_POST['name']));
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;
    $fecha_nacimiento = $_POST['birthdate'];
    $genero = isset($_POST['gender']) ? $_POST['gender'] : null;
    $direccion = htmlspecialchars(trim($_POST['address']));

    $errors = [];

    // Validaciones
    if ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden.";
    }
    if (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El formato del email no es válido.";
    }

    // Verificar si el email ya existe
    $sql = "SELECT id FROM usuario WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Este email ya está registrado.";
    }
    $stmt->close();

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario 
(nombre_completo, password, email, tell, fecha_nacimiento, rol, genero, direccion) 
VALUES (?, ?, ?, ?, ?, 'paciente', ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssss",
            $nombre_completo,
            $hashed_password,
            $email,
            $telefono,
            $fecha_nacimiento,
            $genero,
            $direccion
        );




        if ($stmt->execute()) {
            header("Location: index.php?registration=success");
            exit();
        } else {
            $errors[] = "Error al registrar el usuario: " . $stmt->error;
        }
        $stmt->close();
    }

    if (!empty($errors)) {
        echo "<pre>";
        print_r($errors);
        echo "</pre>";
        exit;
    }
}
