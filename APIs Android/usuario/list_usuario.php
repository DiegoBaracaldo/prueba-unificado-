<?php

include '../cors.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once("../db.php");

    $query = "SELECT nombre, identificacion, telefono FROM usuario"; // Ajusta el nombre de la tabla según tu base de datos
    $result = $mysql->query($query);

    $usuarios = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        echo json_encode($usuarios);
    } else {
        echo json_encode(["error" => "No hay usuarios encontrados"]);
    }

    $result->close();
    $mysql->close();
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>
