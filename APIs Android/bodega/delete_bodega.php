<?php
include '../cors.php';

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    require_once("../db.php");

    // Validación de entrada
    $codigo = isset($_GET['codigo']) ? $_GET['codigo'] : null;

    if (empty($codigo)) {
        echo json_encode(["error" => "Código no proporcionado"]);
        http_response_code(400);
        exit();
    }

    // Validar que el código sea un número entero positivo
    if (!filter_var($codigo, FILTER_VALIDATE_INT) || $codigo <= 0) {
        echo json_encode(["error" => "Código inválido"]);
        http_response_code(400);
        exit();
    }

    // Usar una declaración preparada para evitar inyecciones SQL
    $query = $mysql->prepare("DELETE FROM bodega WHERE codigo = ?");
    $query->bind_param("i", $codigo);

    if ($query->execute()) {
        if ($query->affected_rows > 0) {
            echo json_encode(["success" => "El código fue removido exitosamente"]);
        } else {
            echo json_encode(["error" => "Código no encontrado en bodega"]);
            http_response_code(404);
        }
    } else {
        echo json_encode(["error" => "Error al eliminar el código en bodega"]);
        http_response_code(500);
    }

    // Cierra la declaración y la conexión
    $query->close();
    $mysql->close();
}
?>
