<?php
// Conexión a la base de datos
$servidor = "localhost";
$usuario = "skyper";
$password = "ctpalm2113";
$bd = "escuela";

$conn = new mysqli($servidor, $usuario, $password, $bd);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "Conexión exitosa a la base de datos.";
}
?>
