<?php

	if(headers_sent()){
		//we are in trouble
		
	}else{
		if(isset($_GET['__current_url'])){
			$this->send_b2l_headers();
		}else{
			// work the old way
			//$this->enable_header($this->current_action);
			//$this->head();

		}
	}

	/**
	 * restores the user to a specific point in a search result set,
	 * pagination, sort order, ac, etc, its all restored.
	 * */
	if(!is_array($_SESSION[$mydir]["usr"]["_search"]) || !array_key_exists($this->table,$_SESSION[$mydir]["usr"]["_search"])){
		/** $this->error("there is no list",'std010');	*/
		//just send the user to whatever he was whatching, that must be it.	

		$_GET=$_SESSION[$mydir]["usr"]["_last_list"];
		$this->load_current_module();
		
		//p2($_SESSION[$mydir]["usr"]["_search"]);
	}else{
		#retrieve old data from SESSION
		$_GET=$_SESSION[$mydir]["usr"]["_search"][$this->table];
		
		#go back to were I was, wherever the fuck that is.
		
		$this->load_current_module();
	}

?>
