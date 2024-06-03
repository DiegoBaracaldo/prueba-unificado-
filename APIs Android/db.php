<?php

   $mysql = new mysqli(
    "localhost",
    "root",
    "",
    "dmario_jeans_db"
   );
   if ($mysql->connect_error){
     die("fallo la conexion:" . $mysql->connect_error);
   }