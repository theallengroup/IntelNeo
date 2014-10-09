<?php

	/**
	Title: Novedades	
	Author: f3l	
	Generated Date: 2007-03-22 17:46:52	
	Description: ingreso novedades opr web	
	Generator Version: 0.2	
	*/
	
	class log_model extends std{
		/**
		 * call: log_event('my_module','123','123 record is very mean to other records!')
		 * use log level to define verious "categories" for the log.
		 *
		 * call: 
		 * log_event('my_module','123','interesting ...',1)
		 * log_event('my_module','123','blah ...',2)
		 * log_event('my_module','123','some more random text...',3)
		 * and show it using 3 TABS
		 * 1 = blah
		 *
		 * */
		function log_event($table,$record,$info,$level=1){
			$ssid=ssid();
			if($ssid==''){
				$ssid=0;
			}
			$this->sql('INSERT INTO '.$this->get_table_name().' (usr_id,table_name,record,info,log_date,log_level) values ('.$ssid.',\''.$table.'\',\''.$record.'\',\''.$info.'\',\''.date('Y-m-d H:i:s').'\',\''.$level.'\' )');
		}

		function log_model(){
			$this->mod_get_kids_fields[]='id';
			$this->mod_get_kids_fields[]='table_name';
			$this->mod_get_kids_fields[]='record';
			$this->mod_get_kids_fields[]='info';
			$this->mod_get_kids_fields[]='log_date';
			$this->mod_get_kids_fields[]='log_level';

			$this->std();
		}
		var $default_action='view:edit_all';
		var $default_edit_action='view:edit_all';
		var $table='log';
		var $id="id";
		var $ifield='table_name';
		
	}
?>
