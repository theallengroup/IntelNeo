<?php
	/**
	 * event system, for easy configuration
	 * */

	
class event_model extends std{
	function get_mlist(){
		$mods = $this->get_valid_modules();
		$module_names = array();
		foreach($mods as $mod){
			$module_names[] = basename($mod,'.controller.php');
		}		
		ksort($module_names);
		
		$mods2=array();
		foreach($module_names as $mod_name){
			if($mod_name != 'event'){
				if($m->use_table == 1){
					$m = $this->custom_load_file($mod_name);
					$text = $m->i18n('table_plural');
				}else{
					$text=$mod_name;
				}
			}else{
				$text = $i18n['event']['table_plural'].' (think twice)';
			}
			$mods2[$mod_name]=$text;
		}
		$mods2["*all"]="*all";
		return($mods2);
	}
	function get_events($type,$table,$event){
		//NO META EDITING; PLEASE.
		if($table==$this->program_name()){
			return(array());
		}
		$q = $this->q2obj('SELECT function_name,function_parameters FROM '.$this->get_table_name().' WHERE 
			(table_name = \'*all\' OR table_name = \''.$table.'\') AND 
			name = \''.$event.'\' AND 
			enabled = 1 AND
			event_type = \''.$type.'\' ORDER BY secuence');
		return($q);
	}
	function parse_model_info($fields){
		global $i18n;
		$fields = std::parse_model_info($fields);
		

		$fields['table_name']=array(
			'name'=>'table_name',
			'type'=>'list',
			'options'=>$this->get_mlist(),
		);

		$functions = $this->get_valid_event_handlers();
		

		$fields['id']['type']='label';
		$fields['function_name']=array(
			'name'=>'function_name',
			'type'=>'list',
			'options'=>$functions,
		);
		$functions2 = $this->get_func_list('ac_',1);
		
		$fields['name']=array(
			'name'=>'name',
			'type'=>'list',
			'options'=>array(),// WILL BE FILLED IN AS SOON AS WE KNOW WHAT THE MOD IS; IN ac_all_event_new()
		);
		return($fields);
	}
	function add_processor($pname,$pvalue){
		$this->processors[$pname]=$pvalue;
	}
	function get_processors(){
		$a=array(
			'ssid'=>ssid(),
			'ssname'=>ssname(),
			'now_hm'=>date("H:i"),
			'q_now_hm'=>"\\'".date("H:i")."\\'",
			'uid'=>uid(),
			'now'=>date("Y-m-d H:i:s"),
			'now_ymd'=>date("Y-m-d"),
			'now_hms'=>date("H:i:s"),
			'q_now'=>"\\'".date("Y-m-d H:i:s")."\\'",
			'q_now_ymd'=>"\\'".date("Y-m-d")."\\'",
			'q_now_hms'=>"\\'".date("H:i:s")."\\'",
		);
		foreach($_GET as $gk=>$gv){
			$a["get_".$gk]=$gv;
		}
		$a["current_id"] = $this->get_global_current_record();
		foreach($this->processors as $gk=>$gv){
			$a[$gk]=$gv;
		}
		return($a);
	}
	/** 
	 * storing sql statements inside sql is a bitch!.
	 * */
	function parse_parameter_string($parameters_string){
		//process [#aa]

		$parameters_string = $this->fmt($parameters_string,$this->get_processors());
		return($parameters_string);
	}
	function load_event_handler($handler,$parameters_string=''){
		require_once(STD_LOCATION.'shared/event_handler/'.$handler.'.event_handler.php');

		$handler_class_name = $handler.'_event_handler';
		$h = new $handler_class_name();

		$h->load($parameters_string);
		return($h);
	}
	function event_handler_init(){
		require_once(STD_LOCATION.'shared/event_handler/default.event_handler.php');
	}
	function custom_load_file($table_name){
		global $main;
		if($table_name=='*all'){
			return($main);
		}else{
			return($this->load_file($table_name));
		}
	}
	function run_events($event_type,$table_name,$event_name,&$caller_object){
		$this->event_handler_init();
		//p2(get_declared_classes());
		$w = $this->get_events($event_type,$table_name,$event_name);
		$mod = $this->custom_load_file($table_name);
	
		foreach($w as $row){
			extract($row);//$function=>$parameters;
			$function_parameters = $this->parse_parameter_string($function_parameters);
			$h = $this->load_event_handler($function_name,$function_parameters);
			$h->caller = $caller_object;
			$h->run();
		}
	}
	function get_valid_files_list($folder,$ext){
		return(array_filter($this->aa(array_map(create_function('$x','return(basename($x,".'.$ext.'.php"));'),glob($folder.'/*.'.$ext.'.php'))),create_function('$x','return($x!="default");')));
	}
	/** you gotta love one-liners */
	function get_valid_event_handlers(){
		return($this->get_valid_files_list(STD_LOCATION.'shared/event_handler','event_handler'));
	}
	function ac_all_event_new(){//all_event_new


		if((!isset($_GET['step'])) || ($_GET['step'] != 4)){
			$this->enable_header($this->current_action);
			$this->head();
			$this->menu();
		}

		//step 1
		if(!isset($_GET['step'])){
			$f = $this->get_form_from_fields('all_event_new',array('table_name','function_name','enabled','event_type'),array('title'=>'Paso 1: Definir M&oacute;dulo'));
			$f->add_hidden_field('step','2');
			$f->shtml();

		}elseif(isset($_GET['step'])){

			if($_GET['step'] == 2 || $_GET['step'] == 3){
				$this->fields['table_name']['value']=$_GET['table_name'];
				$this->fields['enabled']['value']=$_GET['enabled'];
				$this->fields['event_type']['value']=$_GET['event_type'];
				$this->fields['function_name']['value']=$_GET['function_name'];
				$this->fields['name']['value']=$_GET['name'];
				$this->fields['table_name']['type']='label';

				$m = $this->custom_load_file($_GET['table_name']);
				$functions_list = $m->get_func_list('ac_',1);
				
				$this->aa($functions_list);
				ksort($functions_list);
				$this->fields['name']['options']=$functions_list;
				$this->fields['enabled']['type']='label';
				$this->fields['event_type']['type']='label';
				$this->fields['function_name']['type']='label';
				$this->fields['name']['options']=$functions_list;
				
				$f = $this->get_form_from_fields('all_event_new',
						array(
								'table_name',
								'function_name',
								'name',
								'enabled',
								'event_type')
								,
						array(
								'title'=>'Paso 2: Definir Acci&oacute;n'
							)
						);
				$f->add_field($this->fields['name']);

				$f->add_hidden_field('step',($_GET['step'] + 1));
			
			}
			if($_GET['step'] == 3 || $_GET['step'] == 4){	

			$eh = $this->load_event_handler($_GET['function_name'],'');
			}
			if($_GET['step'] == 3){		//step 3
				
				$f2 = $eh->user_interface();
				$f->add_separator('<span style="font-size:18pt;">Par&aacute;metros</span>');
				foreach($f2->fields as $field){
					$f->add_field($field);
				}
			}

			if($_GET['step'] == 4){
				if($_GET["secuence"]==''){
					$_GET["secuence"]='0';
				}
				$fields_list = array("name", "table_name", "function_name", "enabled", "event_type","secuence") ;
				$sql='INSERT INTO '.$this->get_table_name().' ('.implode(',',$fields_list).') VALUES (';
				$old_get = $_GET;
				$event_handler = $_GET['function_name'];
				$sa=array();
				foreach($fields_list as $field){
					$sa[]="'".addslashes($this->remove_strange_chars($_GET[$field]))."'";
					unset($_GET[$field]);
				}
				$sql.=implode(',',$sa);
				$sql.=')';
				$this->sql($sql);//save the record, we are still missing the params
				$eh->parameters = $this->noiseless_get();
				$str = $eh->save();
				$this->save_event_configuration($this->last_id(),$str);
				$this->set_cmessage('Evento Creado');
				header('Location: ?mod='.$this->program_name().'&ac=all_b2l');
				die();
				//$this->ac_b2l();
				
			}else{
				//not step 4
				$f->shtml();
			}
		}
		if((!isset($_GET['step'])) || ($_GET['step'] != 4)){
			$this->foot();
		}

	}
	function ac_all_clean(){
		if(!isset($_GET['__item_id'])){
			$id = $this->remove_strange_chars($_GET['id']);
		}else{
			$id = $this->remove_strange_chars($_GET['__item_id']);
		}
		$me = $this->sql('UPDATE '.$this->get_table_name().' SET function_parameters = \'\' WHERE '.$this->id.'=\''.$id.'\'');

	}
	function ac_all_configure(){
		$this->event_handler_init();
		
		if(!isset($_GET['__item_id'])){
			$id = $this->remove_strange_chars($_GET['id']);
		}else{
			$id = $this->remove_strange_chars($_GET['__item_id']);
		}
		$me = $this->q2obj('select * from '.$this->get_table_name().' WHERE '.$this->id.'=\''.$id.'\'');
		$rec=$me[0];
		//in this case, no [#ssid] to 1 please, hence no 'a'
		$eh = $this->load_event_handler($rec['function_name'],$rec['function_parameters']);

		if(!isset($_GET['__item_id'])){//display_event_handler_form
			//headers are disabled, so we must head(); and foot();
			$this->enable_header($this->current_action);
			$this->head();
			$this->menu();
			$f = $eh->user_interface();
			$f->add_hidden_field('mod',$this->program_name());
			$f->add_hidden_field('__item_id',$rec['id']);
			$f->set_title('Edit');
			$f->add_submit_button(array('action'=>$this->current_action,'label'=>'Configure'));
			$f->shtml();

		}else{//save
			//REMOVE NOISE

			$eh->parameters = $this->noiseless_get() ;
			$str = $eh->save();
			//save to database
			$this->save_event_configuration($rec[$this->id],$str);
			$this->set_cmessage('Configuracion guardada');			
			header('Location: ?mod='.$this->program_name().'&ac=all_b2l');
			die();
		}
	}
	function noiseless_get(){
		$g = $_GET;
		unset($g['mod']);
		unset($g['ac']);
		unset($g['submit']);
		unset($g['_submit']);
		unset($g['__output_type']);
		unset($g['__item_id']);
		unset($g['mod']);
		unset($g['step']);//that's right!
		return($g);
	}

	function save_event_configuration($id,$str){
		//Logic, there is no need for this: 
		$str = addslashes($str);//remove one slash
		//turns \\ into \ , which helps stop the proliferation of backslashes
		//this is a "smart removeslashes"
		$str = str_replace("\\\\\\","\\",$str);
		$str = str_replace("\\'",$this->sql_quoted_single_comma(),$str);
		$this->sql('UPDATE '.$this->get_table_name().' SET function_parameters = \''.$str.'\' WHERE '.$this->id.' = \''.$id.'\'');
	}
	function event_model(){
		$this->disable_header('all_configure');
		$this->disable_header('all_event_new');
		$this->std();
	}
	function filter_event_filter($num,$rec){
		if(isset($rec["event_function_parameters"])){
			if($rec["event_function_parameters"]!=''){
				$dx = eval("\$a = ".$rec["event_function_parameters"].";");
				ob_start();
				d2($a);
				$da = ob_get_contents();
				ob_end_clean();
			}
			$rec["event_function_parameters"] = $da;
		}
		return($rec);
	}
	//DISABLE TRIM
	var $default_highlight_filter='none';
	var $default_filter='event_filter';
	var $table="event";
	var $id="id";
	var $ifield='name';
	var $processors=array(
		'fel'=>'felipe',
		);
}
?>
