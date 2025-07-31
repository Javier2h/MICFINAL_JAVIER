<?php
$host = "localhost";
$usuario = "root";
$contrasena = ""; // tu contraseña, si la tienes
$base_datos = "panaderia";
$puerto = 3306; // cambia aquí si usas otro puerto más adelante

$conn = new mysqli($host, $usuario, $contrasena, $base_datos, $puerto);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
