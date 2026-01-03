<?php
require "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $documento = $_POST['documento'];
    $nombre = $_POST['nombre_completo'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];

    try {
        $sql = "INSERT INTO clientes (documento, nombre_completo, telefono, correo) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$documento, $nombre, $telefono, $correo]);
        
        header("Location: clientes.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al registrar: " . $e->getMessage();
    }
}
?>