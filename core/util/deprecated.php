<?php
exit;
?>


/** 
 * changes a link after this input, to reflect the ID in its url of THIS input
 * kinda weird, but it works
 * */
function std_change_next_link_id(node){
	var node2 = null
	node2 = node.nextSibling;
	while(node2 != null){
		///DBG alert(node2.nodeType + ' '+node2.tagName + ' '+node2.innerHTML);
		if(node2.tagName.toUpperCase() == 'A'){
			node2.href = node2.href.replace('/id\=\d+/','id='+node.value);
			break;
		}
		node2 = node2.nextSibling;
	}

}

from std_get_kids
-----------------


/*
  		if(array_key_exists('include',$view)){
			
			foreach($view['include'] as $k=>$included_view){
				$view_name=$included_view['view'];
				if(strpos($view_name,'/')!==0){
					//view has a dot.
					//that means its an external view
					$r=explode('/',$view_name);
					if(count($r)!=2){
						$me->error('ERROR: dot count on:'.$view_name);
					}else{
						$module_name = $r[0];
						$real_view_name = $r[1];
					}
					
				}else{
					$module_name='@me';//FIX
					$real_view_name=$view_name;
				}
				$m=$me->load_file($module_name);
				$m->load_module_views($module_name);
				#$iv2=$m->load_view($module_name,$real_view_name);
				$iv2=$m->load_view($module_name,$real_view_name);
				#p2($iv2,'red');
				#$iv2=$m->get_view($real_view_name);
			//	unset($included_view['view_name']);
			
				if(array_key_exists('override',$included_view)){
					$iv2=$me->inherit($iv2,$included_view['override']);
				}
				$iv2=$me->parse_view($iv2,$current_record_id);
				p2($iv2,'blue');
			}
		}
 */



from usr module
----------------
/*
		$this->fields['login_count']['type']='hidden';
		$this->fields['login_count']['value']=0;
		$this->fields['last_ip']['type']='hidden';
		$this->fields['last_ip']['value']="0.0.0.0";
 */		
		/*
		$this->add_to_view('new_user',array('type'=>'separator','value'=>'<h2>'.$this->fi('roles').'</h2>'));
		$this->add_to_view('new_user',array('type'=>'checklist','options'=>$this->q2op('SELECT * from '.$this->table_prefix.'role'),'name'=>'roles'));
		$this->current_view='new_user';
		//$this->ac_view();	
		 */


	/**
	 * \todo validate role is valid role id.
	 * IS THIS DONE???
	 * */
	function ac_all_new_user2(){
		p2($_GET);
		die();
		$this->ac_new2();

		$uid=$this->last_id();
		/*
		 * DEPRECATED
		if(isset($_GET['roles']) && is_array($_GET['roles'])){
			foreach($_GET['roles'] as $k=>$role){
				$this->sql('INSERT INTO '.$this->table_prefix.'usr2role VALUES(0,'.$uid.','.$role.')');	
				if($this->affected()!=1){
					//wtf? FIX
					$this->i_error('WOW','usr007');
				}
			}
		}
		 */
	}



from advanced search form, no longer relies on get_ed
----------------------------------------------------------
/*
		$form_object=$me->get_ed(array(
			'actions'=>array(array('action'=>$this->current_action,'label'=>$me->get_i18n_text('search_ok'))),
			///'rawtitle'=>$me->get_i18n_text('advanced_search'),
			'data'=>'',
			'style'=>'iframe',
			'fields'=>$fields,
		));
		//		echo($form_object->shtml());
		//		$fields=$form_object->fields;
 */








std style

//	var $shadow_config=array('menu'=>'shadow');
	var $shadow_config=array('menu'=>'negative','form'=>'negative','form_from_fields'=>'negative');
//		'warning_location'=>'warning.jpg'

	var $shadow_config=array(
		'warning_location'=>'negative_warning.jpg',
		'error_image_location'=>'error.gif',
		'menu'=>'negative',
		'form'=>'negative',
		'form_from_fields'=>'negative',
		'delete'=>'negative',
		'msg'=>'negative',
	);
	var $shadow_config=array(
		'warning_location'=>'negative_warning.jpg',
		'error_image_location'=>'error.gif',
		'menu'=>'gray',
		'form'=>'gray',
		'form_from_fields'=>'gray',
		'delete'=>'gray',
		'msg'=>'gray',/*obsolete*/
	);
	var $shadow_config=array(
		'warning_location'=>'negative_warning.jpg',
		'error_image_location'=>'error.gif',
		'menu'=>'negative_shadow',
		'form'=>'gray',
		'form_from_fields'=>'gray',
		'delete'=>'gray',
		'msg'=>'gray',/*obsolete*/
	);


form

		/* attempt 1
		function glob2json($glob,$path){
			$dx=$path." = new Array();\n";
			foreach($glob as $k=>$v){
				$dx.="\n".$path.'["'.str_replace('/','"]["',$k).'"]="'.$v.'";';
				$dx.="\n".$path.'["'.str_replace('/','"]["',$k).'"]="'.$v.'";';
			}
			return($dx);
		}
		*/

		/*
		document.getElementById('std_link_'+group_name+'_'+i).style.backgroundColor='rgb(220,220,220)';
		document.getElementById('std_link_'+group_name+'_'+i).style.border='1px solid rgb(220,220,220)';
		document.getElementById('std_link_'+group_name+'_'+i).style.borderBottom='1px solid black';
		*/
	/*
	document.getElementById('std_link_'+group_name+'_'+tab_name).style.backgroundColor='white';
	document.getElementById('std_link_'+group_name+'_'+tab_name).style.border='1px solid black';
	document.getElementById('std_link_'+group_name+'_'+tab_name).style.borderBottom='1px solid white';
	*/

/*
 *	function get_kids($current_record_id,$view){
		//p2($current_record_id,'red');	
		$dx='';
		//use model information
		if($view['kids']=='model'){
			if(is_array($this->rel['has'])){
				foreach($this->rel['has'] as $k=>$has){
					$has_foreign=0;
					if(is_array($has)){
						//allow override of special info, connect fields, etc					
					}else{
						//simple connect
						$info=array();
						$info['table']=$has;//key is used, avlue is ignored.
						$m=$this->load_file($has);
						#p2($m,'yellow');
						$info['id']=$has;
						$info['id']=$m->id;
						$info['ifield']=$m->ifield;
						foreach($m->fields as $kf=>$field){
							//rudimentary, must improve!
							if($field['name']==$this->table.'_id'){
								$has_foreign=1;
								$info['foreign_key']=$field['name'];
								break;
							}
						}

						if($has_foreign==0){
							$this->i_error('no_foreign_field_found','std043',array('mod'=>$has));
						}
							
							
					
					}
					//p2($info,'red');
					
					//now that we have the info, lets fetch some data, based on it
					//\todo 259 expose thie functionality (AR proxy?)
					//\todo 260 MUST NOT USE THIS ON INSERT VIEWS
					
					if($has_foreign==1){
						$o=$this->q2obj("SELECT ".$info['id'].",".$info['ifield'].
							" FROM ".$info["table"].
							" WHERE ".$info["foreign_key"].' = '.$current_record_id);
						if(count($o)>0){
							foreach($o as $i=>$line){
								#todo fix edit_all: must be configurable
								$o[$i][$info['ifield']]=
									"<a href='?mod=" . $info['table']. "&ac=view:edit_all&id=". $o[$i][$info['id']] ."'>".$o[$i][$info['ifield']].'</a>';
							}
							//$m->i18n('table_plural')
							$this->add_privilege(array(
								"privilege_name"=>$m->get_i18n_text("view:edit_all")." ".$m->i18n('table_name'),
								"action"=>$info['table'].'/view:edit_all',
								"role_name"=>$this->get_role_name()
							));
							//
							$dx.="<br>".$this->table($o,'none',
								array(
									'style'=>'list',
									'border'=>0,
									'title'=>$m->i18n('table_plural')
								)
							);
						}
					}else{
						#there is no $info to begin with!
					}
											
					//$dx.="<br>".$m->dsl(array($m->ifield),$info['foreign_key'] .'='. $current_record[$this->id] );
					
				}
			}
			//\todo allow user to set HBM on view level
			//when
			if(is_array($this->rel) && array_key_exists("hbm",$this->rel)){
				foreach($this->rel["hbm"] as $foreign_table=>$connector_table){
					$m=$this->load_file($foreign_table);

					$this->f->add_field(array(
						"type"=>"checklist",
						"name"=>$foreign_table."_list",
						"i18n_text"=>$m->i18n("table_plural"),
						"i18n_help"=>"something...",//todo here
						"options"=>$this->q2op("SELECT * FROM ".$foreign_table),
						//TODO must be able to determine WTF are the fields!
						"values"=>$this->q2a("SELECT ".$foreign_table."_id FROM ".$connector_table." WHERE ".$this->table."_id = '$current_record_id'"),
					));
					$this->f->add_field(array(
						"type"=>"protected",
						"name"=>$foreign_table."_list_hbm",
						"value"=>$this->rel["hbm"],//todo allow user to set this to: something else.
					));
				}
			}
			#p2(array('rel',$this->rel),'blue');
			//$a=array(1,2,3,4,5,array(1,2,3));echo('<pre>');print_r($a);echo('</pre>');
			
		}		
		return($dx);
	}
 * */
