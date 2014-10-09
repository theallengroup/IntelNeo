<?php

class main{
	var $status='my initial status';
	var $name='my name';
	function testme(){
		echo('testme()<br/>');
	}
	function load($mod){
		
	}
	function nice($p1,$p2,$p3){
		return($this->gc('nice',array($p1,$p2,$p3)));
	}
	function nope(){return($this->gc('nope'));}
	function gc($fn,$args=array()){
		//to IF exists.
		$fn='std_'.$fn;
		$fn($this,$args);
	}

	function load_external(){
		$modules = func_get_args();
		//print_r($modules);
		foreach($modules as $k=>$v){
			include_once("module.$v.php");
			//todo log loaded modules
		}
	}
	function go(){
		$this->load_external('nice');
		echo('<br>name is now:'.$this->name);
		$this->nice(9,8,7);
		$this->nope();
		$this->nope();
		$this->nope();
		$this->nope();
	}
}

$m=new main();
$m->go();
?>
