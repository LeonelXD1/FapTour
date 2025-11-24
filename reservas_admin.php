<?php
require "conexion.php"; // aquí se define $pdo
if ($_SESSION["rol"] !== "admin") {
    echo "<h2>Acceso denegado. Solo administradores.</h2>";
    exit;
}

$estadoFiltro = "";
if (isset($_GET["estado"]) && $_GET["estado"] !== "") {
    $estadoFiltro = $_GET["estado"];
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
        <a href="usuarios.php">Empleados</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <section class="contenedor">

        <h2>Lista de Reservas</h2>

        <form class="formulario" method="GET">
            <label>Filtrar por estado:</label>
            <select name="estado" onchange="this.form.submit()">
                <option value="">Todas</option>
                <option value="activa" <?php if($estadoFiltro=="activa") echo "selected"; ?>>Activa</option>
                <option value="cancelada" <?php if($estadoFiltro=="cancelada") echo "selected"; ?>>Cancelada</option>
            </select>
        </form>


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

                if ($estadoFiltro !== "") {
                    $sql .= " WHERE r.estado = :estado";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(":estado", $estadoFiltro);
                    $stmt->execute();
                } else {
                    $stmt = $pdo->query($sql);
                }

                $reservas = $stmt->fetchAll();

                foreach ($reservas as $row) {
                    echo "<tr>
                          <td>{$row['id']}</td>
                          <td>{$row['nombre_completo']}</td>
                          <td>{$row['numero_habitacion']}</td>
                          <td>{$row['fecha']}</td>
                          <td>{$row['estado']}</td>
                          <td>{$row['fecha_cancelacion']}</td>
                    </tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='6'>Error: " . $e->getMessage() . "</td></tr>";
            }
            ?>
        </table>

    </section>

</body>
</html>
