<?php
global $std_views;
$std_views["privilege"]=array(
	'list_privilege'=>array(
		'title'=>'table_plural',
		'type'=>'table',
		'side_actions'=>array('edit_privilege','delete'),
		'down_actions'=>array('delete_selected','xls','view:new'),
		'fields'=>'all',
	),
	'edit_privilege'=>array(
		'children'=>array(array('role2priv','role')),
		'type'=>'record',
		'actions'=>array('b2l','edit2','delete'),
		'title'=>'edit_table_title',
		'fields'=>'all',
	),

);
?>
