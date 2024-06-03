<?php

include '../cors.php';

if ($_SERVER['REQUEST_METHOD']== 'POST'){
     require_once ("../db.php"); //conexion a la base de datos 
     //obtener datos JSON en el cuerpo de la solicitud
     $data = json_decode(file_get_contents('php://input'), true);

     if (isset($data['codigo'], $data['id_diseño'], $data['dia_exacto'],$data['tipo_movimiento'], $data['cant'])){
       
        $codigo= $data['codigo'];
        $id_diseño= $data['id_diseño'];
        $dia_exacto = $data['dia_exacto'];
        $tipo_movimiento = $data['tipo_movimiento'];
        $cant = $data['cant'];

        // Uso de declaraciones preparadas para prevenir inyecciones SQL
        $stmt = $mysql->prepare("INSERT INTO bodega (codigo, id_diseño, dia_exacto, tipo_movimiento, cant) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $codigo, $id_diseño, $dia_exacto, $tipo_movimiento, $cant);

        if ($stmt->execute()) {
            echo json_encode(["message" => "bodega creada exitosamente"]);
        } else {
            echo json_encode(["message" => "Error al crear bodega: " . $stmt->error]);
        }

        $stmt->close();
        $mysql->close();
    } else {
        echo json_encode(["message" => "Datos incompletos"]);
    }
     } else {
        echo json_encode(["message" => "Método no permitido"]);
     }
     
    ?>

