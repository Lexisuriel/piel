<?php
require_once("db.php");

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y limpiar los datos del formulario
    $nombre_completo = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $telefono = trim($_POST['telefono']);
    $fecha_nacimiento = $_POST['birthdate'];
    $genero = isset($_POST['gender']) ? $_POST['gender'] : '';
    $direccion = trim($_POST['address']);
    
    // Validaciones básicas
    $errors = [];
    
    // Validar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden.";
    }
    
    // Validar longitud mínima de la contraseña
    if (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    }
    
    // Validar formato de email
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
    
    // Si no hay errores, proceder con el registro
    if (empty($errors)) {
        // Hashear la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertar el nuevo usuario en la base de datos
        $sql = "INSERT INTO usuario (nombre_completo, password, email, tell, fecha_nacimiento, rol, genero, direccion) 
                VALUES (?, ?, ?, ?, ?, 'paciente', ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $nombre_completo, $hashed_password, $email, $telefono, $fecha_nacimiento, $genero, $direccion);
        
        if ($stmt->execute()) {
            // Registro exitoso, redirigir con mensaje de éxito
            header("Location: index.php?registration=success");
            exit();
        } else {
            $errors[] = "Error al registrar el usuario. Por favor, inténtalo de nuevo.";
        }
        $stmt->close();
    }
    
    // Si hay errores, almacenarlos en sesión para mostrarlos en el index
    if (!empty($errors)) {
        session_start();
        $_SESSION['registration_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: index.php#registerModal");
        exit();
    }
} else {
    // Si alguien intenta acceder directamente al archivo, redirigir al index
    header("Location: index.php");
    exit();
}
?>