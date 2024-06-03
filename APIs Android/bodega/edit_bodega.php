<?php
  include '../cors.php';


if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

    require_once("../db.php");

    // Obtener datos JSON en el cuerpo de la solicitud
    $data = json_decode(file_get_contents('php://input'), true);

    // Validación de entrada
    $codigo = isset($_GET['codigo']) ? $_GET['codigo'] : null; // El código es obligatorio para identificar la bodega a actualizar

    if (!$codigo) {
        echo json_encode(["error" => "Código no proporcionado"]);
        http_response_code(400);
        exit();
    }

    // Comprobar si la bodega existe antes de intentar actualizar
    $check_query = $mysql->prepare("SELECT * FROM Bodega WHERE codigo = ?");
    $check_query->bind_param("s", $codigo);
    $check_query->execute();
    $check_result = $check_query->get_result();

    if ($check_result->num_rows == 0) {
        echo json_encode(["error" => "Bodega no encontrada"]);
        http_response_code(404);
        exit();
    }

    // Definir variables para los campos opcionales
    $id_diseño = isset($data['id_diseño']) ? $data['id_diseño'] : null;
    $dia_exacto = isset($data['dia_exacto']) ? $data['dia_exacto'] : null;
    $tipo_movimiento = isset($data['tipo_movimiento']) ? $data['tipo_movimiento'] : null;
    $cant = isset($data['cant']) ? $data['cant'] : null;

    // Sanitización de entradas
    if ($id_diseño !== null) {
        $id_diseño = filter_var($id_diseño, FILTER_SANITIZE_STRING);
    }
    if ($dia_exacto !== null) {
        $dia_exacto = filter_var($dia_exacto, FILTER_SANITIZE_STRING);
    }
    if ($tipo_movimiento !== null) {
        $tipo_movimiento = filter_var($tipo_movimiento, FILTER_SANITIZE_STRING);
    }
    if ($cant !== null) {
        $cant = filter_var($cant, FILTER_VALIDATE_INT);
    }

    // Construir la consulta de actualización
    $update_query = "UPDATE Bodega SET ";
    $params = array();
    $types = '';

    if ($id_diseño !== null) {
        $update_query .= "id_diseño = ?, ";
        $params[] = &$id_diseño;
        $types .= 's';
    }
    if ($dia_exacto !== null) {
        $update_query .= "dia_exacto = ?, ";
        $params[] = &$dia_exacto;
        $types .= 's';
    }
    if ($tipo_movimiento !== null) {
        $update_query .= "tipo_movimiento = ?, ";
        $params[] = &$tipo_movimiento;
        $types .= 's';
    }
    if ($cant !== null) {
        $update_query .= "cant = ?, ";
        $params[] = &$cant;
        $types .= 'i';
    }

    // Eliminar la coma adicional y el espacio al final de la consulta de actualización
    $update_query = rtrim($update_query, ', ');

    // Agregar la condición WHERE con el código
    $update_query .= " WHERE codigo = ?";
    $types .= 's';
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
            echo json_encode(["success" => "Bodega actualizada"]);
        } else {
            echo json_encode(["error" => "No se realizaron cambios en la bodega"]);
            http_response_code(200); // Aunque no se realicen cambios, la solicitud se procesó correctamente
        }
    } else {
        echo json_encode(["error" => "Error al actualizar la bodega: " . $stmt->error]);
        http_response_code(500);
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $mysql->close();
}
?>
