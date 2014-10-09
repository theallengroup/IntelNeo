<?php

if(count($view["conditions"])>0){
	$sql_where = "\n WHERE \n\t".implode("\n\t AND ",$view["conditions"]);
}else{
	$sql_where='';
}
$sql_fields=array();
$labels=array();
$t2=array();
foreach($view["fields"] as $fname=>$field){
	$d = explode(".",$fname);
	$m = $this->load_file($d[0]);
	$t2[$d[0]]=$d[0];
	$short_field_name = $d[1];
	$labels[] = $m->fi($short_field_name);
	$sql_fields[]=$fname.' AS '.str_replace('.','_',$fname);
}
$sql = ("SELECT \n\t".implode(",\n\t",$sql_fields)."\n FROM \n\t".implode(",\n\t",$t2).' '.$sql_where);

$result.=$this->shadow($this->table($this->q2obj($sql),$labels,array('style'=>'list','title'=>$view["title"],'border'=>0)));


?>
