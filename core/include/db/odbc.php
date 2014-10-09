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
	var $engine_name='ODBC';
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
	function sql_quote($a){
		return("[$a]");
	}
	
	function cast_as_string($field){
		return("cast($field as varchar)");
	}
	function sql_quoted_single_comma(){
		return("''");
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
		$this->dblink = odbc_connect( $host1, $name1,$pwd1);
		return($this->dblink);
	}
	function real_error(){
		//"[SQL_ODBC_ERROR:"."]:".
		
			$a = odbc_error().odbc_errormsg($this->dblink);//.gp2(error_get_last()) 
		$a=preg_replace('/\[Microsoft\]\[SQL Server.*\]\[SQL Server\]/',' ',$a);
		return($a);
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
		if($sql2[0]=='INSERT'){
			$sqlstring.='; SELECT @@identity AS IDENTITY_COLUMN_NAME';	
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
		
		$r = odbc_exec($this->dblink,$sqlstring,array());
		if($sql2[0]=='INSERT'){
			odbc_next_result($r);
			$this->res = $r;
			$irow = $this->fetch();
			$this->current_query_id = $irow["IDENTITY_COLUMN_NAME"];
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
		/**
		 * COLUMN_NAME=>FIELD
		 *	TYPE_NAME + (PRECISION)

Field	 Type	 Null	 Key	 Default	 Extra
id	int(10)	NO	PRI		auto_increment


if(TYPE_NAME) {int identity} => Key = PRI


TYPE_NAME	 PRECISION	IS_NULLABLE Key PRI



* */
		$q = $this->q2obj("exec sp_columns ".$table);
		$nq=array();
		foreach($q as $k=>$v){
			$t=$v["TYPE_NAME"]."(".$v["PRECISION"].")";
			if($v["TYPE_NAME"]=='datetime' || $v["TYPE_NAME"]=='date' || $v["TYPE_NAME"]=='text'){
				$t= $v["TYPE_NAME"];
			}
			if($v["TYPE_NAME"]=='int identity'){
				$t='int(10)';
			}
			$key='';
			$auto="";
			if($v["TYPE_NAME"]=='int identity'){
				$key = 'PRI';
				$auto='auto_increment';
			}
			$nq[]=array(
				'Field'=>$v["COLUMN_NAME"],
				'Type'=>$t,
				'Null'=>$v["IS_NULLABLE"],
				'Key'=>$key,
				'Default'=>'',
				'Extra'=>$auto,
			);
		}
		return($nq);
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
	/** maps to odbc_fetch_row(),
	 * $mode is a Constant, that indicates "array", "object", or "both", 
	 * it defaults to "object", please check odbc_fetch_row documentation for more information
	 * */
	function fetch($mode=NONE){
		$r = odbc_fetch_row( $this->res );
		if($r==NULL){
			return(null);
		}
		$field_count = odbc_num_fields($this->res); 
		
    		$r=array();
		for($i=1; $i <= $field_count; $i++){ 
	        	$r[odbc_field_name( $this->res,$i)] = odbc_result( $this->res, $i); 
	        }
		return($r);
	}
	/** maps to affected_rows(), useful on insert,update,delete for successful execution checking */
	function affected(){	
		return(odbc_num_rows($this->res));
	}
	/** maps to num_rows(),useful for pagination, et al */
	function rows($res){	
		return(odbc_num_rows($res));
	}
	/**
	 * @todo
	 * set null
	 * set default
	 * */
	function alter_field_name($table_name,$old_field,$new_field,$datatype,$nullable,$default){
		$this->sql("ALTER TABLE $table_name ALTER COLUMN $old_field $datatype");
		$this->sql("EXEC sp_rename '$table_name.$old_field',     '$new_field',     'COLUMN'");
	}
	function alter_drop_field($table_name,$field_name){
		
		$alter_sql="";
		
		$dc = "select 
		    col.name, 
		    col.column_id, 
		    col.default_object_id, 
		    OBJECTPROPERTY(col.default_object_id, N'IsDefaultCnst') as is_defcnst, 
		    dobj.name as def_name
		from sys.columns col 
		    left outer join sys.objects dobj 
			on dobj.object_id = col.default_object_id and dobj.type = 'D' 
		where col.object_id = object_id(N'dbo.$table_name') and col.name='$field_name'
		and dobj.name is not null";
		$constraints = $this->q2obj($dc);
		foreach($constraints as $c){
			$a3 = 'ALTER TABLE '.$table_name.' DROP CONSTRAINT '.$c['def_name'].' ';
			$this->sql($a3);
			$alter_sql.="\n".$a3;
		}
		//echo($alter_sql);
		$a2 = 'ALTER TABLE '.$table_name.' DROP COLUMN '.$field_name.' ';
		$alter_sql.="\n".$a2;
		$this->sql($a2);
		return($alter_sql);
	}
	function alter_drop_table($table){
	
	}
	function alter_rename_table($table_name,$new_name){
		$alter_sql = "sp_RENAME '".$table_name."','".$new_name."'";
		$this->sql($alter_sql);
		return($alter_sql);
	}


}	
?>
