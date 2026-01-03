<?php
session_start(); // 1. IMPORTANTE: Iniciar sesión siempre al principio
require "conexion.php"; 

$usuario = $_POST['usuario'];
$correo  = $_POST['correo'];
$clave   = password_hash($_POST['clave'], PASSWORD_BCRYPT);
$rol     = $_POST['rol'];

try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, clave, rol, correo)
                           VALUES (:usuario, :clave, :rol, :correo)");
    
    $stmt->execute([
        ':usuario' => $usuario,
        ':clave'   => $clave,
        ':rol'     => $rol,
        ':correo'  => $correo
    ]);

    // 2. CONFIGURACIÓN DEL MENSAJE DE ÉXITO
    $_SESSION['mensaje'] = "Usuario registrado correctamente";
    $_SESSION['tipo_mensaje'] = "success"; // 'success' suele ser color verde en CSS/Bootstrap

    // 3. REDIRECCIÓN
    header("Location: usuarios.php");
    exit(); // Detiene el script inmediatamente para evitar errores

} catch (PDOException $e) {
    // También capturamos el error en una sesión para mostrarlo en rojo
    $_SESSION['mensaje'] = "Error al registrar: " . $e->getMessage();
    $_SESSION['tipo_mensaje'] = "danger"; // 'danger' suele ser color rojo
    header("Location: usuarios.php");
    exit();
}
?>