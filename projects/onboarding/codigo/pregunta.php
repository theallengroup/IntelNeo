<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Ruleta</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="stylesheet" href="./media/css/bootstrap.css">
        <link rel="stylesheet" href="./media/css/style.css">
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script type="text/javascript" src="./codigo/answerspinquis.js"></script>        
    </head>
    <body>
	<?php session_start(); ?>
		<?php
			include_once './BaseDatos/conexion.php';
			$id = $_GET['id'];
            function cargatiempo($id) 
			{
			if($_GET['asnw_act']=='1')
			{
				$_SESSION['value']=0;
			}
            $con = new conexion();
            $sql = 'SELECT             
             `activity`.`timer` as tiempos
          FROM
            `question_option`
            INNER JOIN `question` ON (`question_option`.`question_id` = `question`.`id`),activity
          WHERE
            activity.id=question.activity_id and `question_option`.`question_id` =' . $id; 				
		
                $Res2 = $con->TablaDatos($sql);
                return ($Res2);
            }						         		
            $Res2 = cargatiempo($id);?>	
<div class="principal_container spinquiz_question">
    <div class="container">
	        <div id="header_score" class="row">
                <ul class="list-inline">
					<li id="total_preguntas"><?php echo $Res2[0][0]?></li>
                    <li id="spin_score"><p><?php echo $_GET['value'] ?></p><img src="./media/img/points_sca.png"></li>
                    <?php echo'<li id="id_total_preguntas" value="'.$_SESSION['count'].'">'.$_GET['asnw_act'].' / '.$_SESSION['count'].'</li>' ?>				 
                </ul>
             </div>        	
<?php
session_start();
$titulo='';
function Values()
{
	include_once './BaseDatos/conexion.php';
	$id = $_GET['id'];
	$con=new conexion();
	$sql='SELECT 
	  `question`.`id`,
	  `question`.`name` AS `answer`,
	  `question_option`.`id`,
	  `question_option`.`name`,
	  `question_option`.`is_correct`
	FROM
	  `question`
	  INNER JOIN `question_option` ON (`question`.`id` = `question_option`.`question_id`)
	WHERE
	  `question`.`id` = "'.$id.'"';
	$Datos = $con->TablaDatos($sql);

	echo '<input type="hidden" id="is_correct" value="'.$Datos[0]['name'].'"/>';
	echo '<input type="hidden" id="id_value" value="'.$_GET['value'].'"/>';
	$var=($Datos[0]['name']);
	$titulo = $Datos[0]['answer'];
	$Valor = $_GET['value'];
	$var = strtoupper($var);
	$titulo=strtoupper($titulo);
	 
	$var = str_split($var);
	shuffle($var);
	$valores=array();
	for($i=0;$i<count($var); $i++)
	{
		if(!in_array($var[$i],$valores))
		{
			$valores[]=$var[$i];
		}
	}
	return array(0=>$valores,1=>$titulo);
}
$id=($_SESSION['onboarding']['usr']['id']);
$var = Values();
echo "\n".'<div id="pregunta" class="row"><div class="center-block"><p>'.$var[1].'</p></div></div>'. "\n";
echo '<div id="letras-wrap" class="row"><div id="topic_bar" class="col-xs-12 col-md-6 center-block"><img src="./media/img/title_game.png"></div><div id="letras"><div id ="letras_container">';
 
echo '<input type="text" id="respuesta1" class="ClassRespuesta"/>'."\n";
echo '<input type="text" id="respuesta2" class="ClassRespuesta"/>'."\n";
echo '<input type="text" id="respuesta3" class="ClassRespuesta"/>'."\n";
echo '<input type="text" id="respuesta4" class="ClassRespuesta"/>'."\n";
echo '<input type="text" id="respuesta5" class="ClassRespuesta"/>'."\n";
echo '<input type="text" id="respuesta6" class="ClassRespuesta"/>'."\n";
echo '<input type="text" id="respuesta7" class="ClassRespuesta"/>'."\n";
echo '<input type="text" id="respuesta8" class="ClassRespuesta"/>'."\n";
echo '<input type="text" id="respuesta9" class="ClassRespuesta"/>'."\n";
echo '<input type="text" id="respuesta10" class="ClassRespuesta"/>'."\n";
echo '<input type="text" id="respuesta11" class="ClassRespuesta"/>'."\n";
echo '<input type="text" id="respuesta12" class="ClassRespuesta"/>'."\n";
echo '<input type="text" id="respuesta13" class="ClassRespuesta"/>'."\n";
echo '<input type="text" id="respuesta14" class="ClassRespuesta"/>'."\n";

echo '</div></div></div>'. "\n ";



echo '<div id="teclas-wrap" class="row"><div id="teclas">'."\n";
echo '<div id="botones"><ul class="list-inline">';
echo '<li id="boton_x"><input type="button" value="X" onclick="javascript:borrar()"></li>'. "\n";
echo '<li id="boton_saltar"><input type="button" value="next" onclick="javascript:siguiente()"></li>'. "\n";
echo '<li id="boton_a"><input type="button" value="A" onclick="javascript:eliminar()"></li>'. "\n";
echo '</ul></div>'. "\n ";

echo '<div id="teclas_container" class="center-block col-xs-12 col-md-6">'. "\n ";

#no tocar, codigo extremadamente raro...
#===========================================================================================================================#
$Teclas=array();
foreach ($var[0] as $dat) 
{
    $Teclas[] = '<input class="true" type="button" value="' . $dat . '" onclick="values_button(\'' . $dat . '\');" />' . "\n";
}
$i=65;
$Tem=$var[0];
$count_total=14-count($Tem);
$LetrasABC=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
for($i=0;count($Tem)<$count_total;$i++)
{
	$x=rand(1,25);
	$letra=$LetrasABC[$x];
	if(!in_array($letra,$Tem))
	{
		$Tem[]=($letra);
	}
}
foreach($Tem as $dat)
{
    $Teclas[] = '<input class="false" type="button" value="' . $dat . '" onclick="values_button(\'' . $dat . '\');" />' . "\n";
}
foreach($Teclas as $dat)
{
	echo $dat;
}
shuffle($Teclas);
echo '</div>'. "\n ";

echo '</div>';
	echo '<input type="hidden" id="id_user" value="'.$id.'"/>';

echo '</div></div>';
#No tocar----
#===========================================================================================================================#
?>
	    </div>
    </div>
<div id='log'></div>
    </body>
</html>