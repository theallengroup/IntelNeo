<?php
$t1=array();
$t1[]=array(
	$t->wlist->get_label(),
	$t->wlist->user_interface(),
);
foreach($t->fields as $field){
	$t1[]=array(
		$field->get_label(),
		$field->user_interface()
	);
}
common::e_table($t1,array('',''),array('border'=>0));
?>

