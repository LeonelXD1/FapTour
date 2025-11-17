<?php
require "conexion.php"; // define $pdo

$usuario = $_POST['usuario'];
$correo  = $_POST['correo'];
$clave   = password_hash($_POST['clave'], PASSWORD_BCRYPT);
$rol     = $_POST['rol'];

try {
    // Preparar consulta segura con placeholders
    $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, clave, rol, correo)
                           VALUES (:usuario, :clave, :rol, :correo)");
    
    $stmt->execute([
        ':usuario' => $usuario,
        ':clave'   => $clave,
        ':rol'     => $rol,
        ':correo'  => $correo
    ]);

    echo "<script>alert('Usuario registrado correctamente'); window.location='usuarios.php';</script>";

} catch (PDOException $e) {
    echo "Error al registrar usuario: " . $e->getMessage();
}
?>
