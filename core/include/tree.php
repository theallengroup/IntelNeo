<?php // std.php
include_once(INCLUDE_DIR.'q2tree.php');

#p2($view,'red');
$q = $this->get_view_dataset($view);

$parent =$this->tree_get_parent_field($view['fields']);
$root = 1;
foreach($q as $row){
	if($row[$parent] == $row[$this->id]){
		$root = $row[$this->id];
		break;
	}
}
$tree = q2tree($q,$root_id,$this->id,$parent);
p2($q,'red');
p2($tree,'blue');
//$this->table($q2);
$result = "asdasd";
?>
