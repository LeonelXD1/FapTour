<?php
require "conexion.php"; // aquí se define $pdo
if ($_SESSION["rol"] !== "empleado") {
    echo "<h2>Acceso denegado. Solo empleados.</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FapTour - Registro de Reservas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header>
        <h1>FapTour - Registro de Reservas</h1>
    </header>

    <nav class="navbar">
        <a href="clientes.php">Clientes</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <section class="contenedor">

        <h2>Registrar Nueva Reserva</h2>

        <form class="formulario" action="registrar_reserva.php" method="POST">

            <label>ID Cliente:</label>
            <input type="number" name="cliente_id" required>

            <label>Número de Habitación:</label>
            <input type="number" name="numero_habitacion" required>

            <label>Fecha:</label>
            <input type="date" name="fecha" required>

            <button type="submit">Registrar Reserva</button>

        </form>


        <h2>Lista de Reservas</h2>

        <table class="tabla">
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Número Habitación</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Fecha cancelación</th>
            </tr>

            <?php
            try {
                $sql = "SELECT r.id, c.nombre_completo, r.numero_habitacion, r.fecha, r.estado, r.fecha_cancelacion
                        FROM reservas r
                        INNER JOIN clientes c ON r.cliente_id = c.id";

                $stmt = $pdo->query($sql);
                $reservas = $stmt->fetchAll();

                foreach ($reservas as $row) {
                    echo "<tr>
                          <td>{$row['id']}</td>
                          <td>{$row['nombre_completo']}</td>
                          <td>{$row['numero_habitacion']}</td>
                          <td>{$row['fecha']}</td>
                          <td>{$row['estado']}</td>
                          <td>{$row['fecha_cancelacion']}</td>
                          <td>
                            <a href='modificar_reserva.php?id={$row['id']}'>Modificar</a> |
                            <a href='cancelar_reserva.php?id={$row['id']}'
                            onclick=\"return confirm('¿Seguro deseas cancelar esta reserva?');\">
                            Cancelar
                            </a>
                        </td>
                    </tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='4'>Error: " . $e->getMessage() . "</td></tr>";
            }
            ?>
        </table>

    </section>

</body>
</html>
