<?php
/**
 * parameters: 
 * file_name
 * */
class logsql_event_handler extends default_event_handler{
	var $def='file_name';
	function logsql_event_handler(){
		$this->default_event_handler();
	}
	function run(){
		global $database_sql_queries;
		$log='';
		$f = fopen("./logs/".$this->parameters["file_name"].'.log.txt','a+');
		fwrite($f,"\n----------".date('Y m d H:i')."----------GET:\n".gp2($_GET)."\nQueries:\n".implode("\n\n\n------------------\n\n\n",$database_sql_queries));
		fclose($f);
	}
}
