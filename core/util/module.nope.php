<?php

function std_nope(&$me){
	$me->name='nope';
	echo('<br/>NOPE from womewherelse!:'.$me->status);
	return('got from nope');
}
?>
