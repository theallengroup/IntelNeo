<?php

class privilege_manager extends db{
	var $caller;

	/** 
	 * \brief adds a privilege to the in-memory privilege list.
	 *
	@param $privilege_structure is a record, of the privileges table
	it contains :
	- action
	- role_name
	- privilege_name

	function call looks like this:
	\code
	$this->add_privilege(array('action'=>$this->default_module));
	\endcode
	this one is optimized for privilege lookup, and title rendering
	 */	
	function add_privilege($privilege_structure){
		global $mydir;
		$me=&$this->caller;	//ok
		if(!array_key_exists($mydir,$_SESSION)){
			#first time entry, no login.
			$_SESSION[$mydir]=array();
		}
		if(!array_key_exists('usr',$_SESSION[$mydir])){
			#first time entry, no login.
			$_SESSION[$mydir]["usr"]=array();
		}
		if(!array_key_exists('_priv',$_SESSION[$mydir]['usr'])){
			$_SESSION[$mydir]['usr']['_priv']=array();
		}
		if(!array_key_exists('_priv_mods',$_SESSION[$mydir]['usr'])){
			$_SESSION[$mydir]['_priv_mods']=$me->public_modules;
		}
		#the key is the action, for fast lookup.
		$_SESSION[$mydir]['usr']['_priv'][$privilege_structure["action"]]=$privilege_structure;	
		$mod=$me->get_mod($privilege_structure["action"]);
		$_SESSION[$mydir]['usr']['_priv_mods'][$mod]=$mod;
		//echo(b2());
		std::log('Added:<b>'.$privilege_structure["action"]."</b> : ".$privilege_structure["privilege_name"]." AS ".$privilege_structure["role_name"],'PRIV');
	}

	/** \brief shortcut for std::add_privilege()
	 *
	 *
	 * grants the user, privileges over a given action, grouping it using the current's action privileges.
	 * @param $role indicates whether you should use a role, 
	 * 	from the list, or a nice (hopefully i18n compatible) word like "guest"
	 * @param $prefix i18n key prefix
	 * @param  $ac action name
	 * @param $table module name
	 * */
	function ap($ac,$prefix='form',$role='@search',$table='@me'){
		$me=&$this->caller;	//ok
		
		if($table=='@me'){
			$table=$me->program_name();
		}else{
			//this test for wether ior not the module you are adding struff into EXISTS, if not
			//the program breaks.
			$me->load_file($table);
		}
		
		if($role=='@search'){
			$me->log('looking for role for action, since none was given:'.$ac,'PRIV');
			$role=$me->get_role_name($me->mod_and_ac($table,$me->current_action));
		}

		$this->add_privilege(array(
			'action'=>		$me->mod_and_ac($table,$ac),
			'role_name'=>		$role,
			'privilege_name'=>	$i18n[$table]['table_plural']." : ". $i18n_std['list'][$prefix . $ac],
		));
	
	}

	/** \brief allows the user to "login"
	 *
	 * since we are on the default module, that is, our URL without parameters, 
	 * perhaps we should LET the user do this, right?
	 * \todo 1201 Login? => i18n
	*/
	function ap_login(){
		global $i18n_std;
		$me=&$this->caller;	//ok
		/*error 9900 not here*/
		/*
		$this->add_privilege(
			array(
			'role_name'=>$i18n_std["roles"]["guest"],
			'privilege_name'=>'Login', // a simple & naive assumption, not always true
			'action'=>$me->default_module)
			);
		 
		$this->add_privilege(array(
			'role_name'=>$i18n_std["roles"]["guest"],
			'privilege_name'=>'Login',
			'action'=>'usr/login2'));
		$this->add_privilege(array(
			'role_name'=>$i18n_std["roles"]["guest"],
			'privilege_name'=>'Login',
			'action'=>'usr/login3'));
		 */
	}
	
	/**
	 * this one is optimized for rendering 
	 * if you want to add stuff for login, login2, etc make sure you calso call ap_login() (add privileges for login)
	 * if you want all your users to autoagically access stuff from random places, override ap_login or use the 
	 * public_modules array in _base
	 * */
	function retrieve_privilege_list_from_db(){
		global $config;
		std::log('retrieving privileges from db...','PRIV');

		$p='';
		if(isset($config['table_prefix'])){
			$p=$config['table_prefix'];
		}

		$d=$this->q2obj(
		"SELECT 
			${p}role.name as role_name,
			${p}privilege.name as privilege_name,
			${p}privilege.action
		FROM 
			${p}usr,
			${p}usr2role,
			${p}role,
			${p}privilege,
			${p}role2priv
		WHERE 
			${p}usr.id			= '".ssid()."' AND
			${p}usr.id 			= ${p}usr2role.usr_id AND
			${p}usr2role.role_id		= ${p}role.id AND
			${p}role.id 			= ${p}role2priv.role_id AND
			${p}role2priv.privilege_id	= ${p}privilege.id
		ORDER BY ${p}privilege.name");

		//
		return($d);
	}

	/** 
	 * \brief gets the privilege list from cache
	 * this gets run once per login, unless the user changes any of the priv tables.
	 * a good thing iof this function, is that it loads ALL the strings in the app.
	 * this might become a performance problem, when there are many modules
	 * @todo how to not get bitten by perfornamce?  
	 * if one of the module's string file is broken, you'll notice inmediatelly.
	 * @see std::flush_privileges() 
	 * */
	function rebuild_privileges(){
		global $i18n,$mydir;
		$me=&$this->caller;	//ok
		std::log('Privileges rebuilt','PRIV');

		//GET TABLE LIST IN i18n
		$s=array();
		foreach($me->get_valid_i18n() as $mod){

			include_once($mod);
			$dd=basename($mod,'.i18n.php');
			$s[$dd] = $i18n[$dd]['table_plural'];
		}

		$all=$this->retrieve_privilege_list_from_db();
		
		//$_SESSION[$mydir]['_alias']=array();
		$this->ap_login();
		$_SESSION[$mydir]['usr']['_roles']=array();
		foreach($all as $k6=>$priv){
			$all[$k6]['privilege_name']=$this->fmt($all[$k6]['privilege_name'],$s,'#');
			$priv['privilege_name']=$all[$k6]['privilege_name'];
			$this->add_privilege($priv);
			$_SESSION[$mydir]['usr']['_roles'][$priv['role_name']]=$priv['role_name'];
		}
		$_SESSION[$mydir]['usr']['_privileges_menu']=$all;
		return($all);
	}
	
	/** 
	 * return whether or not the menu has been cached into session memory
	 * */
	function menu_is_cached(){
		global $mydir;
		return(!(
			array_key_exists($mydir,$_SESSION) && 
			array_key_exists('usr',$_SESSION[$mydir]) && 
			!array_key_exists("_privileges_menu",$_SESSION[$mydir]['usr'])));
	}
	/**
		ok, I know who you are, no need to go to the database, right?
		this simple query caching, will work untill the user logs out.
		and will save a LOT of (complex queries) trips to the database!.
	*/
	function get_privileges_from_cache(){
		global $mydir;
		return($_SESSION[$mydir]['usr']['_privileges_menu']);
	}
	function get_menu_options(){
		if(!$this->menu_is_cached()){
			#I have no idea who this guy is, lets find out!
			return $this->rebuild_privileges();
		}else{
			return $this->get_privileges_from_cache();

		}
	}
	/** 
	 * determines if a user has privileges to do $action, searching in the Session[$mydir][usr][_priv] struct,
	 * for that key. 
	 * @param $m the key of the session array to be looked up for
	 * @returns boolean whether or not the user has that privilege.
	 *
	 * each privilege is also scanned as a regex like this:
	 *
	 * action = nice*
	 * allows nice, nicer,nicest,nice_stuff
	 *
	 * */

	function is_allowed($m){
		global $mydir;
		return(isset($_SESSION[$mydir]["usr"]["_priv"][$m]) || $this->find_privilege_matching($m,'exists'));
		/*
		if(is_array($_SESSION) && array_key_exists($mydir,$_SESSION) 
			&& array_key_exists('usr',$_SESSION[$mydir]) 
			&& is_array($_SESSION[$mydir]['usr']) 
			&& array_key_exists('_priv',$_SESSION[$mydir]['usr']["_priv"]) 
			&& is_array($_SESSION[$mydir]['usr']) 
				&& array_key_exists($m,$_SESSION[$mydir]["usr"]["_priv"])){
					return(TRUE);
		}else{
			return($this->find_privilege_matching($m,'exists'));
		}
		return(FALSE);
		 */
	}
	/**
	 * will return the privilege object when $get!=existence
	 * */
	function find_privilege_matching($priv,$get1='exists'){
		global $mydir;
		$me=&$this->caller;	//ok
	//	echo($me->table);
	//	p2($me->alias->get_list(),'blue');
		if(isset($_SESSION[$mydir]["usr"]["_priv"]) && is_array($_SESSION[$mydir]["usr"]["_priv"])){
			foreach($_SESSION[$mydir]["usr"]["_priv"] as $privilege){
				if(preg_match('#^'.str_replace('*','.*',$privilege['action']).'$#',$priv)){	
					if($get1=='exists'){
						return(TRUE);
					}else{
						return($privilege);
					}
				}
			}
		}
		return(FALSE);
	}	
	function privilege_manager(&$me){
		$this->caller=&$me;
		$this->table='STD_PRIVILEGE_MANAGER';
		$this->db(0);
	}
}

?>
