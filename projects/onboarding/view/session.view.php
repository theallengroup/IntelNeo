<?php
global $std_views;
$std_views["session"]=array(
	//change to "new" for editing
	'new_custom'=>array(	
		'kids'=>'model',
		'type'=>'record',
		'if_not_exists'=>'create',
		'actions'=>array('all_b2l','all_new2'),
		'title'=>'new_table_title',
		'fields'=>array(
			'id'=>array('type'=>'label'  ),
			'name'=>array('type'=>'text'  ),
		)		
	),
	//change to "edit_all" for editing
	'edit_custom'=>array(
		'title'=>'edit_table_title',
		'type'=>'record',
		'actions'=>array('all_b2l','all_edit2','all_delete'),
		'fields'=>array(
			'id'=>array('type'=>'label'  ),
			'name'=>array('type'=>'text'  ),
		)		
	),
	//change to "list_all" for editing
	'list_custom'=>array(
		'help'=>'list_all',
		'title'=>'table_plural',
		'type'=>'table',
		'side_actions'=>array('all_edit','all_delete'),
		'down_actions'=>array('all_delete_selected','all_xls','all_new'),
		'fields'=>array(
			'id'=>array(),
			'name'=>array(),
			)
		),
	);

?>
