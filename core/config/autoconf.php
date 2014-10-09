<?php
	global $std_env;
	if($_GET['_redir']!=''){
		//This Means We have been "translated"
		echo(' Q '.$_GET['_redir']);

		/*
		/mod/ac/
		/lang/mod/ac/
		 */
		$parts=explode('/',$_GET['_redir']);
		$lang=strtoupper($parts[0]);
		if(file_exists('./i18n/'.$lang.'.i18n.php')){
			echo('<br/>lang detected:'.$lang);
			std_set_language($lang);
		}else{
			echo('<br/>Language Missing, using ES');
			std_set_language($config["language"]);

		}
		$current_web_directory = str_replace(strtolower($_GET['_redir']),'',strtolower($_SERVER['REDIRECT_URL']));
		$std_env['current_web_directory']=$current_web_directory;
		echo('current='.$current_web_directory);

		/// dbg 		echo("<pre>");		print_r($_SERVER);
		//minor shared media hack
		if($_SERVER['HTTP_HOST']=='www.outsourcing.pressstart.co' || $_SERVER['HTTP_HOST']=='www.outsourcing.pressstart.co'){
			define('MEDIA_DIR',$current_web_directory.STD_LOCATION.'../shared_media0274/');
		}else{
			define('MEDIA_DIR',$current_web_directory.STD_LOCATION.'../shared_media/');
		}
		$rp = str_replace($_SERVER['DOCUMENT_ROOT'],'',str_replace("\\",'/',realpath($_SERVER['DOCUMENT_ROOT'].$current_web_directory.STD_LOCATION.'shared/components/'))).'/';
		define('JS_DIR',$rp);//'http://'.$_SERVER['HTTP_HOST'].
		echo('<br>rp='.$rp.' <br>');
		define('SHARED_MODULES_DIR',STD_LOCATION.'shared/');
	/*
	  [DOCUMENT_ROOT] => 	c:/appserv/www
	  [SCRIPT_FILENAME] => 	c:/appserv/www/project0.2.7.4/projects/log_test2/index.php
	  [REDIRECT_URL] => 	/project0.2.7.4/projects/log_test2/EN/
	  [REQUEST_URI] => 	/project0.2.7.4/projects/log_test2/EN/?
	  [SCRIPT_NAME] => 	/project0.2.7.4/projects/log_test2/index.php
	  [PATH_TRANSLATED] =>  c:/appserv/www/project0.2.7.4/projects/log_test2/index.php
	  [PHP_SELF] => 	/project0.2.7.4/projects/log_test2/index.php
	  

		$yw=str_replace($yp, '',$_SERVER['REDIRECT_URL']);
		$url_segments=explode('/',$yw);
		echo("<pre>");
		print_r($_SERVER);
		$yy=dirname($_SERVER['SCRIPT_FILENAME']);
		$yp=str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/',$yy));
		$web_stdloc=dirname($yp);
	 */	
	}else{//proceed with regular stuff

		if(std_get_language()==''){
			std_set_language($config["language"]);
		}
		
		if($_SERVER['HTTP_HOST']=='www.outsourcing.pressstart.co' || $_SERVER['HTTP_HOST']=='www.outsourcing.pressstart.co'){
			define('MEDIA_DIR',STD_LOCATION.'../shared_media0274/');
		}else{
			define('MEDIA_DIR',STD_LOCATION.'../shared_media/');
		}
		define('SHARED_MODULES_DIR',STD_LOCATION.'shared/');
		define('JS_DIR',STD_LOCATION.'shared/components/');
	}

	//define(STD_LOCATION,$web_stdloc);
	define('INCLUDE_DIR',STD_LOCATION.'include/');
	
?>
