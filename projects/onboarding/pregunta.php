<?php
	include_once './BaseDatos/conexion.php';

	function clean_param($param) {
		return str_replace(array('<','>',"'",";","&","\\"),'', $param);
	}

	$id = clean_param($_GET['activity_id']);
	$value = clean_param($_GET['value']);

	$con = new conexion();
	$sql = 'SELECT
	  `question`.`id` as question_id,
	  `question`.`name` as question,
	  `question_option`.`id` as option_id,
	  `question_option`.`name` AS answer,
	  `question_option`.`is_correct`
	FROM
	  `question`
	  INNER JOIN `question_option` ON (`question`.`id` = `question_option`.`question_id`)
	WHERE
	  `question`.`activity_id` = "'.$id.'"
	ORDER BY question.id';

	$Datos = $con->TablaDatos($sql);
	$LetrasABC = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

	$Teclas = array();
	foreach( $Datos as $row ) {
		$letters = array_unique(str_split(strtoupper(str_replace(' ', '', trim($row['answer'])))));

		$tiles = array();
		foreach($letters as $dat) {
			$tiles[] = array($dat, true);
		}

		$count_total = count($letters);

		//echo "before ($count_total): " . print_r($letters, true);
		// NEO-159 - we want answer + 2 characters only
		while( count($letters) < ($count_total + 2) ) {
			$x = rand(1,25);
			$letra = $LetrasABC[$x];
			//echo "checking $letra at " . count($letters) . "\n";
			if(!in_array($letra, $letters)) {
				//echo "adding $letra\n";
				$letters[] = ($letra);
				$tiles[] = array($letra, false);
			}
		}
		//echo "after " . count($letters) . ": " . print_r($letters, true);

		shuffle($tiles);

		//echo "tiles: " . print_r($tiles, true);
		$Teclas[$row['question_id']] = $tiles;
	}
	//exit;
?>

<!--CONTENT_START-->

<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="./media/js/answerspinquis.js"></script>

<script type="text/javascript">
	question_count = parseInt('[#activity_count]');
	answers = {};
	tiles = [];
<?php
	foreach($Teclas as $id => $letters) {
		echo "	tiles[$id] = [";
		$js_array = array();
		shuffle($letters);
		foreach($letters as $letter) {
			$js_array[] = "['" . $letter[0] . "', '" . $letter[1] . "']";
		}
		echo join(',', $js_array) . "]\n";
	}
?>
</script>

<div class="principal_container spinquiz_question">

	<div class="content container">
		[#navigation]
		<form method=GET  action="?" id='spin' class="activity_count_[#activity_count]">
			<input type="hidden" id="score_data" value="" data-maxScore="[#activity_value]" data-countQuestions="[#activity_count]" />

			<input type=hidden name=ac value=r_run_activity />
			<input type=hidden name=pf value='1' />
			<input type=hidden name=team_id value='[#team_id]' />
			<input type=hidden name=activity_id value='[#activity_id]' />
			<input type=hidden name=mod value=usr2session />
			<input type=hidden name=value value=<?php echo $value; ?> />

			<!--LOOP_START-->
			<div class="content_shuffle row activity_count_[#activity_count]"  id='section_[#current]' style='display:[#display]' data-questionId='[#id]'>
				[#options]

				<div id="pregunta" class="row">
					<div class="center-block">
						<p>[#name]</p>
					</div>
				</div>
				<div id="letras-wrap" class="row">
					<div id="topic_bar" class="center-block">
						<p>[#category]</p>
					</div>
					<div id="letras">
						<div id ="letras_container"></div>
					</div>
				</div>

				<div id="teclas-wrap" class="row">
					<div id="teclas">
						<div id="botones">
							<ul class="list-inline">
								<li id="boton_x"><input type="button" value="X" onclick="javascript:borrar()"></li>
								<li id="boton_a"><input type="button" value="A" onclick="javascript:eliminar()"></li>
								<li id="boton_saltar"><input type="button" value="next" onclick="javascript:siguiente()"></li>
							</ul>
						</div>
						<div id="teclas_container" class="center-block col-xs-12 col-md-6">

						</div>
					</div>
				</div>
			</div>
			<!--LOOP_END-->
		</form>

<?php
#No tocar----
#===========================================================================================================================#
?>

		<input class="btn-submit" id="spin_btn" type="submit" value="SUBMIT" onclick="return show_next_section();">
	</div>
</div>
<div id='log'></div>

<!--LOOP_START-->
<!--LOOP_END-->

<!--CONTENT_END-->
