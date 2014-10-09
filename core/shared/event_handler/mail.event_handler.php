<?php
/**
 * parameters: 
 * to
 * from
 * subject
 * message
 * */
class mail_event_handler extends default_event_handler{
	var $def='to,from,subject,message:textarea';
	function mail_event_handler(){
		$this->default_event_handler();
	}
	function run(){
		$f = fopen('mails.txt','a+');
		fwrite($f,print_r($_GET,true).$this->parameters."\n".date('Y m d')."\n");
		fclose($f);
	}
}
