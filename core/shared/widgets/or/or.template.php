<?php
//p2($t->options);
$t1=array();
foreach($t->options as $op){
	$t1[]=array(
		"<input type=checkbox name=me checked />",
		$op->get_label(),
		$op->user_interface()
	);
}
common::e_table($t1,array('','',''),array('border'=>0));
?>

