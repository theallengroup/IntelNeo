<?php


class main{
	var $name='my name';
	function testme(){
		echo('testme()<br/>');
	}
	function load($mod){
		include_once("php.test1.php");
	}
	function go(){
		$wow=create_function('&$me','return($me->name=\'name changed\');');
		echo($wow($this));
		echo('<br>name is now:'.$this->name);
	}
}

$m=new main();
$m->go();
?>
