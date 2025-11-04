<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Agregar link de Bootstrap si lo usas -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="editar.css">
</head>


<body>
    <div class="container mt-5">
        <div class="card p-4 shadow-sm">
            <h2 class="text-center">Editar Perfil</h2>
            <form action="procesar_editar_perfil.php" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del Paciente" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email del Paciente" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Número de Teléfono" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block mt-3">Guardar Cambios</button>
                <a href="dashboard_paciente.html" class="btn btn-secondary btn-block">Cancelar</a>
            </form>
        </div>
    </div>
</body>

</html>