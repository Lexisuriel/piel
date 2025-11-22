<?php
    if(isset($_GET['archivo'])) {
        $archivo = basename($_GET['archivo']); 
        
        //ruta completa de los archivos pdfs
        $ruta = __DIR__ . "/../consentimientos/pdfs-firmados/$archivo";

        //validacion del archivo en el directorio
        if(pathinfo($archivo, PATHINFO_EXTENSION) !== 'pdf'){
            header("Location: visor_pdfs.php?mg=error");
            exit;
        }
        //verificar la existencia del documento y eliminar definitivamente
        if(file_exists($ruta)) {
            if(unlink($ruta)) {
                header("Location: visor_pdfs.php?mg=eliminado");
                exit;
            } else {
                header("Location: visor_pdfs.php?mg=error");
                exit;
            }
        } else {
            header("Location: visor_pdfs.php?mg=error");//el archivo no existe o se movio antes de la eliminacion
            exit;
        }
    } else {
        header("Location: visor_pdfs.php?mg=error");//mensaje de error cuando algo falla en el directorio o servidor
    } 
?>