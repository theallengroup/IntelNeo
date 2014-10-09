<?php

	class leaderboard_model extends onboarding_base{
		function leaderboard_model(){
			$this->onboarding_base();
		}
		function ac_le_(){
			$this->menu();
			if(isset($_GET["blink"])){
				$action=$this->remove_strange_chars($_GET["blink"]);	
				if ($action == '1'){
					$this->ac_le_shuffle_quiz();
				}elseif ($action == '2'){
					$this->ac_le_wager_quiz();
				}elseif ($action == '3'){
					$this->ac_le_spin_quiz();
				}elseif ($action == '4'){
					$this->ac_le_poll_survey();
				}elseif ($action == '5'){
					$this->ac_le_acronym();
				}elseif ($action == '6'){
					$this->ac_le_scanvenger();
				}else{
					echo('error');
				}	
			}else{
				$this->ac_le_shuffle_quiz();
			}
		}
		function ac_le_shuffle_quiz(){
			
			#data para pruebas con formato de sql generado
			$results = $this->q2obj("select 'a' as name, 'correct' as status");
			#conteo de respuestas correctas o incorrectas y operacion para pasar a pixeles
			#el alt del cajon  es de 200px.
			$correct_answers = '6' * 20;
			$incorrect_answers = '3' * 20;
			#meter en results los valores
			echo($this->s_template("shuffle_quiz_results", $results));
		}
		function ac_le_wager_quiz(){
			$results = $this->q2obj("select 'a' as name, 'correct' as status");
			echo($this->s_template("wager_results_backend", $results));
		}
		function ac_le_spin_quiz(){
			$results = $this->q2obj("select 'a' as name, 'correct' as status");
			echo($this->s_template("spin_results_backend", $results));
		}
		function ac_le_poll_survey(){
			$results = $this->q2obj("select 'a' as name, 'correct' as status");
			echo($this->s_template("poll_results_backend", $results));	
		}
		function ac_le_acronym(){
			$results = $this->q2obj("select 'a' as name, 'correct' as status");
			echo($this->s_template("acronym_results_backend", $results));
		}
		function ac_le_scanvenger(){
			echo($this->s_template("scavenger_results_backend", $results));
		}
	}
?>
