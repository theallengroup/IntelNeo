<?php
require_once(dirname(__FILE__).'/../or/or.widget.php');

class xor_widget extends or_widget {
	var $default='';
	function set_default($default){
		$found=0;
		$l=array();

		foreach($this->fields as $op){
			$l[]=$op->get_name();
			//echo("<br>".$op->get_name() .'=='. $default);
			if($op->get_name() == $default){
				$found=1;
				break;
			}
		}
		if($found == 0){
			die('invalid field: <b>'.$default.'</b>, not in list:'.implode(',',$l));
		}
		$this->default=$default;
	}
	function get_default(){
		return($this->default);
	}
	function xor_widget(){
		$this->or_widget();
	}
}
?>
