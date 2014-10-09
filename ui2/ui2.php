<?php

class ui2{
	var $_data_provider=null;
	function supported_actions(){
		$this->_actions = get_class_methods($this->_data_provider);
		return($this->_actions);
	}
	function is_supported($action){
		#print_r($this->_actions);
		return(in_array($action,$this->_actions));
	}
	function set_data_provider_object($o){
		$this->_data_provider=$o;
		$this->supported_actions();
	}
	function out(){
		$dx='';
		if($this->is_supported('all')){
			$all=$this->_data_provider->all();
		}else{
			die("invalid class");
		}
		$has_name = ($this->is_supported('get_name'));
		$has_description = ($this->is_supported('get_description'));
		$has_icon = ($this->is_supported('get_icon'));
		$has_link = ($this->is_supported('get_link'));
		$has_actions = ($this->is_supported('get_actions'));
		$c = 0;
		foreach($all as $id=>$a){
			$name=$a;
			$description='';
			$icon='none.gif';
			if($has_name){$name = $this->_data_provider->get_name($id);}
			if($has_description){$description = $this->_data_provider->get_description($id);}
			if($has_icon){$icon = $this->_data_provider->get_icon($id);}
			if($has_actions){$actions = $this->_data_provider->get_actions($id);}

			$dx.=("\n");
			if($has_icon){
				$dx.=("<img src='$icon' alt='$icon'/><br/>");
			}
			if($has_link){
				$name = $this->_data_provider->get_link($id,$name);
			}
			$dx.=("<h3>". $name ."</h3>");
			if($has_actions){
				$dx.=("<p>".$actions."</p>");
			}
			if($has_description){
				$dx.=("<p>".$description."</p>");
			}
			$c++;
		}
		#$dx.=("<br/>Total:$c");	
		return($dx);
	}
	function run(){
		echo($this->out());
	}
}
?>
