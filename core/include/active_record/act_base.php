<?php

class act_base{
	var $name='@none';
	function act_base($module_name){
		$this->name=$module_name;
	}
	function find(){
		echo('find!');
	}

}
?>
