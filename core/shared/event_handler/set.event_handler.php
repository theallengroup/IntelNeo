<?php
/**
 * parameters: 
 * table
 * field
 * value
 * */
class set_event_handler extends default_event_handler{
	var $def='table,field_name,property_name,value';
	function set_event_handler(){
		$this->default_event_handler();
	}
	function run(){
		global $std_fields;
		extract($this->parameters);
		
		$this->caller->fields[$field_name][$property_name]=$value;
	}
}
