<?php
session_start();
require "conexion.php"; 

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "admin") {
    echo "<h2>Acceso denegado. Solo administradores.</h2>";
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
        <h1>Registro de Empleados</h1>
    </header>

    <nav class="navbar">
        <a href="usuarios.php">Usuarios</a>
        <a href="reportes.php">Reportes</a>
        <a href="admin_tipos.php">Tipos de Habitaciones</a>
        <a href="admin_habitaciones.php">Habitaciones</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <section class="contenedor">

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="msg-alerta msg-<?php echo $_SESSION['tipo_mensaje']; ?>">
                <?php 
                    echo $_SESSION['mensaje']; 
                    // Borramos el mensaje para que no salga al recargar
                    unset($_SESSION['mensaje']);
                    unset($_SESSION['tipo_mensaje']);
                ?>
            </div>
        <?php endif; ?>

        <h2>Registrar Nuevo Empleado</h2>

        <form class="formulario" action="registrar_usuario.php" method="POST">

            <label>Usuario:</label>
            <input type="text" name="usuario" required>

            <label>Correo:</label>
            <input type="email" name="correo" required>

            <label>Contraseña:</label>
            <input type="password" name="clave" required>

            <label>Rol:</label>
            <select name="rol">
                <option value="empleado">Empleado</option>
            </select>

            <button type="submit">Registrar Usuario</button>

        </form>

        <h2>Lista de Empleados</h2>

        <table class="tabla">
            <thead>
                <tr>
                    <th>ID Usuario</th>
                    <th>Rol</th>
                    <th>Correo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $sql = "SELECT id, rol, correo FROM usuarios";
                    $stmt = $pdo->query($sql);
                    $usuarios = $stmt->fetchAll();

                    foreach ($usuarios as $user) {
                        echo "<tr>
                                <td>{$user['id']}</td>
                                <td>{$user['rol']}</td>
                                <td>{$user['correo']}</td>
                              </tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='3'>Error: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>

    </section>
</body>
</html>