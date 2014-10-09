<?php
echo("op1:");
$a=array(1,2,3,4,5,6,7,8,9,10) ;
foreach($a as $k=>$v){
	echo($a[10-$k]."\n<br/>");
}

echo("<br>MAP:");

function p7($t){echo("<br>$T");return($t);}
array_map('p7',range(10,1));
echo("<br>MAP2:");
array_map(create_function('$t','echo("$t<br>");return($t);'),range(10,1));

echo("op1:");
$i=0;
while($i<=10){
	echo((10-$i)."\n<br/>");
	$i++;
}
echo("op1:");
echo(implode('--><br><!--',range(10,1)));

?>
