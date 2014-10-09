<?php
global $is_connected_to_db,$global_link,$database_queries,$database_sql_queries,$database_query_count;
include(dirname(__FILE__)."/any.php");
class db extends any_db {

	/**
	 * This is a wrapper for generic DB functionality, it allows us to use any database, as long as what's called is SQL92
	 * all database access whould be done trough here
	 * */
	var $engine_name='MYSQL';
	var $q2op_trim_length=40;	//max length of SELECT name text in q2op
	var $dblink=null;
	var $dbdatabase='none';		//these constants are got from ./config.php, 
					//make shure you include that somewhere
	var $dbhost='localhost';
	var $dbname='root';
	var $dbpwd='';
	var $res=0;
	var $is_connected=0;
	function sql_quote($a){
		return("`$a`");
	}
	
	/** recieves variable names */
	function concat(){
		$all = func_get_args();
		if(is_array($all[0])){
			$all=$all[0];
		}
		return("concat(".implode(",",$all).")");
	}
	function real_connect($host1, $name1,$pwd1){
		return(mysql_connect($host1, $name1,$pwd1));
	}
	function real_error(){
		return(mysql_error());
	}
	function real_select($db){
		return(mysql_select_db($db));
	}
	function real_sql($sqlstring){
		return(mysql_query($sqlstring,$this->dblink));
	}
	function db($c=1){
		$this->any_db($c);
	}
	/** 
	 * fixes sql duplicate entry 0 for key 1 problem in mysql5
	 * */
	function db_startup(){
		#$this->sql("set sql_mode = \"NO_AUTO_VALUE_ON_ZERO\" ");
	}
	function db_paginate_sql($page_number){
		global $config;
		return("\n LIMIT ".$config['pagination_limit']." OFFSET ".($config['pagination_limit']*$page_number)." ");
	}
	function database_list(){
		return($this->q2obj("show databases"));
	}
	/** maps to mysql_insert_id(), it's not SQL92 */
	function last_id(){
		return(mysql_insert_id());
	}
	/** maps to sql.describe, i'm not sure about its compatibility w/ SQL92 */
	function describe($table){
		return($this->q2obj("DESCRIBE ".$table));
	}
	/** maps to sql."show tables", it's not SQL92 */
	function table_list(){
		return($this->q2obj("SHOW TABLES"));
	}
	/** maps to mysql_fetch_array(),
	 * $mode is a Constant, that indicates "array", "object", or "both", both
	 * it defaults to "object", please check mysql_fetch_array documentation for more information
	 * */
	function fetch($mode=MYSQL_ASSOC){
		return(mysql_fetch_array($this->res,$mode));
	}
	/** maps to mysql_affected_rows(), useful on insert,update,delete for successful execution checking */
	function affected(){	
		return(mysql_affected_rows($this->dblink));
	}
	/** maps to mysql_num_rows(),useful for pagination, et al */
	function rows($res){	
		return(mysql_num_rows($res));
	}
	function alter_field_name($table_name,$old_field,$new_field,$datatype,$nullable,$default){
		//$_GET["TipoSql"]
		$this->sql('ALTER TABLE '.$table_name.' CHANGE '.$old_field.' '.$new_field.' '.$datatype.' '.(($nullable=='YES')?' ':' NOT NULL ').($default!=''?' DEFAULT \''.$default."'":''));

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
