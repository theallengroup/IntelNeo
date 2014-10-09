<?php

	#Title: system	
	#Author: f3l	
	#Generated Date: 2006-05-16 19:09:16	
	#Description: small fix, parfial generation	
	#Generator Version: 0.2	
	
	class role_model extends std{
		function role_model(){
			$this->std();
		}
		var $has_many=array(1);
		var $belongs_to=array(3);
		var $default_action='view:edit_all';		
		var $table='role';
		var $id="id";
		var $name='name';
		
	}
?>
