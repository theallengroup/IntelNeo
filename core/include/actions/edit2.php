<?php

global $mydir;

	/**
	 * Calls Menu()
	 * \todo add 'back2list'
	 * */
	if($this->on_before_edit2()){
		$this->set_global_current_record($this->remove_strange_chars($_GET[$this->id]));

		$sql=array();
		$fc=0;//field count
//		p2($_GET);
//		p2($this->rel);
		$log_info=array();
		$allok=1;
		foreach($this->fields as $k=>$field){
			
			if($this->field_is_available($field)){
				$_GET[$field['name']]=$this->validate($_GET[$field['name']],$field);

				if(array_key_exists('unique',$field) && $field['unique']==1){
					
					$condition='';
					if(isset($field["unique_list"])){
						
						//ok here we go!
						$dx=array();
						foreach($field["unique_list"] as $unique_field_name){
							$dx[]="$unique_field_name ='".$this->remove_strange_chars($_GET[$unique_field_name])."'";
						}
						$condition= " AND ".implode(' AND ',$dx);
					}
					
					$d=$this->q2obj("SELECT count(*) as c FROM ".$this->get_table_name()." WHERE ".$field['name']." = '".$_GET[$field['name']]."' AND ".$this->id."<>'".$_GET[$this->id]."' ".$condition);
					if($d[0]['c']!=0){
						$failed_field_name=$this->fi($field['name']);
						$failed_field_value=$_GET[$field['name']];
						$allok=0;
						break;	
					}
				}


				//SQL SERVER PATCH
				if($field['name']==$this->id){
					continue;
				}
				$sql[]=" ".$field['name']." = '".$_GET[$field['name']]."' ";
				$log_info[$field['name']]=$_GET[$field['name']];
				$fc++;
			}
		}


		if($allok==1){
			if($fc!=0){
				$where_clause = " \nWHERE\n ".$this->id." ='".$_GET[$this->id]."'";
				$old_data = $this->q2obj('SELECT * FROM '.$this->get_table_name().$where_clause);
				$this->sql('UPDATE '.$this->get_table_name()." SET \n\t".implode(",\n\t",$sql).$where_clause);
				$r=($this->affected()==1);
			}else{

				$this->log('no fields available in $_GET.','WARNING');
				$r=0;	
			}
			

			if(isset($_GET['__view_hbm']) && $_GET['__view_hbm'] == 1){
				
				if(isset($this->rel['hbm'])){

					foreach($this->rel["hbm"] as $foreign_table=>$connector_table){

						$finput=$foreign_table."_list";

						///@todo 234897 some (bad) assumptions are made, please remove this, and be more consise next time.
						$m=$this->load_file($connector_table);
						$allok2=1;
						$ids=array();

						$current_record=$this->remove_strange_chars($_GET[$this->id]);
						
						if(is_array($_GET[$finput])){
							foreach($_GET[$finput] as $fkey=>$fvalue){
								$fvalue=$this->remove_strange_chars($fvalue);
								$ids[]=$fvalue;
							}
						}
						$kids_sql = 'SELECT id,id as name FROM '.$m->get_table_name().' WHERE '.$this->program_name().'_id = \''.$current_record.'\'';
						//GET 1:1, 1:2, 3:4
						$f2 = $m->fields;
						unset($f2[$m->id]);
						$list_of_fields = array_keys($f2);
						foreach($list_of_fields as $field_key=>$each_field){
							$list_of_fields[$field_key]=$this->cast_as_string($each_field);
						}
						#p2($list_of_fields);
						#die();
						$str = $this->concat(explode('/',implode('/\':\'/',$list_of_fields)));
						$kids_all_sql = 'SELECT '.$str.' as id,'.$str.' as name FROM '.$m->get_table_name().' WHERE '.$this->program_name().'_id = \''.$current_record.'\'';
						$all = $this->q2op($kids_sql);
						$all_obj = $this->q2op($kids_all_sql);
						if(is_array($all) && count($all)>0){
							$this->sql('DELETE FROM '.$m->get_table_name().' WHERE '.$m->id.' IN (\''.implode('\',\'',$all).'\')');
						}

						if(array_key_exists($finput,$_GET)){
							foreach($_GET[$finput] as $fkey=>$fvalue){

								$fvalue=$this->remove_strange_chars($fvalue);
								$this->sql("INSERT INTO ".$connector_table." (".$this->program_name()."_id,".$foreign_table."_id) VALUES(".$current_record.",".$fvalue.")");
								if($this->affected()!=1){
									$allok2=0;
									$this->set_cmessage("kid insert error","std502");
									break;
								}
							}//for
							$all_obj_new = $this->q2op($kids_all_sql);
							//DBG p2($all_obj,'blue');p2($all_obj_new,'red');
							$diff = array_diff($all_obj,$all_obj_new);
							$diff2 = array_diff($all_obj_new,$all_obj);
							if(count($diff)>0 ||count($diff2)>0){
								$r = 1;
								//DATA UPDATED
							}
						}//field
					}//rel
				}
			}
			if($r){
				$this->log_event('UPDATE:'.$this->nice_array($this->diff_array($log_info,$old_data[0]))  ,$_GET[$this->id]);
				$this->set_cmessage($i18n_std['list']['form_update_ok']);
			}else{
				$this->set_cmessage($i18n_std['list']['form_no_update']);
			}

		}else{
			$this->set_cmessage($this->fmt_msg('not_unique_field_update',
				array(
					'table'=>$this->i18n('table_plural'),
					'field_value'=>$failed_field_value,
					'field_name'=>$failed_field_name
				)
			));
		}
		$this->on_after_edit2();

		//SHOW THE ERROR IF ANY INSTEAD IF HIDING IT ON THE REDIRECT JAVASCRIPT
		#attempted unsuccessfully, @todo finish
		#if($this->query_failed!=0){
		#	$this->enable_edit2_redirect=0;
		#}
		
		if($this->enable_edit2_redirect==1){
			$this->send_b2l_headers();
		}
	}
?>
