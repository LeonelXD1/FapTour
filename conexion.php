<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "127.0.0.1";   // o localhost
$bd   = "fap_tour";
$user = "root";        // usuario de XAMPP
$pass = "";            // contraseña de XAMPP (vacía)

// OPCIONES SEGURAS PARA PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Mostrar errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Datos como arreglo asociativo
    PDO::ATTR_EMULATE_PREPARES => false, // Seguridad en consultas preparadas
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$bd;charset=utf8", $user, $pass, $options);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}
?>
