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
        <h1>FapTour - Registro de Reservas</h1>
    </header>

    <nav class="navbar">
        <a href="usuarios.php">Usuarios</a>
        <a href="reservas.php">Reservas</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <section class="contenedor">

        <h2>Registrar Nueva Reserva</h2>

        <form class="formulario" action="registrar_reserva.php" method="POST">

            <label>ID Cliente:</label>
            <input type="number" name="cliente_id" required>

            <label>Número de Habitación:</label>
            <input type="number" name="numero_habitacion" required>

            <label>Fecha:</label>
            <input type="date" name="fecha" required>

            <button type="submit">Registrar Reserva</button>

        </form>



        <h2>Lista de Reservas</h2>

        <table class="tabla">
                <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Número Habitación</th>
                <th>Fecha</th>
            </tr>

            <?php
            require "conexion.php";

            $sql = "SELECT r.id, c.nombre_completo, r.numero_habitacion, r.fecha
                    FROM reservas r
                    INNER JOIN clientes c ON r.cliente_id = c.id";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>".$row['id']."</td>
                            <td>".$row['nombre_completo']."</td>
                            <td>".$row['numero_habitacion']."</td>
                            <td>".$row['fecha']."</td>
                        </tr>";
                }
            }

            $conn->close();
            ?>
        </table>

    </section>

    <script>
        function confirmarRegistroReserva(event) {
            event.preventDefault();
            
            const form = event.target;
            const usuarioSelect = form.usuario_id;
            const numeroHabitacion = form.numero_habitacion.value.trim();
            const fecha = form.fecha.value;
            
            if (!usuarioSelect.value || !numeroHabitacion || !fecha) {
                alert('Por favor, completa todos los campos');
                return false;
            }
            
            const usuarioTexto = usuarioSelect.options[usuarioSelect.selectedIndex].text;
            const mensaje = `¿Confirmar registro de la reserva?\n\nCliente: ${usuarioTexto}\nNúmero de Habitación: ${numeroHabitacion}\nFecha: ${fecha}`;
            
            if (confirm(mensaje)) {
                form.submit();
            }
            
            return false;
        }
    </script>
</body>
</html>
