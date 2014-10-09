<?php
//http://acomment.net/developer-icon-sets-collection-of-the-best-free-to-both-personal-commercial-use-icon-sets/152
//http://www.icongalore.com/

/**
Title: programa edicion de arreglos, y administracion del sistema
Author: felipe	
Generated Date: 2006-05-29 09:52:26	
Description: programa administracion cosas por hacer	
Generator Version: manual
* */
	
class edit_model extends std{
	function ac_dependency(){
		$this->man_nav();
		$this->show_dependencies($_GET['table']);
	}

	function ac_field_bulk_fix(){
		global $config;
		$r2 = $this->get_valid_modules();
		$r=array();
		foreach($r2 as $m=>$v){
			$d = basename($v,".controller.php");
			$r[$d]=$d;
		}
		$all=array();
		$c=0;
		$tables=$this->table_list();
		
		$lt=array();
		foreach($tables as $t){
			$lt[]=$t['Tables_in_'.$config["database_name"]];
		}
		$lt=$this->aa($lt);
		
		

		foreach($r as $table){
			$m = $this->load_file($table);
			//tablas que no existan, no me interes arregarlas.
			if(!isset($lt[$table])){
				continue;
			}
			foreach($m->fields as $field){
				$full_name = $table."_".$field['name'];
				if(strlen($full_name)>=32){
					$all[]=array(
						' '=>"<a href='?ac=db_manager&mod=edit&edit_module=$table'>EDIT TABLE</a>",
						'  '=>"<a href='?mod=edit&ac=rename_table&table=$table'>RENAME TABLE</a>",
						'table'=>$table,
						'    '=>"<a href='?mod=edit&ac=change_field&field=".$field['name']."&table=$table'>RENAME FIELD</a>",
						'field'=>$field['name'],
						'full_name'=>$full_name,
						'C'=>$c,
						
					);
					$c++;
				}
			}
		}
		$this->e_table($all,'none',array('style'=>'list','title'=>'Fields &gt;32'));
		echo(123);
	}
	function ac_table2mod(){
		$this->menu();
		if(!isset($_GET["newmod"])){
			$f = new form();
			$f->set_title("Crear M&oacute;dulo desde Tabla");
			$f->add_hidden_field("mod",$this->program_name());
			$f->add_field(array('type'=>'checklist','check_all'=>1,"name"=>'newmod','options'=>$this->missing_tables(),'i18n_text'=>'M&oacute;dulo','i18n_help'=>'Tabla a partir de la cual se va a generar el M&oacute;dulo.'));
			$f->add_submit_button(array("label"=>'Crear M&oacute;dulo','action'=>$this->current_action));
			$f->shtml();
		}else{
			//THIS IS A REPETITION AND A SIMPLIFICATION OF .GEN, but won't replace it.
			//please, use GEN.
			foreach($_GET["newmod"] as $mname){
				$this->mod_from_table($mname);
			}
		}
	}
	function mod_from_table($mname){
		global $main;
		$fields=array();
		$ifield='id';//safe asumptions
		$id='id';
		$w = ucwords(str_replace("_"," ",$mname));
		$mi18n=array(
			'table_title'=>$w,
			'table_plural'=>$w.'s',
			'new_table_title'=>"Nuevo $w",
			'edit_table_title'=>"Editar $w",
			'table_gender'=>'M',
			'fields'=>array(
				
				),
		);
		foreach($this->describe($mname) as $f){
			$fn = $f["Field"];
			$fields[$fn]=array('name'=>$fn,'type'=>'text','type'=>$this->sqlfield2type($f["Type"]));
			$mi18n["fields"][$fn]=ucwords(str_replace("_"," ",$fn));
			$mi18n["fields"]['help_'.$fn]=' ';
			
			if($f["Key"]=='PRI'){
				$id=$fn;
				$fields[$fn]['type']='label';
			}
			if(($f["Type"]=='VARCHAR'||$f["Type"]=='CHAR' )&&$ifield=='id'){
				$ifield=$fn;
			}
		}
		if(!file_exists("./view/$mname")){mkdir("./view/$mname");}
		//WHAT ABOUT REL?
		
		$base = get_class($main);
		$now=date("Y-m-d H:i:s");
		$me = ssid().':'.ssname();
		$controller_code="<?php \n#Generated on $now by $me\n class ${mname}_model extends $base {\n function ${mname}_model(){\$this->{$base}();}\n var \$table='$mname';\n var \$id='$id';\n var \$ifield='$ifield'; \n}\n?>";
		$this->file_write("./controller/$mname.controller.php",$controller_code,1,1);
		$this->array2file($fields,'model','./model/'.$mname.'.model.php',"std_fields['".$mname."']");
		$this->array2file($mi18n,'','./i18n/'.std_get_language().'/'.$mname.".i18n.php","i18n['".$mname."']");
		$this->msg("<a href='?ac=db_manager&mod=edit&edit_module=$mname'>Editar M&oacute;dulo:$mname</a>");
		$this->msg("<a href='?filename=./i18n/ES/$mname.i18n.php&step=2&mod=edit&ac=ed'>Editar Cadenas:$mname</a>");
		
	}
	function missing_tables(){
		$t = $this->table_list();
		$t2=array();
		foreach($t as $row){
			foreach($row as $cell){
				$t2[$cell]=$cell;
			}
		}
		$r2 = $this->get_valid_modules();
		$r=array();
		foreach($r2 as $m=>$v){
			$d = basename($v,".controller.php");
			$r[$d]=$d;
		}
		
		$s2 = $this->get_shared_modules();
		$s=array();
		foreach($s2 as $m=>$v){
			$d = basename($v,".controller.php");
			$s[$d]=$d;
		}
		
		$missing=array();
		foreach($t2 as $table){
			if(!isset($r[$table]) && !isset($s[$table])){
				$missing[$table]=$table;
			}
		}
		return($missing);
	}
	function unlink2($f){
		if(file_exists($f)){
			unlink($f);
		}else{
			$this->msg('imposible borrar:'.$f);
		}
	}
	function rmdir2($f){
		if(file_exists($f)){
			rmdir($f);
		}else{
			$this->msg('imposible borrar directorio:'.$f.'. vacio?');
		}
	}
	function rename_in_file($file_name,$old_str,$new_str){
		$this->file_write($new_name,str_replace($old_str,$new_str,file_get_contents($new_name)),1);
	}

	function rename_file($old_name,$new_name,$old_str,$new_str){
		if(file_exists($new_name)){
			$this->msg("El archivo:$new_name ya existe, tiene:".filesize($new_name).' Bytes.');
		}else{
			if(file_exists($old_name)){
				if(rename($old_name,$new_name)){
					$this->file_write($new_name,str_replace($old_str,$new_str,file_get_contents($new_name)),1);
					#$this->msg($new_name." patched OK");
				}else{
					$this->msg("Imposible Renombrar de $old_name a $new_name");
				}
			}else{
				$this->msg($old_name." NO EXISTE.");
			}
		}
	}
	/** 
	 * rename a table and (imrpved for hideous CRM field names imported from as400) 
	 * */
	function ac_rename_table(){
		global $config;
		$this->menu();
		$t = $this->remove_strange_chars(escapeshellcmd($_GET['table']));
		if(!file_exists('./controller/'.$t.'.controller.php')){
			$this->msg("el modulo no existe.");
			return("");
		}
		$m=$this->load_file($t);
		if(isset($_GET["Nombre"])){
			if($_GET["Nombre"] == $t){
				$this->msg("No hubo cambios.");
			}else{
				$new_name = $this->remove_strange_chars(escapeshellcmd($_GET['Nombre']));
				echo("Renombrando:$new_name<br/>");
				//OLD DEPRECATED: $alter_sql = "ALTER TABLE ".$t." RENAME TO ".$new_name;
				$alter_sql = $this->alter_rename_table($t,$new_name);

				$this->log_query_forever('./install/patch.sql',$alter_sql);
	
				$fixes = array(
					'./model/'.$t.'.model.php'		=>'./model/'.$new_name.'.model.php',
					'./view/'.$t.'.view.php'		=>'./view/'.$new_name.'.view.php',
					'./controller/'.$t.'.controller.php'	=>'./controller/'.$new_name.'.controller.php',
					'./i18n/ES/'.$t.'.i18n.php'		=>'./i18n/ES/'.$new_name.'.i18n.php',
					'./rel/'.$t.'.rel.php'			=>'./rel/'.$new_name.'.rel.php',
					);
				foreach($fixes as $k=>$v){
					$this->rename_file($k,$v,$t,$new_name);
				}
			}
			//rename MODEL:
		}else{
			$f = new form();
			$f->set_title("Renombrar Tabla");
			$f->add_separator($t);
			$f->add_text_field('Nombre',$t);
			$f->add_hidden_field('table',$t);
			$f->add_hidden_field('mod',$this->program_name());
			$f->add_submit_button(array("action"=>$this->current_action,'label'=>'Renombrar'));
			$f->shtml();
			$this->show_dependencies($t);
		}
	}
	function show_dependencies($table_name){
		$r2 = $this->get_valid_modules();
		$r=array();
		foreach($r2 as $m=>$v){
			$d = basename($v,".controller.php");
			$r[$d]=$d;
		}
		$deps=array();
		foreach($r as $k=>$v){
			$m = $this->load_file($v);
			if(isset($m->fields[$table_name."_id"])){
				$deps[]=array(
					'    '=>"<a href='?mod=edit&ac=change_field&field=".$table_name."_id"."&table=".$m->get_table_name()."'>RENAME FIELD</a>",
					'TABLE'=>$m->get_table_name(),
					'FIELD'=>$table_name."_id"
				);
			}
		}		
		$this->e_table($deps,'none',array('style'=>'list','title'=>'Dependencies.'));
	}
	function ac_drop_table(){
		global $config;
		$this->menu();
		$t = $this->remove_strange_chars(escapeshellcmd($_GET['table']));
		$m=$this->load_file($t);
		$sql1 = 'drop table '.$m->get_table_name();//if exists 
		$this->sql($sql1);

		$this->log_query_forever('./install/patch.sql',$sql1);

		$privs = $this->q2op('select id,id as name from '.$config['table_prefix'].'privilege where action=\''.$m->program_name().'/all*\'');
		foreach($privs as $id){
			$this->sql('delete from '.$config['table_prefix'].'role2priv where privilege_id = \''.$id.'\'');
			$this->sql('delete from '.$config['table_prefix'].'privilege where id = \''.$id.'\'');
		}

		$this->unlink2('./controller/'.$m->program_name().'.controller.php');
		$this->unlink2('./i18n/'.std_get_language().'/'.$m->program_name().'.i18n.php');
		$this->unlink2('./model/'.$m->program_name().'.model.php');
		$a = glob('./view/'.$m->program_name().'/*.view.php');
		if(is_array($a)){
			foreach($a as $f){
				$this->unlink2($f);
			}
		}
		$this->rmdir2('./view/'.$m->program_name());
		$this->unlink2('./rel/'.$m->program_name().'.rel.php');
		$this->man_nav();
		$this->flush_privileges();
	}
	function ac_denormalize(){
		include(INCLUDE_DIR.'denormalizer.php');
		$r = new denormalizer();
		$d = $this->load_file($this->remove_strange_chars(escapeshellcmd($_GET['module_name'])));
		$r->denormalize($d);
	}
	function ac_hide_field(){
		$this->man_nav();
		$m = $this->load_file($this->remove_strange_chars(escapeshellcmd($_GET['table'])));
		$field_name = $this->remove_strange_chars(escapeshellcmd($_GET['field']));
		$m->fields[$field_name]["type"]='hidden';
		$m->fields[$field_name]["default_display"]=$_GET["hh"];

		$this->array2file($m->fields,'model','./model/'.$m->program_name().'.model.php',"std_fields['".$m->program_name()."']");
	}
	function ac_make_required_field(){
		$this->man_nav();
		$m = $this->load_file($this->remove_strange_chars(escapeshellcmd($_GET['table'])));
		$field_name = $this->remove_strange_chars(escapeshellcmd($_GET['field']));
		$m->fields[$field_name]["required"]=1;
		$this->array2file($m->fields,'model','./model/'.$m->program_name().'.model.php',"std_fields['".$m->program_name()."']");
		
	}
	function write_on_model($m,$a){
		$this->array2file($a,'model','./model/'.$m.'.model.php',"std_fields['".$m."']");	
	}
	function show_view_list(){
		global $main;
		$mn = $this->remove_strange_chars($_GET["module"]);
		$m = $this->load_file($mn);
		$main_views = $this->aa($main->get_view_list());
		$views = $this->aa($m->get_view_list());
		#p2($views);
		$t = array(
			array(
				'name'=>'Agregar',
				'url'=>'?mod=view&ac=vgen&module='.$mn,
				'icon'=>'add',
				)
			);
		foreach($views as $v){
			$view_object=$m->load_view($mn,$v);
			$is_shared = (int)isset($main_views[$v]);
			if(!$is_shared){
				if(!file_exists("./view/".$mn."/".$v.'.view.php')){
					$is_broken=1;
				}else{
					$is_broken=0;
				}
				$t[]=array(
					'name'=>$v,
					'url'=>'?mod=edit&ac=show_views&module='.$mn.'&view_name='.$v,
					'icon'=>"view_".$view_object['type'],
					#'BROKEN'=>$is_broken,
				);
			}
		}
		echo("<br/>");
		$this->e_table2($t,'none',array('border'=>0,'style'=>'list','title'=>'Vistas en: '.$mn));
		#$this->shadow_end();	
	}
	function ac_show_views(){
		$this->man_nav();
		if(isset($_GET["view_name"])){
			echo("<br/>");
			$this->show_view_editor($_GET["view_name"]);
		}else{
			$this->show_view_list();
		}
	}
	function ac_drop_view(){
		$this->man_nav();
		$mname = escapeshellcmd($_GET["module"]);
		$vn = escapeshellcmd($_GET["view_name"]);
		$vf = './view/'.$mname.'/'.$vn.'.view.php';
		$b2l_link = "<br/><a href='?mod=edit&ac=show_views&module=".$_GET["module"]."'>Volver a la lista de Vistas</a>";
		if(file_exists($vf)){
			unlink($vf);
			$q = $this->q2obj("SELECT id FROM privilege WHERE action='$mname/view:$vn'");
			if(count($q)==1){
				$priv_id = $q[0]['id'];
				$this->sql("DELETE FROM privilege where id = '$priv_id'");
				$this->sql("DELETE FROM role2priv where privilege_id = '$priv_id'");
				$this->msg("Se borraron ".$this->affected()." elementos del men\FA, uno por cada ROL.");
				


			}elseif(count($q)>1){
				$this->msg("ERROR: La vista tiene m\E1s de un privilegio asosiado.");
			
			}elseif(count($q)==0){
				$this->msg("La vista no ten\EDa un privilegio asosiado.");
			}


			$this->msg(escapeshellcmd($_GET["view_name"])." ahora es parte del pasado.".$b2l_link);

		}else{
			$this->msg("la vista (ya?) no existe/no es editable".$b2l_link);
		}
		$this->rebuild_privileges();

	}
	function show_view_editor(){
		$m = $this->load_file($this->remove_strange_chars(escapeshellcmd($_GET["module"])));
		$v =$this->remove_strange_chars(escapeshellcmd( $_GET["view_name"]));
		$view = $m->load_view($m->program_name(),$v);
		echo("<h1>" . $v ."</h1>");
		$this->privilege_manager->add_privilege(array(
			'role_name'=>$this->get_role_name(),
			'privilege_name'=>$view["title"],
			'action'=>$m->program_name().'/view:'.$v,
		));
		echo("<a href='?mod=".$m->program_name()."&ac=view:$v'>Ejecutar</a>");
		echo("<br/><br/>");
		echo("<a style='color:red' href='?mod=edit&ac=drop_view&module=".$m->program_name()."&view_name=".$v."'>BORRAR</a>");

		p2($view);

	}
	/** 
	 * shows a/b/c conveniently
	 * */
	function man_nav(){
		echo("<a href='?'>Men&uacute;</a> / ");
		if(isset($_GET["module"])||isset($_GET["edit_module"])){
			echo("<a href='?mod=edit&ac=db_manager'>Admin</a>");
		}
		if(isset($_GET["module"])){
			echo(" / <a href='?mod=edit&ac=db_manager&edit_module=".$_GET["module"]."'>".$_GET["module"]."</a>");
		}
		if(isset($_GET["view_name"])){
			echo(" / <a href='?mod=edit&ac=show_views&module=".$_GET["module"]."'>Vistas</a>");
		}
	}
	function show_table_manager($mod){
			global $main;
	
		$this->privilege_manager->add_privilege(array(
			'role_name'=>$this->get_role_name(),
			'privilege_name'=>'',
			'action'=>$mod.'/load_csv',
		));
		$this->privilege_manager->add_privilege(array(
			'role_name'=>$this->get_role_name(),
			'privilege_name'=>'',
			'action'=>$mod.'/all_load_from_excel',
		));
			
		$this->privilege_manager->add_privilege(array(
			'role_name'=>$this->get_role_name(),
			'privilege_name'=>'',
			'action'=>$mod.'/load_from_excel',
		));

		if(isset($main)){
			$m= $main->load_file($mod);
		}else{
			$m= $this->load_file($mod);
		}
		
		echo("<a name='$mod'></a><div style='text-align:right;font-size:44pt'>$mod</div>");
		echo("<a href='?mod=edit&ac=add_field&module=$mod'>Agregar Campo</a>");
		#echo($this->icon_link("?mod=edit&ac=add_field&module=$mod",'add_field'));
		echo(" | <a href='?mod=view&ac=copy&module=$mod'>Copiar Vista</a>");
		#echo(" | <a href='?mod=view&ac=vgen&module=$mod'>Crear Vista</a>");
		echo(" | <a href='?mod=edit&ac=show_views&module=$mod'>Vistas</a>");
		echo(" | <a href='?mod=event&ac=all_event_new&module=$mod'>Agregar Evento</a>");
		echo(" | <a href='?mod=nav&ac=tables&table=".$m->get_table_name()."&ac2=lista'>Ver Registros</a> ");
		echo(" | <a href='?mod=nav&ac=tables&table=".$m->get_table_name()."&ac2=descripcion'>Estructura</a> ");
		echo(" | <a href='?mod=".$mod."&ac=all_load_from_excel'>Cargar Desde Excel</a> ");
		echo(" | <a href='?mod=".$mod."&ac=load_csv'>Cargar CSV</a> ");
		echo(" | <a href='?mod=edit&ac=rename_table&table=".$mod."'>Renombrar</a> ");
		echo(" | <a href='?mod=edit&ac=denormalize&module_name=$mod'>Denormalizar</a> ");
		echo(" | <a href='?mod=".$mod."&ac=all_load_from_excel&module_name=$mod'>Cargar Excel</a> ");
		
		echo(" | <a href='?filename=./i18n/ES/".$mod.".i18n.php&step=2&mod=".$this->program_name()."&ac=ed'>Editar Textos</a>");
		echo(" | <a href='?mod=edit&ac=dependency&table=".$mod."'>Dependencias</a> ");

		echo(" &nbsp;&nbsp;&nbsp;&nbsp; <a href='?mod=edit&ac=drop_table&table=".$mod."'>Borrar</a> ");
		$t = array();
		$c = 0;
		$fields = $m->describe($m->get_table_name());
		foreach($m->fields as $fn=>$field){
			$r = array();
			$hh = 0;
			$hh2 = '-';
			if(isset($field["default_display"]) && $field["default_display"] == 0){
				$hh = 1;
				$hh2 = '+';
			}
			$fn2 = strtolower(trim(str_replace(array('.',' ','\t','#',':','\F1','?'),array('_','_','_','nn','','nn',''),$m->fi($fn))));
			$r['Actions'] = ("<a href='?mod=edit&ac=drop_field&field=$mod/$fn'>[X]</a>
				&nbsp;&nbsp;<a href='?mod=edit&ac=hide_field&table=$mod&field=$fn&hh=$hh'>[$hh2]</a>
				&nbsp;&nbsp;<a href='?mod=edit&ac=change_field&table=$mod&field=$fn&field_name=".$fn2."'>[&lt;&lt;]</a>
				&nbsp;&nbsp;<a href='?mod=edit&ac=make_required_field&table=$mod&field=$fn&field_name=".$fn2."'>[+RQ]</a>
				
				");
			$r["Name"] = ("<a href='?mod=edit&ac=change_field&field=$fn&table=$mod'>$fn</a>");
			$r["Title"] = $m->fi($fn);
			$r["DBName"] = "<div style='background-color:".($fields[$c]["Field"]!=$fn?'red':'transparent')."'>".$fields[$c]["Field"]."</font>";
			$r["DBType"] = $fields[$c]["Type"];
			$r["Type"] = $m->fields[$fn]["type"];
			$r["Foreign"] =( isset($m->fields[$fn]["foreign"])?$m->fields[$fn]["foreign"]:'&nbsp;');
			$r["DD"] = $m->fields[$fn]["default_display"];
			$r["RQ"] = $m->fields[$fn]["required"];

			$t[]=$r;
			$c++;
		}
		$this->e_table($t,'none',array('style'=>'list'));
	}
	function db_man_privs(){
		$util = $this->load_file('util');

		foreach(array('show_views','create_table','add_field','drop_field','ed','drop_table','change_field','denormalize','table2mod') as $dx){
			$this->privilege_manager->ap($dx);
		}
		foreach(array(
			'util/sql_console',
			'edit',
			'edit/cf',
			'view/copy',
			'nav/tables',
			'view/add',
			'util/install_manager',
			'doc','util/diagnose',
			'diagnose/diagnose',
			'util/rebuild_privileges',
			'edit/rename_table',
			'edit/hide_field',
			'edit/make_required_field',
			'view/vgen',
			'edit/drop_view',
			'edit/table2mod',
			'drill_down_report/all*',
			'chart_type/all*',
			'data_field/all*',
			'dimension_field/all*',
			'columna/all*',
			'informe/all*',
			'fuente/all*',
			'campo_de_agrupamiento/all*',
			'userfilter/all*',
			'userquery/all*',
			'edit/reports_menu',
			'edit/sqldump',
			'edit/field_bulk_fix',
			'edit/dependency',
			) as $dx){

			$this->privilege_manager->add_privilege(array(
				'role_name'=>$this->get_role_name(),
				'privilege_name'=>$dx,
				'action'=>$dx,
			));
		}	
	}
	function ac_reports_menu(){
		$this->man_nav();
		$this->db_man_privs();
		$this->shadow_start();
		$mm = array(
			'drill_down_report',
			'data_field',
			'dimension_field',
			'chart_type',
			'informe',
			'columna',
			'campo_de_agrupamiento',
			'fuente',
			'userfilter',
			'userquery');
		$t=array();
		foreach($mm as $mname){
			$t[]=array(
				'url'=>'?mod='.$mname.'&ac=all',
				'name'=>$mname,
				'icon'=>'table',
			);
		}
		
		$this->e_table2($t);

		$this->shadow_end();
		
	}
	function type_translate($t){
		$t = preg_replace('/^\s*text\s*$/','varchar(1024)',$t);
		$t = preg_replace('/int\s*\((\d+)\)/','numeric(\1,0)',$t);
		$t1=$t;
		

		if(preg_match('/char/',$t1)){
			$t1=$t1.' COLLATE SQL_Latin1_General_CP1_CI_AS ';
		}
		

		if($t=='mediumtext'){
			$t1='text';
		}
		if($t1=='text'){
			$t1='varchar(8000) COLLATE SQL_Latin1_General_CP1_CI_AS ';
		}
		return($t1);

	}
	function sqldump_table($table){
		$f = $this->describe($table);
		$dx="create table $table (";
		$dx.="\n\tid numeric(18, 0) IDENTITY (1, 1) NOT NULL  ,";
		
		$a=array();
		foreach($f as $field){
			if($field["Field"]!='id'){
				$a[]="\n\t".$field["Field"]." ".$this->type_translate($field['Type'])." ".(($field['Null']=='NO')?' NOT ':'')." null ".(($field["Default"]!='')?"DEFAULT '".$field["Default"]."'":"");
			}

		}
		$dx.=implode(",",$a);
		$dx.="\n)\n GO \n";
		$q = $this->q2obj("select * from ".$table." ");
		$dx.="\nSET IDENTITY_INSERT $table ON \n GO \n";
			

		foreach($q as $k=>$v){
			$d = $v;
			foreach($v as $k1=>$v1){
				$v[$k1]=str_replace("'","''",$v1);
			}
			$dx.="\nINSERT INTO $table (".implode(',',array_keys($v)).") VALUES('".implode("','",$v)."')";
		}
		$dx.="\nSET IDENTITY_INSERT $table OFF\n GO \n";
	
		return($dx);
	}
	function ac_sqldump(){
		global $config ;
		$this->man_nav();
		$dx="";
		if(isset($_GET["table_name"])){
			$dx.="\n\n".$this->sqldump_table($_GET["table_name"]);
		}else{
			foreach($this->table_list() as $t){
				$table = $t['Tables_in_'.$config["database_name"]];
				$dx.="<li> <a href='?mod=edit&ac=sqldump&table_name=$table'>$table</a>";
				//$dx.="\n\n".$this->sqldump_table($t['Tables_in_'.$config["database_name"]]);
			}
		}
		echo("<pre>".$dx."</pre>");
	}
	function ac_db_manager(){
		$this->man_nav();
		$this->db_man_privs();

		$this->shadow_start();

		$t=array();
		$t[]=array(
			'url'=>'?mod=edit&ac=create_table',
			'name'=>'Nueva Tabla',
			'icon'=>'add',
		);

		$t[]=array(
			'url'=>'?mod=util&ac=rebuild_privileges',
			'name'=>'Re-Cargar Privilegios',
			'icon'=>'refresh',
		);
		$t[]=array(
			'url'=>'?mod=util&ac=diagnose',
			'name'=>'Integridad de los Datos',
			'icon'=>'warning',
		);
		$t[]=array(
			'url'=>'?mod=diagnose&ac=diagnose',
			'name'=>'Integridad de las Relaciones',
			'icon'=>'warning',
		);
		
		$t[]=array(
			'url'=>'?mod=nav&ac=tables',
			'name'=>'Navegador',
			'icon'=>'search',
		);
		

		$t[]=array(
			'url'=>'?mod=doc',
			'name'=>'Documentos',
			'icon'=>'book',
		);

		$t[]=array(
			'url'=>'?mod=util&ac=install_manager',
			'name'=>'Instaladores',
			'icon'=>'box',
		);
		/*
		$t[]=array(
			'url'=>'?mod=view&ac=add',
			'name'=>'Agregar Vista',
			'icon'=>'tool',
		);
		 */
		$t[]=array(
			'url'=>'?mod=edit&ac=cf',
			'name'=>'Configurar',
			'icon'=>'tool',
		);
		/*
		$t[]=array(
			'url'=>'?mod=edit',
			'name'=>'Cadenas',
			'icon'=>'i18n',
		);
		 */
		$t[]=array(
			'url'=>'?mod=view&ac=vgen',
			'name'=>'Crear Vista',
			'icon'=>'blank_page',
		);
		$t[]=array(
			'url'=>'?mod=util&ac=sql_console',
			'name'=>'Consola SQL',
			'icon'=>'console',
		);
		$t[]=array(
			'url'=>'?mod=edit&ac=table2mod',
			'name'=>'Modulo desde Tabla',
			'icon'=>'tool',
		);
		$t[]=array(
			'url'=>'?mod=edit&ac=reports_menu',
			'name'=>'Reportes',
			'icon'=>'book',
		);
		
		$t[]=array(
			'url'=>'?mod=edit&ac=sqldump',
			'name'=>'Sql Dump',
			'icon'=>'book',
		);
		$t[]=array(
			'url'=>'?mod=edit&ac=field_bulk_fix',
			'name'=>'Bulk Fix',
			'icon'=>'tool',
		);
		
		
		$a=array();		
		foreach($this->get_mods() as $mod){
			#$mm = $this->load_file($mod);
			if(file_exists("./media/icons/".$mod.".png")){
				$i = $mod;
			}else{
				$i = 'table';
			}
			$a[]=array(
				'url'=>'?ac=db_manager&mod=edit&edit_module='.$mod,
				'name'=>str_replace("_"," ",$mod),
				'icon'=>$i,
			);
		}
		
		
		if(isset($_GET['edit_module'])){
			$this->show_table_manager($_GET['edit_module']);
		}else{
			$this->e_table2($t);
			#echo("<br/><div style='clear:both'>&nbsp;</div><br/>");
			$this->e_table2($a);
		}

		$this->shadow_end();
	}
	/** 
	 * array, old key, new key
	 * preserves order (very important)
	 * */
	function overwrite_keyname($a,$k,$nk){
		$a2 = array();
		foreach($a as $ak=>$av){
			$kname = $ak;
			if($ak == $k){
				$kname = $nk;
			}
			$a2[$kname] = $av;
		}
		return($a2);
	}
	function get_sql_info($table,$field_name){
		$fields = $this->describe($table);
		foreach($fields as $field){
			if($field["Field"] == $field_name){
				return($field);
			}
		}
		$this->msg("Field: <b>".$field_name ." </b> does not exist in table:".$table);
		return(array());
	
	}
	function ac_change_field(){//change_field
		global $i18n,$main;
		$this->menu();
		//alter table <table_name> change <old_column_name> <new_column_name> null;
		$t = $this->remove_strange_chars(escapeshellcmd($_GET['table']));
		$c = $this->remove_strange_chars(escapeshellcmd($_GET['field']));
		$new_name = $this->remove_strange_chars(escapeshellcmd($_GET['Nombre']));
		$m=$this->load_file($t);
		$fields = $this->describe($m->get_table_name());
	
		if(isset($_GET["Nombre"])){
			$field_info = array();
			$found = 0;
			foreach($fields as $field){
				if($field["Field"] == $c){
					$field_info=$field;
					$found= 1;
					break;
				}
			}
			if($found == 0){
				$this->msg("el campo:".$c." no existe en la base de datos");
				return("");
			}
			//if($this->check_if_field_exists($t,$new_name)==1){}

			//I18n

			$i18n[$m->program_name()]['fields'] = $this->overwrite_keyname($i18n[$m->program_name()]['fields'],$c,$new_name);
			$i18n[$m->program_name()]['fields'] = $this->overwrite_keyname($i18n[$m->program_name()]['fields'],"help_".$c,"help_".$new_name);

			$i18n[$m->program_name()]['fields'][$new_name]=$_GET["Etiqueta"];
			$i18n[$m->program_name()]['fields']["help_".$new_name]=$_GET["help"];

			$this->array2file($i18n[$m->program_name()],
				'','./i18n/'.std_get_language().'/'.$m->program_name().".i18n.php","i18n['".$m->program_name()."']");

			//I18n

			$m->fields = $this->overwrite_keyname($m->fields,$c,$new_name);
			$m->fields[$new_name]['name']=$new_name;
			$m->fields[$new_name]['type']=$_GET["type"];
			if($_GET["Longitud"]!=''){
				$m->fields[$new_name]['size']=$_GET["Longitud"];
			}else{
				unset($m->fields[$new_name]['size']);
			}
			if($_GET["foreign"]!='foreign_none'){
				$fm = $this->remove_strange_chars(escapeshellcmd($_GET["foreign"]));
				$m3 = $this->load_file($fm);

				$m->fields[$new_name]['foreign']=$fm.'.'.$m3->ifield;
			}else{
				unset($m->fields[$new_name]['foreign']);
			}

			$this->write_on_model($m->program_name(),$m->fields);


			$this->alter_field_name($t,$c,$new_name,$_GET["TipoSql"],$field_info["Null"],$field_info["Default"]);

			//OLD DEPRECATED:
			//$sql = 'ALTER TABLE '.$t.' CHANGE '.$c.' '.$new_name.' '.$_GET["TipoSql"].' '.(($field_info["Null"]=='YES')?' ':' NOT NULL ').($field_info["Default"]!=''?' DEFAULT \''.$field_info["Default"]."'":'');

			$this->log_query_forever('./install/patch.sql',$sql);	
			$this->msg("<br/><a href='?ac=db_manager&mod=edit&edit_module=".$t."'>Volver</a>");
			
		}else{
			$i = $this->get_sql_info($m->get_table_name(),$c);
			$f = new form();
			$f->set_title("Renombrar Campo");
			$f->add_separator($t.'/'.$c);
			$f->add_field(array('value'=>$c,'name'=>'Nombre','type'=>'text','events'=>array('change'=>'document.getElementById("Etiqueta").value=humanize(this.value)')));
			$f->add_field(array('name'=>'type','type'=>'list','i18n_text'=>'Tipo','i18n_help'=>'','options'=>$this->get_field_types_list(),'value'=>$m->fields[$c]['type']));
			$f->add_text_field('TipoSql',$i["Type"]);
			$f->add_field(array(
				'i18n_text'=>'Longitud',
				'i18n_help'=>'Use a number to limit the field, type TEXT must be used for the effect to be visible, this is just cosmetic, wont affect or limit input, also leav blank if you want NOTHING to be set.',
				'name'=>'Longitud',
				'value'=>(isset($m->fields[$c]['size'])?$m->fields[$c]['size']:''),'size'=>2)
			);
			$f->add_text_field('Etiqueta',$m->fi($c));
			$f->add_field(array('name'=>'help','type'=>'textarea','i18n_text'=>'Ayuda','value'=>$m->fh($c),'i18n_help'=>''));

			//$i18n[$m->program_name()]['fields']["help_".$c]
			$f->add_hidden_field('table',$t);
			$f->add_hidden_field('field',$c);
			$cf = array();
			
			$cf = $main->get_local_modules();
			$cf2 = array();
			$cf2['foreign_none']='No Aplica';
			
			foreach($cf as $c){
				$mn = basename($c,'.controller.php');
				$m9 = $main->load_file($mn);
				$cf2[$mn] = $mn.'.'.$m9->ifield;
			}
			if(isset($m->fields[$c]["foreign"])){
				$c3 = explode(".",$m->fields[$c]["foreign"]);
				$value1 = $c3[0];
			}else{
				$value1 = 'foreign_none';
			}

			$f->add_field(array('name'=>'foreign','type'=>'list','i18n_text'=>'Campo Foraneo','value'=>$value1,'i18n_help'=>'','options'=>$cf2));
			$f->add_hidden_field('mod',$this->program_name());
			$f->add_submit_button(array("action"=>$this->current_action,'label'=>'Renombrar Campo'));
			$f->shtml();
			$this->print_edit_js_functions();
		}
		
	}
	function ac_drop_field(){
		global $i18n;

		if(!isset($_GET['field'])){
			$this->menu();
			$f=new form();
			$a=$this->get_local_modules();
			$op=array();
			foreach($a as $k=>$mod){
				$mname=basename($mod,'.controller.php');
				$m=$this->load_file($mname);
				//$op[$m->program_name()]=$m->get_i18n_text('table_plural','');
				foreach($m->fields as $k1=>$field){
					$op[$mname.'/'.$field['name']]=$m->i18n('table_plural'). ' / '. $m->fi($field['name']);
				}
			}
			$f->add_field(array('name'=>'field','options'=>$op,'type'=>'list'));
			$f->add_hidden_field('mod',$this->program_name());
			$f->add_submit_button(array('label'=>'ok','action'=>'drop_field'));
			$f->strings = $i18n['edit']['drop_field'];
			$f->shtml();
		}else{
			//echo('field dies');
			$this->menu();
			list($mod,$field)=explode('/',escapeshellcmd($this->remove_strange_chars($_GET['field'])));

			$d=$this->load_file($mod);
			if(!$this->module_has_field($d->get_table_name(),$field)){
				$this->msg('no such field '.$field.' in mod '.$mod);
				return('');
			}
			$alter_sql = $this->alter_drop_field($d->get_table_name(),$field);
			
			$this->log_query_forever('./install/patch.sql',$alter_sql);

			$this->sql($alter_sql);
			unset($i18n[$d->program_name()]['fields'][$field]);
			unset($i18n[$d->program_name()]['fields']['help_'.$field]);
			unset($d->fields[$field]);
			///p2($i18n[$d->program_name()]);
			$this->array2file(
				$i18n[$d->program_name()],
				'','./i18n/'.std_get_language().'/'.$d->program_name().".i18n.php","i18n['".$d->program_name()."']");
			$this->array2file(
				$d->fields,
				'','./model/'.$d->program_name().".model.php","std_fields['".$d->program_name()."']");

			$d1=$this->load_file('util');
			//check for broken Views.
			$d1->show_menu=0;
			echo("<a href='?mod=util&ac=diagnose'>click aqui para verificar errores (recomendado)</a>");
		}
	}
	function module_has_field($module,$field_name){
		foreach($this->describe($module) as $field){
			if($field['Field']==$field_name){
				return(TRUE);
			}
		}
		return(FALSE);
	}

/**
 * turns an sql field_id into a Field id, replaces _ with space, and capitalizes.
 * from .GEN
 * */
	function humanize($sql_field){

		$s=str_replace('_',' ',$sql_field);
		$s[0]=strtoupper($s[0]);
		return($s);
	}
	/** @todo field grif with repeat=1 */
	function ac_create_table(){
		global $config;

		$this->menu();
		$name = str_replace(" ","_",strtolower($this->remove_strange_chars(escapeshellcmd($_GET['name']))));
		$ifield =str_replace(" ","_",strtolower($this->remove_strange_chars(escapeshellcmd($_GET['ifield']))));
		$plural =$this->remove_strange_chars(escapeshellcmd($_GET['plural']));


		if(isset($_GET['name'])){
			if(strpos($name,"2")!==FALSE){
				//@todo add rels auto using hbm
				$parts = explode("2",$name);
				$f1 =$parts[0];
				$f2 =$parts[1];
				$mf1=$this->load_file($f1);
				$mf2=$this->load_file($f2);
				$label1 = $mf1->i18n('table_title');//user
				$label2 = $mf2->i18n('table_title');//role
				$labelp = $mf1->i18n('table_plural');//users

				$ifield='id';
				$field1 =$f1.'_id';
				$field2 =$f2.'_id';

				$model=array(
					'id'=>array('name'=>'id','type'=>'label'),
					$field1=>array('name'=>$field1),
					$field2=>array('name'=>$field2),
				);
				$plural = $labelp." por ".$label2;
				//WARNING: if you edit this, edit also the templates on projects/.gen !!
				$i18n=array(
					'list_all'=>'<br><br>[#_field_info]',
					'table_title'=>$label1." por ".$label2,
					'table_plural'=>$labelp." por ".$label2,
					'table_gender'=>'M',
					'new_table_title'=>'Nuevo '.$label1." por ".$label2,
					'edit_table_title'=>'Editar '.$label1." por ".$label2,
					'fields'=>array(
						'id'=>'Id',
						'help_id'=>'Id',
						$field1=>$label1,
						'help_'.$field1=>' ',
						$field2=>$label2,
						'help_'.$field2=>' ',
					)
				);
				
				$this->add_rel($f1,'hbm',$f2,$name);
				
			}else{
			
				$model=array(
					'id'=>array('name'=>'id','type'=>'label'),
					$ifield=>array('name'=>$ifield),
				);

				//WARNING: if you edit this, edit also the templates on projects/.gen !!
				$i18n=array(
					'list_all'=>'<br><br>[#_field_info]',
					'table_title'=>$this->humanize($name),
					'table_plural'=>$this->humanize($plural),
					'table_gender'=>'M',
					'new_table_title'=>'Nuevo '.$this->humanize($name),
					'edit_table_title'=>'Editar '.$this->humanize($name),
					'fields'=>array(
						'id'=>'Id',
						'help_id'=>'Id',
						$ifield=>$this->humanize($ifield),
						'help_'.$ifield=>$this->humanize($ifield),
					)
				);
			}
			$controller="<?php #".date('Y-m-d H:i:s')."\nclass ${name}_model extends ".$this->app_name()."_base{\n\tfunction ${name}_model(){\n\t\$this->".$this->app_name()."_base();\n\t}\n\tvar \$ifield='$ifield';\n\tvar \$id='id';\n\tvar \$table='$name';\n}\n?".">";
			$controller_filename='./controller/'.$name.'.controller.php';
			if(file_exists($controller_filename)){
				$this->msg('cannot overwrite controller!');
			}
			$view=array(
				'edit_custom'=>array(
					'title'=>'edit_table_title',
					'type'=>'record',
					'actions'=>array('all_b2l','all_edit2','all_delete'),
					'fields'=>$model,
				),
				'list_custom'=>array(
					'help'=>'list_all',
					'title'=>'table_plural',
					'type'=>'table',
					'side_actions'=>array('all_edit','all_delete'),
					'down_actions'=>array('all_delete_selected','all_xls','all_new'),
					'fields'=>$model,
				),
			);
			$rel=array('has'=>array(),'hbm'=>array(),'belongs_to'=>array());

			$this->array2file($rel,'','./rel/'.$name.'.rel.php',"std_rel['$name']");
			$this->array2file($view,'','./view/'.$name.'.view.php',"std_views['$name']");
			$this->array2file($model,'','./model/'.$name.'.model.php',"std_fields['$name']");
			if(!file_exists('./view/'.$name)){
				mkdir('./view/'.$name);
			}

			$this->array2file($i18n,'','./i18n/'.std_get_language().'/'.$name.'.i18n.php',"i18n['$name']");
			$this->file_write($controller_filename,$controller,1,1);
			//wow
			$m=$this->load_file($name);
			$m->create_table();
			$s2 = 'INSERT INTO '.$config['table_prefix'].'privilege VALUES(0,\''.$plural.'\',\''.$name.'/all*\')';
			$this->sql($s2);
			$this->log_query_forever('./install/patch.sql',$s2);
			$privilege_id =$this->last_id();
			if(is_array($_GET['roles'])){
				foreach($_GET['roles'] as $role){
					$s3 = 'INSERT INTO '.$config['table_prefix'].'role2priv VALUES(0,\''.$this->remove_strange_chars($role).'\',\''.$privilege_id.'/all*\')';
					$this->sql($s3);
					$this->log_query_forever('./install/patch.sql',$s3);

				}
			}
			$this->flush_privileges();
			return('');
		}

		$f = new form();
		$f->strings=array(
			'_form_title'=>'Nueva Tabla',
			'name'=>'Nombre',
			'help_name'=>'Nombre: a_b',
			'roles'=>'Roles',
			'help_roles'=>'Roles',
			'plural'=>'Plural',
			'help_plural'=>'Plural: A B',
			'ifield'=>'Campo Descriptivo',
			'help_ifield'=>'Campo Descriptivo',
		);
		$f->add_text_field('name','');
		$f->add_text_field('plural','');
		$f->add_text_field('ifield','name');
		$f->add_field(array('name'=>'roles','type'=>'checklist','check_all'=>1,'options'=>$this->q2op('select id,name from '.$config['table_prefix'].'role','id','name')));
		$f->add_hidden_field('mod',$this->program_name());
		$f->add_submit_button(array('action'=>$this->current_action,'label'=>'OK'));
		$f->shtml();
	}
	function get_field_types_list(){
		return($this->aa(array('text','textarea','date','number','list','currency','boolean','label')));
	}
	function check_if_field_exists($table,$sqlname){
		foreach($this->describe($table) as $field){
			if($field['Field']==$sqlname){
				return(1);
			}
		}
		if($ok==0){
			return(0);
		}
	}
	function print_edit_js_functions(){
			echo("<script>;
			function auto_type(value){
				if(value.match(/_id$/)){
					document.getElementById('field_length').value=10;
					std_set_select_value('field_type','number');
					document.getElementById('field_default').value=1;
					document.getElementById('field_length').value=10;
					document.getElementById('field_name').value= humanize(value.substr(0,value.length-3));
		}
		}
		function humanize(dx){
			var a = dx.split('_');
			for(var i in a){
				if(a[i].length>2){
					a[i] = a[i][0].toUpperCase() + a[i].substr(1);
		}
		}
		return(a.join(' '));
		}
		;</script>;");
	
	}
	/**
	 * @todo drop fields alter table aviso drop die;
	 * @todo 6001 allow add foreign fields, with custom connections
	 * */
	function ac_add_field(){
		global $i18n,$main;
		$this->menu();
		if($_GET['field_name']==''){
			//Show Add Field Form
			$f=new form();
			$a=$this->get_local_modules();
			if(isset($_GET['module'])){
				$f->add_field(array('name'=>'module','type'=>'label','value'=>$_GET['module']));
			}else{
				$op=array();
				foreach($a as $k=>$mod){
					$mname=basename($mod,'.controller.php');
					$m=$main->load_file($mname);
					$op[$m->program_name()]=$m->get_i18n_text('table_plural','');
					//foreach($m->fields as $k1=>$field){
					//	$op[]=$mname.'/'.$field['name'];
					//}
				}

				$f->add_field(array('name'=>'module','options'=>$op,'type'=>'list','value'=>$_GET['module']));
			}
			$f->add_field(array('name'=>'field_sqlname','events'=>array('change'=>'document.getElementById("field_name").value=humanize(this.value);auto_type(this.value)')));
			$f->add_field(array('name'=>'field_type','options'=>$this->get_field_types_list(),'type'=>'list'));
			$f->add_text_field('field_length',100);
			$f->add_text_field('field_default');
			$f->add_separator();
			$f->add_text_field('field_name');
			$f->add_field(array('name'=>'behavior','type'=>'list','options'=>$this->aa($this->get_func_list('bh')),'value'=>'bh_std_none'));
			$f->add_textarea_field('field_help');
			$f->add_field(array('name'=>'field_options','type'=>'textarea','rows'=>'3'));
			$f->add_hidden_field('mod',$this->program_name());
			$f->add_submit_button(array('label'=>'ok','action'=>'add_field'));

			//	p2($i18n['edit']['add_field']);
			$f->strings = $i18n['edit']['add_field'];

			$f->shtml();
			$this->print_edit_js_functions();
		}else{
			//Add the Field

			$d=$main->load_file(escapeshellcmd(str_replace('/','',$_GET['module'])));
			$fname=$this->remove_strange_chars($_GET['field_name']);
			$help=$this->remove_strange_chars($_GET['field_help']);
			$sqlname=$this->remove_strange_chars($_GET['field_sqlname']);
			$ftype=$this->remove_strange_chars($_GET['field_type']);
			$fdefault=$this->remove_strange_chars($_GET['field_default']);
			$flen=$this->remove_strange_chars($_GET['field_length']);
			$flist=$this->remove_strange_chars($_GET['field_options']);
			$behavior=$this->remove_strange_chars($_GET['behavior']);
			if($ftype=='textarea'||$ftype=='date'){//or any other one that has no a(len) syntax
				$ft = $this->sql2type($ftype);
			}elseif($ftype=='list'){
				$field_option_list = $this->aa(explode(',',$flist));
				$ft = ' '.$this->sql2type($ftype).'(\''.implode('\',\'',explode(',',$flist)).'\')';
			}else{
				$ft = ' '.$this->sql2type($ftype).'('.$flen.')';
			}
			//check if field exists
			$ok=1;
			foreach($this->describe($d->get_table_name()) as $field){
				if($field['Field']==$sqlname){
					$ok=0;
				}
			}
			if($ok==0){
				$this->i_msg('field_exists');
				return(0);
			}
			$alter_sql='ALTER TABLE '.$d->get_table_name().' ADD '.$sqlname.' '.$ft.' DEFAULT \''.$fdefault.'\'';
			$this->log_query_forever('./install/patch.sql',$alter_sql);

			//add field to i18n
			$i18n[$d->program_name()]['fields'][$sqlname] = $fname;
			$i18n[$d->program_name()]['fields']['help_'.$sqlname] = $help;
			//add field to model

			$new_field=array(
				'name'=>$sqlname,
				'type'=>$ftype,
				'value'=>$fdefault
			);
			if($_GET['behavior']!='' && $_GET['behavior']!='bh_std_none' ){
				$new_field['behave']=preg_replace('/^bh_/','',$_GET['behavior']);
			}
			if($ftype =='list'){
				$new_field['options']=$field_option_list;
			}
			$d->fields[$sqlname]=$new_field;
			//	p2($i18n[$d->program_name()]);
			$s=basename($_GET["filename"],".i18n.php");
			$this->sql($alter_sql);
			//echo($alter_sql);
			$this->array2file(
				$i18n[$d->program_name()],
				'','./i18n/'.std_get_language().'/'.$d->program_name().".i18n.php","i18n['".$d->program_name()."']");
			$this->array2file(
				$d->fields,
				'','./model/'.$d->program_name().".model.php","std_fields['".$d->program_name()."']");

			if($this->is_id($sqlname)){

				//current_table now *belongs_to* : fmod
				$fmod = $this->id2mod($sqlname);
				$this->add_rel($fmod,'has',$d->program_name());
			}
			$this->msg("<br/><a href='?ac=db_manager&mod=edit&edit_module=".$d->program_name()."'>Volver</a><br/>".
			"<br/><a href='?mod=edit&ac=add_field&module=".$d->program_name()."'>Agregar Otro campo</a>");
			
		}
	}
		
	function inverse_rel($rel){
		$irel=array(
			'has'=>'belongs_to',
			'belongs_to'=>'has',
			'hbm'=>'hbm',
			);
		return($irel[$rel]);
	}
	/** 
	 * add a relation in module.rel to module2 of type TYPE.
		* */
	function add_rel($module,$type,$module2,$trough_hbm=''){
		global $std_rel;
		//add field to i18n
		$mm = $this->load_file($module);//LOAD REL

		$m3=$module2;
		$m4=$module;

		if($type=='hbm' && $trough_hbm!=''){
			$m3=$trough_hbm;
			$m4=$trough_hbm;
		
		}
		$std_rel[$module][$type][$module2] = $m3;
		$std_rel[$module2][$this->inverse_rel($type)][$module] = $m4;

		$this->array2file(
			$std_rel[$module],
			'','./rel/'.$module.".rel.php","std_rel['".$module."']");
		$this->array2file(
			$std_rel[$module2],
			'','./rel/'.$module2.".rel.php","std_rel['".$module2."']");
	}
	function log_query_forever($file,$query){
		if(file_exists($file)){
			$fc = file_get_contents($file);
		}else{
			$fc='';
		}
		$this->file_write($file,$fc."\n--".date('Y-m-d H:i')." ". ssid()." = ".ssname()."\n".$query.';',1,1);
		$this->log("STORED:".$query,'SQL');

	}
	/**
	 * create table sql
	 * @todo 6100 allow fields to set size, allow fields to have maxlength
	 * @see util/install_manager
	 * */
	function ac_csql(){
		$this->menu();
		if($_GET['module_name']==''){
			foreach($this->get_local_modules() as $mod){
				$s=$this->load_file(basename($mod,'.controller.php'));
				echo($this->make_link(array('mod'=>$this->program_name(),'ac'=>'csql','module_name'=>$s->program_name()) , $s->get_i18n_text('table_plural','')) );
				echo('<br/>');
			}
		}else{
			$s=$this->load_file(escapeshellcmd(str_replace('/','',$_GET['module_name'])));
			$sql='CREATE TABLE '.$s->program_name()."( ";
			foreach($s->fields as $f){
				$dx='';
				if($s->id == $f['name']){
					$dx=' not null auto_increment';
				}
				$sql.="\n\t".$f['name'].' '.$this->type2sql2($f['type']).$dx.' ,';
			}
			$sql.="\n\tprimary key(".$s->id.")\n)";
		}
		echo("<pre>".$sql);
	}

	/**
	 * WARNING: mysql compatible only
	 * includes size
	 * */
	function type2sql2($field_type){
		$a=array(
			'label'=>'int(10)',//this is odd
			'text'=>'VARCHAR(100)',
			'textarea'=>'TEXT',
			'number'=>'int(10)',
			'currency'=>'double',
			'list'=>'ENUM',
			'date'=>'datetime',
			'boolean'=>'int(1)',
			'label'=>'varchar(255)',
		);
		return($a[$field_type]);
	}

	/**
	 * */
	function sqlfield2type($field_type){
		$field_type=strtoupper($field_type);
		$field_type=explode("(",$field_type);
		$field_type=$field_type[0];
		$a=array(
			'VARCHAR'=>'text',
			'TEXT'=>'textarea',
			'INT'=>'number',
			'DOUBLE'=>'currency',
			'ENUM'=>'list',
			'DATETIME'=>'date',
		);
		return($a[$field_type]);

	}

	/**
	 * WARNING: mysql compatible only
	 *
	 * */
	function sql2type($field_type){
		$a=array(
			'text'=>'VARCHAR',
			'textarea'=>'TEXT',
			'number'=>'int',
			'currency'=>'double',
			'list'=>'ENUM',
			'date'=>'datetime',
			'boolean'=>'int',
			'label'=>'varchar',
		);
		return($a[$field_type]);

	}
	/** @todo 248762 test for start of string only??? */
	function is_inside_path($txt,$path){
		#id like to point out that this is brilliant.
		#now that i just ponted that out, id like to point out that this is a hack, dont try this at home.

		$regex="#".str_replace(array("*"),array("[^/]+"),$path)."#";
		#echo("<br/>regex:$regex on $txt gives: ".preg_match($regex,$txt));
		return(preg_match($regex,$txt));
	}
	function get_eda($options){
		global $i18n,$i18n_std,$i18n_datatype;
		$arr=$options['arr'];
		$path=$options['path'];
		$ac=$options['ac'];
		$mod=$options['mod'];
		$title=$options['title'];

		include($options['datatypes']);
		$dt=$i18n_datatype[$path];
		#		p2($dt);
		$this->field_c=0;
		$this->ed_strings=array();
		$this->dt=$dt;
		$field_list=$this->eda($arr,$path,$path,1);
		//	echo("<h1>field list</h1>");p2($field_list);
		$ff=new form($field_list);

		$ff->strings=$this->ed_strings;
		//	$ff->strings['ok']='OK';
		$ff->strings['_form_title']=$title;
		$ff->add_submit_button(array('action'=>$ac,'label'=>'OK'));
		$ff->add_field(array('name'=>'filename','type'=>'hidden','value'=>$options["filename"]));
		$ff->add_field(array('name'=>'mod','type'=>'hidden','value'=>$mod));

		$ff->shtml();
	}


	function eda($arr,$path,$human_path,$depth){

		#Recurse into an array, and create a stree data structure you can later edit, trough a FORM(), it returns an array of fields, suitable for form() input.
		#
		#arr	the information
		#path	the path (so we can do searches, inform the user, and change datatypes when need so)
		#depth	depth into the data structure
		#dt	datatype path tree
		$fields=array();
		foreach($arr as $k=>$v){
			$p=$path.'['.$k.']';
			$hp=$human_path.'/'.$k;
			if(is_array($v)){
				$fields[$hp]=array(
					'type'=>'separator',
					'value'=>'<h'.($depth).'>'.$k.'</h'.($depth).'>'
				);
				$fields[$hp]=$this->path_replace($fields[$hp],$p,$hp);
				$this->field_c++;	

				$fields=array_merge($fields,$this->eda($v,$p,$hp,$depth+1));
			}else{

				$field_type="text";
				$fields['data'.$this->field_c]=array(
					'name'=>$p,
					'type'=>$field_type,
					'value'=>$v
				);
				$this->ed_strings[$p]=$k;
				$this->ed_strings['help_'.$p]=$hp;
				$fields['data'.$this->field_c]=$this->path_replace($fields['data'.$this->field_c],$p,$hp);
				$this->field_c++;	
			}
		}
		return($fields);
	}

	function path_replace($str,$p,$hp){

		foreach($this->dt as $k1=>$v1){
			if($this->is_inside_path($hp,$k1)){
				$this->ed_strings[$p]=$v1["description"];
				$this->ed_strings['help_'.$p]=$v1["help"];
				unset($v1["help"]);
				unset($v1["description"]);
				#add all aditional data from datatype template into my field.
				$str=array_merge($str, $v1);
			}else{
				#item is not in any path, use default (text)
			}
		}
		return($str);
	}

	/** usage
	 * @code
	 $this->array2file(
		 $_GET["i18n"][$s],
		 'i18n',$_GET["filename"],
		 "i18n['$s']");
	@endcode
	 */
	function array2file($arr,$path,$filename,$array_path=''){
		global $i18n;
		$short=basename($filename,".php");
		$a=var_export($arr,true);
		#save a backup.
		#echo($_GET["filename"].getcwd());
		if(file_exists($filename)){
			$c=copy($filename,"./misc/deprecated/$path.".$short.".".date("Y_m_d_H_i_s").".php");
		}else{
			$c = 1; // its ok, the file doesn't exists
		}
		#overwrite file
		if($c){
			$txt=("<?php\n# autogenerated on:".date('Y-m-d H:i:s')." \n# by user:".ssname()." ID:".ssid().", try not to hand edit too much\n".
				"\$$array_path=".$a.";\n?>");	
			$f=$this->file_write($filename,$txt,1,1);
			if($f==1){
				$this->msg($i18n[$this->program_name()]['written_ok'].$short);
			}else{
				$this->error($i18n[$this->program_name()]['write_error'].$short);
			}
		}else{
			$this->error($i18n[$this->program_name()]['copy_error'].$short);
		}

	}

	function ac_ed(){
		global $i18n,$i18n_std;
		$this->menu();
		if($_GET["step"]=='' && $_GET['filename']==''){	
			$f=new form();
			$this->privilege_manager->ap('ed');
			$f->add_field(array('name'=>'filename','type'=>'glob','mask'=>'./i18n/'.std_get_language().'/*','ext'=>'.i18n.php'));
			$f->add_submit_button(array('label'=>$this->get_i18n_text('std_ok'),'action'=>'ed'));
			$f->add_field(array('name'=>'step','type'=>'hidden','value'=>'2'));
			$f->add_field(array('name'=>'mod','type'=>'hidden','value'=>$this->name));
			$f->strings=$i18n['edit']['file_select'];
			$f->shtml();
		}elseif($_GET["step"]=='2'){

			$i18n_bak=$i18n;
			unset($i18n);

			##@todo WARNING: THIS IS OBSCENELY UNSAFE; PLEASE MAKE SURE YOU HAVE THE RIGHT SECURITY SYSTEM ENABLED!			

			include($_GET['filename']);
			//echo("<h1>I18n</h1>");p2($i18n);
			$this->get_eda(array(
				'arr'=>$i18n,
				'path'=>'i18n',
				'ac'=>'ed',//AC is void.
				'mod'=>$this->program_name(),
				'filename'=>$_GET['filename'],
				'datatypes'=>STD_LOCATION.'shared/datatypes/i18n.object.php',
				'title'=>$i18n_bak[$this->name]['step2']
			));
		}elseif($_GET["step"]=='' && $_GET['filename'] != ''){	
			#last step on i18n.edit
			//step 3
			//@todo escape!
			$s=basename($_GET["filename"],".i18n.php");
			$this->array2file(
				$_GET["i18n"][$s],
				'i18n',$_GET["filename"],
				"i18n['$s']");

		}
	}
	/**
	 * 
	 * View Editor
	 *
	 * */
	function ac_vi(){
		global $i18n,$std_views;
		$this->menu();	
		if($_GET["step"]==''&&!isset($_GET['filename'])){	
			$f=new form();
			$f->add_field(array('name'=>'filename','type'=>'glob','mask'=>'./view/*','ext'=>'.view.php'));
			$f->add_field(array('name'=>'ac','type'=>'hidden','value'=>'vi'));
			$f->add_field(array('name'=>'step','type'=>'hidden','value'=>'2'));
			$f->add_field(array('name'=>'mod','type'=>'hidden','value'=>$this->name));
			$f->add_submit_button(array('action'=>'vi','label'=>'ok'));
			$f->strings=$i18n['edit']['view_select'];
			$f->shtml();
		}elseif($_GET["step"]=='2'){
			p2($_GET);

			$dta=array(
				'a'=>array('name[]'=>'a','value[]'=>'100','date[]'=>'2000-01-01 00:00:00','mod'=>'edit'),
				'b'=>array('name[]'=>'b','value[]'=>'1100','date[]'=>'2010-01-01 00:00:00','mod'=>'edit'),
				'c'=>array('name[]'=>'c','value[]'=>'1200','date[]'=>'2020-01-01 00:00:00','mod'=>'edit'),

			);
			$fi1=array(
				'name[]'=>array('name'=>'name[]','type'=>'text'),
				'value[]'=>array('name'=>'value[]','type'=>'text'),
				'date[]'=>array('name'=>'date[]','type'=>'date'),
				'mod'=>array('name'=>'mod','type'=>'hidden','value'=>'edit'),
			); 
			$op=array(
				'strings'=>array(
					'name[]'=>'Nombre',
					'help_name[]'=>'Nombre',
					'date[]'=>'Fecha',
					'help_date[]'=>'',
					'value[]'=>'Valor',
					'help_value[]'=>'Valor',

					'_form_title'=>'Prueba de Grilla #1',

				),
				'table_style'=>'list',
				'table_border'=>'0',
				'style'=>'form',
				'buttons'=>array(
					array('action'=>'vi','label'=>'Guardar'),
				),
				'head'=>array('name','type','date')
			);
			$this->sgrid($fi1,$dta,$op);

		/*
		$fn=$_GET['filename'];
		include($fn);
		$m=basename($fn,'.view.php');
		$this->get_eda(array(
			'arr'=>$std_views[$m],
			'path'=>'view',
			'ac'=>'vi',
			'mod'=>$this->name,
			'filename'=>$fn,
			'datatypes'=>'../shared/datatypes/view.object.php',
			'title'=>$i18n[$this->name]['view_step2']
		));
	 */

		}elseif($_GET["step"]==''&&isset($_GET['filename'])){
			#step3
			p2($_GET);

		}
	}
	/**
	 * Edit modules
	 * */
	function ac_emod(){
		$this->menu();
		echo("mod");
	}
	function ac_cf2(){
		#last step on config.edit
		global $config;
		$this->menu();
		$this->array2file($_GET["config"],'config',"./config.php","config");
	}

	function ac_cf(){
		global $i18n,$config;
		$this->menu();
		$this->privilege_manager->ap('cf2');
		$this->get_eda(array(
			'arr'=>$config,
			'path'=>'config',
			'ac'=>'cf2',
			'mod'=>$this->name,
			'filename'=>'config.php',
			'datatypes'=>STD_LOCATION.'shared/datatypes/config.object.php',
			'title'=>$i18n[$this->name]['cf_step1']
		));
	}

	function edit_model(){
		$this->table='edit';//fix?
		$this->std();
	}
	var $use_table = 0;
	var $default_action='ed';		

}
?>
