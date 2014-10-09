<?php

	#Title: Untitled App	
	#Author: f3l	
	#Generated Date: 2006-05-16 16:11:16	
	#Description: f3l	
	#Generator Version: 0.2	
	
	class system_base extends std {
		var $default_module='usr';
		function system_base(){
			$this->std();
		}
		function app_init(){
			global $i18n_std;
			std::head($i18n_std['appname']);
			$this->load_current_module();
			std::foot();
		}
	}

?>
