<?php
function std_type2sql2($field_type){

	$a=array(
		''=>'VARCHAR(100)',
		'label'=>'int(10)',//this is odd
		'text'=>'VARCHAR(100)',
		'textarea'=>'TEXT',
		'number'=>'int(10)',
		'boolean'=>'int(10)',
		'date'=>'datetime',
		'list'=>'VARCHAR(100)',
	);

	return($a[$field_type]);
}
function std_create_table(&$me,$argv){
	$s=$me;
	$sql='CREATE TABLE '.$s->get_table_name()."( ";
	foreach($s->fields as $f){
		$dx='';
		if($s->id == $f['name']){
			$dx=' not null auto_increment';
		}
		$sql.="\n\t".$f['name'].' '.std_type2sql2($f['type']).$dx.' ,';
	}
	$sql.="\n\tprimary key(".$s->id.")\n)";
	$me->sql($sql);
	$d = $me->load_file("edit");
	$d->log_query_forever('./install/patch.sql',$sql);

	return($sql);

}
?>
