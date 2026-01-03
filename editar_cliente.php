<?php
require "conexion.php";
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "empleado") {
    die("Acceso denegado.");
}

$id = $_GET['id'] ?? null;

// 1. PROCESAR LA ACTUALIZACIÓN (Cuando le das al botón Guardar)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cliente = $_POST['id'];
    $documento = $_POST['documento'];
    $nombre = $_POST['nombre_completo'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];

    try {
        $sql = "UPDATE clientes SET 
                documento = ?, 
                nombre_completo = ?, 
                telefono = ?, 
                correo = ? 
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$documento, $nombre, $telefono, $correo, $id_cliente]);

        header("Location: clientes.php?msg=actualizado");
        exit;
    } catch (PDOException $e) {
        $error = "Error al actualizar: " . $e->getMessage();
    }
}

// 2. OBTENER LOS DATOS ACTUALES (Para llenar el formulario)
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <nav class="navbar">
        <a href="clientes.php">Volver a Clientes</a>
    </nav>

    <section class="contenedor">
        <h2>Editar Datos del Cliente</h2>
        
        <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>

        <form method="POST" action="editar_cliente.php" class="formulario">
            <input type="hidden" name="id" value="<?= $cliente['id'] ?>">

            <label>Documento/DNI:</label>
            <input type="text" name="documento" value="<?= $cliente['documento'] ?>">

            <label>Nombre Completo:</label>
            <input type="text" name="nombre_completo" value="<?= $cliente['nombre_completo'] ?>" required>

            <label>Teléfono:</label>
            <input type="text" name="telefono" value="<?= $cliente['telefono'] ?>">

            <label>Correo Electrónico:</label>
            <input type="email" name="correo" value="<?= $cliente['correo'] ?>">

            <button type="submit">Guardar Cambios</button>
        </form>
    </section>
</body>
</html>