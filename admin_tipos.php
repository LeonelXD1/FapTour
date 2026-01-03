<?php
require "conexion.php";
// Verifica si la sesión NO está iniciada antes de intentar iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. SEGURIDAD: Solo el admin puede entrar aquí
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "admin") {
    echo "<h2>Acceso denegado. Se requiere rol de Administrador.</h2>";
    echo "<a href='reservas.php'>Volver</a>";
    exit;
}

// 2. LÓGICA: Agregar Nuevo Tipo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'crear') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO tipos_habitacion (nombre, precio_base) VALUES (?, ?)");
        $stmt->execute([$nombre, $precio]);
        $mensaje = "Tipo creado con éxito.";
    } catch (PDOException $e) {
        $error = "Error al crear: " . $e->getMessage();
    }
}

// 3. LÓGICA: Eliminar Tipo
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    try {
        $stmt = $pdo->prepare("DELETE FROM tipos_habitacion WHERE id = ?");
        $stmt->execute([$id]);
        $mensaje = "Tipo eliminado correctamente.";
    } catch (PDOException $e) {
        // El error 23000 suele ser por restricción de llave foránea (si hay habitaciones usando este tipo)
        if ($e->getCode() == '23000') {
            $error = "No puedes eliminar este tipo porque hay habitaciones o reservas asociadas a él.";
        } else {
            $error = "Error al eliminar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Tipos - FapTour</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header>
        <h1>Gestión de Tipos de Habitación</h1>
    </header>

    <nav class="navbar">
        <a href="usuarios.php">Usuarios</a>
        <a href="reportes.php">Reportes</a>
        <a href="admin_tipos.php">Tipos de Habitaciones</a>
        <a href="admin_habitaciones.php">Habitaciones</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <section class="contenedor">

        <?php if(isset($mensaje)) echo "<div style='color:green; padding:10px; border:1px solid green; margin-bottom:10px;'>$mensaje</div>"; ?>
        <?php if(isset($error)) echo "<div style='color:red; padding:10px; border:1px solid red; margin-bottom:10px;'>$error</div>"; ?>

        <div style="background:#f9f9f9; padding:20px; border-radius:8px; margin-bottom:30px;">
            <h3>➕ Agregar Nuevo Tipo</h3>
            <form method="POST" action="admin_tipos.php" style="display:flex; gap:10px; align-items:flex-end;">
                <input type="hidden" name="accion" value="crear">
                
                <div>
                    <label>Nombre del Tipo:</label><br>
                    <input type="text" name="nombre" placeholder="Ej: Suite Presidencial" required>
                </div>
                
                <div>
                    <label>Precio Base ($):</label><br>
                    <input type="number" step="0.01" name="precio" placeholder="0.00" required>
                </div>

                <button type="submit" style="height:40px;">Guardar Tipo</button>
            </form>
        </div>

        <h3>📋 Tipos Existentes</h3>
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio Base</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM tipos_habitacion ORDER BY id ASC");
                while ($row = $stmt->fetch()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nombre']}</td>
                            <td>$" . number_format($row['precio_base'], 2) . "</td>
                            <td>
                                <a href='editar_tipo.php?id={$row['id']}' class='btn-accion btn-editar'>Editar</a>
                                <a href='admin_tipos.php?eliminar={$row['id']}' 
                                   class='btn-accion btn-cancelar'
                                   onclick=\"return confirm('¿Estás seguro?');\">Eliminar</a>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>

    </section>
</body>
</html>