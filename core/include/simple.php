<?php
$q2 = $this->get_view_dataset($view);
$headers = $this->get_headers();
$h2=array();
foreach(array_keys($q2[0]) as $v){
	$h2[$v]=$headers[$v];
}

$result = $this->table(
	$q2,
	$h2,
	array('title'=>$this->i18n($view['title']),'style'=>'list','border'=>0));
?>
