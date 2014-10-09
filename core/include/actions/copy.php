<?php

	
$this->menu();
$copy_name = $this->get_table_name()."_copy";
$sql='DROP TABLE IF EXISTS '.$copy_name;
$this->sql($sql);
$sql='CREATE TABLE IF NOT EXISTS '.$copy_name."(";
foreach($this->fields as $k=>$f){
	if($k==$this->id){
		$sql.="$k int(10) not null auto_increment,";
	}else{
		$sql.="$k varchar(200),";
	}
}
$sql.="primary key(id))";
$this->sql($sql);
$this->sql("INSERT INTO $copy_name (SELECT * FROM ".$this->get_table_name().")");
$this->msg("Copied:".$this->affected()." Records.");

?>
