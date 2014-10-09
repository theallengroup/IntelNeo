<?php
global $std_fields;
$std_fields['usr']=array(
		'id'=>array('name'=>'id','type'=>'number'),
		'name'=>array('name'=>'name','type'=>'text','unique'=>1),
		'email'=>array('name'=>'email','type'=>'text','unique'=>1),
		'password'=>array('name'=>'password','type'=>'password'),
		'login_count'=>array('name'=>'login_count','type'=>'number'),
		'last_login'=>array('name'=>'last_login','type'=>'date'),
		'created_date'=>array('name'=>'created_date','type'=>'date'),
		'last_ip'=>array('name'=>'last_ip','type'=>'text'),
	
	);

?>
