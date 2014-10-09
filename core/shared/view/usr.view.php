<?php
global $std_views;
$std_views["usr"]=array(

	'edit_profile'=>array(
		'type'=>'record',
		'kids'=>'NOT_THE_MODEL',
		
		'actions'=>array('all_edit2','edit_profile_change_password'),
		'title'=>'edit_profile',
		'fields'=>array(
			'id'=>array('name'=>'id','type'=>'hidden'),
			'name'=>array('name'=>'name','type'=>'text'),
			'email'=>array('name'=>'email','type'=>'text'),
	//		'password'=>array('name'=>'password','type'=>'hidden'),
			'login_count'=>array('name'=>'login_count','type'=>'label'),
			'last_login'=>array('name'=>'last_login','type'=>'label'),
			'created_date'=>array('name'=>'created_date','type'=>'label'),
			'last_ip'=>array('name'=>'last_ip','type'=>'label'),
		),
	),


	'edit_all'=>array(
		'kids'=>'model',
		'type'=>'record',
		'actions'=>array('all_b2l','all_edit2','all_delete','all_impersonate','all_admin_change_password'),
		'title'=>'edit_table_title',
		'fields'=>array(
			'id'=>array('name'=>'id','type'=>'hidden'),
			'name'=>array('name'=>'name','type'=>'text'),
			'email'=>array('name'=>'email','type'=>'text'),
	//		'password'=>array('name'=>'password','type'=>'hidden'),
			'login_count'=>array('name'=>'login_count','type'=>'label'),
			'last_login'=>array('name'=>'last_login','type'=>'label'),
			'created_date'=>array('name'=>'created_date','type'=>'label'),
			'last_ip'=>array('name'=>'last_ip','type'=>'label'),
		),
	),
	'new_user'=>array(
		'type'=>'record',
		'kids'=>'model',
		'if_not_exists'=>'create',
		'actions'=>array('all_b2l','all_new2'),
		'title'=>'new_table_title',
		'fields'=>array(
			'id'=>array('name'=>'id','type'=>'hidden'),
			'name'=>array('name'=>'name','type'=>'text'),
			'email'=>array('name'=>'email','type'=>'text'),
			'password'=>array('name'=>'password','type'=>'password'),
			'login_count'=>array('name'=>'login_count','type'=>'hidden','value'=>0),
			'last_login'=>array('name'=>'last_login','type'=>'hidden','value'=>'1970-01-01 00:00:00'),
			'created_date'=>array('name'=>'created_date','type'=>'hidden','value'=>date('Y-m-d H:i:s')),
			'last_ip'=>array('name'=>'last_ip','type'=>'hidden','value'=>'0.0.0.0'),
			),
	),
	'register'=>array(
		'type'=>'record',
		'if_not_exists'=>'create',
		'actions'=>array('b2l','register_ok'),
		'title'=>'register',
		'fields'=>array(
			'id'=>array('name'=>'id','type'=>'hidden'),
			'name'=>array('name'=>'name','type'=>'text'),
			'email'=>array('name'=>'email','type'=>'text'),
			'password'=>array('name'=>'password','type'=>'password'),
			),
	),
	
	'list_all'=>array(
		'type'=>'table',
		'side_actions'=>array('all_edit','all_delete'),
		'down_actions'=>array('all_delete_selected','all_xls',
//		'all_new'
		array('action'=>'all_add_user','label'=>'all_new')
	),
		'title'=>'table_plural',
		'fields'=>array(
			'id'=>array('name'=>'id'),
			'name'=>array('name'=>'name'),
			'email'=>array('name'=>'email'),
			'login_count'=>array('name'=>'login_count','default_display'=>0),
			'last_login'=>array('name'=>'last_login','type'=>'date'),
			'created_date'=>array('name'=>'created_date','type'=>'date','default_display'=>0),
			'last_ip'=>array('name'=>'last_ip'),
		)
	),
	'list_readonly'=>array(
		'type'=>'table',
		'side_actions'=>array('view:edit_readonly'),
		'title'=>'table_plural',
		'fields'=>array(
			'id'=>array('name'=>'id'),
			'name'=>array('name'=>'name'),
			)
		),
	);

?>
