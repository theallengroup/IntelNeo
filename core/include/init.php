<?php


	//remove this:
	//bored? set this to true, and get fixin'!
	//error_reporting((E_ALL|E_STRICT) );//& ~E_NOTICE
	
	#set_time_limit(10000);
	set_include_path(get_include_path() . PATH_SEPARATOR . STD_LOCATION.'../PEAR');


	//echo(get_include_path());

	//include(STD_LOCATION.'include/profiler.php');
	global $mydir,$timestart,$std_views,$std_error_log,$std_rel;
	$timestart = microtime();
	session_start();

	$mydir=basename(dirname($_SERVER["PHP_SELF"]));
	function std_set_language($lang){
		global $std_error_log;
		global $mydir;$_SESSION[$mydir]['usr']['PROFILE_LANGUAGE']=$lang;
		$std_error_log[]=array('lvl'=>'LANGUAGE','txt'=>'lang is now '.$lang);
	}
	function std_get_language(){
		global $mydir,$config;
		
		if(isset($_SESSION[$mydir]['usr']['PROFILE_LANGUAGE'])){
			return($_SESSION[$mydir]['usr']['PROFILE_LANGUAGE']);
		}else{
			if(isset($config["language"])){
				return($config["language"]);
			}else{
				return('ES');//defaults spanish;
			}
		}
	}
	#function std_get_language(){global $mydir;return("EN");}
	function ssid(){global $mydir;return($_SESSION[$mydir]['usr']['id']);}
	function ssname(){global $mydir;return($_SESSION[$mydir]['usr']['name']);}
	function sset($key,$value){
		global $mydir;
		$_SESSION[$mydir][$key]=$value;
	}
	#ini_set("session.cookie_path",$mydir);
	#echo(ini_get('session.cookie_path'));

	if(!headers_sent()){//ALLOW INCLUSION BY OTHER SYSTEMS.
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");# 'Expires' in the past
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");# Always modified
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);# HTTP/1.1
		header("Pragma: no-cache");# HTTP/1.0
	}
	
	$std_views=array();
	$std_rel=array();

	$config_file='./config/'.$_SERVER['HTTP_HOST'].'.config.php';
	if(!file_exists($config_file)){
		$config_file="./config/default.config.php";
		//def cfg is used
	}
	//debug config:
	//echo($config_file.':'.$_SERVER['HTTP_HOST']);
	if(file_exists($config_file)){
		include($config_file);
		if(!is_array($config)){
			//this means that something is wrong
			//std_log(error)
			$config=array();	
		}
	}else{
		//default configuration file.
		$config=array();	
	}
	//determine default language
	//print_r($config);
	if(!isset($config["language"])){
		$config["language"]='ES';//Spanish defaults
	}


	//if user logged out redirect them
	if(isset($_GET["logout"]) && $_GET["logout"]==1){
		sset('inside',0);
		sset('usr',array());#delete session data
		
		std_set_language($config["language"]);//Default Language for NO-SESSION info
		if(basename($_SERVER['PHP_SELF'])=='index.php'){
			//clean URL
			$_dx='';
		}else{
			$_dx=$_SERVER['PHP_SELF'];
		}
		header('Location: '.$_dx.'?');
	}
	
	include(STD_LOCATION."config/cf.php");
	include(STD_LOCATION."config/autoconf.php");
	include(STD_LOCATION."include/common.php");


	/** 
	 * ensure we always have a valid language.
	* */
	if(std_get_language()==''){
		std_set_language($config["language"]);
	}

	//main lang file
	include(STD_LOCATION."i18n/".std_get_language().".include.php");
	$_language_file="./i18n/".std_get_language().".i18n.php";
	if(!file_exists($_language_file)){
		$errors=$i18n['error']['main_lang_file_missing'];
	}else{
		include($_language_file);
	}

	include(STD_LOCATION."include/fs.class.php");
	include(STD_LOCATION."include/active_record/act_base.php");
	include(STD_LOCATION."include/db/".DATABASETYPE.".php");

	//is there ANY time i don't need this?
	include_once(STD_LOCATION.'include/privileges.php');
	include_once(STD_LOCATION.'include/alias.php');

	include(STD_LOCATION."include/form.php");		
	#This includes the standard views, like EDIT, ADD, ETC, an EXCELENT way of saving code,
	#this WILL reuce the file count dramaticaly.
	include(STD_LOCATION."shared/view/std.view.php");
	echo($errors);
?>