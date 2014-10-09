<?php

function std_nice(&$me,$args=array()){
	
	$me->name='hello';
	echo('<br/>Hello:my parameters are: '.implode(",",$args).' from somewhere else!: status:'.$me->status);
	return('got from nice');
}
?>
