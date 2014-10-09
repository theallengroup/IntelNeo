<?php
    include_once '../BaseDatos/conexion.php';
    $id = str_replace(array('<','>',"'",";","&","\\"),'', $_REQUEST['id']);

    $con = new conexion();
    $sql='SELECT
        `question`.`id`, `question`.`name`
        FROM `activity`
        INNER JOIN `question` ON (`activity`.`id` = `question`.`activity_id`)
        WHERE `activity`.`id` = ' . $id;
    $res = $con->TablaDatos($sql);

    $Dato = array();
    $Value=100;
    $count=count($res);

    session_start();
    $_SESSION['count'] = $count;
    foreach ($res as $Resultado) {
        $Dato[$Resultado['id']] = "$Value";
        $Value = $Value + 100;
    }

    $value = (json_encode($Dato));
    echo $value;
?>