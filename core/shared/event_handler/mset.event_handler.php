<?php
/**
 * parameters: 
 * table
 * field
 * value
 * */
class mset_event_handler extends default_event_handler{
	var $def='table,grid(field_name/property_name/property_value)';
	function mset_event_handler(){
		$this->default_event_handler();
	}
	function run(){
		global $std_fields;
		#extract($this->parameters);
		
		foreach($this->parameters["field_name"] as $line=>$field_name_item){
			$fn = $this->parameters['field_name'][$line];
			$pn = $this->parameters['property_name'][$line];
			$v  = $this->parameters['property_value'][$line];
			$this->caller->log("SETTING: $fn . $pn TO: $v !<br/>",'EVENT');
			$this->caller->fields[$fn][$pn]=$v;
		}
		#p2($this->caller->fields);
	}
}
