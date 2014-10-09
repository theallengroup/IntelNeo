<?php
class chunk_model extends std{
	function show_report_list(){
		$e = new std_env();
		$t = array();	
		foreach($e->report_list() as $report){
			$t[]=$this->mkl(array('report_name'=>$report,'op'=>'edit'),$report);
		}
		$new_report_link = $this->mkl(array('op'=>'new'),$this->i18n('new'));
		include(SHARED_MODULES_DIR.'templates/chunks/report_list.php');
	}
	function ac_rlist(){
		$this->menu();
		if(!isset($_GET['op'])){
			$this->show_report_list();
		}elseif(isset($_GET['report_name'])){
			$r = new std_report();
			$r->load_by_name($_GET['report_name']);
			if(!isset($_GET["is_new"])){
				$r->ui(array('report_name'=>$_GET['report_name'],'op'=>$_GET['op']));
			}else{
				$r->create();
				$this->msg($this->i18n('saved_ok'));
				$this->show_report_list();
			}

		}elseif($_GET['op'] == 'new'){
			$r = new std_report();
			if(!isset($_GET["is_new"])){
				echo($r->ui(array('op'=>$_GET['op'])));
			}else{
				$r->create();
				$this->msg($this->i18n('created_ok'));
				$this->show_report_list();
			}
		}
		//p2($e->chunks_in('1'));
	}
	function ac_tlist(){
		$this->menu();
		$e = new std_env();
		$t = array();

		foreach($e->table_list() as $table){
			$t[$table]=$this->mkl(array('table'=>$table),$table);
		}
		include(SHARED_MODULES_DIR.'templates/chunks/table_list.php');
		//p2($e->chunks_in('1'));
	}
	function chunk_model(){
		$this->use_table = 0;
		$this->std();
	}
	function install(){
		foreach(explode("\\g",file_get_contents(STD_LOCATION.'shared/install/chunks.sql')) as $sql_command){
			$this->sql($sql_command);
		}
		echo($this->i18n('install_ok'));
	}
	function ac_install(){
		$this->menu();
		$this->install();
	}
}
class std_chunk{
	var $name;
	var $sql;
	var $table;
	var $category;
	function load(){
	
	}
	function save(){
	
	}
	function std_chunk(){
	
	}
	function get_clause(){
	
	} 
}
class std_report{

	var $id=0;
	var $tables;
	var $rtype;
	var $title;
	var $name='noname';
	var $chunks=array();
	function create(){
		$main= new db();
		$this->name = $main->remove_strange_chars($_GET['name']);
		$this->title = $main->remove_strange_chars($_GET['title']);
		$this->rtype = $main->remove_strange_chars($_GET['rtype']);
		if(is_array($_GET['tables'])){
			foreach($_GET['tables'] as $t){
				$this->add_table($main->remove_strange_chars($t));
			}
		}
		$this->save();
	}
	function load(){
	
	}
	function load_by_name($name){
		$db=new db();
		$q = $db->q2obj('SELECT * from std_report WHERE name=\''.$db->remove_strange_chars($name).'\'');
		foreach($q[0] as $name=>$value){
			$this->$name=$value;
		}
		$q2 = $db->q2obj('SELECT table_name from std_report2table WHERE report_id=\''.$this->id.'\'');
		$this->remove_all_tables();
		foreach($q2 as $r){
			$this->add_table($r['table_name']);
		}
	}
	function remove_all_tables(){
		$this->tables=array();
	}
	function save_tables(){
		$main = new db();
		$main->sql('DELETE FROM std_report2table WHERE report_id = \''.$this->id.'\'');
		foreach($this->tables as $table){
			$main->sql('INSERT INTO std_report2table (id,report_id,table_name) VALUES(0,\''.$this->id.'\',\''.$table.'\')');
		}
	}
	function save(){
		$main = new db();
		if($this->id == 0){//CREATE NEW OBJ
			$main->sql('INSERT INTO std_report (id,name,title,rtype) VALUES(0,\''.$this->name.'\',\''.$this->title.'\',\''.$this->rtype.'\')');
			$this->id = $main->last_id();
			$this->save_tables();
		}else{//UPDATE OLD OBJ
			$main->sql('UPDATE std_report SET name=\''.$this->name.'\',title=\''.$this->title.'\',rtype=\''.$this->rtype.'\' WHERE id=\''.$this->id.'\'');
			$this->save_tables();
		}
	}
	function add_table($table){
		$this->tables[$table]=$table;
	}
	function add_chunk($chunk){
		$this->chunks[$chunk->name]=$chunk;
	}
	function remove_all_chunks(){
		$this->chunks=array();
	}
	function chunks(){
		return($this->chunks);
	}
	function std_report(){
		$this->tables=array();
	}
	/** returns the FORM object 
	 * $ops: get
	 * */
	function ui($ops){
		global $main;
		$e = new std_env();
		$f = new form();
		
		$f->strings=$e->i18n('new_report');
		foreach($ops as $kop=>$op){
			$f->add_hidden_field($kop,$op);
		}
		$f->add_hidden_field('mod',$_GET['mod']);
		if($this->id == 0){
			$f->set_title($e->i18n('new'));
			$button_label=$e->i18n('new');
			$is_new=1;
		}else{
			$f->set_title($e->i18n('edit'));
			$is_new=0;
			$button_label=$e->i18n('save');
		}
		$f->add_hidden_field('is_new',$is_new);

		$f->add_field(array('name'=>'name','type'=>'text','value'=>$this->name));
		$f->add_field(array('name'=>'title','type'=>'text','value'=>$this->title));
		$f->add_field(array('name'=>'rtype','type'=>'list','options'=>$e->i18n('report_types'),'value'=>$this->rtype));
		$f->add_field(array('name'=>'tables','type'=>'checklist','options'=>$e->table_list(),'check_all'=>1,'values'=>array_keys($this->tables)));

		$f->add_submit_button(array('label'=>$button_label,'action'=>$_GET['ac']));

		return($f->shtml());
	}
}
class std_env{
	function i18n($key){
		global $main;
		return($main->i18n($key,'chunk'));
	}
	function report_list(){
		global $main;
		return($main->q2op("select id,name from std_report",'id','name'));
	}
	function table_list(){
		global $main;
		$a = $main->get_local_modules();
		$m1 = array();
		foreach($a as $m){
			$m1[]=basename($m, '.controller.php');
		}
		return($main->aa($m1));
	}
	function chunks_in($table_name){
		return(array(1,2,3));
	}
}
?>
