<?php
require "conexion.php";

$usuario = $_POST['usuario'];
$correo = $_POST['correo'];
$clave = password_hash($_POST['clave'], PASSWORD_BCRYPT);
$rol = $_POST['rol'];

$sql = "INSERT INTO usuarios (usuario, clave, rol, correo)
        VALUES ('$usuario', '$clave', '$rol', '$correo')";

if ($conn->query($sql)) {
    echo "<script>alert('Usuario registrado'); window.location='usuarios.html';</script>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
