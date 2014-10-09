<?php //include("c:/funciones.php");
/**
	\todo automagically add this.has_many to ALL vh_record views. (that's what it's for!)
 */
	#Title: Untitled App	
	#Author: f3l	
	#Generated Date: 2006-05-22 11:06:04	
	#Description: minor fix, removed role_id from privilege	
	#Generator Version: 0.2	
	
	class privilege_model extends std{
		function ac_edit_privilege(){
		//	$this->add_to_view('edit_all',array('type'=>'separator','value'=>'<h2>'.$this->fi('roles').'</h2>'));
		//	$this->add_to_view('edit_all',array('type'=>'checklist','options'=>$this->q2op('SELECT * from role'),'name'=>'roles'));
		//	$this->addf(array('type'=>'checklist','options'=>$this->q2op('SELECT * from '.$this->table_prefix.'role'),'name'=>'roles'));
			$this->current_view='edit_privilege';
			$this->ac_view();
		}	
		function privilege_check_mod_exists(){
			$mn=$this->get_mod($_GET['action']);
//			if(!$this->load_file($mn)){
				//$this->msg($i18n[$this->table]["error_no_module"]);
//			}
		}
		function ac_edit2(){
			global $i18n;
			$success=std::ac_edit2();
			if($success==1){
				$this->privilege_check_mod_exists();	
			}
			$this->flush_privileges();
		}
		/**
		 * Checks existance of action, so no DUPS are created.
		 * it also check if the mod exists.
		 * */
		function on_before_new2(){
			$this->flush_privileges();
			return(true);
		}
		/*
		function ac_new2(){
			global $i18n;
			$success=std::ac_new2();
			if($success==1){
				$this->privilege_manager->add_privilege(array(
					'action'=>		'role2priv/add_custom',
					'role_name'=>		$role=$this->get_role_name($this->mod_and_ac($this->table,$this->current_action)),
					'privilege_name'=>	$i18n[$this->table]['p_go_to_role2priv'],
				));
				$this->privilege_check_mod_exists();	
				$this->msg($this->make_link(array('mod'=>'role2priv','ac'=>'add_custom','id'=>$this->last_id()),$i18n[$this->table]["go_to_role2priv"]));
			}

		}
		 */
		
		/*
		function menu(){
	
			echo("you are nuts<hr/>");
			echo(privilege_model::input_privilege_select(array()));

		}
		 */

		function privilege_model(){
			//$this->add_input('privilege_select','');
			$this->std();
		}
		var $has_many=array();
		var $belongs_to=array(3);
		var $default_action='view:edit_all';		
		var $table='privilege';
		var $id="id";
		var $name='name';
		
	}
?>
