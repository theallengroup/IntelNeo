<?php

class file_provider{
	var $extension='.php';
	var $link_template='@name';
	var $glob_path='./*.php';
	function s($a){
		natcasesort(&$a);
		return($a);
	}
	function all(){
		return(array_map(create_function('$x','return(str_replace("@name",basename($x,"'.$this->extension.'"),\''.str_replace('\'','\\\'',$this->link_template).'\' ));'),$this->s(glob($this->glob_path))));
	}
}

?>
