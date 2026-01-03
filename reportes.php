<?php
session_start();
require "conexion.php";

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "admin") {
    echo "<h2>Acceso denegado. Solo administradores.</h2>";
    exit;
}

// Configuración del filtro
$filtro = isset($_GET['estado']) ? $_GET['estado'] : '';
$condicion_sql = "";
$params = [];

// Aplicar lógica de filtro si no es "todas"
if ($filtro != '' && $filtro != 'todas') {
    $condicion_sql = " AND r.estado = :estado";
    $params[':estado'] = $filtro;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>FapTour - Reportes de Reservas</title>
    <link rel="stylesheet" href="estilos.css"> 
</head>
<body>

    <header>
        <h1>Reporte General de Ocupación</h1>
    </header>

    <nav class="navbar">
        <a href="usuarios.php">Usuarios</a>
        <a href="reportes.php">Reportes</a>
        <a href="admin_tipos.php">Tipos de Habitaciones</a>
        <a href="admin_habitaciones.php">Habitaciones</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <section class="contenedor">

        <div class="panel-control" style="margin-bottom: 20px; padding: 15px; background: #f4f4f4; border-radius: 8px;">
            <form action="reportes.php" method="GET" style="display: inline-block;">
                <label><strong>Filtrar por Estado:</strong></label>
                <select name="estado" onchange="this.form.submit()">
                    <option value="todas">-- Ver Todas --</option>
                    <option value="activa" <?php if($filtro == 'activa') echo 'selected'; ?>>Activas</option>
                    <option value="finalizada" <?php if($filtro == 'finalizada') echo 'selected'; ?>>Completadas</option>
                    <option value="cancelada" <?php if($filtro == 'cancelada') echo 'selected'; ?>>Canceladas</option>
                </select>
            </form>

            <button onclick="window.print()" class="btn-print" style="float: right;">Imprimir Reporte</button>
        </div>

        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Habitación</th> <th>Fecha</th>
                    <th>Estado</th>
                    <th>Fecha cancelación</th>
                    <th>Acciones</th> </tr>
            </thead>
            <tbody>
            <?php
            try {
                // Consulta corregida:
                // 1. Usamos JOIN con habitaciones (h) para sacar el número
                // 2. Concatenamos $condicion_sql para que el filtro funcione
                $sql = "SELECT r.id, c.nombre_completo, h.numero, r.fecha, r.estado, r.fecha_cancelacion
                        FROM reservas r
                        INNER JOIN clientes c ON r.cliente_id = c.id
                        INNER JOIN habitaciones h ON r.habitacion_id = h.id
                        WHERE 1=1 " . $condicion_sql . " 
                        ORDER BY r.fecha DESC";

                // Usamos prepare() en lugar de query() por seguridad con los filtros
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $reservas = $stmt->fetchAll();

                if (count($reservas) > 0) {
                    foreach ($reservas as $row) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['nombre_completo']}</td>
                                <td>Hab. {$row['numero']}</td>
                                <td>{$row['fecha']}</td>
                                <td>{$row['estado']}</td>
                                <td>{$row['fecha_cancelacion']}</td>
                                <td>
                                    <a href='cancelar_reserva.php?id={$row['id']}&origen=reportes'
                                    onclick=\"return confirm('¿Seguro deseas cancelar esta reserva?');\"
                                    style='color: red;'>
                                    Cancelar
                                    </a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center'>No hay reservas con este criterio.</td></tr>";
                }

            } catch (PDOException $e) {
                echo "<tr><td colspan='7'>Error: " . $e->getMessage() . "</td></tr>";
            }
            ?>
            </tbody>
        </table>

    </section>

</body>
</html>