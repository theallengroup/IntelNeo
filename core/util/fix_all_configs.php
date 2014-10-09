<?php
foreach(glob('../projects/*/config.php') as $file){
	echo($file.'<br/>');
	$d=basename(dirname($file));
	//	echo($d);
		
	if(copy($file,'../projects/'.$d.'/config/default.config.php')){
		unlink($file);
	}
}
?>
