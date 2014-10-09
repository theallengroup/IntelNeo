<?php
/**
 * unnecessary complexity? you decide.
 * */
class alias_manager{
	var $caller;
	/**
	 * old,new
	 * */
	function add($alias_name,$alias_target){
		global $mydir;
		$me=&$this->caller;	//ok
		$me->log('ADDED TGT='.$alias_target.' NAME=' . $alias_name,'ALIAS');
		$_SESSION[$mydir]['_aliases'][$me->program_name().'/'.$alias_name]=$alias_target;
	}
	function get_list(){
		global $mydir;
		return($_SESSION[$mydir]['_aliases']);
	}
	function alias_manager(&$me){
		$this->caller=&$me;
	}	
}
?>
