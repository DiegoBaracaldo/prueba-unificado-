<?php
include '../cors.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once("../db.php");

    $query = $mysql->query("SELECT * FROM diseño");

    $result = [];
    while ($row = $query->fetch_assoc()) {
        $result[] = $row;
    }

    echo json_encode($result);

    $query->close();
    $mysql->close();
}
?>
