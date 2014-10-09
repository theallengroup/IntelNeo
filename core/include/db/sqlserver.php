<?php
global $is_connected_to_db,$global_link,$database_queries,$database_sql_queries,$database_query_count;
include(dirname(__FILE__)."/any.php");
class db extends any_db {

	//sql server only:
	var $current_query_id=null;
	var $current_affected_rows=0;
	/**
	 * This is a wrapper for generic DB functionality, it allows us to use any database, as long as what's called is SQL92
	 * all database access whould be done trough here
	 * */
	var $engine_name='SQLSERVER';
	var $q2op_trim_length=40;	//max length of SELECT name text in q2op
	var $dblink=null;
	var $dbdatabase='none';		//these constants are got from ./config.php, 
					//make shure you include that somewhere
	var $dbhost='localhost';
	var $dbname='root';
	var $dbpwd='';
	var $res=0;
	var $is_connected=0;
	var $db_field_colum_name='TABLE_NAME';

	function cast_as_string($field){
		return("cast($field as varchar)");
	}

	/** recieves variable names */
	function concat(){
		$all = func_get_args();
		if(is_array($all[0])){
			$all=$all[0];
		}
		return(implode("+",$all));
	}
	function real_connect($host1, $name1,$pwd1){
		$connectionInfo = array(
			"UID"=>$name1,
			"PWD"=>$pwd1,
			"Database"=>"auditor",	#problem arises.
		);
		$this->dblink = sqlsrv_connect( $host1, $connectionInfo);
		return($this->dblink);
	}
	function real_error(){
		$a = sqlsrv_errors();
		$dx='';
		foreach($a as $k=>$v){
			$dx.="\n".$v["SQLSTATE"]."\n".$v["message"];
		}
		return($dx);
	}
	function real_select($db){
		#USE AdventureWorks
		$this->real_sql("USE $db");
		return(true);//THIS MIGHT NOT BE THE CASE @todo
	}
	function real_sql($sqlstring){
		#echo($sqlstring);
		//SOME REWRITING OF THE SQL SO EVERYTHING WORKS OK.
		//REWRITES LIMIT, AND INSERT, so they behave better.
		$sql2 = preg_split("/\s+/",trim(strtoupper($sqlstring)));

		if($sql2[0]=='UDPATE'||$sql2[0]=='DELETE'){
			$sqlstring.='; SELECT rows=@@ROWCOUNT';	
		}
		
		if($sql2[0]=='INSERT'){
			$sqlstring.='; SELECT SCOPE_IDENTITY() AS IDENTITY_COLUMN_NAME';	
		}
		if($sql2[0]=='DESC'||$sql2[0]=='DESCRIBE'){
			$sqlstring='EXEC SP_COLUMNS '.$sql2[1];
		}
		if($sql2[0]=='SHOW'&&$sql2[1]=='TABLES'){
			$sqlstring='EXEC SP_TABLES';
		}
		if($sql2[0]=='SELECT'){

			//DOES NOT WORK FOR SUBQUERIES
			$regex1 = "/\s*(LIMIT|limit)\s+(\d+)\s*((\s*,\s*(\d+))?|(\s+(offset|OFFSET)\s+(\d+))?)?\s*$/";
			#$regex1 = "/LIMIT/";
			//limit = 2
			//offset 8
			if(preg_match($regex1,trim(strtoupper($sqlstring)),$flags)){
				#echo("<br/>MATCHED.".gp2($flags));
				$sqlstring = preg_replace($regex1,"",$sqlstring);
				$offset = $flags[8];
				#echo($offset);
				$sqlstring = preg_replace("/^\s*(SELECT|select)(.*)/","SELECT TOP ".$flags[2]." \\2",trim($sqlstring));
			}else{
				#echo("<br/>".$sqlstring." DID NOT MATCH $regex1");
			}
		}
		
		$r = sqlsrv_query($this->dblink,$sqlstring,array());
		if($sql2[0]=='UDPATE'||$sql2[0]=='DELETE'){
			sqlsrv_next_result($r);
			sqlsrv_fetch($r);
			$this->current_affected_rows = sqlsrv_get_field($r, 1);
		
		}
		if($sql2[0]=='INSERT'){
			sqlsrv_next_result($r);
			sqlsrv_fetch($r);
			$this->current_query_id = sqlsrv_get_field($r, 1);
		}

		return($r);
	}
	function db($c=1){
		$this->any_db($c);
	}
	/** 
	 * */
	function db_startup(){
		#$this->sql("set sql_mode = \"NO_AUTO_VALUE_ON_ZERO\" ");
	}
	/** 
	 * EMITS m y s q l function than later is replaced.
	 * */
	function db_paginate_sql($page_number){
		global $config;
		return("\n LIMIT ".$config['pagination_limit']." OFFSET ".($config['pagination_limit']*$page_number)." ");
	}
	function database_list(){
		#select name from master..sysdatabases
		return($this->q2obj("exec sp_databases"));
	}
	function last_id(){
		return($this->current_query_id);
	}
	/** maps to sql.describe, i'm not sure about its compatibility w/ SQL92 */
	function describe($table){
		return($this->q2obj("exec sp_columns ".$table));
	}
	/** maps to sql."show tables", it's not SQL92 */
	function table_list(){
		global $config;
		$f = $this->q2obj("exec sp_tables");
		$a=array();
		foreach($f as $k=>$v){
			if($v["TABLE_TYPE"]=='TABLE'){
				$b=$v;
				$b["Tables_in_".$config["database_name"]] = $v[$this->db_field_colum_name];
				$a[]=$b;
			}
		}

		return($a);
	}
	/** maps to sqlsrv_fetch_array(),
	 * $mode is a Constant, that indicates "array", "object", or "both", 
	 * it defaults to "object", please check sqlsrv_fetch_array documentation for more information
	 * */
	function fetch($mode=SQLSRV_FETCH_ASSOC){
		$r = sqlsrv_fetch_array($this->res,$mode);
		if($r==NULL){
			return(null);

		}
		$md =sqlsrv_field_metadata($this->res);
		#p2($md);

		foreach($r as $k=>$value){
			if(strtoupper(get_class($value))=="DATETIME"){
				$r[$k]=date_format($value,"Y-m-d h:m:s");
			}
		}

		return($r);
	}
	/** maps to affected_rows(), useful on insert,update,delete for successful execution checking */
	function affected(){	
		return($this->current_affected_rows);
	}
	/** maps to num_rows(),useful for pagination, et al */
	function rows($res){	
		return(sqlsrv_num_rows($res));
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
