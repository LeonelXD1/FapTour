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
        <a href="clientes.php">Empleados</a>
        <a href="usuarios.php">Clientes</a>
        <a href="reservas.php">Reservas</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <section class="contenedor">

        <h2>Registrar Nuevo Empleado</h2>


        <form class="formulario" action="registrar_cliente.php" method="POST">

            <label>Nombre Completo:</label>
            <input type="text" name="nombre_completo" required>

            <button type="submit">Registrar Cliente</button>

        </form>


        <h2>Administrador existente</h2>
        <p>El administrador principal ya está creado y no se puede registrar otro desde este formulario.</p>

        <table class="tabla">
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
            </tr>

            <?php
            require "conexion.php";

            $sql = "SELECT id, nombre_completo FROM clientes";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>".$row['id']."</td>
                            <td>".$row['nombre_completo']."</td>
                        </tr>";
                }
            }

            $conn->close();
            ?>
        </table>

    </section>

    <script>
        function confirmarRegistroEmpleado(event) {
            event.preventDefault();
            
            const form = event.target;
            const nombreCompleto = form.nombre_completo.value.trim();
            const rol = form.rol.value;

            if (!nombreCompleto || !rol) {
                alert('Por favor, completa todos los campos');
                return false;
            }

            const mensaje = `¿Confirmar registro del empleado?\n\nNombre: ${nombreCompleto}\nRol: ${rol}`;

            if (confirm(mensaje)) {
                form.submit();
            }

            return false;
        }
    </script>

</body>
</html>
