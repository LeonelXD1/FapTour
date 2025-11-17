<?php
require "conexion.php";

$nombre = $_POST['nombre_completo'];

$sql = "INSERT INTO clientes (nombre_completo)
        VALUES ('$nombre')";

if ($conn->query($sql)) {
    echo "<script>alert('Cliente registrado'); window.location='empleados.html';</script>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
