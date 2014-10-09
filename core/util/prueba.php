<?php

function a(){
	b();
}
function b(){
	z();
}
function z(){
	k(12345);
}

function k($txt){
	echo($txt);
	print_r(debug_backtrace());
}
/*
$funcion='k';

$funcion('hola!');
 */
a();
?>
