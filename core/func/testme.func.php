<?php

function std_testme(&$me,$argv){
	///p2($argv,'red');
	$me->log('<br/>HELLO: from TESTME!','TEST');
	return('got from testme:'.implode(',',$argv).' and my name is:'.$me->table);
}
?>
