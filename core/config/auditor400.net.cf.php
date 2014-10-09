<?php
	#TODO allow multiple configs, for different Apps, and for different hosts.
	define('DEBUG',1);
	define('DATABASETYPE','mysql');
	define('DATABASE','auditor_alpha');
	define('HOST','localhost');
	define('DBNAME','auditor');
	define('DBPWD','santiagovalencia');
	define('DATABASELIST','test,demo');/*IF the user has no SHOW DATABASES privilege, he must be able to choose from a list, i give him., TODO emulate, test*/
	define('PROJECTSDIR','..');
?>
