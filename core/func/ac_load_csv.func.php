<?php
function std_csv_cleanup($file_name){
	global $mydir;
	if(file_exists($file_name)){
		unlink($file_name);
	}else{
		echo('file missing');
		echo(b2());
	}
	unset($_SESSION[$mydir]['sif_file_name']);
	unset($_SESSION[$mydir]['sif_map']);
	unset($_SESSION[$mydir]['sif_missing']);
	unset($_SESSION[$mydir]['sif_missing_from_db_in_file']);
}

function std_clean_user_fields($user_fields){
	foreach($user_fields as $f=>$user_field){
		if(!$user_field==''){
			$user_field=$user_fields[$f]=strtolower($user_field);
		}else{
			//Avoid "spaces", and most right ONES
			//unset($user_fields[$f]);
		}
	}
	return($user_fields);
}
function std_clean_csv_data($file_name){
	global $main;
	$lines=file($file_name);
	$data=array();
	$split_char = ';';//excel
	if(strpos($lines[0],";")===FALSE){
		$split_char=',';
	}
	foreach($lines as $line){
		$line_data=explode($split_char,str_replace(array("\n","\r"),'',$line));
		foreach($line_data as $k=>$value){
			$line_data[$k]=$main->remove_strange_chars(str_replace('"','',$value));
		}
		$data[]=$line_data;
	}
	return($data);
}

function std_ac_load_csv(&$me,$argv){
	global $mydir;
	$options=$argv[0];
	$me->menu();
	if(!isset($_FILES["file"]) && !isset($_SESSION[$mydir]['sif_file_name'])){
		$s=new form();
		$s->method='POST';
		$s->action='?ac='.$me->current_action.'&mod='.$me->program_name();

		$s->strings=array(
			'_form_title'=>$me->i18n('table_plural') . ':Cargar Archivo<br/>',
			'file'=>'Archivo',
			'help_file'=>'Archivo',
		);
		$s->add_field(array('name'=>'file','type'=>'file'));
		$s->add_field(array('name'=>'mod','type'=>'hidden','value'=>$me->program_name()));

		$s->add_submit_button(array(
			'label'=>'Cargar',
			'action'=>$ac,
		));
		$s->shtml();
	}elseif(!isset($_SESSION[$mydir]['sif_file_name'])){
		if($_FILES["file"]['error']==0){
			if(!file_exists('./uploaded_files')){mkdir('./uploaded_files');}
			copy($_FILES["file"]['tmp_name'],'./uploaded_files/'.$_FILES["file"]["name"]);
			$file_name=$_FILES["file"]['tmp_name'];
			$data=std_clean_csv_data($file_name);
			$c=0;
			$d2=array();
			foreach($data as $row){
				$d2[]=$row;
				$c++;
				if($c>10){break;}
			}

			$map=array();//local=>theirs

			$user_fields=$me->aa(std_clean_user_fields($data[0]));

			$missing_from_file_in_db=array();
			//exact 
			foreach($me->fields as $dbfield){
				if((!array_key_exists($dbfield['name'],$user_fields)) || (!array_key_exists(strtolower($me->fi($dbfield['name'])),$user_fields))){
					$missing_from_file_in_db[$dbfield['name']]=$dbfield['name'];
				}else{
					//exact match
					$map[$dbfield['name']]=$dbfield['name'];
				}
			}

			//inside
			$user_field_count=1;
			foreach($user_fields as $user_field){
				$cc=0;
				foreach($missing_from_file_in_db as $missing_field){
					if($cc==1){
						break;
					}
					//echo("<br>$missing_field = $user_field ");
					if($user_field==''){
						$me->msg('<br/>Encabezado Vacio! Columna # '.$user_field_count);
						$cc=1;
						continue;
					}
					if(strpos(strtolower($me->fi($missing_field)),$user_field)!==FALSE || strpos($missing_field,$user_field)){
						//found!
						$map[$missing_field]=$user_field;
						unset($missing_from_file_in_db[$missing_field]);
						$cc=1;
					}
				}
				$user_field_count++;
				if($cc==1){
					$cc=0;
					continue;
				}
				// ... 
				//never runs on cc==1
			}

			//check for fields in CSV file not in dbfields
			//
			$map2=array();
			foreach($map as $k=>$v){
				$map2[$v]=$k;
			}
			$headers=std_clean_user_fields($data[0]);

			$missing_from_db_in_file=array();	//12 => rare_field
			$additional_fields=array(
				'_sep'=>array('type'=>'separator'),
			);

			//remove already mapped fields from options, we already know what goes there!
			$all_dbfields=array();
			$all_dbfields['@none']='Ninguno';
			$all_dbfields=array_merge($all_dbfields,$me->get_headers());

			foreach($map as $local_map_name=>$value2){
				unset($all_dbfields[$local_map_name]);
			}

			foreach($headers as $collumn_number=>$user_field){
				if(count($headers)<=$collumn_number){ //never happens
					//far right, we don't give a shit aboout these data
					continue;
				}

				if(!isset($map2[$headers[$collumn_number]])){
					$missing_from_db_in_file[$collumn_number]=$user_field;

					$additional_fields['added_'.$collumn_number]=array(
						'name'=>'added_'.$collumn_number,
						'type'=>'list',
						'options'=>$all_dbfields,
						'i18n_text'=>$user_field . "($collumn_number)",
						'i18n_help'=>'none',
					);

					//echo(' MAP INVALIDO h[cn]='.$headers[$collumn_number].' CN='.$collumn_number);
					//std_csv_cleanup($file_name);
					//die();
				}
			}

			if(isset($missing_from_file_in_db[$me->id])){
				//
				//we don't want the ID then
				//we either get the ID in the field list, or not at all
				unset($missing_from_file_in_db[$me->id]);
			}
			//p2($missing_from_file_in_db,'red');

			$_SESSION[$mydir]['sif_file_name']=$_FILES["file"]["name"];
			$_SESSION[$mydir]['sif_map']=$map;
			$_SESSION[$mydir]['sif_missing']=$missing_from_file_in_db;
			$_SESSION[$mydir]['sif_missing_from_db_in_file']=$missing_from_db_in_file;

			///$me->form_from_fields($me->current_action,$missing_from_file_in_db,array());
			$form_fields=array();
			foreach($missing_from_file_in_db as $missing_field){
				$form_fields[$missing_field]=$me->fields[$missing_field];
			}
			$form_fields=array_merge($form_fields,$additional_fields);
			///@todo 9123478 WARNING! what happens if there are NO FIELDS THAT NEED TO BE ADDED
			$me->shadow_start('round');
			$me->ed(array(
				'width'=>$options['width'],
				'actions'=>array($me->current_action),
				'rawtitle'=>'Archivo: '.$_FILES["file"]["name"],
				'data'=>'',
				'style'=>'form',
				'fields'=>$form_fields,
			));
			echo("<div style='width:600px;overflow:scroll' >");
			$me->e_table($d2,$d2[0],array('style'=>'list'));
			echo("</div>");
			$me->shadow_end('round');
		}else{
			echo('Error en el archivo.');
		}
	}else{
		//insert the records.
		$file_name='./uploaded_files/'.$_SESSION[$mydir]['sif_file_name'];
		$rc=0;
		$headers=array();
		$ids=array();
		foreach(std_clean_csv_data($file_name) as $row){
			if($rc==0){
				//fisrt row is header always
				$headers=std_clean_user_fields($row);

			}else{
				//invert array
				$map2=array();
				foreach($_SESSION[$mydir]['sif_map'] as $k=>$v){
					$map2[$v]=$k;
				}

				//get
				//

				$dbfields=array();
				$dbvalues=array();
				$has_id=0;

				foreach($row as $collumn_number=>$value){
					if(count($headers)<=$collumn_number){
						//far right
						continue;
					}
					if(!isset($headers[$collumn_number])){
						echo(' CAMPO INVALIDO:'.$collumn_number);
						std_csv_cleanup($file_name);
						die();
					}
					//apply hand made mappings
					foreach($_SESSION[$mydir]['sif_missing_from_db_in_file'] as $collumn_number2=>$user_field2){
						//@none
						if($collumn_number2==$collumn_number){
							$map2[$user_field2]=$_GET['added_'.$collumn_number];
							break;
						}
					}
					if(!isset($map2[$headers[$collumn_number]])){
						echo(' MAP INVALIDO h[cn]='.$headers[$collumn_number].' CN='.$collumn_number);
						std_csv_cleanup($file_name);
						die();
					}
					$mapped_field=$map2[$headers[$collumn_number]];
					if($mapped_field==$me->id){
						$has_id=1;
					}

					if($mapped_field!='@none'){
						//we ignore the @none==Ninguno Fields, so they don't get inserted in the database
						///@todo 123948 in the NEXT version, make sure we show ALL fields with All mappings so the user can make "None" anything he wants, or switch fields crazily (i don't think theyll ask for it)
						$dbfields[$map2[$headers[$collumn_number]]]=$map2[$headers[$collumn_number]];
						$dbvalues[$map2[$headers[$collumn_number]]]="'$value'";
					}else{
						//si usa NONE, entonces use el DATO QUE PUSO el usuario
					}
				}
				if(!$has_id){
					$dbfields[$me->id]=$me->id;
					$dbvalues[$me->id]="'0'";
				}
				foreach($_SESSION[$mydir]['sif_missing'] as $missing_field){
					$dbfields[$missing_field]=$missing_field;
					$dbvalues[$missing_field]="'".$me->remove_strange_chars($_GET[$missing_field])."'";
				}
				$sql='INSERT INTO '.$me->get_table_name().' ('.implode(',',$dbfields).') VALUES ('.implode(',',$dbvalues).')';
				$me->sql($sql);
				$ids[]=$me->last_id();
				//echo($sql.';<br>');
			}
			$rc++;
		}

		echo("<br/>--RUN ".$file_name."<br/> DELETE FROM ".$me->get_table_name().' WHERE id in ('.implode(',',$ids).');<br/>--to undo the last command');
		std_csv_cleanup($file_name);
	}
}

?>
