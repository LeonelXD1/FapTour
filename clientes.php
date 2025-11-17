<?php
require "conexion.php"; // define $pdo
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
    <title>FapTour - Registro de Empleados</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header>
        <h1>FapTour - Registro de Clientes</h1>
    </header>

    <nav class="navbar">
        <a href="clientes.php">Clientes</a>
        <a href="reservas.php">Reservas</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <section class="contenedor">

        <h2>Registrar Nuevo Cliente</h2>

        <form class="formulario" action="registrar_cliente.php" method="POST">

            <label>Nombre Completo:</label>
            <input type="text" name="nombre_completo" required>

            <button type="submit">Registrar Cliente</button>

        </form>

        <table class="tabla">
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
            </tr>

            <?php
            try {
                $sql = "SELECT id, nombre_completo FROM clientes";
                $stmt = $pdo->query($sql);
                $clientes = $stmt->fetchAll();

                foreach ($clientes as $row) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nombre_completo']}</td>
                        </tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='2'>Error: " . $e->getMessage() . "</td></tr>";
            }
            ?>
        </table>

    </section>

</body>
</html>
