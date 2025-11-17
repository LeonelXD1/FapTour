<?php
require "conexion.php"; // define $pdo

// Recibir datos del formulario
$cliente_id = $_POST['cliente_id'];
$numero = $_POST['numero_habitacion'];
$fecha = $_POST['fecha'];

try {
    // Preparar la consulta con placeholders (previene inyección SQL)
    $stmt = $pdo->prepare("INSERT INTO reservas (cliente_id, numero_habitacion, fecha)
                           VALUES (:cliente_id, :numero_habitacion, :fecha)");

    // Ejecutar con los valores
    $stmt->execute([
        ':cliente_id' => $cliente_id,
        ':numero_habitacion' => $numero,
        ':fecha' => $fecha
    ]);

    echo "<script>alert('Reserva registrada correctamente'); window.location='reservas.php';</script>";

} catch (PDOException $e) {
    echo "Error al registrar reserva: " . $e->getMessage();
}
?>
