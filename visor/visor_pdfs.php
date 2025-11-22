<?php
//Definir la ruta relativa ala carpeta donde estan almacenados los pdfs

$directorio = "../consentimientos/pdfs-firmados";

//Obtener todos los archivos del directorio, excluyendo los especiales "." y ".."
$archivos = array_diff(scandir($directorio), array('..', '.'));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visor de Documantos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        #buscador{
            max-width: 400px;
        }
        </style>
    </head>
<body class="bg-light">
        <div class="container mt-5">
            <h2 class="mb-4">Visor de PDF's Firmados</h2>
            <div class="mb-3">
                <input type="text" id="buscador"class="form-control" placeholder="Buscar archivo PDF...">
            </div>

            <?php if (count($archivos) > 0) : ?>
                <table id="tablaPDFs" class="table table-bordered table-hover bg-white text center">
                    <thead class="thead-dark">
                      <tr>
                            <th>Nombre del archivo</th>
                            <th>Tamaño</th>
                            <th>Fecha de Creacion</th>
                            <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($archivos as $archivo) : //Iteramos sobre todos los archivos encontrados en la carpeta y contruimos la ruta completa del archivo
                        $ruta = "$directorio/$archivo";
                        ?>
                        <tr>
                            <!-- Mostrar el nombre del archivo -->
                             <td class="text-left"><?=htmlspecialchars($archivo)?></td>
                            <!--Mostrar el tamaño del archivo KB-->
                             <td><?= round(filesize($ruta)/1024, 2) ?> KB</td>
                        
                        <!--Mostrar la fecha de creacion del archivo-->  
                        <td><?= date("d/m/Y H:i", filemtime($ruta)) ?></td>

                        <td>
                          <!-- Acciones disponibles-->
                          <!-- Abrir PDF en nuevo tab -->
                          <a href="<?=$ruta ?>" target="_blank" class="btn btn-sm btn-primary">Ver</a>

                          <!-- Descargar PDF -->
                           <a href="<?=$ruta ?>" download class="btn btn-sm btn-success">Descargar</a>

                           <!-- Eliminar PDF -->
                           <a href="eliminar_pdf.php?archivo=<?=urldecode($archivo)?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estas seguro de eliminar este documento PDF?');">Eliminar</a>
                           </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>

                <?php else : ?>
                <!-- Mostrar un mensaje si no hay archivos en la carpeta pdf's firmados-->
                 <div class="alert alert-warning">No hay archivos PDF's en la carpeta</div>
                 <?php endif; ?>
        </div>

                <!--Scrip para filtrar filas en tiempo real-->
                <script>
                    //Usamos keyup del imput para buscar archivos
                    document.getElementById("buscador").addEventListener("keyup", function(){
                        let filtro = this.value.toLowerCase(); //Convertir el texto en minusculas
                        let filas = document.querySelectorAll("#tablaPDFs tbody tr"); //Verificamos todas las filas de la tabla

                        //Iterar sobre cada fila mostrando y ocultando segun el texto
                        filas.forEach(function(fila){
                            let textoFila = fila.innerText.toLowerCase(); //Muestra el contenido de la fila
                            fila.style.display = textoFila.includes(filtro)? "" : "none"; //Mostramos datos coincidentes
                        });
                    });
                </script>
    

</body>
</html>