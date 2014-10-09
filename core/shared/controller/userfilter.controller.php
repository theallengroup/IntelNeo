<?php #2009-01-03 16:44:02
class userfilter_model extends std{
	function get_user_filter($report_name){
		$q2op = $this->q2op("SELECT id,sql_filter from ".$this->get_table_name()." WHERE usr_id='".ssid()."' and reporte='".$this->remove_strange_chars($report_name)."'",'id','sql_filter');

		return(str_replace("#","'","(".implode(" AND ",array_merge(array("1=1"),$q2op)).")"));
	}
	function userfilter_model(){
		$this->std();
	}
	var $ifield='sql_filter';
	var $id='id';
	var $table='userfilter';
}
?>
