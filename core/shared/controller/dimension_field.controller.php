<?php 
#Generated on 2009-05-18 17:13:27 by 1:Administrator
class dimension_field_model extends std {
	function get_report_id($view_name){
		if($view_name == "new"){
			if($_GET["__auto_field_name"]=="drill_down_report_id"){
				//we know which report it is
				$report_id = $_GET["__auto_field_value"];
			}else{
				$report_id = -1;
			}
		}elseif($view_name == "edit_all"){
			$t = $this->q2obj("SELECT drill_down_report_id FROM ".$this->get_table_name()." WHERE ".$this->get_table_name().".id='".$this->remove_strange_chars($_GET["id"])."'");
			$report_id = $t[0]["drill_down_report_id"];
		}
		return($report_id);
	}
	function parse_view($view){
		global $config;
		$view = std::parse_view($view);
		#WHERE DO YOU GET TABLE_NAME FROM ?


		if($view["view_name"] == "edit_all" || $view["view_name"] == "new" ){
			$report_id =$this->get_report_id($view["view_name"]);
			if($report_id == -1){
				// I don't know which table, so please enter field manually
				$view["fields"]["name"]["type"]='text';
				return($view);
			}
			$t = $this->q2obj("select table_name from drill_down_report WHERE id='$report_id'");
			if(count($t)>0){
				$t = $t[0]["table_name"];
			}else{
				$view["fields"]["name"]["options"]=array('ERROR'=>'ERROR TABLE DOES NOT EXIST, PLEASE CHECK ID:'.$report_id);
				return($view);			
			}
			$old_db = $config['database_name'];
			$change_connection=0;
			if(strpos($t,'.')!==FALSE){
				//has dot
				$parts = explode('.',$t);
				$t=$parts[1];
				$new_db = $parts[0];
				$change_connection=1;
				$this->use_db($new_db);
			}

			//check for table existence
			$d = $this->table_list($t);
			$table_list = array();
			foreach($d as $row){
				foreach($row as $cell){
					$table_list[$cell]=$cell;
				}
			}
			if(!isset($table_list[$t])){
				//table does not exist.
				$view["fields"]["name"]["options"]=array('ERROR'=>'ERROR TABLE DOES NOT EXIST, PLEASE CHECK');
				return($view);
			}

			$d =$this->describe($t);
			$r = array();
			
			foreach($d as $row){
				$r[$row["Field"]]=$row["Field"];
			}
			$view["fields"]["name"]["options"]=$r;
			$this->use_db($old_db);
		}
			
		return($view);
	}
	function dimension_field_model(){$this->std();
	}
	var $table='dimension_field';
	var $id='id';
	var $ifield='name'; 
	
	var $mod_get_kids_fields=array('id',"name","label","chart_type_id");
}
?>
