<?php

include '../cors.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['identificacion']) || empty($input['identificacion'])) {
        echo json_encode(["error" => "Identificación no proporcionada"]);
        exit();
    }

    $identificacion = $input['identificacion'];

    if (!filter_var($identificacion, FILTER_VALIDATE_INT)) {
        echo json_encode(["error" => "Identificación inválida"]);
        exit();
    }

    require_once("../db.php");

    $query = $mysql->prepare("SELECT * FROM usuario WHERE identificacion = ?");
    $query->bind_param("i", $identificacion);

    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $array = $result->fetch_assoc();
        echo json_encode($array);
    } else {
        echo json_encode(["error" => "Usuario no encontrado con identificación $identificacion"]);
    }

    $result->close();
    $mysql->close();
}
?>
