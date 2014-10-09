<?php
/**
 These are the shared views, they apply to ALL apps, so be careful, 
 if you want YOUR app to override globally , any of these settings, please use your index.php, 
 or your <appname>_base.php (recomemded)

 I am DETERMINED not to write special case codes, for such trivialities like "new", "edit" etc,
 nor they should be replicated in EVERY module, that's just stupid, if someone, WANTS them to be 
 different, then so be it, but as far as I'm concerned, this is a much better approach, 
 it remains to be seen HOW we make special views (perhaps an "add view" button,
 that takes, table, selects fields, sets a title, and finally sets actions for it?),
 it can be of type VIEW , RECORD, or another one set by you.
 (check the vh_* functions)

 You should add new fields in ONLY ONE PLACE: ./model/<your table>.model.php

 */
global $std_views;
$std_views["std"]=array(

	'edit_all'=>array(
		'kids'=>'model',
		'type'=>'record',
		'actions'=>array('all_b2l','all_edit2','all_delete'),
		'title'=>'edit_table_title',
		'fields'=>'all',
	),
	'show_all'=>array(
		'kids'=>'model',
		'type'=>'show',
		'actions'=>array(),
		'title'=>'table_title',
		'fields'=>'all',
	),
	'simple_all'=>array(
		'kids'=>'model',
		'type'=>'list_show',
		'actions'=>array(),
		'title'=>'table_title',
		'fields'=>'all',
	),
	'new'=>array(	
		'kids'=>'model',
		'type'=>'record',
		'if_not_exists'=>'create',
		'actions'=>array('all_b2l','all_new2'),
		'title'=>'new_table_title',
		'fields'=>'all',
	),
	'list_all'=>array(
		'current_action'=>'all',
		'help'=>'list_all',
		'title'=>'table_plural',
		'type'=>'table',
		'side_actions'=>array('all_edit','all_delete'),
		'down_actions'=>array('all_delete_selected','all_xls','all_new'),
		'fields'=>'all',
	),
	'grid_all'=>array(
		'title'=>'table_plural',
		'type'=>'grid',
		'side_actions'=>array('delete'),
		'down_actions'=>array('save_grid_changes','delete_selected','xls','view:new'),
		'fields'=>'all',
	),
	'list_read_only'=>array(
		'title'=>'table_plural',
		'type'=>'table',
		'side_actions'=>array('read_only_view'),
		'fields'=>'all',
	),
	'read_only_view'=>array(
		'title'=>'view_table_title',
		'type'=>'record',
		'actions'=>array('read_only'),
		'fields'=>'all',
	),

);

?>
