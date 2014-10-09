<?php
global $is_connected_to_db,$global_link,$database_queries,$database_sql_queries,$database_query_count;
include(dirname(__FILE__)."/any.php");

class db extends any_db {
	var $engine_name='SQLITE';
	var $sqliteerror;
	var $pragma_fixed=0;
	
	function db($c=1){
		$this->any_db($c);
	}
	/** there is only one database */
	function database_list(){
		return(array($this->dbdatabase));
	}
	function real_connect($host1, $name1,$pwd1){
		return(sqlite_open($this->dbdatabase, 0666, $this->sqliteerror));
	}
	/** 
	 * since you cannot change db's, then I guess I'm always true
	 * */
	function real_select($data1){
		return true;
	}
	function sql_quote($a){
		return("\"$a\"");
	}
	function sql_quoted_single_comma(){
		return("''");
	}

	function real_error(){
		return(sqlite_error_string(sqlite_last_error($this->dblink)) .':'. $this->sqliteerror);
	}
	function last_id(){
		#returns last insert id
		return(sqlite_last_insert_rowid($this->dblink));
	}
	/**
	 * @todo describe compatibility with mysql
	 * 		#Field        | Type         | Null | Key | Default | Extra
	 */
	function describe($table){
		return($this->q2obj("PRAGMA table_info('".$table."')"));
	}
	function table_list(){
		return($this->q2obj("select * from sqlite_master"));
	}
	function fetch($mode=SQLITE_ASSOC){
		return(sqlite_fetch_array($this->res,$mode));
	}
	function real_sql($sqlstring){
		if($this->pragma_fixed==0){
			sqlite_query($this->dblink,'PRAGMA short_column_names = 1');
		}
		return(sqlite_query($this->dblink,$sqlstring));
	}
	function affected(){	
		return(sqlite_changes($this->dblink));
	}
	function rows($res){	
		return(sqlite_num_rows($res));
	}
	function alter_field_name($table_name,$old_field,$new_field,$datatype,$nullable,$default){
		//$_GET["TipoSql"]
		die("NOT IMPLEMENTED");
		#$this->sql('ALTER TABLE '.$table_name.' CHANGE '.$old_field.' '.$new_field.' '.$datatype.' '.(($nullable=='YES')?' ':' NOT NULL ').($default!=''?' DEFAULT \''.$default."'":'');

	}
	function alter_drop_field($table_name,$field_name){
		die("NOT IMPLEMENTED");
			$alter_sql='ALTER TABLE '.$table_name.' DROP '.$field_name.' ';
		$this->sql($alter_sql);
		return($alter_sql);
	}
	function alter_rename_table($table_name,$new_name){
		die("NOT IMPLEMENTED");
			$alter_sql = "ALTER TABLE ".$table_name." RENAME TO ".$new_name;
		$this->sql($alter_sql);
		return($alter_sql);
	}

}	

?>
