<?php
require_once(INCLUDE_DIR."rlib/report.lib.php");
ob_start();
$view["table"]=$this->get_table_name();
$view["preserve"]['mod']='mod';
$view["preserve"]['ac']='ac';
foreach(array('dimension_fields','data_fields') as $ftype){
	foreach($view[$ftype] as $df_name=>$df_array){
		if(!is_array($df_array)){
			$df_array=array('value'=>$df_array);
		}
		if(!isset($df_array['label'])){
			$clean_df_name = str_replace(array("sum(","count(",")"),'',strtolower($df_name));
			$df_array['label']=$this->fi($clean_df_name);
		}
		$view[$ftype][$df_name]=$df_array;
	}
}

show_report($view);
$result = ob_get_contents();
ob_end_clean();
?>
