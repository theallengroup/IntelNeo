<?php
/** 
 * relies on a valid fs
 * */
class basic_date_widget extends default_widget {
	function user_interface(){
		$f=new form();
		return($f->input_date($this->get_fs()));
	}
	function basic_date_widget(){
		$this->default_widget();
	}
}
?>
