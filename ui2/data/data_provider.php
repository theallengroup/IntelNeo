<?php

class data_provider{
	function get_name($rec){
		return($rec["name"]);
	}
	function get_description($rec){
		return("This particular rec has:".$rec["amount"]." !");
	}
	function get_icon($id){
		return("<img src='icon.gif' alt='icon' />");
	}
	function get_actions($id){
		$ac=array('kill','help','description','edit','print');
		if($id==1){unset($ac[0]);}
		return($ac);
	}
	function run_action($id,$action){
		echo("Action Executed!:$action on item:$id");
	}
	function all(){
		return(array(
			array('id'=>1,'name'=>'Hi','amount'=>5),
			array('id'=>2,'name'=>'Hello','amount'=>15),
			array('id'=>3,'name'=>'Bye','amount'=>0),
			array('id'=>4,'name'=>'Cya','amount'=>1),
			array('id'=>5,'name'=>'l8r','amount'=>51),
			
			));
	}
}

?>
