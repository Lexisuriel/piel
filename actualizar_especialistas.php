<?php
require_once 'db.php';

// ESPECIALISTAS DEFINITIVOS
$especialistas = [
    [
        'id' => 1,
        'nombre' => 'Dr. Hugo Alarcón',
        'especialidad' => 'Dermatologo',
        'correo' => 'hugo@propiel.com',
        'password' => 'HuA-2025@'
    ],
    [
        'id' => 2,
        'nombre' => 'Dr. Miguel Torres',
        'especialidad' => 'Podologo',
        'correo' => 'miguel@propiel.com',
        'password' => 'MiT-2025#'
    ],
    [
        'id' => 3,
        'nombre' => 'Dra. Ana Ruiz',
        'especialidad' => 'Tamizaje',
        'correo' => 'ana@propiel.com',
        'password' => 'AnR-2025%'
    ],
];

foreach ($especialistas as $esp) {

    $passHash = password_hash($esp['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        UPDATE especialistas 
        SET nombre = ?, especialidad = ?, correo = ?, password = ?
        WHERE id = ?
    ");

    $stmt->bind_param("ssssi", 
        $esp['nombre'], 
        $esp['especialidad'], 
        $esp['correo'], 
        $passHash, 
        $esp['id']
    );

    if ($stmt->execute()) {
        echo "✅ Actualizado ID {$esp['id']} ({$esp['nombre']})<br>";
    } else {
        echo "❌ Error en ID {$esp['id']}: " . $stmt->error . "<br>";
    }

    $stmt->close();
}

$conn->close();
?>
