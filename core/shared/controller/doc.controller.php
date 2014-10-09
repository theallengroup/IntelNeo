<?php
/** 
 * @todo let hbm rels be included in the  graph (possibly with another color?
 * */	
class doc_model extends std{
	function all_rango_ingresos(){
	
	}
	function etl_fields_destination(){
	}
	function etl_fields(){
	
	}
	function dot_code($mod_name,$field1,$field2){
		global $main;
		$d = $this->load_file($mod_name);
		$all=$d->foreign_select(array('fields'=>$d->fields));
		$s = $this->q2obj($all['sql']);
		//$this->e_table($s);
		$field1=$d->get_config_prefix().$field1;
		$field2=$d->get_config_prefix().$field2;
		$field2=$d->prefix.$field2;

		$d1='';
		$d2 = "";
		//p2($s);
		foreach($s as $line){
			$d2.="\n\"".$field1.':'.$line[$field1].'"[shape=rect,label="'.$line[$field1].'",color=red];';
			$d2.="\n\"".$field2.':'.$line[$field2].'"[shape=rect,label="'.$line[$field2].'",color=blue];';
			$d1.="\n\"".$field1.':'.$line[$field1].'" -> "'.$field2.':'.$line[$field2]."\" ;";
		}
		return(array('def'=>$d2,'code'=>$d1));
	}

	function get_mod_info($mod){
		global $i18n;
		$me=$this->load_file($mod);
		if(!is_array($me->fields)||count($me->fields)==0){
			return('');
		}
		$tmpl=file_get_contents(SHARED_MODULES_DIR.'templates/dev_field_info.html');
		
		$a=array();
		$c=0;
		foreach($me->fields as $k=>$field){
			$a[$c]=array();
			$a[$c]['field_help']=str_replace('\\n','<br/>',$me->fh($field['name']));
			$a[$c]['field_name']=$me->fi($field['name']);
			$a[$c]['field_type']=$me->fi($field['name']);
			$a[$c]['field_type']=$me->fi($field['name']);
			$a[$c]['field_sql']=$field['name'];
			$a[$c]['field_type']=$field['type'];
			$c++;
		}
		$dww=$me->get_i18n_array();
		$tree=array('isnode'=>1);
		$tree['HEADER']=array(
			'table_name'=>$me->i18n('table_plural'),
			'table_help'=>$me->has_i18n_key('table_help')?$me->i18n('table_help'):'',
			'description'=>$me->has_i18n_key('description')?$me->i18n('description'):'',
			'table_sql_name'=>$me->get_table_name(),
			);
		$tree['FOOTER']=array();
		$tree['CONTENT']['LOOP']=array(
			'HEADER'=>$dww,
			'FOOTER'=>$dww,	
			'CONTENT'=>$a,
			'isnode'=>1,
		);
		$me->log("Available keys to the Template:".implode(" , ",array_keys($a)),'TEMPLATE');
		$ret=$me->parse_template($tree,$tmpl,'CONTENT','_');
		$q = $me->q2obj("select count(*) as c from ".$me->get_table_name());
		$q2 = $me->foreign_select(array('fields'=>$me->fields));
		$cd=$me->q2obj($q2['csql']);
		$row_count = $cd[0]['c'];
		if($row_count<15 && $row_count>0){
			$ret.=$me->dsl();
		}
		return($ret);
	}

	function ac_data_dictionary(){
		global $config;
		$this->menu();
		$ds='';
		$tl = array();
		foreach($this->table_list() as $t){
			$k ="Tables_in_".$config['database_name'];
			$tl[$t[$k]]=$t[$k];

		}
		foreach($this->get_valid_modules() as $mod){
			$tname = basename($mod,'.controller.php');
			if(isset($tl[$tname])){
				$ds.=($this->get_mod_info($tname));
			}
		}
		$this->shadow($ds,'round');
	}
	function install(){
	
	}
	/**
	 * @todo dot_cleanup
	 * */
	function dot_run($code,$file='somefile'){
		//echo($d1);
		$fname = './doc/'.$file.'.dot';
		$this->file_write($fname,$code,1,1);
		// echo('<pre>'.$code.'</pre><br>');
		if(PHP_OS=='Darwin'){
			passthru("/usr/local/bin/dot -Tpng -o $fname.png $fname > $fname.error.txt");
		}else{
			passthru("dot -Tpng -o $fname.png $fname > $fname.error.txt");
		}
		echo(file_get_contents($fname.'.error.txt'));
		echo('<img src=\''.$fname.'.png\' /><br>');
	}
	function dot_wrap($code){
		return("\n digraph G {\nrankdir=LR;\n$code}");
	}
	function ac_doc(){
		$this->menu();
		$c1 = $this->dot_code('role2priv','role_name','privilege_name');
		$c2 = $this->dot_code('usr2role','usr_name','role_name');
		$this->dot_run($this->dot_wrap($c1['def'].$c2['def'].$c1['code'].$c2['code']));
	}

	function data_dict_get_cluster($cluster_name,$color,$modules){
		global $i18n,$config;
		echo("--");
		//echo("<h1>$cluster_name</h1>");
//			node [style=filled,fillcolor='.$color.'];
		$dict="\n subgraph \"cluster_".$cluster_name.'" { 
			label="'.$cluster_name.'";
			color='.$color.';
			';
		$rel6=array();

		$m2=array();//this->aa() style like module list for simple lookup
		foreach($modules as $m){
			$m2[basename($m,'.controller.php')]=basename($m,'.controller.php');
		}
		$dont_display=array();
		$all_hbm_rels=array();
		$table_list=array();
		$tl = $this->table_list();
		

		foreach($tl as $t){
			$k ="Tables_in_".$config['database_name'];
			$table_list[$t[$k]]=$t[$k];
		}

		foreach($modules as $m){
			$m=basename($m,'.controller.php');
			//don't display non-existant tables
			if(!isset($table_list[$m])){
				continue;
			}
			$m1=$this->load_file($m);


			if(is_array($m1->fields) && $m1->use_table==1){
				$p=$m1->program_name();
				
				if($m1->has_i18n_key('description')){
					$dict.="\nnotas_" . $p . ':n -> '.$p.":s [arrowhead=none,color=black,style=dashed];\n";
					$dict.="\n{rank=same " . $p . '; notas_'.$p."};\n";
				}
				$table_list[$p]="\n".$p.'[shape=none,label=<<TABLE port="'.$p.'" BORDER="0" CELLSPACING="0" CELLPADDING="2">';
				//'.$m1->i18n('table_plural').'
				$table_list[$p].='<TR><td></td><TD BORDER="1" BGCOLOR="#d0d0d0" ><FONT FACE="verdana" POINT-SIZE="12pt">'.''.$m1->program_name().' </FONT></TD><td></td></TR>';
				$ff=array();
				$f=1;
				//draw Edges
				if(isset($m1->rel['has']) && is_array($m1->rel['has'])){
					foreach($m1->rel['has'] as $rel){
						if(array_key_exists($rel,$m2)){//see if module related is inside the list, otherwise, don't draw 'orphan' (disgusting) edges
							$rel6[]=$p.':'.$m1->id.'_from->'.$rel.':'.$p.'_id_to;';
						}	
					}
				}
				if(isset($m1->rel['hbm']) && is_array($m1->rel['hbm'])){
					foreach($m1->rel['hbm'] as $ftable=>$middle_table ){
						if(array_key_exists($rel,$m2)){
							#$rel6[]=$p.':'.$m1->id.'_from->'.$middle_table.':'.$p.'_id_to[color=blue];';
							#$rel6[]=$middle_table.':'.$p.'_from->'.$ftable.':id_to[color=red];';

							#ONLY DRAW HBM RELS ONCE
							$rname = "$p/$ftable";
							if(!isset($all_hbm_rels["$p/$ftable"])){
								$rel6[]=$p.':'.$m1->id.'_from->'.$ftable.':id_to[label='.$middle_table.',labelfontname=verdana,labelfontsize=8pt];';
								$dont_display[$middle_table]=$middle_table;

								$all_hbm_rels["$p/$ftable"]="$p/$ftable";
								$all_hbm_rels["$ftable/$p"]="$ftable/$p";
							}
						}	
					}
				}
				foreach($m1->fields as $field_name=>$field){
					if($m1->field_is_foreign($field)){
						#echo($m1->get_table_name().'.'.$field_name.' IS FOREIGN <br/>');
						$dx=' BGCOLOR="beige" ';
					}elseif($field['name']==$m1->id) {
						$dx=' BGCOLOR="#f0f0f0" ';
					}else {
						$dx='';
					}

					$ff[]="<TR><TD port=\"".$field['name']."_to\" ></TD>".
					//'. $i18n[$m1->program_name()]['fields'][$field_name] .'	
					"<TD ".$dx." BORDER=\"1\" ALIGN=\"LEFT\" width=\"90%\"><FONT FACE=\"tahoma\" POINT-SIZE=\"10pt\">".''. $field['name'] .' </FONT></TD>'.
					"<TD port=\"".$field['name']."_from\"></TD></TR>";

					$f++;
				}
				$table_list[$p].=implode("\n",$ff);
				$table_list[$p].="\n</TABLE>>];\n\n";
				/// dbg echo($p.' = '.$this->has_i18n_key('description').'<br/>');
				
				if($m1->has_i18n_key('description')){
					$table_list[$p].="\nnotas_" . $p . "[style=filled,fillcolor=beige,label=<<TABLE BORDER=\"0\"><TR><TD ALIGN=\"LEFT\"><FONT FACE=\"tahoma\" POINT-SIZE=\"10pt\">".$m1->i18n('description')."</FONT></TD></TR></TABLE>>,shape=box,color=black];\n";
				}
			}
		}
		#REMOVE RELATION TABLES
		foreach($dont_display as $tn){
			unset($table_list[$tn]);
		}
		$dict.=implode("\n",$table_list)." \n";
		//p2($dict);
		$dict.=implode("\n",$rel6)."\n }\n";
		return($dict);
	}
	/** special implementation */
	function pizano(){
	
		$dict=$this->data_dict_get_cluster('Sistema de Seguridad','black',array(
			'usr',
			'role',
			'usr2role',
			'privilege',
			'role2priv',
			'log',
			));
		$this->dot_run('digraph G { [rankdir=LR]  '.$dict.' }','seguridad');	
		$dict=$this->data_dict_get_cluster('Informacion Geografica','black',array(
			'rodal',
			'nucleo_forestal',
			'parcela',
			'predio',
			'proyecto',
			));
		$this->dot_run('digraph G { [rankdir=LR]  '.$dict.' }','geografica');
		$dict=$this->data_dict_get_cluster('Informacion Volumetrica','black',array(
			'especie',
			'e_plantula',
			'tipo_de_plantula',
			'origen',
			'rodal',
			'e_historial_del_terreno',
			'tipo_de_terreno',
			'preparacion_terreno',
			'tipo_de_suelo',
			'uso_del_terreno',
			));
		$this->dot_run('digraph G { [rankdir=LR]  '.$dict.' }','volumetrica');
		$dict=$this->data_dict_get_cluster('Administracion de Tareas','black',array(
			'rodal',
			'tarea',
			'razon_incumplimiento',
			'actividad',
			'e_inventario',
			'calidad_de_sitio',
			'tipo_muestreo',
		));
		$this->dot_run('digraph G { [rankdir=LR]  '.$dict.' }','tareas');		
		/*
		$dict=$this->data_dict_get_cluster('Ordenes de Servicio','black',array(
			'orden',
			'item_orden',
		));
		 */
		$dict=$this->data_dict_get_cluster('Ordenes de Servicio','black',array(
			'unidad_medida',
			'tarifa',
			'orden',
			'historial_orden',
			'estado_orden',
			'estado_item_orden',
			'causa_trabajo_adicional',
			'estado_orden',
			'rodal',
			'item_orden',
			'tarea',
			'recibo_de_obra',
		));
		$this->dot_run('digraph G { [rankdir=LR]  '.$dict.' }','ordenes');		
		$dict=$this->data_dict_get_cluster('Interfase Contable','black',array(

			'interfase_contable',
			'predio',
			'tarifa',
			'tipo_de_actividad',
			'proyecto',
			'actividad',
			'proveedor',
		));
		$this->dot_run('digraph G { [rankdir=LR]  '.$dict.' }','interfase_contable');
		
		$dict=$this->data_dict_get_cluster('Automatizacion de Tareas','black',array(
			'tarea',
			'actividad',
			'proveedor',
			'clase_proveedor',
#			'actividad_economica',
			'unidad_medida',
			'tarifa',
			'razon_incumplimiento',
			'buena_practica',
			'especie',
			'rodal',
		));
		$this->dot_run('digraph G { [rankdir=LR]  '.$dict.' }','automatizacion');
		$dict=$this->data_dict_get_cluster('Informacion GIS','black',array(
			'geo_rodal',
			'geo_via',
			'rodal',
			'predio',
			'nucleo_forestal',
			'tipo_de_punto',
			'tipo_de_via',
			'via',
		));
		$this->dot_run('digraph G { [rankdir=LR]  '.$dict.' }','gis');
	}
	function ac_data_dict(){
		$this->menu();

		//Cambios necesarios para documentacion de PIZANO
		if(dirname($_SERVER['PHP_SELF'])=='sif76'){
			$this->pizano();
			return(0);
		}

		$dict=$this->data_dict_get_cluster('Local Modules','red',$this->get_local_modules());
		$dict.=$this->data_dict_get_cluster('Shared Modules','blue',$this->get_shared_modules());

		$code=$dict;
		
		//	$code=$this->dot_wrap($code);
		//	subgraph cluster_d {a->b->c->d->e }
		$code='digraph G { [rankdir=LR]  '.$code.' }';
		$this->dot_run($code);
	}
	function ac_docmenu(){
		$this->menu();
		$this->show_menu=0;
		$this->shadow("
			<ul>
			<li><a href='?mod=doc&p=data_dict'>IMPRIMIR MODELO ENTIDAD RELACION</a>
			<li><a href='?mod=doc&p=doc'>IMPRIMIR RELACIONES ENTRE USUARIOS Y ROLES</a>
			<li><a href='?mod=doc&p=data_dictionary'>IMPRIMIR LISTA DE CAMPOS COMPLETA</a>
			</ul>
			");
		if($_GET['p']=='data_dict'){
			$this->ac_data_dict();
		}elseif($_GET['p']=='doc'){
			$this->ac_doc();
		}elseif($_GET['p']=='data_dictionary'){
			$this->ac_data_dictionary();
		}else{
			//invalid p
		}
		
	}
	function doc_model(){
		$this->add_public_module('ac_');
		$this->std();
	}
	var $use_table =0;
	var $default_action='docmenu';
}
?>
