<?php
class std_hierarchy {

	function get_view_contents($view){
		$all = explode("/",$view["hierarchy"]);
		return($this->get_node_contents($all,'1=1'));

	}
	function  get_node_contents($all,$cond){
		/*
		//$plevel_cond = '1=1';
			$m = $this->load_file($mod);
			$data = $m->q2op("SELECT ".$m->id." , " . $m->ifield.' FROM ' .$m->get_table_name() .' WHERE '.$plevel_cond);
			p2($data);
			$plevel_cond = $mod . '_'.$m->id.' = ' . $id;
			foreach($data as $id=>$name){
				//echo($m.':'.$mod->ifield."<br/>");
			}

		foreach($all as $mod){

		}
		 */

		$result = 'Hello!';	
		return('');
	}


}
