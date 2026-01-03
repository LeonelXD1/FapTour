<?php
require "conexion.php";
// Verifica si la sesión NO está iniciada antes de intentar iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Solo empleados y admin pueden ver esto
if (!isset($_SESSION["rol"])) {
    header("Location: index.php");
    exit;
}

// 1. Configuración de Fechas (Mes y Año actual o seleccionado)
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('n');
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');

// Validar navegación de fechas
if ($mes < 1) { $mes = 12; $anio--; }
if ($mes > 12) { $mes = 1; $anio++; }

// Calcular días del mes seleccionado
$dias_en_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);

// Nombres de meses en español
$nombres_meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

// 2. Obtener TODAS las Habitaciones
$stmtH = $pdo->query("SELECT h.id, h.numero, t.nombre as tipo 
                      FROM habitaciones h 
                      JOIN tipos_habitacion t ON h.tipo_id = t.id 
                      ORDER BY h.numero ASC");
$habitaciones = $stmtH->fetchAll();

// 3. Obtener Reservas del mes (Solo las activas)
// NOTA: Usamos la columna 'fecha' según tu estructura actual. 
// Si usaras rangos (entrada/salida) la lógica sería un poco distinta.
$sqlR = "SELECT r.habitacion_id, DAY(r.fecha) as dia, c.nombre_completo 
         FROM reservas r 
         JOIN clientes c ON r.cliente_id = c.id
         WHERE MONTH(r.fecha) = :mes 
         AND YEAR(r.fecha) = :anio 
         AND r.estado = 'activa'";

$stmtR = $pdo->prepare($sqlR);
$stmtR->execute([':mes' => $mes, ':anio' => $anio]);
$reservas_raw = $stmtR->fetchAll();

// Organizar reservas en un array fácil de consultar: $ocupacion[habitacion_id][dia] = nombre_cliente
$ocupacion = [];
foreach ($reservas_raw as $res) {
    $ocupacion[$res['habitacion_id']][$res['dia']] = $res['nombre_completo'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planning de Disponibilidad - FapTour</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        /* Estilos específicos para el Calendario */
        .calendario-container {
            overflow-x: auto; /* Permite scroll horizontal si son muchos días */
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .tabla-calendario {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85em;
        }
        .tabla-calendario th, .tabla-calendario td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 5px;
        }
        .tabla-calendario th {
            background-color: #333;
            color: white;
            min-width: 30px;
        }
        .col-habitacion {
            background-color: #f4f4f4;
            font-weight: bold;
            text-align: left !important;
            padding-left: 10px !important;
            position: sticky;
            left: 0;
            z-index: 10; /* Para que la columna de habitación no se mueva */
            border-right: 2px solid #ccc !important;
        }
        /* Celdas de estado */
        .dia-libre {
            background-color: #e8f5e9; /* Verde muy claro */
            color: #2e7d32;
        }
        .dia-ocupado {
            background-color: #ffcdd2; /* Rojo claro */
            color: #c62828;
            font-weight: bold;
            font-size: 0.8em;
            cursor: help; /* Muestra interrogación al pasar mouse */
        }
        .controles-mes {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-nav {
            background: #007bff;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <header>
        <h1>Calendario</h1>
    </header>

    <nav class="navbar">
        <a href="clientes.php">Clientes</a>
        <a href="reservas.php">Reservas</a>
        <a href="calendario.php">Calendario</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <section class="contenedor">

        <div class="controles-mes">
            <a href="calendario.php?mes=<?= $mes-1 ?>&anio=<?= $anio ?>" class="btn-nav">« Mes Anterior</a>
            <h2 style="margin:0;"><?= $nombres_meses[$mes] ?> <?= $anio ?></h2>
            <a href="calendario.php?mes=<?= $mes+1 ?>&anio=<?= $anio ?>" class="btn-nav">Mes Siguiente »</a>
        </div>

        <div class="calendario-container">
            <table class="tabla-calendario">
                <thead>
                    <tr>
                        <th class="col-habitacion" style="min-width: 120px;">Habitación</th>
                        <?php for ($d = 1; $d <= $dias_en_mes; $d++): ?>
                            <th><?= $d ?></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($habitaciones as $hab): ?>
                        <tr>
                            <td class="col-habitacion">
                                <?= $hab['numero'] ?> <br>
                                <small style="color:gray; font-weight:normal;"><?= $hab['tipo'] ?></small>
                            </td>

                            <?php for ($d = 1; $d <= $dias_en_mes; $d++): ?>
                                <?php 
                                    $id = $hab['id'];
                                    // Verificamos si existe reserva para esta habitación en este día
                                    if (isset($ocupacion[$id][$d])) {
                                        $cliente = $ocupacion[$id][$d];
                                        // Celda OCUPADA (Rojo)
                                        // Title permite que al pasar el mouse veas el nombre del cliente
                                        echo "<td class='dia-ocupado' title='Reservado por: $cliente'>X</td>";
                                    } else {
                                        // Celda LIBRE (Verde)
                                        echo "<td class='dia-libre'></td>";
                                    }
                                ?>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 15px;">
            <span style="display:inline-block; width:15px; height:15px; background:#ffcdd2; border:1px solid #ccc;"></span> Ocupado
            <span style="display:inline-block; width:15px; height:15px; background:#e8f5e9; border:1px solid #ccc; margin-left:15px;"></span> Disponible
        </div>

    </section>

</body>
</html>