<?php
session_start();
require "conexion.php";

// Solo empleados y admins pueden buscar disponibilidad
if (!isset($_SESSION["rol"])) {
    header("Location: index.php");
    exit;
}

// Variables para el filtro
$fecha_entrada = isset($_GET['fecha_entrada']) ? $_GET['fecha_entrada'] : date('Y-m-d');
$fecha_salida  = isset($_GET['fecha_salida']) ? $_GET['fecha_salida'] : date('Y-m-d', strtotime('+1 day'));
$tipo          = isset($_GET['tipo']) ? $_GET['tipo'] : 'todos';

$habitaciones_disponibles = [];

// LOGICA DE BÚSQUEDA
if (isset($_GET['buscar'])) {
    try {
        // Esta consulta busca habitaciones que NO estén ocupadas en esas fechas
        // "Selecciona todas las habitaciones cuyo número NO esté en la lista de reservas activas que se solapen con las fechas elegidas"
        
        $sql = "SELECT * FROM habitaciones 
                WHERE numero NOT IN (
                    SELECT numero_habitacion FROM reservas 
                    WHERE estado = 'activa' 
                    AND (
                        (fecha_entrada < :salida AND fecha_salida > :entrada)
                    )
                )";
        
        $params = [
            ':salida' => $fecha_salida,
            ':entrada' => $fecha_entrada
        ];

        // Si se seleccionó un tipo específico, lo agregamos al filtro
        if ($tipo != 'todos') {
            $sql .= " AND tipo = :tipo";
            $params[':tipo'] = $tipo;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $habitaciones_disponibles = $stmt->fetchAll();

    } catch (PDOException $e) {
        $error = "Error al buscar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar Disponibilidad - FapTour</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header>
        <h1>Buscador de Disponibilidad</h1>
    </header>

    <nav class="navbar">
        <a href="clientes.php">Clientes</a>
        <a href="reservas.php">Reservas</a>
        <a href="disponibilidad.php" style="border-bottom: 3px solid #1a73e8; color: #1a73e8;">Disponibilidad</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <section class="contenedor">

        <div class="panel-control" style="display: block;"> <h3>Filtrar Alojamientos</h3>
            
            <form action="disponibilidad.php" method="GET" style="display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-end;">
                
                <div>
                    <label>Fecha Entrada:</label>
                    <input type="date" name="fecha_entrada" value="<?php echo $fecha_entrada; ?>" required style="margin-bottom:0;">
                </div>

                <div>
                    <label>Fecha Salida:</label>
                    <input type="date" name="fecha_salida" value="<?php echo $fecha_salida; ?>" required style="margin-bottom:0;">
                </div>

                <div>
                    <label>Tipo de Habitación:</label>
                    <select name="tipo" style="margin-bottom:0; min-width: 150px;">
                        <option value="todos">Cualquiera</option>
                        <option value="simple" <?php if($tipo == 'simple') echo 'selected'; ?>>Simple</option>
                        <option value="doble" <?php if($tipo == 'doble') echo 'selected'; ?>>Doble</option>
                        <option value="suite" <?php if($tipo == 'suite') echo 'selected'; ?>>Suite</option>
                    </select>
                </div>

                <button type="submit" name="buscar">🔍 Buscar Disponibles</button>
            </form>
        </div>

        <?php if (isset($_GET['buscar'])): ?>
            
            <h3>Resultados de búsqueda:</h3>
            
            <?php if (count($habitaciones_disponibles) > 0): ?>
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Habitación</th>
                            <th>Tipo</th>
                            <th>Precio / Noche</th>
                            <th>Detalles</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($habitaciones_disponibles as $hab): ?>
                            <tr>
                                <td style="font-size: 1.2em; font-weight: bold; color: #1a73e8;">
                                    <?php echo $hab['numero']; ?>
                                </td>
                                <td style="text-transform: capitalize;">
                                    <span class="badge" style="background: #6c757d;">
                                        <?php echo $hab['tipo']; ?>
                                    </span>
                                </td>
                                <td>$<?php echo $hab['precio']; ?></td>
                                <td><?php echo $hab['detalles']; ?></td>
                                <td>
                                    <a href="reservas.php?habitacion=<?php echo $hab['numero']; ?>&fecha=<?php echo $fecha_entrada; ?>" 
                                       class="badge bg-activa" 
                                       style="text-decoration: none; font-size: 0.9em; cursor: pointer;">
                                       Asignar Habitación
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="msg-alerta msg-danger">
                    No hay habitaciones disponibles de tipo <strong><?php echo strtoupper($tipo); ?></strong> para estas fechas.
                </div>
            <?php endif; ?>

        <?php else: ?>
            <p style="text-align: center; color: #666;">Selecciona las fechas y haz clic en buscar para ver habitaciones libres.</p>
        <?php endif; ?>

    </section>

</body>
</html>