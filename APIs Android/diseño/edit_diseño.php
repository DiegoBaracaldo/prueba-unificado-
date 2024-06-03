<?php

include '../cors.php';

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

    require_once("../db.php");

    // Obtener datos JSON en el cuerpo de la solicitud
    $data = json_decode(file_get_contents('php://input'), true);

    // Validación de entrada
    $codigo = isset($_GET['codigo']) ? $_GET['codigo'] : null; // El código es obligatorio para identificar el diseño a actualizar

    if (!$codigo) {
        echo json_encode(["error" => "Código no proporcionado"]);
        http_response_code(400);
        exit();
    }

    // Comprobar si el diseño existe antes de intentar actualizar
    $check_query = $mysql->prepare("SELECT * FROM Diseño WHERE codigo = ?");
    $check_query->bind_param("i", $codigo);
    $check_query->execute();
    $check_result = $check_query->get_result();

    if ($check_result->num_rows == 0) {
        echo json_encode(["error" => "Diseño no encontrado"]);
        http_response_code(404);
        exit();
    }

    // Definir variables para los campos opcionales
    $nombre_diseño = isset($data['nombre_diseño']) ? $data['nombre_diseño'] : null;
    $descripcion = isset($data['descripcion']) ? $data['descripcion'] : null;
    $archivo = isset($data['archivo']) ? $data['archivo'] : null;

    // Sanitización de entradas
    if ($nombre_diseño !== null) {
        $nombre_diseño = filter_var($nombre_diseño, FILTER_SANITIZE_STRING);
    }
    if ($descripcion !== null) {
        $descripcion = filter_var($descripcion, FILTER_SANITIZE_STRING);
    }
    if ($archivo !== null) {
        $archivo = base64_decode($archivo);
    }

    // Construir la consulta de actualización
    $update_query = "UPDATE Diseño SET ";
    $params = array();
    $types = '';

    if ($nombre_diseño !== null) {
        $update_query .= "nombre_diseño = ?, ";
        $params[] = &$nombre_diseño;
        $types .= 's';
    }
    if ($descripcion !== null) {
        $update_query .= "descripcion = ?, ";
        $params[] = &$descripcion;
        $types .= 's';
    }
    if ($archivo !== null) {
        $update_query .= "archivo = ?, ";
        $params[] = &$archivo;
        $types .= 'b'; // Para el BLOB usamos el tipo 'b'
    }

    // Eliminar la coma adicional y el espacio al final de la consulta de actualización
    $update_query = rtrim($update_query, ', ');

    // Agregar la condición WHERE con el código
    $update_query .= " WHERE codigo = ?";
    $types .= 'i';
    $params[] = &$codigo;

    // Usar una declaración preparada para evitar inyecciones SQL
    $stmt = $mysql->prepare($update_query);
    if ($stmt === false) {
        echo json_encode(["error" => "Error en la consulta de actualización: " . $mysql->error]);
        http_response_code(500);
        exit();
    }

    // Bind los parámetros dinámicos
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    // Ejecutar la consulta de actualización
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => "Diseño actualizado"]);
        } else {
            echo json_encode(["error" => "No se realizaron cambios en el diseño"]);
            http_response_code(200); // Aunque no se realicen cambios, la solicitud se procesó correctamente
        }
    } else {
        echo json_encode(["error" => "Error al actualizar el diseño: " . $stmt->error]);
        http_response_code(500);
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $mysql->close();
}
?>
