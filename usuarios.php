<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FapTour - Registro de Clientes</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header>
        <h1>FapTour - Registro de Clientes</h1>
    </header>

    <nav class="navbar">
        <a href="empleados.php">Empleados</a>
        <a href="usuarios.php">Clientes</a>
        <a href="reservas.php">Reservas</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <section class="contenedor">

        <h2>Registrar Nuevo Cliente</h2>

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
                <option value="admin">Admin</option>
            </select>

            <button type="submit">Registrar Usuario</button>

        </form>



        <h2>Lista de Clientes</h2>

        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                </tr>
            </thead>
            <tbody>
                <!-- Registros dinámicos desde BD -->
            </tbody>
        </table>

    </section>

    <script>
        function confirmarRegistroCliente(event) {
            event.preventDefault();
            
            const form = event.target;
                const nombreCompleto = form.nombre_completo.value.trim();
            
                if (!nombreCompleto) {
                    alert('Por favor, completa el nombre');
                    return false;
                }

                const mensaje = `¿Confirmar registro del cliente?\n\nNombre: ${nombreCompleto}`;
            
            if (confirm(mensaje)) {
                form.submit();
            }
            
            return false;
        }
    </script>
</body>
</html>
