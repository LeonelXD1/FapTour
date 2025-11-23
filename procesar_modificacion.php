<?php
require "conexion.php";

$id = $_POST["id"];
$habitacion = $_POST["numero_habitacion"];
$fecha = $_POST["fecha"];

$sql = "UPDATE reservas 
        SET numero_habitacion=?, fecha=? 
        WHERE id=?";

$stmt = $pdo->prepare($sql);

if ($stmt->execute([$habitacion, $fecha, $id])) {
    header("Location: reservas.php");
} else {
    echo "Error al actualizar.";
}
?>
