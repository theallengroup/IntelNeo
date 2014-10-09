<?php
session_start();
$id=$_POST['id'];
$Valores=array();
$Datos=$_SESSION['id'];
foreach($Datos as $res)
{
	if($res!=$id)
	{
		$Valores[]=$res;
	}
}
unset($_SESSION['id']);
$_SESSION['id']=$Valores;
foreach($Valores as $val)
{
echo $val; 
exit();
}
?>