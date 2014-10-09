<?php

	#Title: system	
	#Author: f3l	
	#Generated Date: 2006-05-16 19:09:16	
	#Description: small fix, parfial generation	
	#Generator Version: 0.2	
	
	class role2priv_model extends std{
		function ac_add_custom(){
			global $i18n;
			$this->menu();
			/** hide some fields, set some data */
			$this->fields['id']['type']='hidden';
			$this->fields['privilege_id']['type']='label';
			$this->fields['privilege_id']['value']=$this->remove_strange_chars($_GET["id"]);
			$this->form_from_fields('new2','all',
				array(
					'title'=>$this->i18n('new_table_title'),
				)
			);
		}
		function ac_edit2(){

			std::ac_edit2();
			$this->flush_privileges();
		}
		/** 
		 * \todo on update, and on delete
		 *
		 * */
		function ac_new2(){
			global $mydir;
			$_GET['role_id']=$this->remove_strange_chars($_GET['role_id']);
			$_GET['privilege_id']=$this->remove_strange_chars($_GET['privilege_id']);

			if(count($this->q2obj('SELECT * from '.$this->get_table_name().' WHERE role_id='."'".$_GET['role_id']."' AND privilege_id='".$_GET['privilege_id']."'"))!=0){
				$this->menu();
				$this->msg($this->i18n('priv_exists'));
			}else{
				std::ac_new2();
			}
			$this->flush_privileges();
		}
		function role2priv_model(){
			$this->std();
		}
		var $has_many=array(1);
		var $belongs_to=array(3);
		var $default_action='view:edit_all';		
		var $table='role2priv';
		var $id="id";
		var $name='@none';
		
	}
?>
