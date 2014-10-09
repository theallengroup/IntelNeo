<?php
/**
 * \brief  retrieves kids information.
 * to be displayed within AFTER th record (not HBM)
 *
 * a kid is teh records of a table, that have a relationship with this record.
 *
 * for example, consider the following table schema:
 *
 * user
 * +uid
 * +name
 *
 * role
 * +rid
 * +name
 *
 * role2priv
 * +role_id
 * +user_id
 *
 * in this schema, user, and role, are conected trough role2priv, to express this relationship, 
 * you must put a file like: ./rel/user.role.php with this see core/shared/rel/* :
 @code 
global $std_rel;
$std_rel['usr']=array(
		'has'=>array(
			'log'=>'log',
		),
		'hbm'=>array (
			'role'=>'usr2role'
		),
	);

@endcode


* */


function std_get_kids(&$me,$argv){
	$current_record_id=$argv[0];
	$view=$argv[1];
	include_once(STD_LOCATION.'include/std_tab.php');
	$tabs = new tab();
	$tabs->name='children';
	$tab_count=0;

	//use model information
	if($view['kids']=='model' || is_array($view['kids'])){
		$rel_a = $me->rel;
		if(is_array($view['kids'])){
			$rel_a = $view['kids'];
		}
		if(is_array($rel_a['has'])){
			foreach($rel_a['has'] as $k=>$has){
				$has_foreign=0;
				if(is_array($has)){
					//allow override of special info, connect fields, etc					
				}else{
					//simple connect
					$info=array();
					$m=$me->load_file($has);
					$info['table']=$m->get_table_name();
					$info['action']=$m->default_edit_action;
					$info['create_action']=$m->default_create_action;
					$info['module']=$m->program_name();
					#p2($m,'yellow');
					$info['id']=$m->id;
					$info['ifield']=$m->ifield;
					//echo($m->get_connector_field($me->program_name()));

					foreach($m->fields as $kf=>$field){
						//rudimentary, must improve!
						if($field['name']==$me->program_name().'_id'){
							$has_foreign=1;
							$info['foreign_key']=$field['name'];
							break;
						}
					}

					if($has_foreign==0){
						$me->log('error no foreign field found for '.$has.' std043','ERROR');
						$me->i_error('no_foreign_field_found','std043',array('mod'=>$has));
					}
				}
				#p2($info,'blue');
				//now that we have the info, lets fetch some data, based on it
				//\todo 259 expose thie functionality (AR proxy?)
				//\todo 260 MUST NOT USE THIS ON INSERT VIEWS
				
				if($has_foreign==1){
					//p2($m->program_name().'/'.$m->default_action);
					//gracias, bracamonte!
					if($me->privilege_manager->is_allowed($m->program_name().'/'.$m->default_action) == 1){
						$where_expression = $info['table'] . '.'.$info["foreign_key"].' = '.$current_record_id;
						$kc = $m->kid_contents($where_expression);
						if($kc!=''){
							$kids_content = $kc;
						}else{
							$o = $m->mod_get_kids($where_expression);
							

							if(count($o)>0){
								$kid_headers=array();
								
								$lq=array();
								foreach($m->fs_options["sql_fields"] as $n5=>$v5){
									$lq[$v5]=$n5;
								}

								foreach($o[0] as $field_name1=>$field_value1){
									//TIENE HUEVO
									/*
									$old99 = $field_name1;
									$field_name1=str_replace($info['module'].'_','',$field_name1);
									if($field_name1 == $old99){
										//error, therefore, foreign.
										
									}
									 */

									$kid_headers[]=$m->fi($lq[$field_name1]);
								}
								$kid_action=$me->mod_and_ac($info['module'],$info['action']);
								if($me->privilege_manager->is_allowed($kid_action) == 1){
									foreach($o as $i=>$line){

										#todo fix edit_all: must be configurable
										$full_field_name = $info['module'].'_'.$info['ifield'];

										$o[$i][$full_field_name]=
											$me->make_link(array(
												'mod'=>$info['module'],
												'ac'=>$info['action'],
												'id'=>$o[$i][$info['module'].'_'.$info['id']],
												'__current_url'=>$me->get_current_url(),
											),
											$o[$i][$full_field_name]);
									}
								}
								// THIS IS NOT SAFE
								/* 			
								$me->privilege_manager->add_privilege(array(
									"privilege_name"=>$m->get_i18n_text($info['action'])." ".$m->i18n('table_name'),
									"action"=>$kid_action,
									"role_name"=>$me->get_role_name()
								));
								 */

								$kids_content = $me->table($o,
									$kid_headers,
									array(
										'style'=>'list',
										'border'=>0,
										//'title'=>$m->i18n('table_plural')
									)
								);

							}else{
								$kids_content='';
								// no kids found
							}
						}
						$tab_count++;
						$tabs->add_tab($m->i18n('table_plural'),$kids_content);
					}
					//ADD THE add_kid LINK
					$tabs->add_tab_content($m->i18n('table_plural'),
						"<br/>".
						$m->get_add_link(array( 
						'__auto_field_name'=>$info["foreign_key"],
						'__auto_field_value'=>$current_record_id,
					)));
				}else{
					#there is no $info to begin with!
				}
				#$dx.="<br>".$m->dsl(array($m->ifield),$info['foreign_key'] .'='. $current_record[$me->id] );

			}
		}
		///\todo allow user to set HBM on view level
		//when

		if(is_array($rel_a) && array_key_exists("hbm",$rel_a)){
			foreach($rel_a["hbm"] as $foreign_table=>$connector_table){
				$m=$me->load_file($foreign_table);
				$columns = 1;

				#NUMBER OF COLUMNS
				if(isset($m->meta["hbm_columns"])){
					$columns = $m->meta["hbm_columns"];
				}
				$chk=1;
				if(isset($m->meta["hbm_check_all"])){
					$chk = $m->meta["hbm_check_all"];
				}

				#hbm_order
				$obc = '';
				if(isset($m->meta["hbm_order"])){
					$obc = "ORDER BY ".$m->meta["hbm_order"];
				}

				$conn = $me->load_file($connector_table); 
				$me->f->add_field(array(
					"type"=>"checklist",
					"name"=>$foreign_table."_list",
					'columns'=>$columns,
					"check_all"=>$chk,
					"i18n_text"=>$m->i18n("table_plural"),
					"i18n_help"=>"something...",/// \todo 004 here
					"options"=>$me->q2op("SELECT ".$m->id.",".$m->ifield." FROM ".$m->get_table_name().' '.$obc,$m->id,$m->ifield),
					/// \todo 002 must be able to determine WTF are the fields!
					"values"=>$me->q2a("SELECT ".$foreign_table."_id FROM ".$conn->get_table_name()." WHERE ".$me->table."_id = '$current_record_id'"),
					'after'=>'<br/>' . '' ,
					
				));
			}
				
			/*
			 * this code is useless and stupid.
			 *
			$me->f->add_field(array(
				"type"=>"protected",
				"name"=>$foreign_table."_list_hbm",
				"value"=>$rel_a["hbm"],/// \todo allow user to set THIS to: something else.
			));
			 */
			$me->f->add_field(array(
				"type"=>"hidden",
				"name"=>"__view_hbm",
				"value"=>1,
			));
		}
	}
	
	if($tab_count>0){
		$dx = $tabs->out();	
	}else{
		$dx='';
	}
	#echo('this is DX:'.$dx);
	return($dx);
}

?>
