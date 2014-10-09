<?php
global $config;	
	/**
	 * WARNING: unlike previous versions of this software, 
	 * this config file does not contain DB info, if you need to edit that one, 
	 * proceed to /project<version>/projects/<project_name>/config/<host>.config.php
	 * The Clients don't really need all that debug info, right?
	 * */

	/** 
	 * where does the class live?
	 * */
	

if($_SERVER["HTTP_HOST"]=='localhost' || $_SERVER["HTTP_HOST"]=='localhost' ){

	define('DEBUG',1);
}else{
	define('DEBUG',0);
}

if(isset($config['engine_name'])){
	define('DATABASETYPE',$config['engine_name']);
}else{
	define('DATABASETYPE','mysql');
}
	define('PROJECTSDIR','..');
	/** \todo 3030 allow multiple languages, a lang per user */
	

?>
