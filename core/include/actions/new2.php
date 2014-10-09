<?php
	/**
	 * returns 1 on success and 0 on faluire
	 * */
	if($this->on_before_new2()){
		$r=0;
		#p2($_GET,'red');
		#p2($_POST,'blue');
		#p2($_FILES,'green');
		#p2($_SERVER);
		#if($_SERVER["REQUEST_METHOD"]=='POST'){
		//welcome to special mode
		
		#}

		if($this->silent==0){
			
		}
		$f=array();
		$log_info=array();
		$allok=1;
		$failed_field='';
		$fnames=array();
		

		foreach($this->fields as $k=>$field){
			//if($field['type']=='password'){	
			
			/** \todo 10001 more validation schemes, a validation function, 
			 * \todo 10001 validate dates within ranges, 
			 * \todo 10001 validate stuff within ranges, 
			 * \todo 10001 validate within lists, etc, 
			 * \todo 10001 remove errors like feb 31, etc.
			 * */
			
			//	$_GET[$k]=md5($_GET[$field['name']]);
			//}
			$_GET[$field['name']]=$this->validate($_GET[$field['name']],$field);
			
			#DBG echo("<h1>".$field['name'].'.'.$_GET[$field['name']]."</h1>");
			if($field['name'] == $this->id  ){ //&& ($config["engine_name"]=='odbc' || $config["engine_name"]=='sqlsrv' )
				//this is a serial ID, 
				//I won't put it in the list.
				//BUT ONLY IN SQLSRV CASE!!!!

			}else{
				//pgfix
				if($field['type']=='number'){
					if($_GET[$field['name']]==''){
						$_GET[$field['name']]=0;
					}
					$f[]=$_GET[$field['name']];		
				}else{
					$f[]="'".$_GET[$field['name']]."'";
				}
				$fnames[]=$field['name'];
			}
			$log_info[$field['name']]=$_GET[$field['name']];
			if($field["type"]=='date'){
				$this->log("date valor:".$_GET[$field['name']],'INFO');
			}
			/*This field cannot be repeated in the database, sorry. */
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
				$d=$this->q2obj("SELECT count(*) as c FROM ".$this->get_table_name()." WHERE ".$field['name']." = '".$_GET[$field['name']]."' ".$condition);
				if($d[0]['c']!=0){
					$failed_field_name=$this->fi($field['name']);
					$failed_field_value=$_GET[$field['name']];
					$allok=0;
					break;	
				}
			}
		}
		if($allok==1){
			$this->sql("INSERT INTO \n".$this->get_table_name()."\n(".implode(",\n",$fnames).") VALUES(".implode(",\n",$f).')');
			$current_record=$this->last_id();
			$this->set_global_current_record($current_record);
			if($this->affected()==1){
				if($this->silent==0){ 
					$this->set_cmessage($this->fmt_msg('form_inserted_ok'));
				}
				$this->log_event('INSERT:'.$this->nice_array(array_merge($log_info,array('id'=>$this->last_id()))),$this->last_id());
				$r=1;
			}else{
				($this->silent==0) ? $this->set_cmessage($this->fmt_msg('insert_error')):'';
				$r=0;
			}

			//insert kids, when available
			///@todo fix vulnerability: check for __hbm view_id hashbm
			///@todo check permissions over the other tables (all_new2)
			if(isset($_GET['__view_hbm']) && $_GET['__view_hbm'] == 1){
				
				if(isset($this->rel['hbm'])){
					
					//debug: 
					//p2($this->rel["hbm"] ,'red');
					
					foreach($this->rel["hbm"] as $foreign_table=>$connector_table){

						$finput=$foreign_table."_list";

						if(array_key_exists($finput,$_GET)){
							//info is available, insert.
							//some (bad) assumptions are made, please remove this, and be more consise next time.
							//todo WHICH IS MY FIELD?
							//todo error __file__ info...
							$m=$this->load_file($connector_table);
							$allok2=1;
							
							foreach($_GET[$finput] as $fkey=>$fvalue){
								
								$fvalue=$this->remove_strange_chars($fvalue);
								$this->sql("INSERT INTO ".$connector_table." (".$this->program_name()."_id,".$foreign_table."_id) VALUES(".$current_record.",".$fvalue.")");
								if($this->affected()!=1){
									$allok2=0;
									$this->set_cmessage("kid insert error","std502");
									break;
								}
							}//for
						}//field
					}//rel
				}
			}
				
		}else{
			$this->set_cmessage($this->fmt_msg('not_unique_field',
				array(
					'table'=>$i18n[$this->table]['table_plural'],
					'field_value'=>$failed_field_value,
					'field_name'=>$failed_field_name
				)
			));
			$r=0;
		}
		$this->on_after_new2();
		if(!$this->silent){
			if($this->enable_new2_redirect==1){
				$this->send_b2l_headers();
			}
		}
	}
?>
