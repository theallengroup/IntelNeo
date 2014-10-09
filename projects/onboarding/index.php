<?php

define('WORKING_DIRECTORY', getcwd());

function fatal_handler() {
	$errfile = "unknown file";
	$errstr  = "shutdown";
	$errno   = E_CORE_ERROR;
	$errline = 0;

	$error = error_get_last();

	if( $error !== NULL) {
		$errno   = $error["type"];
		$errfile = $error["file"];
		$errline = $error["line"];
		$errstr  = $error["message"];

		chdir(WORKING_DIRECTORY);
		print_it_no_matter_what($errno, $errstr, $errfile, $errline);
	}
}

// error handler function
function print_it_no_matter_what($errno, $errstr, $errfile, $errline) {
	#ob_end_flush();
	if (!(error_reporting() & $errno)) {
		// This error code is not included in error_reporting
		return;
	}
	file_put_contents("./errlog.txt", "$errno:$errstr $errfile:$errline\n", FILE_APPEND);

	switch ($errno) {
	case E_USER_ERROR:
		echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
		echo "  Fatal error on line $errline in file $errfile";
		echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
		echo "Aborting...<br />\n";
		
		exit(1);
		break;

	case E_USER_WARNING:
		echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
		break;

	case E_USER_NOTICE:
		echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
		break;

	default:
		echo "Unknown error type: [$errno] $errstr<br />\n";
		break;
	}

	return false;
}

# error handler doesn't log fatal errors
$old_error_handler = set_error_handler("print_it_no_matter_what");
# work around to log fatal errors
register_shutdown_function( "fatal_handler" );


#define('DEBUG','1');
if($_SERVER["HTTP_HOST"] != "locahost"){
	error_reporting(E_ALL&~E_NOTICE&~E_STRICT&~E_DEPRECATED);
	error_reporting(0); # WHY NO ERROR REPORTING?
	define('DEBUG','0');
}else{
	error_reporting(E_ALL&~E_NOTICE);
	define('DEBUG','1');
}

define('STD_LOCATION','../../core/');
include(STD_LOCATION."include/std.php");
std::load('onboarding');
$main=new onboarding_base();
$main->app_init();

?>
