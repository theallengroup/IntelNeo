<?php


include('act_base.php');
class table1 extends act_base{
	var $v1=99;
	function find_by_name($name){
		return($this->find_by('name',$name));
	}
	function find_all_by_name($name){
		return($this->find_all_by('name',$name));
	}
	function find_all_by($field,$value){
		echo('<br/>fnid all:'.$field.' value:'.$value);
	}
	function find_by($field,$value){
		echo('<br/>fnid by:'.$field.' value:'.$value);
	}
	function find($l){
		echo('list:'.print_r($l));
	}
	function main(){
		/*
		$f=create_function('$arg,$me','echo("hello $arg from cf".$me->);');
		$f('test');
//		echo('hi:');
		$this->find(array(12,2,2));
		 */
		$a=8;
		$b=5;
		$c=9;
		$d=compact('a','b','c');
		print_r($d);

		$this->find_all_by_name('nah');
//		$this->find("name='$woa' and pwd='$no'");
	}
}

$t=new table1();
$t->main();

?>
