<?php
global $is_connected_to_db,$global_link,$database_queries,$database_sql_queries,$database_query_count;
	/**
	 * This is a wrapper for generic DB functionality, it allows us to use any database, as long as what's called is SQL92
	 * all database access whould be done trough here
	 * */

class any_db extends common{
	var $q2op_trim_length=40;	//max length of SELECT name text in q2op
	var $dblink=null;
	var $dbdatabase='none';		//these constants are got from ./config.php, 
					//make shure you include that somewhere
	var $db_field_colum_name='Field';
	var $query_failed=0;		//
	var $query_error='';		//

	var $dbhost='localhost';
	var $dbname='root';
	var $dbpwd='';
	var $res=0;
	var $is_connected=0;

	/* code to run just after you connected (enviroment setup) */
	function db_startup(){
	
	}
	function sql_quote($a){
		return("`$a`");
	}
	function sql_quoted_single_comma(){
		return("\'");
	}
	function cast_as_string($field){
		return($field);
	}
	function concat(){
		$all = func_get_args();
		if(is_array($all[0])){
			$all=$all[0];
		}
		return(implode(" || ",$all));
	}
	/**
	 * Class Constructor, connects to the database.
	 * @param $c whether or not to connect to the database
	 * */
	function any_db($c=1){
		global $config,$global_link,$is_connected_to_db;
		$this->dbdatabase=$config['database_name'];
		$this->dbhost=$config['database_host'];
		$this->dbname=$config['database_user'];
		$this->dbpwd=$config['database_password'];
		if($is_connected_to_db==1){
			$this->dblink=$global_link;
		}
		if($c==1){
			//this no longer connects
		}
		$this->db_connect();
		$this->db_startup();
		$this->common();
	}

	/**
	 *
	 * */	

	function db_connect(){
		$this->dblink=$this->cnn(
			$this->dbdatabase,
			$this->dbhost,
			$this->dbname,
			$this->dbpwd
		);
	}
	
	/**
	 * Returns the SQL necesary to paginate, since it's not standard SQL.
	 *
	 * I know that pagination is much more complex in other 
	 * systems, like oracle, where you have rownum, and let's not talk about
	 * mssql, but at least its here, and not in std.php !
	 *
	 */
	function db_paginate_sql($page_number){
		global $config;
		return("\n LIMIT ".$config['pagination_limit']." OFFSET ".($config['pagination_limit']*$page_number)." ");
	}
	/**
	 * removes anything that could be used for SQL injection, 
	 * anything that comes from the user, should be filtered trough here first.
	 * it also removes html attacks.
	 * */

	function remove_strange_chars($txt){
		return(str_replace(array('<','>',"'",";","&","\\"),'',$txt));
	}
	function remove_non_html($txt){
		return(str_replace(array("'",";","\\"),'',$txt));
	}
	function remove_html($txt){
		return(str_replace(array("<",">"),'',$txt));
	}
	/*
	 * 	Returns a bi-dimensional array, with the result of the sql query,
	 * 	in a suitable way for an input field,
	 *	it can be used, for instance, to get a list of values, name, value, from 2 fields, id and first varchar.
	 *	@param $sql a SQL query to be run
	 *	@param $f1 the first fields (acts as the ID)
	 *	@param $f2 the second field (ascts as the description field, like Name, for instance)
	 *	@param $trim can take two values, "no" and "trim"
	 *
	 * */

	function q2op($sql,$f1='id',$f2='name',$trim='no'){
		$q=$this->q2obj($sql);
		$r=array();
		foreach($q as $k=>$row){
			if($trim=='no'){
				$v=$row[$f2];
			}elseif($trim=='trim'){
				$v=$this->cut($row[$f2],$this->q2op_trim_length);
			}else{
				std::log_once('invalid parameter:$trim','ERROR');
				$v=$row[$f2];
			}
			$r[$row[$f1]]=$v;
		}
		return($r);
	}
	/**
	 *returns "show databases", since it's not SQL92
	 * */
	function database_list(){
		return($this->q2obj("show databases"));
	}
	function real_connect($host1, $name1,$pwd1){
	
	}
	function real_select($db){
		return(true);
	}
	function real_error(){
		return("error?");
	}
	/**
	 * This function connects you to the database.
	 * altough the function might be called several times, by several objects that inherith this class or STD (which inheriths from DB),
	 * the function only connects to the database once, and stopres the db link in $global_link
	 * this is done to improve performance.
	 *
	 * @param $data1 database_name (usually in the config File) when the database is '', we just connect to the server, not to the database $data1
	 * */
	function cnn($data1,$host1,$name1,$pwd1){
		global $is_connected_to_db,$global_link;
		if(!$is_connected_to_db){
			///echo("Connection Trace".b2());
			$link = $this->real_connect($host1, $name1,$pwd1) or die("error 1:" . $this->real_error());
			$global_link = $link;
			$is_connected_to_db = 1;
			///p2($global_link);
		}else{
			$link=$global_link;
		}
		if($data1!=''){
			$this->use_db($data1);
		}
		
		return($link);	
	}
	/**
	 *  returns true or false depending on BD connectivity (but displays no error message), nor it die()s
	 *  follows the presime of "easier to ask for forgiveness rather than ask for permission"
	 *  so ill just apologize to the user, and log the error somewhere
	 * */
	function ping(){
		$link = $this->real_connect($this->dbhost, $this->dbname, $this->dbpwd);
		if(!$link){
			return(FALSE);
		}
		if($this->dbdatabase!=''){
			if($this->real_select($this->dbdatabase)){
				return(TRUE);
			}
		}else{
			//no DB given, but server connect ok (.gen, _util, etc)
			return(TRUE);
		}
		return(FALSE);
	}

	/** maps to mysql_insert_id(), it's not SQL92 */
	function last_id(){
		return(-1);
	}
	/** Allows you to change the current database, for inter-database operability. */
	function use_db($data1){
		global $i18n_std;
		if(!$this->real_select($data1)){
			echo('no db connection:'.$data1.' ('.$this->real_error().') engine='.$this->engine_name);
			//.'cwd='.getcwd()
			die('');
		}
	}
	/** maps to sql.describe, i'm not sure about its compatibility w/ SQL92 */

	function describe($table){
		return(array());
	}
	function table_list(){
		return(array());
	}
	function fetch(){
		return(array());
	}
	/*
	 * Executes an SQL query, and returns a result-set, keep in mind that UPDATES, and INSERTS (among other)
	 * don't return a valid result-set (since there isn't any)
	 * */
	function sql($sqlstring,$notes='',$allow_fail=0){
		global $is_connected_to_db,$global_link,$main;

		//RESET ERROR STATE
		$this->query_failed=0;
		$this->query_error='';


		#this function send a query to the db engine and returns the result.
	//	echo('icdb='.$is_connected_to_db);
		if(!$is_connected_to_db){
			$this->db_connect();
		}
		global $database_queries,$database_query_count,$database_sql_queries;
		$q=explode(' ',$sqlstring);

#		if(DEBUG){ //THIS IS DBLOG; BUT CANNOT CALL DBLOG; SINCE IS OR ISNT STATIC!
#			$database_query_count++;
#			$database_queries.="<h2>--".$q[0]."</h2><hr/><pre>".$sqlstring.'</pre>';
#			$database_sql_queries[]=$sqlstring;
#		}
		//p2($global_link);
		//echo("mysql_query Trace".b2());

		$this->res = $this->real_sql($sqlstring,$this->dblink);
		if($this->res=='' && $allow_fail==1){
			$this->query_failed=1;
			$this->query_error = "SQL error: " . htmlentities($this->real_error());
			return("FAILED");
		}
		if($this->res==''){
			$database_query_count++;
			if(isset($_GET['__output_type']) && $_GET['__output_type']!='HTML'){
				$err_text=htmlentities($this->real_error());
				$err_text.=(DEBUG? ("\n\n".htmlentities($sqlstring)):'.');

			}else{
				$this->head();//ERROR?
				echo($this->get_log());
				$err_text = "DEBUG=".DEBUG." SQL error: " . htmlentities($this->real_error()). ($txt .$txt2. "<br/><pre>".htmlentities($sqlstring)."</pre>");
			}
			#$s = new std();
			$this->error($err_text,'SQL001');
			if(DEBUG){
				$this->trace();
				$this->foot();
			}
			die("");
		}
		return($this->res);
	}
	/** maps to mysql_affected_rows(), useful on insert,update,delete for successful execution checking */
	function affected(){	
		return(0);
	}
	/** maps to mysql_num_rows(),useful for pagination, et al */
	function rows($res){	
		return(0);
	}
	/** 
	 * returns a bi-dimentional array, with the rows in the form key=>value *
	 * */

	function dblog($sql,$result,$notes=''){
		global $database_queries,$database_sql_queries,$database_query_count;
		$database_sql_queries[]=$sql;
		if(is_array($result[0])){
			$titles=array_keys($result[0]);
			include_once(STD_LOCATION.'include/std_tab.php');
			$t = new tab();
			$t->name='sql'.rand(0,99999);//$database_query_count
			
			$t->add_tab('Null',$notes);
			$t->add_tab('Query',"<xmp>$sql</xmp>");
			$t->add_tab('Trace',b2());
			$t->add_tab('Results',common::table(
				$result,
				$titles,
				array(
					'title'=>'Query Results',
					'style'=>'db_result',
					'border'=>1)));
			$database_queries.=$t->out();
		}
	}
	/** returns an array of sql run for execution.
	 * useful if you wish to save such SQl queries, or send them somewhere, etc
	 * */
	function queries_run(){
		global $database_sql_queries;
		return($database_sql_queries);
	}
	function q2obj($sql,$notes='',$allow_fail=0){
		if(DEBUG){
			$mtime = microtime();
			$mtime = explode(' ', $mtime);
			$mtime = $mtime[1] + $mtime[0];
			$starttime = $mtime;
		}
		$r =$this->sql($sql,$notes,$allow_fail);
		if($this->query_failed==1 || $r =='FAILED' && $allow_fail==1){
			return(array("FAIL"=>"QUERY FAILED"));
		}
		$result=array();
		while($row=$this->fetch()){
			$result[]=$row;
		}
		if(DEBUG){
			$mtime = microtime(); 
			$mtime = explode(" ", $mtime); 
			$mtime = $mtime[1] + $mtime[0]; 
			$endtime = $mtime; 
			$totaltime = ($endtime - $starttime); 
			$notes = $notes.' <br/>RUNS IN:' .$totaltime. ' seconds.'; 

			$this->dblog($sql,$result,$notes);
		}
		return($result);
	}
	function q2js($all,$name,$idfield,$script_tag=1,$quote_values=0){
		$dx6='';
		$dx7='';
		$q="";
		$aa="[]";
		if($quote_values==1){
			$q="'";
			$aa="{}";
		}
		if($script_tag==1){
			$dx6="<script>;";
			$dx7=";</script>";
		}
		$out="$dx6\n{$name}=$aa;";
		foreach($all as $row){
			$dx="\n{$name}[$q".$row[$idfield]."$q]=";
			$dx1=array();
			foreach($row as $property_name=>$property_value){
				$dx1[]="'$property_name':'".str_replace(array("\r","\n","'"),array("",'',''),$property_value)."'";
			}
			$dx.='{'.implode(",",$dx1).'}';
		
			$out.=$dx;
		}
		if($script_tag==1){
			$out.=$dx7;
		}
		return($out);
	}

	/** returns all the SQL statements that were executed, VERY useful for debugging. */
	function get_trace(){
		global $database_queries,$database_query_count,$is_connected_to_db,$global_link;
		return("<div style='width:100%'></div>".$this->get_shadow("<h1>Mysql Trace information</h1>Note: this information s for debug purposes only, and will not be shown in the final product.<br/>Query Count:".
			'db used:'.$this->dbdatabase.'<br/>Connected to Database:'.(int)$is_connected_to_db.'<br/>Link:'.$global_link.'<br/> Query Count: '.
			$database_query_count.'<br/>'.$database_queries,$this->default_style,'center','80%'));
	}
	/** prints all the SQL statements that were executed, VERY useful for debugging. */
	function trace(){
		echo($this->get_trace()/(1024*1024)." MB");
	}

}	

?>
