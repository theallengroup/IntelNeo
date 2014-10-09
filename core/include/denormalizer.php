<?php
/**
 usage:
	include(INCLUDE_DIR.'ddenormalizer.php');
	$r = new denormalizer();
	$r->denormalize($this);
*/
class denormalizer{
	function denormalizer(){
	
	}
	/** 
	 * @param $module an STD object derivate,
	 * @param $table_list a table array : (0=>table,1:table,2:table), NOTE: modules are espected, tables with prefix won't quite work here.
	 * returns an array with: $fields, $conditions, $query and $tables.
	 *
	 * field: FIELD as field
	 * sfield: FIELD
	 * afield: field
	 * ofield: array(table,field)
	 *
	 * @todo handle tables that join themselves, aliases, and foreign=>table.field syntax.
	 * $field_list must be: table.field
	 * */
	function sql_join($module,$table_list,$field_list='all'){
		$fields = array();
		$conditions = array();
		$tables = array();
		$query = "SELECT \n\t";
		foreach($table_list as $table){
			$m = $module->load_file($table);
			$tables[] = $m->get_table_name();
			foreach($m->fields as $field_name=>$field){
				$field_expr = $m->get_table_name().'.'.$field_name;
				if($field_list == 'all' || in_array($field_expr,$field_list)){
					$fields[] = $field_expr." AS ".$m->get_table_name().'_'.$field_name;
					$sfields[] = $m->get_table_name().'.'.$field_name;
					$afields[] = $m->get_table_name().'_'.$field_name;
					$ofields[] = array('table'=>$m->get_table_name(),'field'=>$field_name);
				}
				
				if($m->field_is_foreign($field) && ($m->id != $field_name)){
					$fm=$m->id2mod($field_name);
					if(in_array($fm,$table_list)){
						$fm_m = $module->load_file($fm);
						$conditions[]=$m->get_table_name() .'.'. $field_name . ' = '.$fm_m->get_table_name().'.'.$fm_m->id;
					}
				}
			}
		}
		$w = implode(" AND \n\t",$conditions);
		if(count($conditions)>0){
			$w2="\nWHERE \n\t(".$w.")";
		}else{
			$w2='';
		}
		$query.=implode(",\n\t",$fields)." \nFROM \n\t".implode(",\n\t",$tables)." $w2 \n";
		return(array(
			'query'=>$query,
			'conditions'=>$conditions,
			'tables'=>$tables,
			'fields'=>$fields,
			'sfields'=>$sfields,
			'afields'=>$afields,
			'ofields'=>$ofields,
		));

	}
	function denormalize($module){
		if(!isset($_GET["tables"]) && (!isset($_POST['fields']))){

			$f = $module->get_local_modules_form(array(
				'action'=>'Denormalizar',
				'type'=>'checklist',
			));
			$f->add_field(array('name'=>'table_name','type'=>'text'));
			$f->add_hidden_field('module_name',$_GET['module_name']);
			$f->strings['table_name']='Nueva Tabla';
			$f->strings['help_table_name']='Nombre de la nueva tabla contenedora, para la información denormalizada';
			$f->shtml();


		}elseif(!isset($_POST['fields'])){
			$table_list = $_GET['tables'];
			$f = new form();
			$f->method='POST';
			$f->action='?mod=edit&ac=denormalize&module_name='.$_GET["module_name"];
			$f->strings = array();
			$f->set_title('Seleccione los Campos');
			$f->add_hidden_field('mod',$_GET['mod']);
			$f->add_hidden_field('module_name',$_GET['module_name']);
			$f->add_hidden_field('table_name',$_GET['table_name']);
			foreach($table_list as $k=>$table){
				$f->add_hidden_field('tables['. $k .']',$table);
			}
			$f->add_submit_button(array('action'=>$_GET["ac"],'label'=>'Aceptar'));
			$op = array();
			$values = array();
			foreach($table_list as $table){
				$m = $module->load_file($table);
				$tables[] = $m->get_table_name();
				
				foreach($m->fields as $field_name=>$field){ //ID S get AUTOMAGICALLY DISABLED.
					$op[$m->get_table_name().'.'.$field_name] = $m->get_table_name().'.'.$field_name;

					if(!($m->field_is_foreign($field) || $field_name == $m->id )){
						$values[] = $m->get_table_name().'.'.$field_name;
					}else{
						$op[$m->get_table_name().'.'.$field_name] = '<span style="color:lightgray">'.$m->get_table_name().'.'.$field_name."</span>";
					}
				}
			}
			$f->add_field(array(
				'name'=>'fields',
				'options'=>$op,
				'values'=>$values,
				'check_all'=>'1',
				'type'=>'checklist',
				'i18n_text'=>'Campos',
				'i18n_help'=>'',
				'type'=>'checklist',
				));
			$f->shtml();

		}else{
			extract($this->sql_join($module,$_POST['tables'],$_POST['fields']));

			p2($query);
			p2($fields,'red');
			p2($tables,'blue');
			p2($conditions,'purple');
			p2($_GET);
		}
	}
}
/**
mysql> select sum(cantidad) as sc,predio.id as predio_id , predio.nombre as pred
io_nombre ,actividad.nombre as actividad_nombre , count(actividad.nombre) as cno
mbre  from actividad,tarea,orden,item_orden,predio,rodal where item_orden.orden_
id = orden.id  and predio.id =  rodal.predio_id and orden.rodal_id = rodal.id an
d item_orden.tarea_id = tarea.id and tarea.actividad_id = actividad.id  and pred
io.id = 7   group by actividad.id                  limit 10\G
 *
 * */
?>
