<?php
/**
 * parameters: 
 * importfield(fromtable,fromfield,tolocalfield,connection_sql_where)
 * exportfield(totable,tofield,[fromlocalfield is sql expr],connection_sql_where)
 * */
class setfield_event_handler extends default_event_handler{
	var $def='source_table,target_table';
	function user_interface(){
		global $main;
		$f = default_event_handler::user_interface();
		if(!isset($_GET["selected_table"])){
			if(!isset($_GET["table_name"])){
				//EDIT mode
				//the OLD value, form database record
				if($this->parameters["target_table"]!=''){
					$_GET["selected_table"]=$this->parameters["target_table"];
				}else{
					$_GET["selected_table"]='usr';
				}
			}else{
				//NEW mode
				$_GET["selected_table"]=$_GET["table_name"];
			}
		}
		#$f->add_separator('Campos de:'.$_GET["selected_table"]);
		$m = $main->load_file($_GET["selected_table"]);
		foreach($m->fields as $fname=>$field){
			if($m->id!=$fname){
				if(isset($this->parameters[$fname])){
					//EDIT mode
					$v = $this->parameters[$fname];
				}else{
					//NEW mode
					$v='';
				}
				$f->add_field(array(
					'i18n_text'=>$m->fi($fname),
					'i18n_help'=>$m->fh($fname),
					'name'=>$fname,
					'type'=>'textarea',
					'rows'=>'2',
					'cols'=>'40',
					'value'=>$v,
				));
				
			}
		}
		$l = $main->get_mods();
		$l["_none"]='Seleccione Tabla';
		ksort($l);
		$f->fields['target_table']['type']='list';
		$f->fields['target_table']['options']=$l;
		$f->fields['target_table']['value']=$_GET["selected_table"];
		$f->fields['target_table']['events']=array('change'=>'location.href=window.location.href+"&selected_table="+this.value');

		return($f);
	}
	/**
	 * remove unused parameters
	 * */
	function save(){
		global $main;
		$m = $main->load_file($this->parameters['target_table']);
		foreach($m->fields as $fname=>$field){
			if(trim($this->parameters[$fname])==''){
				unset($this->parameters[$fname]);
			}
		}
		return(default_event_handler::save());
	}

	function setfield_event_handler(){
		$this->default_event_handler();
	}
	function run(){
		global $main;
		$log=gp2($this->parameters);
		#$main->load_file("usuario");
		$f = fopen('./logs/setfield.txt','a+');
		if($this->parameters["source_table"] == $this->parameters["target_table"]){
			$m = $main->load_file($this->parameters["target_table"]);
			$sql_statement = 'UPDATE '.$this->parameters["source_table"] .' SET ';
			$a=array();
			foreach($m->fields as $fname=>$field){
				if(isset($this->parameters[$fname])){
					$a[]="$fname = ".$this->parameters[$fname]." ";
				}
			}
			$sql_statement.= implode(",\n",$a);
			$sql_statement.= ' WHERE '.$m->id.'= \''.$main->get_global_current_record()."'";
			$log.="\nSETFIELD_Query:".$sql_statement;
			$main->sql($sql_statement);
			$log.="\nAffected:".$main->affected();
		}
		fwrite($f,"\n----------".date('Y m d H:i')."----------\n".$log);
		fclose($f);
	}
}
