<?php
	
class simplequery_model extends std{
	function simplequery_model(){
		$this->std();
	}
	function ac_list(){
		$this->menu();
		$this->shadow_start();
		if(!isset($_GET["query"])){
			$a = glob("./queries/*.sql");

			foreach($a as $q){
				$n = basename($q,".sql");
				echo("<br/><a href='?mod=".$this->program_name()."&ac=".$this->current_action."&query=".$n."'>".str_replace("_"," ",ucwords($n))."</a>");
			}
		}else{
			echo("<a href='?mod=".$this->program_name()."&ac=".$this->current_action."'>Back to list</a><br/>");
			$a = explode(";",file_get_contents("./queries/".escapeshellcmd($this->remove_strange_chars($_GET["query"])).".sql"));
			foreach($a as $q){
				$this->e_table($this->q2obj($q),'none',array('title'=>'Query results','border'=>'0','style'=>'list')); 
			}
		}
		$this->shadow_end();
	}

	var $use_table = 0;
}
?>
