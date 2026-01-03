<?php
require "conexion.php"; // define $pdo

// Verificación de sesión segura
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
    <title>FapTour - Gestión de Clientes</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header>
        <h1>Registro de Clientes</h1>
    </header>

    <nav class="navbar">
        <a href="clientes.php">Clientes</a>
        <a href="reservas.php">Reservas</a>
        <a href="calendario.php">Calendario</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>

    <section class="contenedor">

        <?php if(isset($_GET['msg']) && $_GET['msg']=='actualizado'): ?>
            <div style="background:#d4edda; color:#155724; padding:10px; margin-bottom:15px; border-radius:5px;">
                ¡Cliente actualizado correctamente!
            </div>
        <?php endif; ?>

        <h2>Registrar Nuevo Cliente</h2>

        <form class="formulario" action="registrar_cliente.php" method="POST">
            
            <label>Cedula/RUC:</label>
            <input type="text" name="documento" required>

            <label>Nombre Completo:</label>
            <input type="text" name="nombre_completo" required>

            <label>Teléfono:</label>
            <input type="text" name="telefono">

            <label>Correo:</label>
            <input type="email" name="correo">

            <button type="submit">Registrar Cliente</button>

        </form>

        <h2>Directorio de Clientes</h2>

        <table class="tabla">
            <tr>
                <th>ID</th>
                <th>Documento</th>
                <th>Nombre Completo</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Acciones</th> </tr>

            <?php
            try {
                $sql = "SELECT * FROM clientes ORDER BY id DESC";
                $stmt = $pdo->query($sql);
                $clientes = $stmt->fetchAll();

                foreach ($clientes as $row) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['documento']}</td>
                            <td><strong>{$row['nombre_completo']}</strong></td>
                            <td>{$row['telefono']}</td>
                            <td>{$row['correo']}</td>
                            <td>
                                <a href='editar_cliente.php?id={$row['id']}' class='btn-accion btn-editar'>
                                    ✎ Editar
                                </a>
                            </td>
                        </tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='6'>Error: " . $e->getMessage() . "</td></tr>";
            }
            ?>
        </table>

    </section>

</body>
</html>