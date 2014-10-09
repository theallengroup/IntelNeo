<?php

function fmtx($template_string,$data){
	$template_string=preg_replace("/\{(inc|include)\s+([^}]+)\}/e","fmtx((file_exists('\\2.tmpl'))?file_get_contents('\\2.tmpl'):'<b>File missing!:[\\1] @'.getcwd().'</b>',\$data);",$template_string);


	foreach($data as $key=>$value){
		$template_string=str_replace("{".$key."}",$value,$template_string);
	}
	return($template_string);
}

?>
