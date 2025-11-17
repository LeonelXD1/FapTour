<?php
require "conexion.php"; // define $pdo

$nombre = $_POST['nombre_completo'];

try {
    // Preparar consulta segura con placeholders
    $stmt = $pdo->prepare("INSERT INTO clientes (nombre_completo) VALUES (:nombre)");
    $stmt->execute([':nombre' => $nombre]);

    echo "<script>alert('Cliente registrado correctamente'); window.location='clientes.php';</script>";

} catch (PDOException $e) {
    echo "Error al registrar cliente: " . $e->getMessage();
}
?>
