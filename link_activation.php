<?php

  //echo $_GET["link"];

  try {

    $base = new PDO('mysql:host=localhost; dbname=pruebas', 'root', '');

    $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $base->exec("SET CHARACTER SET utf8");


    // Procedemos a activar al usuario

    $sql_activation = $base->prepare("UPDATE usuarios SET state = :status WHERE codigo_activation = :link_activation");

    $status = 'Enabled';

    $link_activation = $_GET["link"];

    $sql_activation->bindParam(":status", $status);
    $sql_activation->bindParam(":link_activation", $link_activation);

    $sql_activation->execute();

    echo "CUENTA ACTIVADA";

  } catch (\Exception $e) {

  }






?>
