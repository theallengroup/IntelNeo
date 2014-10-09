<?php
	/**
	* \todo better system: do not display
	* */

	$data=array();
	foreach($this->fields as $k=>$field){
		if($k==$this->id){
			/* better system: do not display*/
			$data[0][$k]='0';	/*Mysql for NEW WARNING: Mysql Dependant*/
		}else{
			$data[0][$k]=$field['default'];
		}
	}
	$this->fields[$this->id]['type']='hidden';/*Hide the ID */
	$this->ed(array(
		'actions'=>array('new2'),
		'title'=>'new_table_title',
		'data'=>$data,
		'fields'=>$this->fields,
	));
?>
