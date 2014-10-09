<?php

function std_ac_all_load_from_excel(&$me,$argv){
	global $mydir;
	$options=$argv[0];

	if(isset($_POST['_postback']) && isset($_POST['_postback'])==1){
		//PROCEED WITH UPLOAD
		if(!file_exists('./uploaded_files')){mkdir('./uploaded_files');}
		copy($_FILES["file"]['tmp_name'],'./uploaded_files/'.$_FILES["file"]["name"]);
		$file_name=$_FILES["file"]['tmp_name'];
		$me->load_external('ac_load_csv');
		$data=std_clean_csv_data($file_name);
		unlink($file_name);

		$headers=$me->aa($data[0]);

		$h2=$headers;
		
		unset($data[0]);//DONT INSERT DATA
		
		//CHECK COLUMN NAMES MUST BE PERFECT
		foreach($me->fields as $fname=>$field){
			if(!isset($headers[$me->fi($fname)])){
				$me->set_cmessage("Falta Columna: ".$me->fi($fname));
				$me->send_b2l_headers();
				return('');
			}else{
				unset($h2[$me->fi($fname)]);
			}
		}
		//CHECK UKNOWN COLUMNS
		if(count($h2)>0){
			$me->set_cmessage("Sobra Columna:'".implode(',',$h2)."'");
			$me->send_b2l_headers();
			return("");
		}

		//USE THIS FOR FOREIGN ID RESOLUTION
		$fs=$me->foreign_select(array(
			'fields'=>$me->fields,
			'restrict'=>' FALSE ',
			'get_fid'=>0,
		));

		//CREATE DICTIONARIES FOR FOREIGN NAME TO ID CONVERSION
		/*
		$mem=array();
		foreach($me->fields as $fname => $field){
			if($fs['qfields'][$fname]['is_foreign'] == 1){
				$ft = $fs['qfields'][$fname]['table'];
				$m = $me->load_file($ft);
				$mem[$ft]=$me->q2op("SELECT ".$m->ifield.",".$m->id." FROM ".$m->get_table_name(),$m->ifield,$m->id);
			}
		}
		*/
		$queries=array();
		foreach($data as $rownum=>$row){
			$isql='INSERT INTO '.$me->get_table_name();
			$fn=array();
			$fv=array();
			$cn=0;
			foreach($me->fields as $fname => $field){
				if($fname==$me->id){
					$row[$cn]=0;
				}
				/*

				//CONVERT Administrator to 1 on usr_id
				if($fs['qfields'][$fname]['is_foreign'] == 1){
					$ft = $fs['qfields'][$fname]['table'];
					if(!isset($mem[$ft][$row[$cn]])){
						$me->set_cmessage("<br/>NO EXISTE EL VALOR:".$row[$cn]." EN LA TABLA:".$ft.' campo:'.$fname.' en la linea:'.$rownum);
						$me->send_b2l_headers();

						return("");
					}else{
					
					}
					$row[$cn] = (int)$mem[$ft][$row[$cn]];
				}
				 */

				if($fname==$me->id){
					//for mysql
					$fv[$fname]="0";

				}elseif($field['type']=='number'){
					$row[$cn] =(int)$row[$cn];//WATCH IT!
					if(!is_int($row[$cn])){
						$me->set_cmessage("<br/>VALOR INVALIDO PARA CAMPO $fname : ".$row[$cn]." DEBE SER NUMERO".' en la linea:'.$rownum);
						$me->send_b2l_headers();
						return("");
					}
					$fv[$fname]=$row[$cn];
				}elseif($field['type']=='date'){
					if(!preg_match('/^\d\d\d\d-\d\d-\d\d/',$row[$cn])){
						$me->set_cmessage("<br/>VALOR INVALIDO PARA CAMPO $fname : ".$row[$cn]." DEBE SER FECHA: YYYY-MM-DD".' en la linea:'.$rownum);
						$me->send_b2l_headers();
						return("");
					}
					$fv[$fname]="'".$row[$cn]."'";
				}else{
					//VARCHAR; TEXT; DOUBLE; ETC
					$fv[$fname]="'".$row[$cn]."'";
				}

				//TODO field lengths
				$fn[$fname]=$fname;
				$cn++;
			}
			$isql.='('.implode(',',$fn).')';
			$isql.=' VALUES('.implode(',',$fv).')';
			$queries[]=$isql;
			#DBG echo($isql.";<br/>");
		}

		//RUN QUERIES
		$ids = array();
		foreach($queries as $q){
			$me->sql($q);
			$ids[]=$me->last_id();
		}
		$me->log_event('IMPORT:'.count($queries)." rows: ".implode(',',$ids));
		$me->set_cmessage('Importadas:'.count($queries));
		$me->send_b2l_headers();
	}else{
		$me->enable_header($me->current_action);
		$me->head();
		$me->menu();
		//SHOW UPLOAD FILE FORM
		$s=new form();
		$s->method='POST';
		$s->action='?ac='.$me->current_action.'&mod='.$me->program_name();

		$s->strings=array(
			'_form_title'=>$me->i18n('table_plural') .':'.$me->i18n_std('load_csv'),
			'file'=>'Archivo',
			'help_file'=>'Archivo',
		);
		$s->add_field(array('name'=>'file','type'=>'file'));
		$s->add_field(array('name'=>'mod','type'=>'hidden','value'=>$me->program_name()));
		$s->add_hidden_field('_postback',1);
		$s->add_submit_button(array(
			'label'=>$me->i18n_std('load_csv'),
			'action'=>$ac,
		));
		$s->shtml();
		//NO NEED TO: FOOT
	}
}

?>
