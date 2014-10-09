<?php
require_once(STD_LOCATION."include/std_pivot.php");
require_once(STD_LOCATION."include/std_chart.php");
include_once(STD_LOCATION.'include/std_tab.php');

echo("<a href='?force_menu=1'>Volver al Men&uacute;</a>&nbsp;&nbsp;&nbsp;");
$p = new std_pivot();
$ptable = $view;
$p->title=ucwords($view["title"]);
$t = $this->get_table_name();
if(isset($view["table"])){	
	$t = $view["table"];
}
$p->set_data_source($t);
$p->use_foreign_info=1;
$p->add_row_field($view["side"],$view["side"]);
$p->add_column_field($view["top"],$view["top"]);
$p->add_data_field($view["data"]);

if(isset($view["show_totals"])){//defaults to 1
	$p->show_totals = $view["show_totals"];
}


if(isset($view["restrict"])){
	$p->add_restriction($view["restrict"]);
}else{
	$p->add_restriction('9=9');
}
if(isset($config['userfilter']) && $config['userfilter']==1){
	$uf = $this->load_file("userfilter");
	$userfilter=$uf->get_user_filter($t);
	$p->add_restriction($userfilter);
}
if(isset($view["parameters"])){
	$p->parameters = $view["parameters"];
}
$p->add_restriction($this->default_sql_filter($this->get_table_name()));
$p->only_columns_greater_than_zero=1;

ob_start();
echo($p->out());
$o = ob_get_contents();
ob_end_clean();

$data = $p->data_cache;

#p2($p);
#$results = $this->shadow();
#$results.=gp2($data,'red');
#$results.=gp2($data,'red');
#$results.=$this->table($data);
#$results.=gp2($p->get_columns_tree(),'blue');
#$results.=gp2($view,'red');


$a = array();
$b = array();
$m=array();
$d2=array();
foreach($data as $k=>$row){
	$m=array();
	$current_category = $row[$view["side"]];
	unset($row[$view["side"]]);
	$d2[$current_category]=array();
	foreach($row as $cell_name=>$cell){
		$temp1=$cell_name;
		$temp1=explode("///",$temp1);
		$temp1=$temp1[0];
		$m[]=$temp1;
		$d2[$current_category][]=$cell;//add value set
	}
}

$d = new std_chart();
$d->title=ucwords($view['title']);
$d->type= "multiple.series.3d";
$d->xtitle= " ";
$d->ytitle= " ";
$d->set_headers($m);
foreach($d2 as $category=>$ds){
	$d->add_entry($category,$ds);
}

$t = new tab();	
$t->add_tab('Gr&aacute;fico',$d->out());
//total is broken, doees not consider, user filter, or runtime filters.
$n=$this->q2op("select 0 as id,sum(".$view["data"].") as name from ".$view['table'].' WHERE ('.implode(" AND ",$p->restrictions).")",'id','name');
$t->add_tab('Tabla',$o."<div class='std_pivot_cell std_pivot_total std_pivot_group'><br/>N = ".$n[0]."</div>");
$results = $t->out();		

?>
