<?php
include("../fmtx.php");
echo(fmtx(file_get_contents("test1.tmpl"),array(
	'a'=>'This is a test A',
	'b'=>'This B',
	'p'=>"This is P",
)));

