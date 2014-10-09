<?php
/** 
 * this will tell you if the application has referential integrity
 * it is used in util-diagnose
 * you can overwrite and define your own definition of healthy() for each module
 * just make sure you call std::healthy as well.
 * */
function std_healthy(&$me,$argv){

	$a=$me->rel['has'];
	if(isset($me->rel['hbm'])){
		foreach($me->rel['hbm'] as $foreign_table=>$conection_table){
			$a[$conection_table]=$conection_table;
		}
	}

	if(count($a)==0){
		return(TRUE);
	}
	$ret=TRUE;
	echo("<div class='standard_text'>");
	
	foreach($a as $table){
		$m=$me->load_file($table);
		$m->fields=$m->fields_transform($m->fields,array());
		foreach($m->fields as $name=>$field){
			if($m->field_is_foreign($field)){
				$mod2=$me->id2mod($name);
				if($mod2==$me->program_name()){
					//These are related
					$master_id=$name;
					$my_id=$me->id;
					$my_table=$me->get_table_name();
					$child_name=$m->ifield;
					if($child_name=='STD_NO_IFIELD'){
						//paila
						$child_name=$master_id;
					}
					$l=$me->q2op("select $my_id from $my_table",$my_id,$my_id);
					if(count($l)>0){
					
						$sql=$master_id . " not in (". implode(',',$l) .") ";
						$q=$me->q2obj("SELECT $master_id,$child_name FROM " . $m->get_table_name() . " WHERE " . $sql);
						if(count($q)>0){
						$dx.=	$m->e_slist($sql);
							$ret=(FALSE);

						}else{
							#echo($table.": ok<br/>");
							$ret=(TRUE);
						}
					}else{
						$q2=$me->q2obj("SELECT $master_id,$child_name FROM ".$m->get_table_name());
						if(count($q2)>0){
							$m->e_slist();
							echo('todos malos');
							$ret=(FALSE);
						}
					}
				}
			}
		}
	}
	if($dx!=''){
		echo('<h2 class=\'standard_title form_title\'>'.$me->i18n('table_title').' ('.count($a).')</h2><hr>');
		echo($dx);
	}
	echo("</div>");
	return($ret);
}
?>
