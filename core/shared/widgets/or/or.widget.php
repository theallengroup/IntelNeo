<?php
class or_widget extends default_widget {

	/**
	 * @param $field a string, or a widget
	 * when a striong is given, a label widget is created.
	 * */
	function add_field($field){
		if(gettype($field)=='string'){
			$w = $this->load_widget('label');
			$w->set_value($field);
			$field=$w;
		}
		$this->fields[]=$field;
		//echo('<br/>Field Added:'.$field->get_name());
	}
	function or_widget(){
		$this->default_widget();
	}
}
?>
