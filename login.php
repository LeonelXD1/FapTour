<?php
require_once "conexion.php";

if (isset($_SESSION['usuario'])) {
    // Si ya está logueado NO debe ver el login
    header("Location: usuarios.html"); // O usuarios.html si usas html
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"]);
    $clave   = trim($_POST["clave"]);

    // Buscar usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch();

    if ($user && password_verify($clave, $user["clave"])) {

        // Crear sesión
        $_SESSION["usuario"] = $user["usuario"];
        $_SESSION["rol"]     = $user["rol"];  // guardar rol en sesión

        // Redirigir según rol
        if ($user["rol"] === "admin") {
            header("Location: empleados.html");
        } else if ($user["rol"] === "empleado") {
            header("Location: usuarios.html");
        } else {
            // Rol desconocido → por si acaso
            header("Location: usuarios.html");
        }
        exit;

    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>

<header>
    <h1>Sistema de Gestión</h1>
</header>

<div class="navbar">
    <a href="#">Inicio</a>
    <a href="#">Ayuda</a>
</div>

<div class="contenedor">
    <h2>Iniciar Sesión</h2>

    <form method="POST" class="formulario">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="clave" placeholder="Contraseña" required>
        <button type="submit">Ingresar</button>
    </form>

    <?php if ($error): ?>
        <p style="color:red; font-weight:bold;"><?php echo $error; ?></p>
    <?php endif; ?>
</div>

</body>
</html>
