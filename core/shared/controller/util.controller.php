<?php
	/**
	 * okTODO fix output!
	 * @todo mod_del
	 * @todo mod edit
	 * @todo mod_stats
	 * */
	#Title: programa edicion de arreglos
	#Author: felipe	
	#Generator Version: manual
	
class util_model extends std{
	function ac_rel_editor(){
		$this->menu();
		$error_count=0;
		$this->shadow_start();
		foreach($this->get_local_modules() as $path){
			$mod = basename($path,'.controller.php');
			$m = $this->load_file($mod);
			foreach($m->fields as $f){
				if($m->field_is_foreign($f)){
					$m2=$this->load_file($m->id2mod($f['name']));
					if(!isset($m2->rel['has'][$mod])){
						echo("<br>ERROR: misslink in: $mod.{$f['name']} not in ".$m2->program_name().".rel.php has[]['$mod']='$mod'; ");
						$error_count++;

					}
					if(!isset($m->rel['belongs_to'][$m2->program_name()])){
						echo("<br>ERROR: misslink in: $mod.{$f['name']} not in ".$m->program_name().".rel.php belongs_to[]['".$m2->program_name()."']='".$m2->program_name()."'; ");
						$error_count++;

					}
				}
			}
		}
		echo("<br/>Total:$error_count");
		$this->shadow_end();
	}

	function ac_sql_manager(){
		$this->menu();
		$this->shadow_start();
		echo('<br/><a href="?mod=util&ac=sql_console">Consola SQL</a>');
		echo('<br/><a href="?mod=util&ac=sql_console">Consola SQL</a>');
		echo('<br/><a href="?mod=util&ac=sql_console">Consola SQL</a>');
		echo('<br/><a href="?mod=util&ac=sql_console">Consola SQL</a>');
		$this->shadow_end();
	}
	function ac_csv_loader(){
		global $mydir;
		$this->menu();

		if(isset($_GET['final']) && $_GET['final']==1 ){

			$mname=$this->remove_strange_chars(escapeshellcmd($_GET['mod_name']));
			$mod=$this->load_file($mname);

			//insert the records.
			if(!file_exists($_FILES["file"]['tmp_name'])){
				echo('click <a href="?mod=util&ac=csv_loader">here</a>');
				return('');
			}

			$file_name='./uploaded_files/'.$_SESSION[$mydir]['sif_file_name'];
			$rc=0;
			$headers=array();
			$ids=array();
			foreach($this->clean_csv_data($file_name) as $row){
				if($rc==0){
					//fisrt row is header always
					$headers=$this->clean_user_fields($row);
					
				}else{
					//invert array
					$map2=array();
					foreach($_SESSION[$mydir]['sif_map'] as $k=>$v){
						$map2[$v]=$k;
					}
					
					//get
					//
					
					$dbfields=array();
					$dbvalues=array();
					$has_id=0;

					foreach($row as $collumn_number=>$value){
						if(count($headers)<=$collumn_number){
							//far right
							continue;
						}
						if(!isset($headers[$collumn_number])){
							echo(' CAMPO INVALIDO:'.$collumn_number);
							$this->csv_cleanup($file_name);
							die();
						}
						//apply hand made mappings
						foreach($_SESSION[$mydir]['sif_missing_from_db_in_file'] as $collumn_number2=>$user_field2){
							//@none
							if($collumn_number2==$collumn_number){
								$map2[$user_field2]=$_GET['added_'.$collumn_number];
								break;
							}
						}
						if(!isset($map2[$headers[$collumn_number]])){
							echo(' MAP INVALIDO h[cn]='.$headers[$collumn_number].' CN='.$collumn_number);
							$this->csv_cleanup($file_name);
							die();
						}
						$mapped_field=$map2[$headers[$collumn_number]];
						if($mapped_field==$mod->id){
							$has_id=1;
						}

						if($mapped_field!='@none'){
							//we ignore the @none==Ninguno Fields, so they don't get inserted in the database
							///@todo 123948 in the NEXT version, make sure we show ALL fields with All mappings so the user can make "None" anything he wants, or switch fields crazily (i don't think theyll ask for it)
							$dbfields[$map2[$headers[$collumn_number]]]=$map2[$headers[$collumn_number]];
							$dbvalues[$map2[$headers[$collumn_number]]]="'$value'";
						}else{
							//si usa NONE, entonces use el DATO QUE PUSO el usuario
						}
					}
					if(!$has_id){
						$dbfields[$mod->id]=$mod->id;
						$dbvalues[$mod->id]="'0'";
					}
					foreach($_SESSION[$mydir]['sif_missing'] as $missing_field){
						$dbfields[$missing_field]=$missing_field;
						$dbvalues[$missing_field]="'".$mod->remove_strange_chars($_GET[$missing_field])."'";
					}
					$sql='INSERT INTO '.$mod->get_table_name().' ('.implode(',',$dbfields).') VALUES ('.implode(',',$dbvalues).')';
					$mod->sql($sql);
					$ids[]=$mod->last_id();
					//echo($sql.';<br>');
				}
				$rc++;
			}
			
			echo("<br/>--RUN ".$file_name."<br/> DELETE FROM ".$mod->get_table_name().' WHERE id in ('.implode(',',$ids).');<br/>--to undo the last command');
			$this->csv_cleanup($file_name);

		}elseif(isset($_POST['mod_name'])){

/* ********************************************************************************************* */			

			$mname=$this->remove_strange_chars(escapeshellcmd($_POST['mod_name']));
			$mod=$this->load_file($mname);
			if(!file_exists($_FILES["file"]['tmp_name'])){
				echo('click <a href="?mod=util&ac=csv_loader">here</a>');
				return('');
			}
			if(!file_exists('./uploaded_files')){mkdir('./uploaded_files');}
			$c4 = copy($_FILES["file"]['tmp_name'],'./uploaded_files/'.$_FILES["file"]["name"]);
			if(!$c4){
				$this->msg("Copy error.");
			}

			$file_name=$_FILES["file"]['tmp_name'];
			echo("File name=".$file_name);
			$data=$this->clean_csv_data($file_name);
			$c=0;
			$d2=array();
			foreach($data as $row){
				$d2[]=$row;
				$c++;
				if($c>10){break;}
			}

			$map=array();//local=>theirs
			
			$user_fields=$mod->aa($this->clean_user_fields($data[0]));

			$missing_from_file_in_db=array();
			//exact 
			foreach($mod->fields as $dbfield){
				if((!array_key_exists($dbfield['name'],$user_fields)) || (!array_key_exists(strtolower($mod->fi($dbfield['name'])),$user_fields))){
					$missing_from_file_in_db[$dbfield['name']]=$dbfield['name'];
				}else{
					//exact match
					$map[$dbfield['name']]=$dbfield['name'];
				}
			}
			
			//inside
			$user_field_count=1;
			foreach($user_fields as $user_field){
				$cc=0;
				foreach($missing_from_file_in_db as $missing_field){
					if($cc==1){
						break;
					}
					//echo("<br>$missing_field = $user_field ");
					if($user_field==''){
						$mod->msg('<br/>Encabezado Vacio! Columna # '.$user_field_count);
						$cc=1;
						continue;
					}
					if(strpos(strtolower($mod->fi($missing_field)),$user_field)!==FALSE || strpos($missing_field,$user_field)){
						//found!
						$map[$missing_field]=$user_field;
						unset($missing_from_file_in_db[$missing_field]);
						$cc=1;
					}
				}
				$user_field_count++;
				if($cc==1){
					$cc=0;
					continue;
				}
				// ... 
				//never runs on cc==1
			}

			//check for fields in CSV file not in dbfields
			//
			$map2=array();
			foreach($map as $k=>$v){
				$map2[$v]=$k;
			}
			$headers=$this->clean_user_fields($data[0]);
			
			$missing_from_db_in_file=array();	//12 => rare_field
			$additional_fields=array(
				'_sep'=>array('type'=>'separator'),
				);

			//remove already mapped fields from options, we already know what goes there!
			$all_dbfields=array();
			$all_dbfields['@none']='Ninguno';
			$all_dbfields=array_merge($all_dbfields,$mod->get_headers());
			
			foreach($map as $local_map_name=>$value2){
				unset($all_dbfields[$local_map_name]);
			}
			
			foreach($headers as $collumn_number=>$user_field){
				if(count($headers)<=$collumn_number){ //never happens
					//far right, we don't give a shit aboout these data
					continue;
				}

				if(!isset($map2[$headers[$collumn_number]])){
					$missing_from_db_in_file[$collumn_number]=$user_field;

					$additional_fields['added_'.$collumn_number]=array(
						'name'=>'added_'.$collumn_number,
						'type'=>'list',
						'options'=>$all_dbfields,
						'i18n_text'=>$user_field . "($collumn_number)",
						'i18n_help'=>'none',
					);

					//echo(' MAP INVALIDO h[cn]='.$headers[$collumn_number].' CN='.$collumn_number);
					//$mod->csv_cleanup($file_name);
					//die();
				}
			}
			
			if(isset($missing_from_file_in_db[$mod->id])){
				//
				//we don't want the ID then
				//we either get the ID in the field list, or not at all
				unset($missing_from_file_in_db[$mod->id]);
			}
			//p2($missing_from_file_in_db,'red');
			
			$_SESSION[$mydir]['sif_file_name']=$_FILES["file"]["name"];
			$_SESSION[$mydir]['sif_map']=$map;
			$_SESSION[$mydir]['sif_missing']=$missing_from_file_in_db;
			$_SESSION[$mydir]['sif_missing_from_db_in_file']=$missing_from_db_in_file;
			
			///$mod->form_from_fields($mod->current_action,$missing_from_file_in_db,array());
			$form_fields=array();
			foreach($missing_from_file_in_db as $missing_field){
				$form_fields[$missing_field]=$mod->fields[$missing_field];
			}
			$form_fields=array_merge($form_fields,$additional_fields);
			///@todo 9123478 WARNING! what happens if there are NO FIELDS THAT NEED TO BE ADDED
			$mod->shadow_start('round');
			$f = $mod->get_ed(array(
				'width'=>$options['width'],
				'actions'=>array(array('action'=>$this->current_action,'label'=>'Load')),
				'rawtitle'=>'Archivo: '.$_FILES["file"]["name"],
				'data'=>'',
				'style'=>'form',
				'fields'=>$form_fields,
			));
			$mod->f->add_hidden_field('mod',$this->program_name());//REDIRECT THE FORM TO: mod=util
			$mod->f->add_hidden_field('mod_name',$_POST['mod_name']);//REDIRECT THE FORM TO: mod=util
			$mod->f->add_hidden_field('final',1);//REDIRECT THE FORM TO: mod=util
			echo($mod->f->out());
			echo("<div style='width:600px;overflow:scroll' >");
			$mod->e_table($d2,$d2[0],array('style'=>'list'));
			echo("</div>");
			$mod->shadow_end('round');

		}else{
/* ********************************************************************************************* */			

			$f=$this->mod_selector_form($this->current_action,'Load CSV File: select a Module','Load');
			$f->method='POST';
			$f->add_field(array('name'=>'file','type'=>'file','i18n_text'=>'File','i18n_help'=>'','accept'=>'text/csv'));
			$f->shtml();
		}
	}
	function mod_selector_form($ac,$title='Select a Module',$button_label='OK'){
		$mods=$this->get_valid_modules();
		$f=new form();
		$f->strings=array(
			'_form_title'=>$title,
			'mod_name'=>'Module',
			'help_mod_name'=>'Select a Module',
		);
		$f->add_hidden_field('mod',$this->program_name());
			
		$op=array();
		foreach($mods as $mod){
			$m = basename($mod,'.controller.php');
			$op[$m]=$m;
		}
		$f->add_field(array('name'=>'mod_name','options'=>$op,'type'=>'list','size'=>'20'));
		$f->add_submit_button(array('action'=>$ac,'label'=>$button_label));
		return($f);
	
	}

	function ac_load_csv(){
		global $mydir;
		$this->menu();
		if(!isset($_FILES["file"]) && !isset($_SESSION[$mydir]['sif_file_name'])){
		
		}elseif(!isset($_SESSION[$mydir]['sif_file_name'])){
			if($_FILES["file"]['error']==0){
				//123
			}else{
				echo('Error en el archivo.');
			}
		}else{
			//124
		}
	}

	function csv_cleanup($file_name){
		global $mydir;
		if(file_exists($file_name)){
			unlink($file_name);
		}else{
			echo('file missing');
			echo(b2());
		}
		unset($_SESSION[$mydir]['sif_file_name']);
		unset($_SESSION[$mydir]['sif_map']);
		unset($_SESSION[$mydir]['sif_missing']);
		unset($_SESSION[$mydir]['sif_missing_from_db_in_file']);
	}

	function clean_user_fields($user_fields){
		foreach($user_fields as $f=>$user_field){
			if(!$user_field==''){
				$user_field=$user_fields[$f]=strtolower($user_field);
			}else{
				//Avoid "spaces", and most right ONES
				//unset($user_fields[$f]);
			}
		}
		return($user_fields);
	}
	function clean_csv_data($file_name){
		$lines=file($file_name);
		$data=array();
		$l0=$lines[0];

		if(strpos($l0,';')!==false){
			$separator=';';
		}elseif(strpos($l0,',')!==false){
			$separator=',';
		}elseif(strpos($l0,"\t")!==false){
			$separator="\t";
		}else{
			$this->error('file separator invalid, must be in [;,\t] ');
			$this->csv_cleanup($file_name);
			die();
		}
		foreach($lines as $line){
			$line_data=explode($separator,str_replace(array("\n","\r"),'',$line));
			foreach($line_data as $k=>$value){
				$line_data[$k]=$this->remove_strange_chars(str_replace('"','',$value));
			}
			$data[]=$line_data;
		}
		return($data);
	}
	function install(){
		echo('hi mom');
	}

	function ac_install_manager2(){
		$this->ac_install_manager();
	}
	function ac_install_manager3(){
		$this->ac_install_manager();
	}
	/**
	 * this will allow you to manage noy-installed modules easilly
	 * all you have to do is to create a function called install() on the modules, to create specific behavior
	 * this is most useful in shared modules (/core/shared/controller/*) and also when you upgrade stuff.
	 * */
	function ac_install_manager(){
		global $config;
		
		$this->privilege_manager->add_privilege(array(
			'action'=>'util/install_manager2',
			'role_name'=>'me',
			'privilege_name'=>'Install Manager',
		));
		$this->privilege_manager->add_privilege(array(
			'action'=>'util/install_manager3',
			'role_name'=>'me',
			'privilege_name'=>'Install Manager',
		));


		if($_GET['allow']=='1'){

			$ac_ids=array();
			$mod_name=$this->remove_strange_chars($_GET['mod_name']);
			foreach($_GET['actions'] as $ac){
				$ac=$this->remove_strange_chars($ac);
				$this->sql('INSERT INTO '.$config['table_prefix'].'privilege VALUES(0,\''.$ac.'\',\''.$this->mod_and_ac($mod_name,$ac).'\')');
				$id=$this->last_id();
				$ac_ids[$id]=$id;
			}

			foreach($_GET['role'] as $role_id){
				$role_id=$this->remove_strange_chars($role_id);
				foreach($ac_ids as $priv_id){
					$this->sql('INSERT INTO '.$config['table_prefix'].'role2priv VALUES(0,'.$role_id.','.$priv_id.')');
				}
			}
			$this->ac_rebuild_privileges();
			$this->msg('Done (click the logo, or any action to continue)');
			return('');
		}

		$this->menu();
		

		if($_GET['submit']=='Model'){
			$m=escapeshellcmd($this->remove_strange_chars($_GET['mod_name']));
			$d=$this->load_file($m);
			$d->create_table();

			return("");
		}
		if($_GET['submit']=='Actions'){
			$m=escapeshellcmd($this->remove_strange_chars($_GET['mod_name']));
			$d=$this->load_file($m);
			$oa=$d->get_own_actions();
			$action_list = $this->aa($d->get_valid_actions());
			foreach($oa as $action_item){
				$action_list[$action_item]="[".$action_item."]";
			}
			
			#$oa = $d->get_valid_actions();
			#if(!is_array($oa)){
			#	echo('mod has no acts');
			#	return("");
			#}

			$f=new form();
			$f->strings=array(
				'_form_title'=>'Mod Manager',
				'actions'=>'Actions',
				'help_actions'=>'Actions',
				'role'=>'Roles',
				'help_role'=>'Roles',
			);
			$f->add_hidden_field('mod',$this->program_name());
			
			$f->add_hidden_field('mod_name',$_GET['mod_name']);
			$f->add_hidden_field('allow',1);
			$f->add_field(array('name'=>'actions','options'=>$action_list,'type'=>'checklist','check_all'=>1,'values'=>array_keys($oa)));
			$f->add_field(array('name'=>'role','options'=>$this->q2op('SELECT * FROM '.$config['table_prefix'].'role'),'type'=>'checklist','check_all'=>1,'values'=>array(1)));

			$f->add_submit_button(array('action'=>'install_manager','label'=>'Allow'));
			$f->shtml();
			return("");
		}

		if(isset($_GET['mod_name'])){
			$m=escapeshellcmd($this->remove_strange_chars($_GET['mod_name']));
			$d=$this->load_file($m);
			$d->install();
		
			return('');
		}
		$mods=$this->get_valid_modules();
		$f=new form();
		$f->strings=array(
			'_form_title'=>'Mod Manager',
			'mod_name'=>'Module',
			'help_mod_name'=>'Select a Module',
		);
		$f->add_hidden_field('mod',$this->program_name());

		#table list
		
		$t = $this->table_list();
		$k=array();

		foreach($t as $r){
			$k[] = $r['Tables_in_'.$config["database_name"]];
		}
		$k =$this->aa($k);


		$op=array();
		foreach($mods as $mod){
			$m = basename($mod,'.controller.php');

			$d=$this->load_file($m);
			$module_label=$m;
			if(!isset($k[$m]) && $d->use_table){
				$module_label=$module_label.'*';
			}
			if(in_array('install',get_class_methods($m.'_model'))){
				$module_label=$module_label.'->install()';
			}
			$op[$m]=$module_label;

			
			
		}
		$f->add_field(array('name'=>'mod_name','options'=>$op,'type'=>'list','size'=>'20'));
		$f->add_submit_button(array('action'=>'install_manager2','label'=>'Actions'));
		$f->add_submit_button(array('action'=>'install_manager3','label'=>'Model'));
		$f->add_submit_button(array('action'=>'install_manager','label'=>'Install'));
		//
		$f->shtml();
	}














	/** edit colors 
	 *
	 * D4D590 E2E3B5 F8F8ED 7F804D
	 * */
	function ac_colors(){
		$this->menu();
		echo('<hr width=400><input type=color><table>');
		foreach(range(0,10) as $r){
			$c=$r*25; //0 - 250
			$c2=(255-$c)%255;
			if(abs($c2-$c)>180){
				$c2+=(($c2-$c)*0)%255;
			}
			echo('<tr>
				<td style=\'background-color:rgb('.$c.',0,0)\' width=20 height=20>&nbsp;</td>
				<td style=\'background-color:rgb('.$c2.',0,0)\' width=20 height=20>&nbsp;</td>
				<td style=\'background-color:rgb('.$c.',0,0);color:rgb('.$c2.',0,0)\' width=20 height=20>Sample</td>
				<td  width=20 height=20>&nbsp;</td>
				</tr>');
		}
		echo('</table>');
			

	}	
	function check_fields_same($mod_name){
		if($mod_name==''){
			echo("<br>MOD NAME INVALID:$mod_name");
			return("");
		}
		$m = $this->load_file($mod_name);
		$dbfields=$this->describe($m->get_table_name());
		$dfields=array();
		foreach($dbfields as $dbfield){
			$dfields[$dbfield['Field']]=$dbfield['Field'];
		}
		foreach($m->fields as $mod_field_name=>$field){
			if(!isset($dfields[$mod_field_name])){
				echo("<br><font color=red>IN MODULE BUT NOT IN TABLE:$mod_name.$mod_field_name</font>");
			}else{
				//field exists, unset it
				unset($dfields[$mod_field_name]);
			}
		}
		foreach($dfields as $dfield){
			echo("<br><font color=red>IN TABLE BUT NOT IN MODULE:$mod_name.$dfield</font>");
		}
	}
	/** 
	 * search for fields that live in views but not in tables
	 * also search for differences in the model filñes and the actual tables
	 * */
	function ac_broken_fields(){
		$this->menu();
		$this->shadow_start('round');
		echo('<h1 class="standard_title form_title"> broken fields </h1>');
		$modulos2=$this->aa(array_map(create_function('$x','return(basename($x,".controller.php"));'),$this->get_local_modules()));

		$modulos=array();
		
		foreach($modulos2 as $modulo){
			$modulos[basename($modulo,'.controller.php')] = basename($modulo,'.controller.php');
		}
		$mod_c = array();
		foreach($this->get_shared_modules() as $modulo_compartido){
			$mod_c[basename($modulo_compartido,'.controller.php')] = basename($modulo_compartido,'.controller.php');
		}

		$db_tables = $this->table_list();
		$tables=array();
		foreach($db_tables as $table){
			$tables[$table['Tables_in_'.$this->dbdatabase]]=$table['Tables_in_'.$this->dbdatabase];
		}

		//p2($tables);
		//p2($modulos);

		//modules with no real table
		foreach($modulos as $modulo){
			if(!isset($tables[$modulo])){
				echo("<br>Modulo $modulo no tiene tabla en MYSQL ");
				unset($tables[$modulo]);//no point in checking twice
			}else{
				$this->check_fields_same($modulo);
			}
		}

		//tables without a module 
		foreach($tables as $table){
			if(!isset($modulos[$table])){
				if(!isset($mod_c[$table])){
					echo("<br>Tabla $table no tiene modulo en ./model/  ");
				}else{
					//mod is shared, no warning is needed
				}
			}else{
				$this->check_fields_same($table);
			}
		}



		foreach($modulos as $modulo){
					
			$mod = $this->load_file($modulo);
			foreach($mod->get_views() as $nombre_vista=>$vista) {
				$vista['fields']=$mod->view_expand($vista['fields']);

				if(!is_array($vista['fields'])){
					echo("ERROR: CAMPOS de la vista:".$nombre_vista.' :'.$vista['fields']);
					continue;
				}
				foreach($vista['fields'] as $nombre => $field) {
					if(!array_key_exists($nombre,$mod->fields)) {
					   echo("<li>$modulo/$nombre_vista/$nombre");
					}
				}
			}
		}
		$this->shadow_end('round');
	
	}	

	
	/** 
	 * will checdk on integrity reference errors
	 * */	


	function ac_diagnose(){
		$this->menu();
		$allok=TRUE;
		$this->shadow_start();

		foreach($this->get_valid_modules() as $path){
			$m = basename($path,'.controller.php');
			$mod = $this->load_file($m);
			$h = $mod->healthy();
			$allok = ($mod->healthy() and $allok);
			#echo("h=$h, allok=".(int)$allok);
		} 
		$a = array(1=>'Bueno',0=>'Malo');
		echo("<font color=red>Diagnostico:$allok ".$a[(int)$allok].'</red>');
		$this->shadow_end();
	}
	function ac_test21(){
		$this->menu();
		echo('hello punkass!');
	}
	function ac_test2(){
		$this->menu();
		p2($_GET);
		$f = new form();
		$f->strings=array(
			'item'=>'Item','help_item'=>'Item',
			'item2'=>'Item','help_item2'=>'Item',
			'item3'=>'Item','help_item3'=>'Item',
		);
		$f->set_title('Test Form 1');
		$f->add_submit_button(array('action'=>'test2','label'=>'OK'));
		$f->add_hidden_field('mod',$this->program_name());
		//$this->jsc('combobox');
		$f->add_field(array('type'=>'combobox','name'=>'item','options'=>array('Ant','Argh','Arse','All','Bear')));
		$f->add_field(array('type'=>'combobox','name'=>'item2','options'=>array('Ant','Argh','Arse','All','Bear')));
		$f->add_field(array('type'=>'combobox','name'=>'item3','options'=>array('Ant','Argh','Arse','All','Bear')));
		$f->shtml();
	}
	function ac_test1(){
		$this->menu();
		global $std_views;
	//	p2($std_views);

		//$std_views['std']['edit_all'];
		$std_views['util']['test_view']=array(
			'extends'=>'edit_all',
			'filter'=>'1=2',
		);
		p2($this->extended_view($this->load_view($this->program_name(),'test_view')));
	}
	function extended_view($view){
		if(array_key_exists('extends',$view)){
			$l = $this->load_view($this->program_name(),$view['extends']);
			foreach($l as $k=>$v){
				if(!array_key_exists($k,$view)){
					$view[$k]=$l[$k];
				}
			}
		}
		unset($view['extends']);
		return($view);
	}
	function show_sql_console_form(){
		global $mydir;
		$a=new form();
		$a->strings=$this->i18n('sql_console');
		//p2(array(1,2,3,$this->i18n('sql_console')));
		if(!isset($_POST['get_results'])){
			$_POST['get_results']=1;
		}
		#$_SESSION[$mydir]['sql_commands']=array();
		if(!is_array($_SESSION[$mydir]['sql_commands'])){
			$_SESSION[$mydir]['sql_commands']=array();
		}
		$opts = $this->aa($_SESSION[$mydir]['sql_commands']);
		if(is_array($opts)){
			$opts2=array();
			foreach($opts as $optid=>$opt){
				$opts2[str_replace("'","&apos;",$optid)]=substr($opt,0,100)." ...";
			}
		}
		$opts=$opts2;
		//p2($opts);
		if(!is_array($opts)){
			$opts=array();
		}
		$a->add_field(array('type'=>'list','name'=>'history','options'=>$opts,'events'=>array('change'=>'document.getElementById("sql").value = this.value')));
		$a->add_field(array('type'=>'boolean','name'=>'get_results','value'=>$_POST['get_results']));
		$a->add_field(array('type'=>'textarea','name'=>'sql','value'=>$_POST['sql'],'css'=>'width:100%'));

		$a->add_hidden_field('mod',$this->program_name());
		$a->action='?mod='.$this->program_name().'&ac='.$this->current_action;
		$a->method='POST';
		$a->add_hidden_field('mod',$this->program_name());
		$a->add_submit_button(array('action'=>'sql_console','label'=>$this->i18n('sqlgo')));
		echo("<center><div style='width:100%'>");
		$a->shtml();
		echo("</div></center>");
		echo("<script>;document.getElementById('sql').focus();</script>");
	
	}
	function sql_that_can_fail($query,$f='sql'){
		$q = $this->$f($query,"USER QUERY",1);
		if($this->query_failed==0){
			if($f=='sql'){
				$this->msg('<br/>Affected:'.$this->affected().'<br/>Last ID:'.$this->last_id());
			}else{
				
				$this->e_table($q,'none',array('style'=>'list'));
			}
		}else{
			echo($this->error("QUERY FAILED:<br/>" . $this->query_error ));
			return("");
		}
	}
	function ac_sql_console(){
		$this->menu();
		global $mydir;
		//PURPOSELY FIX  \'
		$_POST['sql']=str_replace("\\'","'",$_POST['sql']);

		$queries2 = explode(";" , $_POST['sql']);
		//WARNING, IF YOU HAVE ; WITHIN DATA; THIS WILL CREATE BAD QUERIES.

		#clear blank queries
		$queries=array();
		foreach($queries2 as $q){
			if(!preg_match("/^(\s|\n)*$/m",$q)){
				$queries[]=$q;
			}
		}

		#$this->shadow_start();
		if($_POST['sql']==''){
			$this->show_sql_console_form();
		}else{
			
			foreach($queries as $q){
				$_SESSION[$mydir]['sql_commands'][]=$q;
			}
			#save a log file
			foreach($queries as $q){
				$this->log_event(str_replace(array("'",'"'),array("\'","\\\""),$q),-1,'SQLCONSOLE','SQL');
			}

			$this->show_sql_console_form();
			if($_POST['get_results']!=1){
				foreach($queries as $q){
					$this->sql_that_can_fail($q);
				}
				
			}else{
				foreach($queries as $q){
					$words = preg_split("/\s+/",trim(strtolower($q)));
					if($words[0]=='select'||$words[0]=='desc'||$words[0]=='describe'||$words[0]=='show'||$words[0]=='exec'||$words[0]=='call'){//exec was added, for SQL Server support.
						$this->shadow_start();
						echo("<center><div style='margin:10px;width:100%;max-width:1000px;overflow:scroll'>");
						$this->sql_that_can_fail($q,"q2obj");
						
						echo("</div></center>");
						$this->shadow_end();
					}else{
						$this->sql_that_can_fail($q);					
					}
				}
			}
		}
		#$this->shadow_end();
		
	}
	function util_model(){
		$this->table='none';
		$this->use_table=0;
		$this->std();
	}
//	var $default_action='none';
	
}
?>
