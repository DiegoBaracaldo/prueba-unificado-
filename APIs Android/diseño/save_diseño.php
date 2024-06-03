<?php

include '../cors.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once("../db.php"); // conexión a la base de datos

    // Verificar si se ha subido un archivo
    if (isset($_FILES['archivo']) && isset($_POST['nombre_diseño']) && isset($_POST['codigo']) && isset($_POST['descripcion'])) {
        $nombre_diseño = $_POST['nombre_diseño'];
        $codigo = $_POST['codigo'];
        $descripcion = $_POST['descripcion'];
        
        // Leer el contenido del archivo
        $archivo = file_get_contents($_FILES['archivo']['tmp_name']);

        // Uso de declaraciones preparadas para prevenir inyecciones SQL
        $stmt = $mysql->prepare("INSERT INTO Diseño (nombre_diseño, codigo, descripcion, archivo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $nombre_diseño, $codigo, $descripcion, $archivo);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Diseño creado exitosamente"]);
        } else {
            echo json_encode(["message" => "Error al crear diseño: " . $stmt->error]);
        }

        $stmt->close();
        $mysql->close();
    } else {
        echo json_encode(["message" => "Datos incompletos o archivo no subido"]);
    }
} else {
    echo json_encode(["message" => "Método no permitido"]);
}
?>
