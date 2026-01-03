<?php
require "conexion.php";
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "admin") {
    die("Acceso denegado.");
}

$id = $_GET['id'] ?? null;
$mensaje = "";

// Actualizar datos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    $stmt = $pdo->prepare("UPDATE tipos_habitacion SET nombre = ?, precio_base = ? WHERE id = ?");
    $stmt->execute([$nombre, $precio, $id]);
    
    // Redirigir de vuelta al admin
    header("Location: admin_tipos.php");
    exit;
}

// Obtener datos actuales para rellenar el formulario
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM tipos_habitacion WHERE id = ?");
    $stmt->execute([$id]);
    $tipo = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tipo</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <section class="contenedor">
        <h2>Editar Tipo de Habitación</h2>
        
        <form method="POST" action="editar_tipo.php" class="formulario">
            <input type="hidden" name="id" value="<?= $tipo['id'] ?>">
            
            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?= $tipo['nombre'] ?>" required>
            
            <label>Precio Base:</label>
            <input type="number" step="0.01" name="precio" value="<?= $tipo['precio_base'] ?>" required>
            
            <button type="submit">Actualizar</button>
            <a href="admin_tipos.php" style="margin-left:10px;">Cancelar</a>
        </form>
    </section>
</body>
</html>