<?php
require "conexion.php"; // define $pdo
// Control de acceso por rol
if ($_SESSION["rol"] !== "admin") {
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
        <h1>FapTour - Registro de Empleados</h1>
    </header>

    <nav class="navbar">
        <a href="reservas_admin.php">Reservas</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <section class="contenedor">

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

    <script>
        function confirmarRegistroUsuario(event) {
            event.preventDefault();
            
            const form = event.target;
            const usuario = form.usuario.value.trim();
            const correo = form.correo.value.trim();
            const clave = form.clave.value.trim();
            const rol = form.rol.value;

            if (!usuario || !correo || !clave || !rol) {
                alert('Por favor, completa todos los campos');
                return false;
            }

            const mensaje = `¿Confirmar registro del usuario?\n\nUsuario: ${usuario}\nCorreo: ${correo}\nRol: ${rol}`;

            if (confirm(mensaje)) {
                form.submit();
            }

            return false;
        }
    </script>

</body>
</html>
