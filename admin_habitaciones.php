<?php
require "conexion.php";
// Verifica si la sesión NO está iniciada antes de intentar iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Solo admin
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "admin") {
    echo "Acceso denegado.";
    exit;
}

// LÓGICA: Crear Habitación Física
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'crear') {
    $numero = $_POST['numero'];
    $tipo_id = $_POST['tipo_id'];
    
    try {
        // Insertamos la habitación y la marcamos como disponible por defecto
        $stmt = $pdo->prepare("INSERT INTO habitaciones (numero, tipo_id, estado) VALUES (?, ?, 'disponible')");
        $stmt->execute([$numero, $tipo_id]);
        $mensaje = "¡Habitación $numero creada exitosamente!";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "El número de habitación $numero ya existe.";
        } else {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// LÓGICA: Eliminar Habitación
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    try {
        $stmt = $pdo->prepare("DELETE FROM habitaciones WHERE id = ?");
        $stmt->execute([$id]);
        $mensaje = "Habitación eliminada.";
    } catch (PDOException $e) {
        $error = "No se puede eliminar: Probablemente tiene reservas asociadas.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Habitaciones - FapTour</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header>
        <h1>Gestión de Habitaciones Físicas</h1>
    </header>

    <nav class="navbar">
        <a href="usuarios.php">Usuarios</a>
        <a href="reportes.php">Reportes</a>
        <a href="admin_tipos.php">Tipos de Habitaciones</a>
        <a href="admin_habitaciones.php">Habitaciones</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <section class="contenedor">

        <?php if(isset($mensaje)) echo "<div style='color:green; border:1px solid green; padding:10px; margin-bottom:10px;'>$mensaje</div>"; ?>
        <?php if(isset($error)) echo "<div style='color:red; border:1px solid red; padding:10px; margin-bottom:10px;'>$error</div>"; ?>

        <div style="background:#f9f9f9; padding:20px; border-radius:8px; margin-bottom:30px;">
            <h3>➕ Nueva Habitación</h3>
            <form method="POST" action="admin_habitaciones.php" style="display:flex; gap:10px; align-items:flex-end;">
                <input type="hidden" name="accion" value="crear">
                
                <div>
                    <label>Número de Puerta:</label><br>
                    <input type="text" name="numero" placeholder="Ej: 501" required>
                </div>
                
                <div>
                    <label>Tipo de Habitación:</label><br>
                    <select name="tipo_id" required style="padding: 5px;">
                        <option value="">-- Selecciona el Tipo --</option>
                        <?php
                        // Aquí cargamos los tipos que creaste (incluida tu nueva Suite Doble)
                        $stmtT = $pdo->query("SELECT * FROM tipos_habitacion ORDER BY nombre ASC");
                        while ($t = $stmtT->fetch()) {
                            echo "<option value='{$t['id']}'>{$t['nombre']} - \${$t['precio_base']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" style="height:38px;">Crear Habitación</button>
            </form>
        </div>

        <h3>📋 Habitaciones del Hotel</h3>
        <table class="tabla">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Tipo</th>
                    <th>Estado Actual</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consultamos uniendo tablas para ver el nombre del tipo
                $sql = "SELECT h.id, h.numero, h.estado, t.nombre as nombre_tipo 
                        FROM habitaciones h 
                        JOIN tipos_habitacion t ON h.tipo_id = t.id 
                        ORDER BY h.numero ASC";
                $stmt = $pdo->query($sql);
                
                while ($row = $stmt->fetch()) {
                    echo "<tr>
                            <td><strong>{$row['numero']}</strong></td>
                            <td>{$row['nombre_tipo']}</td>
                            <td>{$row['estado']}</td>
                            <td>
                                <a href='admin_habitaciones.php?eliminar={$row['id']}' 
                                   class='btn-accion btn-cancelar'
                                   onclick=\"return confirm('¿Borrar habitación {$row['numero']}?');\">Eliminar</a>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>

    </section>

</body>
</html>