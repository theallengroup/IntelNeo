<?php
include_once '../BaseDatos/conexion.php';
$con = new conexion();
$id=$_REQUEST['id_url_'];


$sql='SELECT
  question.id,
  category.name,
  question.name,
  question.category_id
FROM
  question, category
WHERE
  question.category_id = category.id and
  question.activity_id ='.$id;

$Res=$con->TablaDatos($sql);
$Total =  count($Res);

session_start();
$_SESSION['Total']=$Total;

$x =  rand(0, ($Total-1));
$datos[0][0]=$Res[$x][0];
$datos[0][1]=$Res[$x][1];
echo json_encode($datos);
?>