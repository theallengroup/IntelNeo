<?php
	/**
	 * okTODO fix output!
	 * */
	#Title: programa edicion de arreglos
	#Author: felipe	
	#Generated Date: 2006-05-29 09:52:26	
	#Description: programa administracion cosas por hacer	
	#Generator Version: manual
	
class diagnose_model extends std{
	function ac_diagnose(){
		$this->menu();
		$ecount = 0;
		foreach($this->get_mods() as $mod){
			$m= $this->load_file($mod);
			
			//seek fields not in model
			$ff=array();
			foreach($this->describe($mod) as $field){
				$ff[$field["Field"]] = $field["Field"];
				if(!isset($m->fields[$field["Field"]])){
					$msg.="<br/>Warning: field not in model:$mod.".$field["Field"];
					$ecount++;
				}
			}
			foreach($m->fields as $fn=>$field){
				if(!isset($ff[$fn])){
					$msg.="<br/>Error: field not in database:$mod.".$fn;
					$ecount++;
				}
			}
		}
		if($ecount>0){
			$this->msg("<h1>Errors</h1><pre style='text-align:left'>".$msg."</pre>");
		}else{
			$this->msg("Everything is OK, it seems.");
		}

	}

	function diagnose_model(){
		$this->table='diagnose';//fix?
		$this->std();
	}
	var $use_table = 0;
	var $default_action='diagnose';
}
?>
