<?php

$t1=array();
foreach($t->fields as $op){

	$t1[]=array(
		"<input type=radio name='".$t->get_name()."' value=".$op->get_name()." ". (($op->get_name()==$t->get_default())?'checked':'')." />",
		$op->get_label(),
		$op->user_interface()
	);
}
common::e_table($t1,array('','',''),array('border'=>0));
?>

