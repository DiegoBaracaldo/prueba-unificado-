<?php

include '../cors.php';


if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    require_once("../db.php");

    // Validación de entrada
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        echo json_encode(["error" => "ID no proporcionado"]);
        http_response_code(400);
        exit();
    }

    $identificacion = $_GET['id'];

    // Validar que la identificación sea un número (o ajustar según tu esquema de base de datos)
    if (!filter_var($identificacion, FILTER_VALIDATE_INT)) {
        echo json_encode(["error" => "ID inválido"]);
        http_response_code(400);
        exit();
    }

    // Usar una declaración preparada para evitar inyecciones SQL
    $query = $mysql->prepare("DELETE FROM usuario WHERE identificacion = ?");
    $query->bind_param("i", $identificacion);

    if ($query->execute()) {
        if ($query->affected_rows > 0) {
            echo json_encode(["success" => "El usuario fue removido exitosamente"]);
        } else {
            echo json_encode(["error" => "Usuario no encontrado"]);
            http_response_code(404);
        }
    } else {
        echo json_encode(["error" => "Error al eliminar el usuario"]);
        http_response_code(500);
    }

    // Cierra la declaración y la conexión
    $query->close();
    $mysql->close();
}
?>
