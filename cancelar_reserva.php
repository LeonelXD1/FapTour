<?php
require "conexion.php";

$id = $_GET["id"];

$sql = "UPDATE reservas 
        SET estado='cancelada', fecha_cancelacion=NOW() 
        WHERE id=?";

$stmt = $pdo->prepare($sql);

if ($stmt->execute([$id])) {
    header("Location: reservas.php");
} else {
    echo "Error al cancelar.";
}
?>
