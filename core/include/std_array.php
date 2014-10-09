<?php

class stdapi {
	var $data='';
	var $file_name='';
	var $name_space;
	var $array_name;
	function stdapi(){
	}
	function load($file_name,$array_name,$name_space){
		global $$name_space;
		include($file_name);
		$d=$$name_space;
		$this->data=$d[$array_name];
		$this->file_name=$file_name;
		$this->array_name=$array_name;
		$this->name_space=$name_space;
	}

	function read($key){
		return($this->data[$key]);
	}

	function set_data($data){
		$this->data=$data;
	}

	function write($file_name=''){
		if($file_name==''){
			//use same as when opnening, overwrite 
			$file_name=$this->file_name;
		}
		$f=fopen($file_name,'w+');
		//fwrite();
		$var_name="\${$this->name_space}['{$this->array_name}']";
		$code="<?php //modified:".date('Y-m-d H:i:s')."\nglobal \${$this->name_space};\n$var_name=".var_export($this->data,true).";\n?".">";
		echo($code);
		fwrite($f,$code);
		fclose($f);
		p2($this->data,'blue');
	}

	function change($key,$value){
		$this->data[$key]=$value;
	}
}

?>
