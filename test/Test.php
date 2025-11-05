<?php
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase {
    public function testConexionBaseDatos() {
        $mysqli = @new mysqli("localhost", "root", "", "piel");
        $this->assertFalse($mysqli->connect_errno, "Error al conectar con la base de datos");
    }

    public function testPaginaPrincipal() {
        $contenido = file_get_contents("http://localhost/piel/index.php");
        $this->assertStringContainsString("<!DOCTYPE html>", $contenido, "El index.php no carga correctamente");
    }
}
