<?php

class default_widget {
	var $fs=array();
	var $fields=array();
	var $name='';
	var $value='';
	var $label='';
	var $widget_name='';
	function get_widget_name(){
		if($this->widget_name==''){
			$this->widget_name = preg_replace('/_widget$/','',get_class($this));
		}
		return($this->widget_name);
	}
	function default_widget(){
		//nil
	}
	function user_interface(){
		$tfile=STD_LOCATION.'shared/widgets/'.$this->get_widget_name().'/'.$this->get_widget_name().'.template.php';
		$t = $this;
		$t1 = common::template($tfile,$t);
		return($t1);
	}
	function validate(){
		return true;	
	}
	function get_fields(){
		return $fields;
	}
	function set_name($name){
		$this->name=$name;
	}
	function set_value($value){
		$this->value=$value;
	}
	function set_label($label){
		$this->label=$label;	
	}
	function set_fields(){
		$this->fields=$fields;
	}
	function add_field($field){
		$this->fields[]=$field;
	}
	function get_value(){
		return($this->value);
	}
	function get_fs(){
		return($this->fs);
	}
	function set_fs($fs){
		$this->fs=$fs;
	}
	function get_name(){
		return($this->name);
	}
	function get_label(){
		return($this->label);	
	}
	function get_result(){
		return($this->name.' '.$this->fields[0]);
	}
	/**
	 * this function creates a widget, based on the information of $this widget, so
	 * that you can easilly transfer the responsability from one widget to another, transparently of
	 * the widget's responsabilities
	 * */
	function make_replacement($type){
		$r = $this->load_widget($type);
		$r->set_name($this->get_name());
		$r->set_label($this->get_label());
		$r->set_fs($this->get_fs());
		$r->set_fields($this->get_fields());
		$r->set_value($this->get_value());
		return($r);
	}


	/** 
	 * @param $widget_type the type of the widget
	 *  when the widget type is missing
	 * this will load a default_widget (this class) and send a log() to the ERROR tab
	 * this kind of permissive error management, will allow you to create special input types using the old
	 * system your_controller::input_*()
	 * which should still be possible.
	 * this will avoid any kind of nagging these widgets would create otehrwise, like a bunch of 
	 * "widget type=X not found" messages, which I don't want at the moment.
	 *
	 *
	 * -- 
	 *
	 * also, if you want to create a widget, to replace your own's widget's functions, use make_replacement() instead.
	 * 
	 * a widget, is a component that allows the programmer full control over UI and result (sql?) generation
	 * such wisgets, usually located at SHARED_MODULES_DIR/widgets, have this format:
	 *
	 * [widget_name].widget.php
	 *
	 * which is a file that has a class called [widget_name]_widget, which usually extends default_widget, but can also
	 * extend other classes, like or_widget, xor_widget, etc.
	 *
	 * the philosophy here is very simple: if you can extend and copy-paste, choose extend, unless its too damn hard.
	 * most of the widgets here should do most of the stuff you want, so you should probably check the comments on those
	 * before rolling your own widgets. 
	 *
	 * also, some widgets have a [wisget_name].template.php 
	 * this file is responsible for displaying the UI of such widget, and also they are there to *separate* UI from logic
	 * 
	 * the API user can always just override user_interface() and take full control over the UI,
	 * making the template file irrelevant.
	 *
	 *
	 * @todo 1293847 allow application specific widgets ./widgets
	 *
	 * */

	function load_widget($widget_type){
		$file_name=STD_LOCATION.'shared/widgets/'.$widget_type.'/'.$widget_type.'.widget.php';
		if(!file_exists($file_name)){
			std::log('missing widget:'.$widget_type,'ERROR');
			$widget_type='default';
		}
		require_once(STD_LOCATION.'shared/widgets/'.$widget_type.'/'.$widget_type.'.widget.php');

		$wclass=$widget_type.'_widget';
		$w=new $wclass();
		$w->widget_name=$widget_type;
		return($w);
	}


}

?>
