<?php
global $config,$mydir;
# autogenerated on:2010-11-29 14:21:47 
# by user:Administrator ID:1, try not to hand edit too much
$std_fields['drill_down_auth']=array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'label',
  ),
  'table_name' => 
  array (
	  'name' => 'table_name',
	  'type'=>'text',
	  'options'=>$this->q2op("show tables",'Tables_in_'.$config["database_name"],'Tables_in_'.$config["database_name"]),
  ),
);
?>
