<?php
function q2tree($query,$root_id,$my_id,$my_parent_id){

	$tree=array(
		'row'=>array(),
		'kids'=>array(),
	);
	$kids = array();
	//get kids
	foreach($query as $row){
		extract($row);	
		if($root_id == $row[$my_id]){
			$tree['row']=$row;
		}
		if($root_id == $row[$my_parent_id] && $root_id != $row[$my_id]){
			$kids[ $row[$my_id] ]= $row[$my_id];
		}
	}
	
	foreach($kids as $kid){
		if($root_id == $kid){
			//avoid self link
		}else{
			$tree['kids'][$kid]=q2tree($query,$kid,$my_id,$my_parent_id);
		}
	}
	return($tree);
}
?>
