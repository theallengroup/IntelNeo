<head>
<title>linecount</title>
</head>
 grep .* * |wc -l

<?php

$e=file('../include/std.php');
$cfunc='NONE';
$func=array();
foreach($e as $line){
	if(preg_match('/^\s+function ([^(]*)\(/',$line,$match)){
		$cfunc=$match[1];
		
	}
	$func[$cfunc]++;
}
foreach($func as $k=>$f){
	$func[$k]=str_pad($f,4,'0',PAD_LEFT).' '.$k;
}

echo('<pre>');
//print_r($func);
sort($func);
$func = array_reverse($func);
print_r($func);
?>
