<?php
/**


select sum(amount),count(*)/(select count(*) from testdata) grp from testdata group by grp;

pivot tables

http://www.youtube.com/watch?v=E10W1rnDD5Q&eurl=http://www.ozgrid.com/Excel/excel-pivot-tables.htm



SELECT a,b,a+b INTO OUTFILE '/tmp/result.txt'
  FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"'
  LINES TERMINATED BY '\n'
  FROM test_table;
 * 
 *a filter view:
 * ALL,ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789
 * this makes where field_name%

 * @TODO text in labels: mst be valign=top
 * @TODO editor. (requires bug101)
 * @TODO remove vh_ ac_ etc: 
 * @todo translate: 
 * @todo set visibility: some actions to be insisible (from certain view_types)
 * (result actions, like edit2, delete2 new2, etc (FROM view))
 * @TODO item order!
 *
 * @TODO translate items in ac_, filter_ and view_, make repositories.
 * @TODO add auto add privileges: role list.
 * hackish
 * @?TODO add insert point to view files all over the place.
 * @?TODO add insert point warning.
 *
 * on_save: insert privilege
 * insert in role2priv for all marked items.
 *
 * write views file (INSERT POINT)
 * write i18n file (if option is NEW, otherwise leave alone)
 * write 
 * */	

class view_model extends std{
	/** @returns 1 on success 0 on faluire 
	 * @todo move code to array2file
	 * @todo move this to i18n mod
	 * array module*/
	function i18n_add_file_key($module_name,$key,$value){
		$m=$this->load_file($module_name);
		$mod_i18n = $m->get_i18n_array();
		if(array_key_exists($key,$mod_i18n)){
			$this->msg('i18n key exists');
			return(0);
		}
		$mod_i18n[$_GET['view_name']]=$_GET['view_title'];

		$d=$this->load_file('edit');
		$d->array2file(
			$mod_i18n,
			'','./i18n/'.std_get_language().'/'.$m->program_name().".i18n.php","i18n['".$m->program_name()."']"
		);
		return(1);
	}
	/** from http://www.php.net/is_dir */
	function isDir($dir) {
		$cwd = getcwd();
		$returnValue = false;
		if (@chdir($dir)) {
			chdir($cwd);
			$returnValue = true;
		}
		return $returnValue;
	}

	function ac_copy(){
		global $main;
		$this->menu();	
		if($_GET['view']==''){
			$d=$this->get_local_modules();
			$views=array();
			if(isset($_GET['module'])){
				$d=array($_GET['module']);
			}
			foreach($d as $k=>$mod){

				if(isset($main)){
					$m= $main->load_file(basename($mod,'.controller.php'));
				}else{
					$m= $this->load_file(basename($mod,'.controller.php'));
				}
				
				#$m=$this->load_file(basename($mod,'.controller.php'));
				$vl=$m->get_view_list();
				foreach($vl as $v1){
					$views[$m->program_name().'/'.$v1]=$m->program_name().'/'.$v1;
				}
			}//p2($views);
			$f=new form();
			$f->strings=array('_form_title'=>'Seleccione la Vista');
			$f->add_field(array(
				'events'=>array(
					'change'=>'document.forms[0].elements["view_name"].value=this.value.split("/")[1]+"_copy"',
				),
				'name'=>'view',
				'type'=>'list',
				'options'=>$views,'i18n_text'=>'Vista','i18n_help'=>'Vista'));
			$f->add_field(array('name'=>'view_name','type'=>'text','i18n_text'=>'Nombre de la Nueva Vista','i18n_help'=>'Vista'));
			#$f->add_field(array('name'=>'view_title','type'=>'text','i18n_text'=>'T&iacute;tulo','i18n_help'=>'[espa&ntilde;ol], si se deja vacio, se usa el DEFAULT'));
			$f->add_hidden_field('mod',$this->program_name());
			$f->add_field(array('name'=>'create_priv',
				'type'=>'list',
				'options'=>array('0'=>'No','1'=>'Si'),
				'value'=>'0',
				'i18n_text'=>'Crear Privilegio',
				'i18n_help'=>'Crea un privilegio view:nombre_vista'));

			$roles = $this->q2op('SELECT * FROM '.$this->table_prefix.'role');

			$f->add_field(array('name'=>'roles','check_all'=>1,
				'type'=>'checklist',
				'options'=>$roles,
				'i18n_text'=>'Roles',
				'i18n_help'=>'Roles asignados a esta vista'));
			
			$f->add_submit_button(array('action'=>'copy','label'=>'OK'));
			$f->shtml();

		}elseif(!isset($_GET['fields'])){//step2
			$parts=explode('/',$_GET['view']);
			$mod=$parts[0];
			$view=$parts[1];
			if(isset($main)){
				$m= $main->load_file($mod);
			}else{
				$m= $this->load_file($mod);
			}
		
			#$m=$this->load_file($mod);///@todo 7100 unsafe
			$v=$m->load_view($m->program_name(),$view);///@todo 7100 unsafe
			
			$v['fields']=$m->view_expand($v['fields']);

			
			$f=new form();
			$f->strings=array('_form_title'=>'Seleccione los Campos');
			$f->add_field(array('name'=>'fields','check_all'=>1,
				'type'=>'checklist',
				'options'=>$this->aa(array_keys($v['fields'])),
				'values'=>array_keys($v['fields']),
				'i18n_text'=>'Campos',
				'i18n_help'=>'Campos'));
			$f->add_hidden_field('mod',$this->program_name());

			if(is_array($_GET['roles'])){
				$f->add_hidden_field('roles',implode(',',$_GET['roles']));
			}
			$f->add_hidden_field('create_priv',$_GET['create_priv']);
			$f->add_hidden_field('view',$_GET['view']);
			$f->add_hidden_field('view_name',$_GET['view_name']);
		#	$f->add_hidden_field('view_title',$_GET['view_title']);
			$f->add_submit_button(array('action'=>'copy','label'=>'OK'));
			$f->shtml();

		}else{	//step3
		#	$_GET['view_title'] = $this->remove_strange_chars($_GET['view_title']);
			$_GET['view_name'] = $this->remove_strange_chars($_GET['view_name']);

			///p2($_GET);
			$parts=explode('/',$_GET['view']);
			$mod=$parts[0];
			$view=$parts[1];
			#$m=$this->load_file($mod);///@todo 7100 unsafe
			if(isset($main)){
				$m= $main->load_file(basename($mod,'.controller.php'));
			}else{
				$m= $this->load_file(basename($mod,'.controller.php'));
			}
					
			$v=$m->load_view($m->program_name(),$view);///@todo 7100 unsafe
			$v['title']='table_plural';
			
			$v['fields']=$m->view_expand($v['fields']);
				
			foreach($v['fields'] as $k=>$field){
				if(!in_array($k,$_GET['fields'])){
					unset($v['fields'][$k]);
				}
			}

			//does the View exists?
			//p2($v);
			//echo('<xmp>');echo();echo('</xmp>');

			$code=$this->fmt(file_get_contents(SHARED_MODULES_DIR.'templates/view.php'),array(
				'program_name'=>$m->program_name(),
				'view_name'=>$_GET['view_name'],
				'view_contents'=>$this->win_var_export($v,true),
				),'_');
			//echo('<xmp>');
			//echo($code);
			//echo('</xmp>');
			
			$dx="";
			if(!$this->isDir('./view/'.$m->program_name())){
				mkdir('./view/'.$m->program_name());
				$dx.=('<br/>dir created:'.$m->program_name());
			}
			$file_name='./view/'.$m->program_name().'/'.$_GET['view_name'].'.view.php';
			$dx.=('<br/>file created:'.$this->file_write($file_name,$code,1,1));
			if(trim($_GET['view_title'])!=''){
				$this->i18n_add_file_key($m->program_name(),$_GET['view_name'],$_GET['view_title']);
			}

			if($_GET['create_priv']=='1'){
				$roles=explode(',',$_GET['roles']);
				$ac_name='view:'.$_GET['view_name'];
				$pname=$m->program_name().'/'.$ac_name;

				//create a privilege or reuse an existing one
				$privs=$this->q2op('SELECT id,action FROM '.$this->table_prefix.'privilege WHERE action=\''.$pname.'\'','id','action');
				if(count($privs)==0){
					$this->sql('INSERT INTO '.$this->table_prefix.'privilege (name,action) VALUES(\''.$_GET['view_title'].'\',\''.$pname.'\')');
					$pid = $this->last_id();
					foreach($roles as $k=>$role){
						$this->sql('INSERT INTO '.$this->table_prefix.'role2priv (role_id,privilege_id) VALUES(\''.$this->remove_strange_chars($role).'\',\''.$pid.'\')');
					}
				
				}else{
					///since the privilege already exists, please assign the stuff by hand
					///@todo 7101 delete role2priv, add stuff
					foreach($privs as $priv_id=>$action){
						$pid=$priv_id;
					}
				}
			
				$dx.=('<br/>Roles:'.count($roles));
			}
			$this->flush_privileges();
			$this->privilege_manager->add_privilege(array(
				'action'=>$pname,
				'privilege_name'=>$_GET['view_title'],
				'role_name'=>$this->get_role_name(),
			));
			$dx.=("<br/>".$this->make_link(array('mod'=>$m->program_name(),'ac'=>$ac_name),'Ir a la vista'));
			$this->msg($dx);
		}
	}
	/**
	 * 
	 * View Add
	 * features: touches not the database, all the informations got from the .model fields
	 * */
	function ac_add(){
		global $i18n,$std_views;
		$this->menu();	

		if($_GET["step"]==''){
			$f=new form();
			$f->add_field(array('name'=>'filename','type'=>'glob','mask'=>'./model/*','ext'=>'.model.php'));
			//$f->add_field(array('name'=>'ac','type'=>'hidden','value'=>'add'));
			$f->add_field(array('name'=>'step','type'=>'hidden','value'=>'2'));
			$f->add_field(array('name'=>'mod','type'=>'hidden','value'=>$this->name));
			$f->strings=$i18n['view']['view_select'];
			$f->add_submit_button(array('action'=>'add','label'=>'OK'));
			$f->shtml();
		}elseif($_GET["step"]=='2'){

			$f=basename($_GET["filename"],".model.php");
			#echo($f);
			$mod=$this->load_file($f);


			#p2($mod->fields);
			$ff=new form();
			$op=array();
			#p2($i18n);
			foreach($mod->fields as $k=>$field){
				$op[$k]=$k;	
			}

			$filter_functions=$mod->get_func_list("filter_");
			$action_functions=$mod->get_func_list("ac_");
			$handler_functions=$mod->get_func_list("vh_");
			#p2($filter_functions,'red');
			#p2($action_functions,'green');
			#p2($handler_functions,'blue');
			
			//$ff->add_field(array('name'=>'ac','type'=>'hidden','value'=>'add'));
			
			$ff->add_field(array('name'=>'mod','type'=>'hidden','value'=>'view'));
			$ff->add_field(array('name'=>'step','type'=>'hidden','value'=>'3'));
			$ff->add_field(array('name'=>'filename','type'=>'label','value'=>$f));
			$ff->add_field(array('name'=>'name','type'=>'text','value'=>'user1'));
			$ff->add_field(array('name'=>'title','type'=>'text','value'=>'Unnamed View 1'));//COMBOBOX: TODO list add adds to i18n, shows i18n items that belong to: x,xplurarl, xyz.
			$ff->add_field(array('name'=>'type','type'=>'list','options'=>$handler_functions));//$this->aa(array('table','record','grid','calendar','timeline','from_user'))
			$templates=glob("./template/*.html");
			if(!is_array($templates) ||count($templates)==0){
				$ff->add_field(array('name'=>'template','type'=>'label','value'=>$i18n["view"]["no_templates_found"]));
			}else{
				$ff->add_field(array('name'=>'template','type'=>'glob','mask'=>'./templates/*','ext'=>'.html'));//TODO template on type:record
			}
			$ff->add_field(array('name'=>'actions','type'=>'checklist','options'=>$action_functions));
			$ff->add_separator('');
			$ff->add_field(array('name'=>'side_actions','type'=>'checklist','options'=>$action_functions));
			$ff->add_separator('');
			$ff->add_field(array('name'=>'down_actions','type'=>'checklist','options'=>$action_functions));
			$ff->add_separator('');
			$ff->add_field(array('name'=>'filter','type'=>'list','options'=>$filter_functions));
			$ff->add_field(array('name'=>'restrict','type'=>'textarea','value'=>'1=1'));
			$ff->add_field(array('name'=>'fields','type'=>'checklist','options'=>$op,'check_all'=>1));
			$ff->strings=$i18n['view']['part2'];
			$ff->add_submit_button(array('action'=>'add','label'=>'OK'));
		//	p2($this->i18n'part2');
			$ff->shtml();
		}elseif($_GET["step"]=='3'){
			p2($_GET);
		}
	}
	
	function vg_chart_pie_save($dta){
		$view1=array(
			"type"=>'chart',
			"title"=>$this->remove_strange_chars($_GET["title"]),
			"help"=>$this->remove_strange_chars($_GET["help"]),
			'dimension_fields'=>array(),
			'data_fields'=>array(),
			'restrict'=>'1=1',
			);
		if(isset($_GET["query_name"])){
			$view1["query_name"] = $_GET["query_name"];
		}
		p2($_GET,'red');
		$sum = $this->aa($_GET['sum']);
		$count = $this->aa($_GET['count']);
		$dim_fields=array();

		foreach($_GET["dimension_field_name"] as $row_number=>$field_name){
			$field_name = $this->remove_strange_chars($field_name);
			$chart_type = $this->remove_strange_chars($_GET['chart_type'][$row_number]);
			$field_label = $this->remove_strange_chars($_GET['dimension_field_label'][$row_number]);
			$r = array(
				'field_name'=>$field_name,
				'chart_type'=>$chart_type,
			);
			if(trim($field_label)!=''){
				$r["label"] = $field_label;
			}
			$view1["dimension_fields"][$field_name]=$r;
		}

		foreach($_GET["data_field_name"] as $row_number=>$field_name){
			$field_name    = $this->remove_strange_chars($field_name);
			$field_label   = $this->remove_strange_chars($_GET['data_field_label'][$row_number]);
			$function_name = $this->remove_strange_chars($_GET['function_name'][$row_number]);
			$func_field = $function_name.'('.$field_name.')';
			if($field_name == '*' && $field_label == ''){
				$field_label = 'Total';
			}
			$r = array(
				'value'=>$func_field,
				'label'=>$field_label,
			);
			if(trim($field_label)!=''){
				$r["label"] = $field_label;
			}
			$view1['data_fields'][$func_field]=$r;
		}

		return($view1);
	}
	function sqlquery2model($sqlquery_fields){
		return($sqlquery_fields);
	
	}
	function vg_sqlview_form($f){
		return($f);
	}
	function vg_sqlview_save($dta){
		print_r($dta);
	}
	function vg_chart_pie_form($f){
		/*
		$k = new form();
		$k->add_field(array(
			'type'=>'text',
			'repeat'=>1,
		)
		);
		$k->shtml();
			 */

		$m = $this->load_file($_GET["module"]);
		$view_fields = $m->fields;
		if(isset($_GET["query_name"])){
			$f->add_hidden_field('query_name',$_GET["query_name"]);
			$vn = $m->load_view($m->program_name(),$_GET["query_name"]);;
			$view_fields = $vn["fields"];
		}
		$field_name_list = array_keys($view_fields);
		$defaults=array();
		$chart_defaults=array();
		$labels=array();
		$value_defaults=array();
		$names=array();
		foreach($field_name_list as $name){
			$names[]="*";
			$defaults[]='';
			$value_defaults[]=$c++;
			$chart_defaults[]='pie';
			$labels[]='';
			$functions[]='';
			break;
		}
		$field_name_list=$this->aa($field_name_list);
		$data_fields = $field_name_list;
		$data_fields['*']='*';

		$f->add_field(array(
			'type'=>'list',
			'options'=>$this->aa($field_name_list),
			'name'=>'dimension_field_name',
			'i18n_text'=>'Name',
			'value'=>$names,
			'repeat'=>1,
			'group'=>'g1',
			'group_label'=>"Dimension Fields",
			'group_add_link'=>"Add Dimension Field",
		)
		);
		$f->add_field(array(
			'type'=>'text',
			'editable'=>0,
			'name'=>'dimension_field_label',
			'i18n_text'=>'Label',
			'value'=>$labels,
			'repeat'=>1,
			'group'=>'g1')
		);
	
		$f->add_field(array(

			'type'=>'list',
			'i18n_text'=>'Chart Type',
			'options'=>$this->aa(array('pie','bar')),
			'name'=>'chart_type',
			'value'=>$chart_defaults,
			'repeat'=>1,
			'group'=>'g1')
		);

		$f->add_field(array(
			'group_add_link'=>"Add Data Field",
			'group_label'=>"Data Fields",
			'type'=>'list',
			'options'=>$this->aa($data_fields),
			'name'=>'data_field_name',
			'i18n_text'=>'Name',
			'value'=>$names,
			'repeat'=>1,
			'group'=>'g2')
		);
		$f->add_field(array(
			'type'=>'text',
			'editable'=>0,
			'name'=>'data_field_label',
			'i18n_text'=>'Label',
			'value'=>$labels,
			'repeat'=>1,
			'group'=>'g2')
		);

		$f->add_field(array(
			'type'=>'list',
			'options'=>$this->aa(array('sum','count')),
			'name'=>'function_name',
			'i18n_text'=>'Function',
			'value'=>$names,
			'repeat'=>1,
			'group'=>'g2')
		);

		return($f);
	}
	
	function ac_vgen(){
		global $main;
		$this->menu();
		
		if(!isset($_GET["step"])){
			$f=new form();
			$f->set_title('Crear Vista: Paso 1/2');
			$mod_list = $this->aa($this->get_mods());
			$mod_list2 = $mod_list;
			foreach($mod_list as $m1){
				#echo($m1." ");
				$m2 = $main->load_file($m1);
				foreach($m2->get_view_list() as $vname){
					#p2($m2->get_view_list());
					$v1 = $m2->load_view($m2->program_name(),$vname);
					if($v1["type"]=='sqlquery'){
						$b2 = $m2->program_name().'/'.$vname;
						$mod_list2[$b2] = $b2;
					}
				
				}

			}
			$f->add_field(array('name'=>'module','type'=>'list','options'=>$mod_list2,'value'=>$_GET["module"]));
			$f->add_text_field('title');
			$f->add_text_field('name');
			$f->add_field(array('name'=>'type','type'=>'list','options'=>$this->aa(array(
				'3dchart',
				'chart_pie',
				'chart_bar',
				'form_readonly',
				'form_new',
				'form_edit',
				'table_readonly',
				'table_fulledit',			
				'table_sqlquery',			
				'raw_sqlquery',			
				))));
			#$f->add_field(array('name'=>'restrict','type'=>'textarea'));
			$f->add_field(array('name'=>'help','type'=>'textarea'));
			$f->add_field(array('name'=>'role','type'=>'checklist','check_all'=>1,'options'=>$this->q2op("select id,name from role",'id','name')));
			$f->add_hidden_field('mod',$this->program_name());
			$f->add_hidden_field('step','2');
			$f->add_submit_button(array('label'=>'Siguiente','action'=>$this->current_action));
			$f->shtml();
		}else{	
			if($_GET["step"]=='2'){
				$f=new form();
				$f->set_title('Crear Vista: Paso 2/2');
				$f->add_hidden_field('module',$_GET["module"]);
				$f->add_hidden_field('title',$_GET["title"]);
				$f->add_hidden_field('type',$_GET["type"]);
				$f->add_hidden_field('name',$_GET["name"]);
				#$f->add_hidden_field('restrict',$_GET["restrict"]);
				$f->add_hidden_field('help',$_GET["help"]);
				$f->add_hidden_field('mod',$this->program_name());
				$f->add_hidden_field('step','3');
				if(isset($_GET["role"]) && is_array($_GET["role"])){
					foreach($_GET["role"] as $k=>$r){
						$f->add_hidden_field('role['.$k.']',$r);
					}
				}
				$f->add_submit_button(array('label'=>'Siguiente','action'=>$this->current_action));


				#minor hack
				if(strpos($_GET["module"],'/')!==FALSE){
					$ex = explode("/",$_GET["module"]);
					$_GET["module"]=$ex[0];
					$_GET["query_name"]=$ex[1];
				}
				$fn = "vg_".$_GET["type"].'_form';
				$f = $this->$fn($f);
				$f->shtml();
			}elseif($_GET["step"]=='3'){

				#minor hack
				if(strpos($_GET["module"],'/')!==FALSE){
					$ex = explode("/",$_GET["module"]);
					$_GET["module"]=$ex[0];
					$_GET["query_name"]=$ex[1];
				}

				$fn = "vg_".$_GET["type"].'_save';
				$result = $this->$fn($_GET);
				$mn = escapeshellcmd($_GET["module"]);
				$vn = escapeshellcmd($_GET["name"]);
				$file_name = './view/'.$mn.'/'.$vn.'.view.php';
				$ed = $this->load_file("edit");
				if(!file_exists('./view/'.$mn)){
					mkdir('./view/'.$mn);
				}
				$ed->array2file($result,'view',$file_name, "std_views['$mn']['$vn']");
				$pname = $this->remove_strange_chars($_GET["module"]).'/view:'.$this->remove_strange_chars($_GET["name"]);
				if(count($_GET["role"])>0){
					$this->sql("INSERT INTO privilege VALUES(NULL,'".$this->remove_strange_chars($_GET["title"])."','".$pname."')");
					$priv_id = $this->last_id();
					foreach($_GET["role"] as $role_id){
						$this->sql("INSERT INTO role2priv VALUES(NULL,'".$this->remove_strange_chars($role_id)."','".$this->remove_strange_chars($priv_id)."')");
					}
				}else{
					$this->msg("Esta vista no ha sido asignada a ningún ROL (lo cual la hace inútil), para asignarla, cree un privilegio : $pname ");
				}
				$this->flush_privileges();
				$this->privilege_manager->add_privilege(array(
					'action'=>$pname,
					'privilege_name'=>$_GET['view_title'],
					'role_name'=>$this->get_role_name(),
					));
				$this->msg("<a href='?mod=".$_GET["module"]."&ac=view:".$_GET["name"]."'>Test : ".$_GET["title"]."</a>");
			}else{
				$this->msg("invalid step");
			}
		}
	}
	function array_transpose($array_list){
		$out = array();
		foreach($array_list as $collumn_name=>$cell_values){
			foreach($cell_values as $row_id=>$one_value){
				$out[$row_id][$collumn_name]=$one_value;
			}

		}
		return($out);
	}
	#####################################################
	
	function vg_raw_sqlquery_form($f){
		$f->add_field(array('name'=>'rawsql','type'=>'textarea','value'=>'SELECT * FROM '.$_GET["module"]));
		return $f;
	}
	/** 
	 * @todo fix too much garbage in DTA
	 * */
	function vg_raw_sqlquery_save($dta){

		$dta["type"]='rawsql';
		$dta["sql"]=$_GET["rawsql"];
		return($dta);
	}

	#####################################################
			
		
	function vg_3dchart_save($dta){
		$dta=array();
		$dta["type"]='3dchart';
		$dta["table"]=$_GET["table"];
		$dta["title"]=$_GET["title"];
		$dta["top"]=$_GET["top"];
		$dta["side"]=$_GET["side"];
		$dta["data"]=$_GET["data"];
		$dta["restrict"]='1=1';
		return($dta);
	}
	function vg_3dchart_form($f){
		$m= $this->load_file($_GET["module"]);
		$field_list=$this->aa(array_keys($m->fields));
		$field_list[1]=1;

		$f->add_field(array('name'=>'table','type'=>'text','value'=>$_GET["module"]));//override
		$f->add_field(array('name'=>'top','type'=>'list','options'=>$field_list));
		$f->add_field(array('name'=>'side','type'=>'list','options'=>$field_list));
		$f->add_field(array('name'=>'data','type'=>'list','options'=>$field_list));
		return $f;
	}
	
	#####################################################
	function vg_table_sqlquery_form($f){
		if(isset($_GET["query_name"])){
			$this->msg("ERROR: CANNOT SQLQUERY FROM A SQLQUERY! (yet)");//@todo 9218374
		}
		if(!isset($_GET["qrystep"])){
			//@todo make checklist various checklists 
			//grouping modules, by modcat
			$f->set_title("Crear Vista: Paso 2. parte 1/3");
			$ml = $this->get_valid_modules();
			$ml2=array();
			foreach($ml as $mn){
				$ml2[]=basename($mn,'.controller.php');
			}
			
			$f->add_field(array('name'=>'mods','type'=>'checklist','options'=>$this->aa($ml2),'values'=>array($_GET["module"])));
			$f->fields["step"]["value"]=2;
			$f->add_hidden_field('qrystep',1);
		}elseif($_GET["qrystep"]==1){
			$f->set_title("Crear Vista: Paso 2. parte 2/3");
			foreach($_GET["mods"] as $mod_name){
				$f->add_hidden_field("mods[$mod_name]",$mod_name);
				$m = $this->load_file($mod_name);			
				$ops=array();
				foreach($m->fields as $fname=>$field){
					if($m->is_foreign_id($fname)){
						$color='gray';
						$tag='i';
					}else{
						if($fname==$m->ifield){
							$tag='b';
						}else{
							$tag='span';
						}
						$color='black';
					}
					$fn4='<'.$tag.'><font color="'.$color.'">'.$fname.'</font></'.$tag.'>';
					$ops[$mod_name.'.'.$fname]=$fn4;
				}
				$f->add_field(array(
					'values'=>array($mod_name.'.'.$m->ifield),
					'i18n_text'=>$mod_name,
					'name'=>'fields['.$mod_name.']',
					'type'=>'checklist',
					'options'=>$ops,
					'check_all'=>1)
				);
			}
			$f->fields["step"]["value"]=2;
			$f->add_hidden_field('qrystep',2);
		}elseif($_GET["qrystep"]==2){
			$f->set_title("Crear Vista: Paso 2. parte 3/3");
			
			$ops=array();
			foreach($_GET["mods"] as $mod_name){
				$f->add_hidden_field("mods[$mod_name]",$mod_name);
				$m = $this->load_file($mod_name);
				foreach($m->fields as $fname=>$field){
					$ops[] = $mod_name.'.'.$fname;
				}
			}
			foreach($_GET["fields"] as $field_name){
				$f->add_hidden_field("fields[$field_name]",$field_name);
			}
			$ops2=array(
				'EQ'=>'Igual a',
				'NEQ'=>'Distinto de',
				'GT'=>'Mayor que',
				'GTE'=>'Mayor o Igual a',
				'LT'=>'Menor que',
				'LTE'=>'Menor o Igual a',
				'IN'=>'Pertenece a',
				'NOT_IN'=>'No Pertenece a',
			);

			//determine conditionals
			$conds = array();
			$field1_list = array();
			$operators= array();
			$field2_list = array();
			$value_list=array();
			
			$mod_list = $this->aa($_GET["mods"]);
			$mod_list6 = $mod_list;
			unset($mod_list6[$_GET["module"]]);
			
			$unrelated_tables = $mod_list;
			$hbm_tables=array();
			$at_top=array();
			$at_bottom=array();
			//hbm table, and table re-sort
			foreach($mod_list6 as $table_name){
				$m3 = $this->load_file($table_name);
				if(strpos($table_name,"2")!==FALSE){
					$hbm_tables[$table_name]=2;
					$at_top[$table_name]=$table_name;
				}else{
					$at_bottom[$table_name]=$table_name;
					$hbm_tables[$table_name]=1;
				}
			}
			$mod_list2 = $this->aa(array_merge(array($_GET["module"]),$at_top,$at_bottom));
			p2($mod_list2,'red');

			$new_tables=$unrelated_tables;
			foreach($mod_list2 as $mod_name){
				$m3 = $this->load_file($mod_name);
				//$m3->fields as $field_name=>$field_str
				echo("<h1>CONNECTING $mod_name</h1>");
				
				$unrelated_tables=$new_tables;
				unset($unrelated_tables[$mod_name]);//remove self reference
				if(count($unrelated_tables)==0){
					break;//I'M done
				}

				echo("<h2>LEFT TO CONNECT:</h2>");
				p2($unrelated_tables);
				foreach($unrelated_tables as $foreign_candidate){
					$cfield1 = '';
					$cfield2 = '';
					$candidate=$this->load_file($foreign_candidate);
					if($foreign_candidate == $mod_name){//dont like a_id = a.a_id
						continue;
					}
					echo("<br/>COMPARING:$mod_name WITH $foreign_candidate");
					$foreign_field = $m3->get_connector_field($foreign_candidate);
					//4 cases
					//CASE01 MAIN connects to KID
					if($foreign_field!='@none'){
						$found = 1;
						$cfield1 = $m3->get_table_name().'.'.$foreign_field;
						$cfield2 = $foreign_candidate .'.'.$m->id;

						echo("<font color=red>CASE01 OK </font>");
	
					//CASE02 KID connects to MAIN
					}elseif(($local_field = $candidate->get_connector_field($mod_name))!='@none'){
						$cfield1 = $mod_name.'.'.$m3->id;
						$cfield2 = $candidate->get_table_name().'.'.$local_field;
						$found = 1;
						echo("<font color=blue>CASE02 OK </font>");
						//KID1 connects to KID2, and NONE connect to MAIN
						//KID1 connects to KID2 and either one of them connects to MAIN
					}else{
						$found = 0;
						echo(" NOT ");
					}
					if($found == 1){
						$field1_list[] = $cfield1;
						$operators[]   = 'EQ';
						$field2_list[] = $cfield2;
						$value_list[]='';
						$hbm_tables[$foreign_candidate]--;
						
						if($hbm_tables[$foreign_candidate]==0){
							unset($new_tables[$foreign_candidate]);		
						}
					
					}
				}
			}

			if(count($unrelated_tables)>0){
				$this->msg("Las siguientes tablas no tienen conecci&oacute;n:".d2_recursive($unrelated_tables));
			}
			$this->e_table($this->array_transpose(array($field1_list,$operators,$field2_list)),'green');

			#p2($field1_list,'green');
			#p2($operators,'green');
			#p2($field2_list,'green');
			$f->add_field(array('name'=>'conditional_field',  'type'=>'list','options'=>$this->aa($ops),	'repeat'=>1,'value'=>$field1_list,	'i18n_text'=>'Campo 1','group_label'=>'Condiciones','group_add_link'=>'Agregar Condici&oacute;n'));
			$f->add_field(array('name'=>'conditional_op',     'type'=>'list','options'=>$ops2,		'repeat'=>1,'value'=>$operators,	'i18n_text'=>'Operador'));
			$f->add_field(array('name'=>'conditional_field2', 'type'=>'list','options'=>$this->aa($ops),	'repeat'=>1,'value'=>$field2_list,	'i18n_text'=>'Campo 2'));
			$f->add_field(array('name'=>'conditional_value',  'type'=>'text',				'repeat'=>1,'value'=>$value_list,	'i18n_text'=>'Valor'));
			$f->add_hidden_field('qrystep',4);
		
		}
		return($f);
	}
	function is_related($m1,$m2){
		return($m1->get_connector_field($m2->program_name())!='@none' OR $m2->get_connector_field($m1->program_name())!='@none'  );
	}
	/** 
	 * this seemed like a good idea at some point, but now i see it a useless...
	 * */
	function op2sql($op){
		$ops2=array(
			'EQ'=>'=',
			'NEQ'=>'<>',
			'GT'=>'>',
			'GTE'=>'>=',
			'LT'=>'>',
			'LTE'=>'<=',
			'IN'=>'IN',
			'NOT_IN'=>'NOT IN',
		);
		return($ops2[$op]);
	}
	function vg_table_sqlquery_save($v){
		$conditions=array();
		foreach($_GET["conditional_field"] as $row_id=>$cf){		
			$cv = $_GET["conditional_field2"][$row_id];
			if(trim($_GET["conditional_value"][$row_id])!=''){
				$cv = $_GET["conditional_value"][$row_id];
			}
			$conditions[]=$cf.' '.$this->op2sql($_GET["conditional_op"][$row_id]).' ('. $cv .')';
		}
		$field_list=array();
		foreach($_GET['fields'] as $f){
			//@todo grow this 38947
			//THIS IS WHERE THIS SYSTEM GROWS; 
			//BY ADDING PROPERTIES TO EACH FIELD, 
			//IM SURE WELL FIGURE SOMETHING MORE CLEVER THAN JUST "LABELS"
			$field_list[$f]=array();	
		}
		$view=array(
			'title'=>$_GET["title"],
			'help'=>$_GET["help"],
			'type'=>'sqlquery',
			#'tables'=>$_GET["mods"],
			'fields'=>$field_list,
			'conditions'=>$conditions,
		);
		return($view);
		#p2($_GET);
		#p2($v);
		die();
	}
	function view_model(){
		$this->std();
	}
	var $default_action='add';
	var $use_table = 0;	
}
?>
