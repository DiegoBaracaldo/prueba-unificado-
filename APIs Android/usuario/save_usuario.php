<?php

include '../cors.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once("../db.php"); // conexion a la base de datos 

    // Obtener datos JSON del cuerpo de la solicitud
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['nombre'], $data['identificacion'], $data['cargo'], $data['telefono'], $data['contraseña'])) {
        $nombre = $data['nombre'];
        $identificacion = $data['identificacion'];
        $cargo = $data['cargo'];
        $telefono = $data['telefono'];
        $contraseña = $data['contraseña'];

        // Uso de declaraciones preparadas para prevenir inyecciones SQL
        $stmt = $mysql->prepare("INSERT INTO usuario (nombre, identificacion, cargo, telefono, contraseña) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $identificacion, $cargo, $telefono, $contraseña);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Usuario creado exitosamente"]);
        } else {
            echo json_encode(["message" => "Error al crear usuario: " . $stmt->error]);
        }

        $stmt->close();
        $mysql->close();
    } else {
        echo json_encode(["message" => "Datos incompletos"]);
    }
} else {
    echo json_encode(["message" => "Método no permitido"]);
}
?>
