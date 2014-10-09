<?php
// on varchar types use %rand% for a number!
class random_data_generator{
	function random_data_generator(){
	
	}
	function generate_random_data($module){
		if(!isset($_GET['make_random'])){
			$f = new form();
			$f->strings=array(
				'rows'=>'Filas',
				'help_rows'=>'Filas',
				);
			$f->add_hidden_field('make_random',1);
			$f->add_hidden_field('mod',$_GET['mod']);
			$f->add_text_field('rows','2000');
			
			$f->add_field(array('name'=>'delete_previous','type'=>'boolean','value'=>'1'));
			$f->strings['delete_previous'] = "Borrar Datos de la Tabla antes de Empezar";
			$f->strings['help_delete_previous'] = ' ';

			foreach($module->fields as $field_name => $field){
				if($field_name != $module->id){
					$f->add_separator("<span style='font-size:20pt'>".$module->fi($field_name)."</span>");
					
					if($module->field_is_foreign($field)){
						//$ex = $this->explain_foreign_field($field_name);
						$f->add_field(array('name'=>$field['name'],'type'=>'boolean','value'=>'1'));
						$f->strings[$field['name']] = "Usar datos de Tabla";
						$f->strings['help_'.$field['name']] = $module->fh($field['name']);
						
					}elseif($field['type'] == 'date'){

						$f->add_field(array('name'=>$field['name'].'_start','value'=>date('Y-m-d H:i:s'),'type'=>'date'));
						$f->strings[$field['name'].'_start'] = "Inicio" ;
						$f->strings['help_'.$field['name'].'_start'] = $module->fh($field['name']);

						$f->add_field(array('name'=>$field['name'].'_end','value'=>date('Y-m-d H:i:s'),'type'=>'date'));
						$f->strings[$field['name'].'_end'] = "Fin";
						$f->strings['help_'.$field['name'].'_end'] = $module->fh($field['name']);
					}elseif($field['type'] == 'number'||$field['type'] == 'currency'){

						$f->add_field(array('name'=>$field['name'].'_start', 'value'=>0, 'size' => '4'));
						$f->strings[$field['name'].'_start'] = "Inicio";
						$f->strings['help_'.$field['name'].'_start'] = $module->fh($field['name']);

						$f->add_field(array('name'=>$field['name'].'_end', 'value'=>100, 'size' => '4'));
						$f->strings[$field['name'].'_end'] = "Fin";
						$f->strings['help_'.$field['name'].'_end'] = $module->fh($field['name']);
					}else{
						$f->add_text_field($field['name'],$field['value']);
						$f->strings[$field['name']] = $module->fi($field['name']);
						$f->strings['help_'.$field['name']] = $module->fh($field['name']);
					
					}
				}

			}
			$f->set_title("Random");
			$f->add_submit_button(array("action"=>$_GET['ac'],'label'=>'Generar Datos'));
			$f->shtml();
		}else{

			foreach($module->fields as $name=>$field){
				if($field["type"] == 'date'){
					$field['name']=$name.'_start';
					$_GET[$name.'_start'] = $module->validate($_GET[$name.'_start'],$field);
					$field['name']=$name.'_end';
					$_GET[$name.'_end'] = $module->validate($_GET[$name.'_end'],$field);
				}else{
					$_GET[$name] = $module->validate($_GET[$name],$field);
				}
				
			}
			p2($_GET);
			extract($_GET);//this is register_globals()
			$foreign_values = array();
			foreach($module->fields as $field_name => $field){
				if($field_name != $module->id){
					if($module->field_is_foreign($field)){
						$m = $module->load_file($module->id2mod($field_name));
						$foreign_values[$field_name] = $m->q2obj('SELECT '.$m->id.' as n from '.$m->get_table_name(),$m->id,$m->id);
					}
				}
			}//fields
			
			
			//debug p2($foreign_values);
			echo("<pre>");
			foreach(range(0,$rows) as $row){
				$sql = 'INSERT INTO '.$module->get_table_name();
				$sql_fields = array();
				$sql_values = array();
				foreach($module->fields as $field_name => $field){
					$sql_fields[$field_name]=$field_name;
					if($field_name != $module->id){
						
						if($module->field_is_foreign($field)){
							//$ex = $this->explain_foreign_field($field_name);
							if($$field_name){
								$sql_values[$field_name]=$foreign_values[$field_name][rand(0,  count($foreign_values[$field_name])-1  )]['n'];
							}else{
								$sql_values[$field_name]=rand(0,9999);
							}
							//
						}elseif($field['type'] == 'date'){
							$sql_values[$field_name] ='\''. date('Y-m-d',rand(strtotime(${$field_name.'_start'}),strtotime(${$field_name.'_end'}))).'\'';

							//${$field_name.'_start'};
							//${$field_name.'_end'};
						}elseif($field['type'] == 'number'){
							$sql_values[$field_name]=rand(${$field_name.'_start'},${$field_name.'_end'});
						}else{
							$sql_values[$field_name] = "'".str_replace("%rand%",rand(0,9999),$$field_name)."'";
						}
					}else{
						// ID is 0 always, for INSERT
						$sql_values[$field_name]=0;
					}
				}//fields

				$sql.='('.implode(',',$sql_fields).') values ('.implode(',',$sql_values).');';

				echo("<br>".$sql);
			}//rows
			echo("</pre>");
	
		}//if
	}
}
?>
