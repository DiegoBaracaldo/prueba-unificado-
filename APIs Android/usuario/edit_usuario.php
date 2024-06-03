<?php

include '../cors.php';

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

    require_once("../db.php");

    // Obtener datos JSON en el cuerpo de la solicitud
    $data = json_decode(file_get_contents('php://input'), true);

    // Validación de entrada
    $identificacion = isset($data['identificacion']) ? $data['identificacion'] : null; // La identificación es obligatoria para identificar el usuario a actualizar

    if (!$identificacion) {
        echo json_encode(["error" => "La identificación de usuario no fue proporcionada"]);
        http_response_code(400);
        exit();
    }

    // Comprobar si el usuario existe antes de intentar actualizar
    $check_query = $mysql->prepare("SELECT * FROM usuario WHERE identificacion = ?");
    $check_query->bind_param("s", $identificacion);
    $check_query->execute();
    $check_result = $check_query->get_result();

    if ($check_result->num_rows == 0) {
        echo json_encode(["error" => "Usuario no encontrado"]);
        http_response_code(404);
        exit();
    }

    // Definir variables para los campos opcionales
    $nombre = isset($data['nombre']) ? $data['nombre'] : null;
    $cargo = isset($data['cargo']) ? $data['cargo'] : null;
    $telefono = isset($data['telefono']) ? $data['telefono'] : null;
    $contraseña = isset($data['contraseña']) ? $data['contraseña'] : null;

    // Sanitización de entradas
    if ($nombre !== null) {
        $nombre = filter_var($nombre, FILTER_SANITIZE_STRING);
    }
    if ($cargo !== null) {
        $cargo = filter_var($cargo, FILTER_SANITIZE_STRING);
    }
    if ($telefono !== null) {
        $telefono = filter_var($telefono, FILTER_SANITIZE_STRING);
    }
    if ($contraseña !== null) {
        $contraseña = password_hash($contraseña, PASSWORD_BCRYPT); // Hasheando la contraseña
    }

    // Construir la consulta de actualización
    $update_query = "UPDATE usuario SET";
    $params = array();
    if ($nombre !== null) {
        $update_query .= " nombre = ?,";
        $params[] = &$nombre;
    }
    if ($cargo !== null) {
        $update_query .= " cargo = ?,";
        $params[] = &$cargo;
    }
    if ($telefono !== null) {
        $update_query .= " telefono = ?,";
        $params[] = &$telefono;
    }
    if ($contraseña !== null) {
        $update_query .= " contraseña = ?,";
        $params[] = &$contraseña;
    }

    // Eliminar la coma adicional al final de la consulta de actualización
    $update_query = rtrim($update_query, ',');

    // Agregar la condición WHERE con la identificación
    $update_query .= " WHERE identificacion = ?";
    $params[] = &$identificacion;

    // Usar una declaración preparada para evitar inyecciones SQL
    $stmt = $mysql->prepare($update_query);
    if ($stmt === false) {
        echo json_encode(["error" => "Error en la consulta de actualización: " . $mysql->error]);
        http_response_code(500);
        exit();
    }

    // Bind los parámetros dinámicos
    if (!empty($params)) {
        $types = str_repeat('s', count($params)); // Todos los parámetros son cadenas
        $stmt->bind_param($types, ...$params);
    }

    // Ejecutar la consulta de actualización
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => "Usuario actualizado"]);
        } else {
            echo json_encode(["error" => "No se realizaron cambios en el usuario"]);
            http_response_code(200); // Aunque no se realicen cambios, la solicitud se procesó correctamente
        }
    } else {
        echo json_encode(["error" => "Error al actualizar el usuario: " . $stmt->error]);
        http_response_code(500);
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $mysql->close();
}
?>
