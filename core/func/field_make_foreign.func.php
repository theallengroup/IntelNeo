<?php
/**
 * takes a field, like a_id and makes it FOREIGN, ( type = list, options = select ...)
 * fields, should have a Value
 * */
function std_field_make_foreign(&$me,$argv){
	///p2($argv);
	$field=$argv[0];
	$is_foreign=is_array($field) && array_key_exists('foreign',$field);
	if($me->field_is_foreign($field)){
		/**
		 * its and ID and its not ME, then its foreign.
		 */
		if($field['type'] !='hidden'){

			if($field['type']=='label'){
				#since it's a label, i want to show it's current value, 
				#and preserve the hidden input, with the actual ID's value
			
				#$field['type']='list';
				//$save=1;
			}else{
				$field['type']='list';
			}
			$foreign_restrict=' 1=1 ';	
			if(array_key_exists('restrict',$field)){
				$foreign_restrict=$field["restrict"];	
			}

			$sp=0;
			if($is_foreign){
				#restrict the records obtained by a sql where expression.
				
				#syntax is tablename.fieldname
				#WARNING THIS LOGIC IS REPLICATED @ Foreign_select, & @ ML()
				$f3 = $me->explain_foreign_field($field["foreign"]);
				$foreign_mod = $f3['table'];
				$foreign_field = $f3['field'];
				$foreign_connection_id = $f3['id'];
				$sp = $f3['has_special_connector'];
				/// \todo 264 WHY is this repeated from foreign_select() ?????
				
			}else{
				$sp=1;
				$foreign_mod = $me->id2mod($field['name']);
			}
			
			if($foreign_mod==''){
				///dbg 				echo('<br>no module for:'.$field['name']);
			}else{
				$fm = $me->load_file($foreign_mod);
			}
			if($sp == 0){//use default id 
				$foreign_connection_id=$fm->id;
			}else{
				if($is_foreign){
					$foreign_connection_id = $f3['id'];
				}else{//always uses default.
					$foreign_connection_id=$fm->id;	
				}
			}
			/// dbg echo('<br/>CID:'.$foreign_connection_id.' : '.$field['name']);
			if(!$is_foreign){
				//$foreign_field=$fm->name;
				$foreign_field=$fm->ifield;
			}
			$sql_alias = $foreign_field;

			if(isset($field["idfield"])){
				$foreign_field=$field["idfield"];
			}
			//
			$obc='';//order by clause
			
			if(array_key_exists('foreign_order',$field)){
				$me->log('used foreign_order on'.$field["name"],'SQL');
				$obc.=' ORDER BY '.$field['foreign_order'];
			}else{
				$me->log('NOT used f.o ON '.$field["name"],'SQL');
			}
			$priv = $fm->program_name().'/'.$fm->default_edit_action;
			$is_edit_ok = $me->privilege_manager->is_allowed($priv);
			$is_add_ok = $me->privilege_manager->is_allowed($fm->program_name().'/'.$fm->default_create_action);

			if(!isset($field['foreign_links'])){
				$field['foreign_links']=1;
			}


			if($field['type']=='label'){
				#FETCH a single Record, the one with the current's "value"
			//	$v9=$me->q2op("SELECT * FROM ".$fm->get_table_name()." WHERE ".$foreign_restrict." AND ".$foreign_connection_id." = '".$field["value"]."'",$foreign_connection_id,$foreign_field,'trim');
				$field_filter = 'trim';
				if(isset($field["trim"]) && $field["trim"]==0){
					$field_filter='none';
				}

				$sql_query ="SELECT $foreign_connection_id,$foreign_field as $sql_alias FROM ".$fm->get_table_name()." WHERE ".$foreign_restrict." AND ".$foreign_connection_id." = '".$field['value']."' ".$obc;
				
				$v9=$me->q2op($sql_query,$foreign_connection_id,$sql_alias,$field_filter);
				$link1 = $v9[$field['value']];
				if($is_edit_ok && $field['foreign_links']==1){
					$link1 = $me->make_link(array(
						'mod'=>$fm->program_name(),
						'ac'=>$fm->default_edit_action,
						'id'=>$field['value'],
					),$link1,'action_link');
				}
				$field['display_value']=$link1;
				//$field['value']=$v9[$form_fields[$field['name']]["value"]];
				//$me->log('here');
				$save=1;
			}else{
				//type == list

				$sql_query="SELECT $foreign_connection_id,$foreign_field FROM ".$fm->get_table_name()." WHERE ".$foreign_restrict.' '.$obc;
				#p2($sql_query);
				$field['options']=$me->q2op($sql_query,$foreign_connection_id,$foreign_field,'trim');
				$current_url = '__current_url='.$me->get_current_url();
				
				if($field['foreign_links']==1){
					if($is_edit_ok){
						$l = '?'.$current_url.'&mod='.$fm->program_name().'&ac='.$fm->default_edit_action.'&id=';
						$field['after'].= "\n&nbsp;&nbsp;<span onclick='location.href = \"$l\" + document.getElementById(\"".$field['name']."\").value' class='standard_link make_link action_link '>".$fm->get_i18n_text($fm->default_edit_action).'</span>';
					}
					if($is_add_ok){
						$l = '?'.$current_url.'&mod='.$fm->program_name().'&ac='.$fm->default_create_action;
						$field['after'].= "\n&nbsp;&nbsp;<span onclick='location.href = \"$l\"' class='standard_link make_link action_link '>".$fm->get_i18n_text($fm->default_create_action).'</span>';
					}
					if($is_add_ok || $is_edit_ok){
						$field['before']="<nobr>".$field['before'];
						$field['after']=$field['after']."</nobr>";
					}
				}

				
			}
		}
	}
	return($field);
}
?>
