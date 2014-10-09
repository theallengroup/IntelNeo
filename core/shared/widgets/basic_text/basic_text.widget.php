<?php
/** 
 * relies on a valid fs
 * */
class basic_text_widget extends default_widget {
	function user_interface(){
		$f=new form();
		return($f->input_text($this->get_fs()));
	}
	function basic_text_widget(){
		$this->default_widget();
	}
}
?>
