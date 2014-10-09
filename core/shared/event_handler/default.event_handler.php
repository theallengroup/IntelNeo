<?php
class default_event_handler {
	var $def='';
	var $parameters = array();
	var $fields = array();
	function _str2fields($str){
		global $main;
		$fields = explode(',',$str);
		$f2 = array();
		$grid_fields=array();
		$is_grid=0;

		foreach($fields as $field_str){
			$q = explode('(',$field_str);
			if($q[0]=='grid'){
				$this_grid_fields = explode('/',str_replace(")",'',$q[1])) ; 
				foreach($this_grid_fields as $gf){
					$f2[$gf]=array(
						'i18n_help'=>$gf,
						'i18n_text'=>$gf,
						'name'=>$gf,
						'repeat'=>1,
						'group'=>'grid1',
						'type'=>'text',
						'value'=>array(),
					);
				}
			}else{
			
				$p = explode(':',$field_str);
				if(count($p)>=2){
					$name = $p[0];
					$type = $p[1];
				}else{
					$name = $p[0];
					$type = 'text';
				}
				$i18n_text=$name;
				$i18n_help=$type;
				$f2[$name] = array(
					'name'=>$name,
					'type'=>$type,
					'i18n_help'=>$i18n_help,
					'i18n_text'=>$i18n_text
				);
			}
		}

		return($f2);
	}
	function default_event_handler(){
		if($this->def!=''){
			$this->fields=$this->_str2fields($this->def);
		}
	}
	function user_interface(){
		$f = new form();
		
		#$f = $this->_load_form_values($f);
		foreach($this->fields as $field_name=>$field){
			$this->fields[$field_name]['value'] = $this->parameters[$field_name] ;//FIXME, damaged for GRIDS
			$f->add_field($this->fields[$field_name]);
		}
		std::log(gp2($this->fields,'red'),'EVENT');
		std::log(gp2($f->fields,'blue'),'EVENT');
		#p2($f);
		return($f);
	}
	#function _load_form_values($f){
	#	return($f);
	#}
	function load($parameters_string){
		if(trim($parameters_string) ==''){
			$this->error_info = 'pstring_empty';
			std::log('pstring is EMPTY DUDE!','EVENT');
			//well, you clearly aren't loading anything, arent you!
			$this->parameters=array();
			return(0);
		}
		$this->pstring = $parameters_string;
		#$str = str_replace("::-::","'",$str);//old hack inverted

		$parameters = eval("\$p = $parameters_string;");
		$parameters = $p;
		#NO LONGER TRUE, params might be anything the event handler wants, 
		#which might even be uknown at this moment.
		#foreach($this->fields as $field_name=>$field){
		#	$this->parameters[$field_name] = $parameters[$field_name];
		#}
		$this->parameters=$parameters;
	}
	function save(){
		return(var_export($this->parameters,true));
	}
	function run(){
	}
}
