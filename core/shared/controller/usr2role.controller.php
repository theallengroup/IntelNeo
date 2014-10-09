<?php

	#Title: system	
	#Author: f3l	
	#Generated Date: 2006-05-16 19:09:16	
	#Description: small fix, parfial generation	
	#Generator Version: 0.2	
	
	class usr2role_model extends std{
		function on_after_new2(){
			$this->flush_privileges();
		}
		function on_after_delete_selected(){
			$this->flush_privileges();
		}
		function on_after_delete2(){
			$this->flush_privileges();
		}	
		function on_after_edit2(){
			$this->flush_privileges();
		}
		function usr2role_model(){
			$this->std();
		}
		var $has_many=array(1);
		var $belongs_to=array(3);
		var $default_action='view:edit_all';		
		var $table='usr2role';
		var $id="id";
		var $name='@none';
		
	}
?>
