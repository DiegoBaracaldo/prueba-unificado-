<?php

include '../cors.php';


if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    require_once("../db.php");

    // Validación de entrada
    if (!isset($_GET['codigo']) || empty($_GET['codigo'])) {
        echo json_encode(["error" => "Código no proporcionado"]);
        http_response_code(400);
        exit();
    }

    $codigo = $_GET['codigo'];

    // Validar que la identificación sea un número entero positivo
    if (!filter_var($codigo, FILTER_VALIDATE_INT) || $codigo <= 0) {
        echo json_encode(["error" => "Código inválido"]);
        http_response_code(400);
        exit();
    }

    // Usar una declaración preparada para evitar inyecciones SQL
    $query = $mysql->prepare("DELETE FROM diseño WHERE codigo = ?");
    $query->bind_param("i", $codigo);

    if ($query->execute()) {
        if ($query->affected_rows > 0) {
            echo json_encode(["success" => "El diseño fue eliminado exitosamente"]);
        } else {
            echo json_encode(["error" => "Diseño no encontrado"]);
            http_response_code(404);
        }
    } else {
        echo json_encode(["error" => "Error al eliminar el diseño"]);
        http_response_code(500);
    }

    // Cierra la declaración y la conexión
    $query->close();
    $mysql->close();
}
?>
