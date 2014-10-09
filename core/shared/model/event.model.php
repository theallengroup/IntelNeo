<?php
global $std_fields;
$std_fields['event']=array(
		'id'=>array('name'=>'id','type'=>'label'),
		'name'=>array(),
		'table_name'=>array(),
		'function_name'=>array(),
		'function_parameters'=>array('type'=>'textarea'),
		'enabled'=>array('type'=>'list','options'=>array('1'=>'1','0'=>'0')),
		'event_type'=>array('type'=>'list','options'=>array('before'=>'before','after'=>'after')),
		'secuence'=>array('type'=>'number'),
		'info'=>array('type'=>'text'),
	);
?>
