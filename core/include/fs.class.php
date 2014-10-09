<?php
/** field list structure */
class fs {
	var $fields;
	function fs($fields=array()){
		foreach($fields as $k=>$field){
			$this->fields[$k]=new field($field);
		}
	}
}
class field {
	var $options;
	function field($options){
		$this->options=$options;
	}
}
class viewport {
	var $options;
	function viewport($options=array()){
		//print_r($options);
		foreach($options as $k=>$op){
			$this->$k=$op;
		}
		$this->fields=new fs($options['fields']);
	}
}
?>
