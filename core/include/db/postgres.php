<?php
global $is_connected_to_db,$global_link,$database_queries,$database_sql_queries,$database_query_count;
include(dirname(__FILE__)."/any.php");
class db extends any_db {

	/**
	 * This is a wrapper for generic DB functionality, it allows us to use any database, as long as what's called is SQL92
	 * all database access whould be done trough here
	 * */
	var $engine_name='POSTGRES';
	var $q2op_trim_length=40;	//max length of SELECT name text in q2op
	var $dblink=null;
	var $dbdatabase='none';		//these constants are got from ./config.php, 
					//make shure you include that somewhere
	var $dbhost='localhost';
	var $dbname='root';
	var $dbpwd='';
	var $res=0;
	var $is_connected=0;

	function real_connect($host1, $name1,$pwd1){
		return(pg_connect("host=$host1 dbname=".$this->dbdatabase." user=$name1 password=$pwd1"));
	}
	function real_error(){
		return(pg_last_error($this->dblink));
	}
	function real_select($db){
		//does nothing
		return(TRUE);
	}
	function real_sql($sqlstring){
		return(pg_query($this->dblink,$sqlstring));
	}
	function db($c=1){
		$this->any_db($c);
	}

	function db_paginate_sql($page_number){
		global $config;
		return("\n LIMIT ".$config['pagination_limit']." OFFSET ".($config['pagination_limit']*$page_number)." ");
	}
	function database_list(){
		return($this->q2obj("SELECT datname as \"Database\" FROM pg_database"));
	}
	/** maps to pg_insert_id(), it's not SQL92 */
	function last_id(){
		$old_res = $this->res;//save and restore OLD res, so we can still do affected() and stuff doesn't fuck up.
		$q = $this->q2obj("SELECT LASTVAL() as c");
		$this->res=$old_res;
		return($q[0]['c']);
	}
	/** 
	 * mimics my-sql's describe output as closely as possible
	 * and also fucks up the output so it contains valid int(10) and auto_increment out of fields we ASSUME SHOULD have such characteristics (like: id)
	 * this is NOT a valid describe, but its close enough to the original, that it allows us to generate code (hopefully) and use 
	 * existing tools (as long as they use this instead of "describe")
	 * */
	function describe($table){
		$q2 = $this->q2obj("SELECT numeric_precision,numeric_precision_radix,character_maximum_length, column_name as \"Field\" ,udt_name as \"Type\",is_nullable as \"Null\", '' as \"Key\", column_default as \"Default\",'' as \"Extra\"  FROM information_schema.columns WHERE table_name ='$table' ORDER BY ordinal_position");
		foreach($q2 as $k=>$v){
			if($v["Field"]=='id'){
				$q2[$k]['Key']='PRI'; //Yes, its a hack, I know, so what
				$q2[$k]['Extra']='auto_increment'; 
				$q2[$k]['Default']=NULL; 
			}
			//Translate DataTypes
			$dict = array(
				'int4'=>'int',
				'timestamp'=>'datetime');

			if(isset($dict[$v["Type"]])){
				$q2[$k]["Type"]=$dict[$v["Type"]];
				$v["Type"]=$q2[$k]["Type"];
			}
			
			if(!in_array($v["Type"],array('datetime','text'))){
				//add length
				$length = $v['numeric_precision'].
				$v['numeric_precision_radix'].
				$v['character_maximum_length'];
				if($q2[$k]["Type"]=='int'){
					$length=10;		//horrible, but needed.
				}
				$q2[$k]["Type"] = $v["Type"].'('.$length.')';
			}
			if(preg_match("/^'(.*)'::/",$v["Default"],$matches)){
				$q2[$k]["Default"] = $matches[1];
			}else{
				//god help us
			}
			unset($q2[$k]['numeric_precision']);
			unset($q2[$k]['numeric_precision_radix']);
			unset($q2[$k]['character_maximum_length']);
			
		}
		return($q2);
	}
	/** 
	 * attempts to emulate my-sql show tables
	 * */
	function table_list(){
		return($this->q2obj("SELECT table_name as \"Tables_in_".$this->dbdatabase."\" FROM information_schema.tables WHERE table_schema = 'public'"));
	}
	/** maps to pg_fetch_array(),
	 * $mode is a Constant, that indicates "array", "object", or "both", 
	 * it defaults to "object", please check pg_fetch_array documentation for more information
	 * */
	function fetch($mode=PGSQL_ASSOC){
		return(pg_fetch_array($this->res,NULL,$mode));
	}
	/** maps to pg_affected_rows(), useful on insert,update,delete for successful execution checking */
	function affected(){	
		return(pg_affected_rows($this->res));
	}
	/** maps to pg_num_rows(),useful for pagination, et al */
	function rows($res){	
		return(pg_num_rows($res));
	}
	function alter_field_name($table_name,$old_field,$new_field,$datatype,$nullable,$default){
		//$_GET["TipoSql"]
		$this->sql('ALTER TABLE '.$table_name.' CHANGE '.$old_field.' '.$new_field.' '.$datatype.' '.(($nullable=='YES')?' ':' NOT NULL ').($default!=''?' DEFAULT \''.$default."'":'');

	}
	function alter_drop_field($table_name,$field_name){
		$alter_sql='ALTER TABLE '.$table_name.' DROP '.$field_name.' ';
		$this->sql($alter_sql);
		return($alter_sql);
	}
	function alter_rename_table($table_name,$new_name){
		$alter_sql = "ALTER TABLE ".$table_name." RENAME TO ".$new_name;
		$this->sql($alter_sql);
		return($alter_sql);
	}

}	
?>
