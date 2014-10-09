<?php
/**
 * parameters: 
 * to
 * from
 * subject
 * message
 * */
class sql_event_handler extends default_event_handler{
	var $def='sql_statement:textarea';
	function sql_event_handler(){
		$this->default_event_handler();
	}
	function run(){
		global $main;
		$log='';
		foreach(explode(";",$this->parameters["sql_statement"]) as $s){
			$log.="\n\nRunning SQL:". $s ;
			$main->sql($s);
			$log.="\n\tAffected:".$main->affected();
			$log.="\n\tLast ID:".$main->last_id();
		}
		$f = fopen('sqls.txt','a+');
		
		fwrite($f,"\n----------".date('Y m d H:i')."----------\n".$log);
		fclose($f);
	}
}
