<?php

function std_parse_auth(&$me,$argv){
	$view=$argv[0];
	if($view["type"]=='record'){
		$m =$this->load_file("auth");
		$rlist=$m->get_auth_idlist_q('role');

		//cannot authorize anyone.
		$me->f->add_field(array(
			"type"=>"checklist",
			"name"=>'_auth',
			"check_all"=>1,
			"i18n_text"=>$m->i18n("auth_plural"),
			"i18n_help"=>$m->i18n("auth_plural_help"),
			"options"=>$me->q2op("SELECT role_id,role.name FROM ".$m->get_table_name().',role WHERE role_id = role.id and role_id in ('. $rlist.')','id','name'),
			/// \todo 002 must be able to determine WTF are the fields!
			"values"=>$me->q2a("SELECT ".$foreign_table."_id FROM ".$conn->get_table_name()." WHERE ".$me->table."_id = '$current_record_id'"),
			'after'=>'<br/>' . '' ,

		));
	}elseif($view["type"]=='list'){
		if(!isset($view["filter"])){
			$view["filter"]="1=1";
		}
		$auth_list = get_auth_idlist_q($me->get_table_name());
		$view["filter"]=$view["filter"]." and (".$m->get_table_name().".".$me->id." in (".$auth_list."))";
		
	}
	return($view);
	
}

?>
