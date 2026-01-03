<?php
require "conexion.php";
session_start();

// Validar que se recibieron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recibimos los datos (Nota: ahora usamos habitacion_id)
    $cliente_id = $_POST['cliente_id'];
    $habitacion_id = $_POST['habitacion_id'];
    $fecha = $_POST['fecha'];
    
    // Validación básica
    if (empty($cliente_id) || empty($habitacion_id) || empty($fecha)) {
        header("Location: reservas.php?mensaje=error");
        exit;
    }

    try {
        // 1. Verificar si ya existe una reserva ACTIVA para esa habitación en esa fecha
        // (Para evitar doble reserva)
        $sqlCheck = "SELECT COUNT(*) FROM reservas 
                     WHERE habitacion_id = ? 
                     AND fecha = ? 
                     AND estado = 'activa'";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([$habitacion_id, $fecha]);
        
        if ($stmtCheck->fetchColumn() > 0) {
            // Ya está ocupada
            header("Location: reservas.php?mensaje=ocupada");
            exit;
        }

        // 2. Insertar la reserva
        // OJO: Aquí usamos 'habitacion_id', no 'numero_habitacion'
        $sql = "INSERT INTO reservas (cliente_id, habitacion_id, fecha, estado) 
                VALUES (?, ?, ?, 'activa')";
        
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$cliente_id, $habitacion_id, $fecha])) {
            // Éxito
            header("Location: reservas.php?mensaje=registrado");
            exit;
        } else {
            // Fallo en la ejecución SQL
            header("Location: reservas.php?mensaje=error");
            exit;
        }

    } catch (PDOException $e) {
        // Error de conexión o base de datos
        // Si quieres ver el error específico para depurar, puedes descomentar la siguiente línea:
        // die("Error SQL: " . $e->getMessage());
        header("Location: reservas.php?mensaje=error");
        exit;
    }

} else {
    // Si intentan entrar directo sin POST
    header("Location: reservas.php");
    exit;
}
?>