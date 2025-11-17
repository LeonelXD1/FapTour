<?php
require "conexion.php";

$cliente_id = $_POST['cliente_id'];
$numero = $_POST['numero_habitacion'];
$fecha = $_POST['fecha'];

$sql = "INSERT INTO reservas (cliente_id, numero_habitacion, fecha)
        VALUES ('$cliente_id', '$numero', '$fecha')";

if ($conn->query($sql)) {
    echo "<script>alert('Reserva registrada'); window.location='reservas.html';</script>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
