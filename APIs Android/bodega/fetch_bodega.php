<?php

include '../cors.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' || $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trae la conexión a la base de datos
    require_once("../db.php");

    // Usa una declaración preparada para evitar inyecciones SQL
    $query = $mysql->prepare("SELECT * FROM bodega");

    // Ejecuta la consulta
    $query->execute();
    $result = $query->get_result();

    // Verifica si se encontraron resultados
    if ($result->num_rows > 0) {
        $array = [];
        while ($row = $result->fetch_assoc()) {
            $array[] = $row;
        }
        echo json_encode($array);
    } else {
        echo json_encode(["error" => "No se encontraron registros en la bodega"]);
    }

    // Cierra el resultado y la conexión
    $result->close();
    $mysql->close();
}
?>
