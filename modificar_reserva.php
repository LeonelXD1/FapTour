<?php
require "conexion.php";

$id = $_GET["id"];

$stmt = $pdo->prepare("SELECT * FROM reservas WHERE id = ?");
$stmt->execute([$id]);
$reserva = $stmt->fetch();

if (!$reserva) {
    echo "Reserva no encontrada";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Reserva</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<header>
    <h1>FapTour - Modificar Reserva</h1>
</header>

<nav class="navbar">
    <a href="reservas.php">Reservas</a>
    <a href="clientes.php">Clientes</a>
    <a href="logout.php">Cerrar sesión</a>
</nav>

<section class="contenedor">

    <h2>Modificar Reserva</h2>

    <?php if ($reserva['estado'] === 'cancelada'): ?>

        <h3 style="color: red;">Esta reserva está cancelada y no puede modificarse.</h3>
        <a href="reservas.php">Volver</a>

    <?php else: ?>

        <form class="formulario" action="procesar_modificacion.php" method="POST">

            <input type="hidden" name="id" value="<?= $reserva['id'] ?>">

            <label>Número de Habitación:</label>
            <input type="number" name="numero_habitacion" value="<?= $reserva['numero_habitacion'] ?>" required>

            <label>Fecha:</label>
            <input type="date" name="fecha" value="<?= $reserva['fecha'] ?>" required>

            <button type="submit">Guardar Cambios</button>
        </form>

    <?php endif; ?>

</section>

</body>
</html>
