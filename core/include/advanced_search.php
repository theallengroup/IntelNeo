<?php
/** 
 * this is just a simple Widget Contanier,. that's all 
 * it COULD be a widget, but this one is special.
 * 
 * dependencies:
 * default_widget class must be loaded. (usually core/include/widget_init.php does this)
 *
 * the object of the class is to generate UI, validation and SQL for a given list of fields, see advanced_search::set_fields() and field_structure
 *
 * */

class advanced_search {
	/** 
	 * a widget list 
	 * */

	var $fields=array();
	/** 
	 * creates widgets from standard fields
	 * */
	function set_fields($fields){
		$dw=new default_widget();				
		foreach($fields as $f){
			$w = $dw->load_widget($f['type']);
			$w->set_name($f['name']);
			$w->set_label($f['i18n_text']);
			$w->set_fs($f);
			$this->fields[$f['name']]=$w;
		}
	}
	function set_values($user_data){
		if(is_array($user_data)){
			foreach($this->fields as $f){
				echo('<br/>setting:'.$f->get_name());
			}
		}
	}
	function user_interface(){
		
		$template=array();
		foreach($this->fields as $f){
			
			$template[$f->get_name()] = array(
				'label'=>$f->get_label(),
				'input'=>$f->user_interface()
			);
		}
		$tfile=STD_LOCATION.'shared/templates/advanced_search.php';
		//p2($template);
		//p2($this,'green');
		$ui = common::template($tfile,$template);
		return($ui);
	}
	function get_result(){
		return('1=1');	
	}
	function advanced_search(){
//		$this->default_widget();
	}
}
?>
