<?php #2009-01-03 15:43:06
class userquery_model extends std{
	function ac_runast(){
		if(isset($_GET["__output_type"]) && strtolower($_GET["__output_type"])=='html'){
			$this->menu();
		}
		if($_GET["runast"]==''){
			$_GET["runast"]='csif';
		}
		require_once("../whocares/runast.php");

	}
	function ac_all_run(){
		$this->menu();
		$q = $this->q2obj("SELECT * from ".$this->get_table_name()." WHERE id='".$this->remove_strange_chars($_GET["id"])."' ");
		if(count($q)>0){
			$qf = $this->q2op("SELECT sql_filter from userfilter WHERE usr_id='".ssid()."' and reporte='".$q[0]["fuente_de_datos"]."'",'sql_filter','sql_filter');
			$sql = $q[0]["sql_text"];
			if(count($qf)>0){
				$sql_parts=explode("ORDER BY",$sql);

				$sql_parts[0].='AND ('.str_replace("#","'",implode(" OR ",$qf)).")";
				$sql = $sql_parts[0].' ORDER BY '.$sql_parts[1];
			}
			$this->e_table($this->q2obj($sql),'none',array('style'=>'list','title'=>$q[0]["titulo"],'border'=>0));
		}
	}
	function ac_all_edit(){
		$q = $this->q2obj("SELECT * from ".$this->get_table_name()." WHERE id='".$this->remove_strange_chars($_GET["id"])."' ");		
		if($q[0]["usr_id"]==ssid()){
			std::ac_all_edit();
			
		}else{
			$this->set_cmessage("Solo es posible editar consultas creadas por el usuario actual");
			$this->ac_b2l();
		}
	
	}
	function ac_all_delete(){
		$q = $this->q2obj("SELECT * from ".$this->get_table_name()." WHERE id='".$this->remove_strange_chars($_GET["id"])."' ");
		
		if($q[0]["usr_id"]==ssid()){
			std::ac_all_delete();
		}else{
			$this->set_cmessage("Solo es posible borrar consultas creadas por el usuario actual");
			$this->ac_b2l();
		}
	}
	function ac_all_new(){
		$this->ac_runast();
	}
	function userquery_model(){
		$this->std();
	}
	var $ifield='titulo';
	var $id='id';
	var $table='userquery';
}
?>
