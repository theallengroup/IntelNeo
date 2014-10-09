<?php

class meta_model extends std{
	function ac_menu(){
		$this->menu();
		echo("yeah");
	}
	function meta_model(){
		$this->table='meta';
		$this->std();

	}
	var $default_action='menu';
	var $use_table = 0;	
}
?>
