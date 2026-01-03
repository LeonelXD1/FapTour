<?php
session_start();
require "conexion.php";

// 1. Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["rol"])) {
    // Si no está logueado, lo mandamos al login
    header("Location: login.php");
    exit;
}

// 2. DEFINIR QUIÉNES PUEDEN ENTRAR
// Aquí agregamos 'empleado' (o como se llame el rol en tu base de datos: 'recepcionista', 'user', etc.)
$roles_permitidos = ['admin', 'empleado']; 

if (!in_array($_SESSION["rol"], $roles_permitidos)) {
    // Si su rol NO está en la lista de permitidos, se detiene.
    die("Acceso denegado: No tienes permisos para cambiar estados.");
}

// 3. El resto de tu lógica sigue igual...
if (isset($_GET['id']) && isset($_GET['estado'])) {
    $id = $_GET['id'];
    $estado = $_GET['estado'];

    try {
        $stmt = $pdo->prepare("UPDATE reservas SET estado = :estado WHERE id = :id");
        $stmt->execute([':estado' => $estado, ':id' => $id]);
        
        // Redirige a la página anterior (para que no importe si vienes de reportes o de inicio)
        if(isset($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            header("Location: reportes.php?estado=todas");
        }
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>