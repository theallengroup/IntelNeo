<?php
/** @brief find out which is the parent field
 *
 * @param $fields a fs */
function std_tree_get_parent_field(&$me,$argv){//tree_get_parent_field
	$fields = $argv[0];
	$parent ='@none';
	foreach($fields as $field_name=>$field){
		if($me->program_name().'_id' == $field_name){
			$parent = $field_name;
			break;
		}
	}
	return($parent);
}
?>
