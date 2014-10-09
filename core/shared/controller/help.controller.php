<?php
	/**
	 * okTODO fix output!
	 * */
	#Title: programa edicion de arreglos
	#Author: felipe	
	#Generated Date: 2006-05-29 09:52:26	
	#Description: programa administracion cosas por hacer	
	#Generator Version: manual
	
class help_model extends std{
	/**
	 * displays all the help info from all the modules
	 * @see get_field_info()
	 * */	
	function ac_help(){
		global $i18n;
		$this->menu();

		include(INCLUDE_DIR.'std_view.php');
		$this->shadow_start();

		foreach($this->get_valid_modules() as $mod){
			$module_name=basename($mod,'.controller.php');
			$m=$this->load_file($module_name,'light');
			//$m->show_help_menu();
			$v=new view($m,array('options'=>array()));
			if(count($m->fields)>0){//a good indicator on wether or not this is a table module, or a utility shared module
	
				echo("<h1>".$m->i18n('table_title').' ('.$m->program_name().')</h1>');
			//	echo($v->get_info('table_help',$m->fields));
				echo($v->get_field_info($m->fields));
				echo('<hr>');
			}
		}
		$this->shadow_end();
		
	}

	function help_model(){
		//$this->table='help';
		$this->std();
	}
	var $default_action='help';
	var $use_table = 0;

}
?>
