<?php
require "conexion.php"; // aquí se define $pdo

// Verifica si la sesión NO está iniciada antes de intentar iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "empleado") {
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
        <h1>Registro de Reservas</h1>
    </header>

    <nav class="navbar">
        <a href="clientes.php">Clientes</a>
        <a href="reservas.php">Reservas</a>
        <a href="calendario.php">Calendario</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <section class="contenedor">

        <h2>Registrar Nueva Reserva</h2>

        <form class="formulario" action="registrar_reserva.php" method="POST">

            <label>Cliente:</label>
            <select name="cliente_id" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                <option value="">-- Seleccione un cliente --</option>
                <?php
                try {
                    // Consultamos ID y Nombre de la tabla clientes
                    $stmtClientes = $pdo->query("SELECT id, nombre_completo FROM clientes ORDER BY nombre_completo ASC");
                    while ($cliente = $stmtClientes->fetch()) {
                        // El value es el ID (para la BD), pero mostramos el Nombre
                        echo "<option value='{$cliente['id']}'>{$cliente['nombre_completo']}</option>";
                    }
                } catch (PDOException $e) {
                    echo "<option disabled>Error cargando clientes</option>";
                }
                ?>
            </select>

            <label>Habitación Disponible:</label>
            <select name="habitacion_id" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                <option value="">-- Seleccione una habitación --</option>
                <?php
                try {
                    $sqlHabs = "SELECT h.id, h.numero, t.nombre AS tipo, t.precio_base 
                                FROM habitaciones h
                                INNER JOIN tipos_habitacion t ON h.tipo_id = t.id
                                WHERE h.estado = 'disponible'
                                ORDER BY h.numero ASC";
                    
                    $stmtHabs = $pdo->query($sqlHabs);
                    
                    while ($hab = $stmtHabs->fetch()) {
                        echo "<option value='{$hab['id']}'>
                                Hab. {$hab['numero']} - {$hab['tipo']} ($" . number_format($hab['precio_base'], 2) . ")
                              </option>";
                    }
                } catch (PDOException $e) {
                    echo "<option disabled>Error: " . $e->getMessage() . "</option>";
                }
                ?>
            </select>

            <label>Fecha:</label>
            <input type="date" name="fecha" required>

            <button type="submit">Registrar Reserva</button>

        </form>

        <?php 
        if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'registrado') { 
            echo "
            <div style='background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;'>
                La reserva se ha registrado correctamente.
            </div>";
        } 

        if (isset($_GET['mensaje']) && $_GET['mensaje'] == 'error') { 
            echo "
            <div style='background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px;'>
                <strong>Error:</strong> No se pudo registrar la reserva.
            </div>";
        }
        ?>

        <h2>Lista de Reservas</h2>
        
        <form method="GET" action="reservas.php" class="formulario-filtros" style="background: #f1f1f1; padding: 15px; border-radius: 5px; margin-bottom: 20px; display: flex; gap: 10px; align-items: flex-end;">
            
            <div>
                <label style="display:block; font-size: 0.8em;">Fecha:</label>
                <input type="date" name="f_fecha" value="<?= $_GET['f_fecha'] ?? '' ?>">
            </div>

            <div>
                <label style="display:block; font-size: 0.8em;">Tipo Habitación:</label>
                <select name="f_tipo">
                    <option value="">-- Todos --</option>
                    <?php
                    try {
                        $stmtT = $pdo->query("SELECT * FROM tipos_habitacion");
                        while($t = $stmtT->fetch()){
                            $selected = ($_GET['f_tipo'] ?? '') == $t['id'] ? 'selected' : '';
                            echo "<option value='{$t['id']}' $selected>{$t['nombre']}</option>";
                        }
                    } catch (Exception $e) {}
                    ?>
                </select>
            </div>

            <div>
                <label style="display:block; font-size: 0.8em;">Estado:</label>
                <select name="f_estado">
                    <option value="">-- Todos --</option>
                    <option value="activa" <?= ($_GET['f_estado']??'')=='activa'?'selected':'' ?>>Activa</option>
                    <option value="finalizada" <?= ($_GET['f_estado']??'')=='finalizada'?'selected':'' ?>>Finalizada</option>
                    <option value="cancelada" <?= ($_GET['f_estado']??'')=='cancelada'?'selected':'' ?>>Cancelada</option>
                </select>
            </div>

            <button type="submit">Filtrar</button>
            <a href="reservas.php" style="margin-left: 10px; padding: 10px; text-decoration: none; color: #333;">Limpiar</a>
        </form>

        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Habitación</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php
            try {
                $sql = "SELECT r.id, c.nombre_completo, h.numero AS num_hab, t.nombre AS nombre_tipo, 
                               r.fecha, r.estado, r.fecha_cancelacion
                        FROM reservas r
                        INNER JOIN clientes c ON r.cliente_id = c.id
                        INNER JOIN habitaciones h ON r.habitacion_id = h.id
                        INNER JOIN tipos_habitacion t ON h.tipo_id = t.id
                        WHERE 1=1"; 

                $params = [];

                if (!empty($_GET['f_fecha'])) {
                    $sql .= " AND r.fecha = :fecha";
                    $params[':fecha'] = $_GET['f_fecha'];
                }
                if (!empty($_GET['f_tipo'])) {
                    $sql .= " AND t.id = :tipo_id";
                    $params[':tipo_id'] = $_GET['f_tipo'];
                }
                if (!empty($_GET['f_estado'])) {
                    $sql .= " AND r.estado = :estado";
                    $params[':estado'] = $_GET['f_estado'];
                }

                $sql .= " ORDER BY r.fecha DESC";

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                
                $reservas = $stmt->fetchAll(); 

                foreach ($reservas as $row) {
                    $estado = strtolower(trim($row['estado'])); 
                    $clase_estado = 'bg-finalizada'; 

                    if ($estado == 'activa') { 
                        $clase_estado = 'bg-activa'; 
                    } elseif ($estado == 'cancelada') { 
                        $clase_estado = 'bg-cancelada'; 
                    }

                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nombre_completo']}</td>
                            <td>Hab. {$row['num_hab']} <br><small>({$row['nombre_tipo']})</small></td>
                            <td>{$row['fecha']}</td>
                            <td><span class='badge {$clase_estado}'>{$row['estado']}</span></td>
                            <td style='text-align: center;'>";

                    if ($estado == 'activa') {
                        echo "<a href='cambiar_estado.php?id={$row['id']}&estado=finalizada' 
                                class='btn-accion btn-completar'
                                onclick=\"return confirm('¿Marcar como finalizada?');\">✔</a> ";

                        echo "<a href='modificar_reserva.php?id={$row['id']}' 
                                class='btn-accion btn-editar'>✎</a> ";

                        echo "<a href='cambiar_estado.php?id={$row['id']}&estado=cancelada' 
                                class='btn-accion btn-cancelar' 
                                onclick=\"return confirm('¿Seguro deseas cancelar?');\">✖</a>";
                    } else {
                        if($estado == 'cancelada') {
                            echo "<small style='color:red;'>Cancelada el:<br>{$row['fecha_cancelacion']}</small>";
                        } else {
                            echo "<small style='color:gray;'>✔ Finalizada</small>";
                        }
                    }

                    echo "</td>
                        </tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='6'>Error: " . $e->getMessage() . "</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </section>

</body>
</html>