<?php

/** \brief display a single record.
 * 
 * SIDE EFFECT: sets $me->f to something!.
 *
 *	 recieve STYLE
 *	 options.
 *	 @param $options
 *	 $options : an array:
 *	 $options.fields
 *	 $options.width
 *
 *	 actions: an actions array:
 *	 items are like: edit, delete, etc,
 *	 it can also be an array: array('action'=>something,'')
 *
 *	 this retrieves the templates from /template/[#my_table]/[#view_name].template.html 
 *	 if they exists, and applies them.
 *
 *	 warning: called by: view::get_advanced_search_form
 *	 \bug label does not recognize foreign data
 *	 \todo 240 poorly documented
 *
 *
 * throws error std001:
 * this error responds to a problem: 
 * a field is being added in the wrong way, adding items
 * to the $me->fields array.
 * to add fields correctly (on runtime), you must use the addf function
 * */

function std_get_ed(&$me,$argv){

	global $i18n_std,$i18n,$config;
	$options=$argv[0];
	//p2($argv);
	if(!array_key_exists($options['title'],$i18n[$me->program_name()])){
		if(!array_key_exists('rawtitle',$options)){
			$title='';
			$me->log("error: title not found.".$options['title'],'NOTICE');
		}else{
			$title=$options['rawtitle'];
		}	
	}else{
		$title=$i18n[$me->program_name()][$options['title']];
	}

	$method=(array_key_exists('method',$options) && $options['method'] != '')?$options['method']:'GET';
	$form_action=(array_key_exists('action',$options) && $options['action'] != '')?$options['action']:'?';

	$dta=$options['data'];


	#$ac,$title,$ok,$dta
	#just a nick name
	$form_fields=$options["fields"];
	#p2($form_fields,'red');
	$width=$options["width"];

	if($dta==''){
		#no data was given. use defaults.
		$dta[0]=array();
		foreach($form_fields as $kk1=>$vv1){
			$dta[0][$kk1]=$vv1['value'];
		}
	}
	//aun no hay valores aqui

	foreach($dta as $k=>$row){	//WTF!
		foreach($row as $k1=>$field_value){
			/** \todo if not in list, warn user about changed database, send email admin*/
			
			if(!array_key_exists($k1,$form_fields)){
				$me->error($i18n_std['error']['application_corrupt'].$k1,'std001');
				$me->plog($form_fields,'fields');
			}

			$form_fields[$k1]['value']=$field_value;
			$form_fields[$k1] = $me->field_make_foreign($form_fields[$k1]);

			$form_fields[$k1]['exists_in_list']=1;
		}
	}
	#p2($form_fields);
	//apply behavior if any.
	$d1 = $form_fields;
	$form_fields = $me->fields_transform($form_fields,$options);
	$form_fields = $me->fields_auto_value($form_fields,$options);
	$form_fields = $me->fields_make_multiple($form_fields,$options);

	//add current_url field
	if(isset($_GET['__current_url'])){
		$form_fields['__current_url'] = array(
			'type'=>'hidden',
			'name'=>'__current_url',
			'value'=>$_GET['__current_url'],
			'added','is_late_addition'=>1
		);
	}

	///some debug ha!
	///$me->e_table(array(array(gp2($form_fields,'blue'),gp2($d1,'green'))));

	foreach($form_fields as $k=>$field){
		if($field["exists_in_list"] !="1" && (!array_key_exists("is_late_addition",$field))){
			$me->log(''.$k.' not in list','ERROR');
			$me->error($i18n_std['error']['application_corrupt2'].$k,'std002');		
		}
	}
	
	$me->f =new form($form_fields);
	
	if(isset($options["columns"])){
		$me->f->set_columns($options["columns"]);
	}

	//if the action is not set, and the form is POST, we must indicate mod and act headers in the act
	//otherwise, the form wont work, but send trhe user to the login page.

	/*
	foreach($form_fields as $f1=>$f11){
		if(isset($f11["type"])&&$f11["type"]=='upload'){
			$method='POST';
			$me->f->add_field(array('name'=>'__form_method','value'=>'POST','type'=>'hidden'));
			break;
		}

	}
	 */
	if($method=='POST'){
		#p2($me->f->action,'red');
		//$me->f->action='?mod='.$form_fields['mod']['value'].'&ac='.$form_fields['ac']['value'];
	}


	$me->f->method=$method;
	$me->f->action=$form_action;

	/*
	 * retrieve template: can belong to template_file view option.
	 * templaye_file syntax:
	 * - TEMP
	 * - MOD/TEMP
	 * - nothing (defaults to MOD/VIEW_NAME
	 *
	 * */
	if(isset($options["template_file"])){
		if(strpos($options["template_file"],"/")!==FALSE){
			$temp='./template/'.$options["template_file"].'.template.html';	
		}else{
			$temp='./template/'.$me->program_name().'/'.$options["template_file"].'.template.html';	
		}
		
	}else{
		$temp='./template/'.$me->table.'/'.$options['view_name'].'.template.html';
	}
	if(isset($options['req_message'])){
		$me->f->req_message = $options['req_message'];
	}

	if(file_exists($temp)){
		$me->f->in_template=1;
		$me->f->template=file_get_contents($temp);
		$me->log('USING TEMPLATE FILE:'.$temp,'FORM');
	}else{
		$me->log('no such template file:'.$temp.', moving on.','NOTICE');
	}
	
	///\todo a better way \TODO ALLOW THIS ON OTHER CONTEXTS (WHICH ONES?)
	foreach($me->get_func_list('input_') as $k9=>$v9){
		$datatype_name=substr($v9,strlen('input_'),strlen($v9));
		//$fn=$me->table . '_model::' . $v9 ;
		//$fc=$me->table . '_model::' . $v9 . '($field)';
		$obj=$me;
		$objf=$v9;
		///$me->log("Added Function <B>$v9</B> " . $fc,0);
		//$me->f->$v9=create_function('$field','return('.$fc.');');
		$me->f->register_function($datatype_name,$fn,$obj,$objf);
	}
	//p2($me->f);
	#$f->add_field(array('type'=>'hidden','name'=>'ac','value'=>$ac));
	$me->f->add_field(array('type'=>'hidden','name'=>'mod','value'=>$me->program_name()));
	$me->f->strings=$i18n[$me->program_name()]['fields'];
	$me->f->strings["_form_title"]=$title;
	
	
	#$f->strings["ok"]=$ok;
	foreach($options['actions'] as $k1=>$v1){
		$s=$me->normalize_action($v1);
		$me->f->add_submit_button($s);

	}

	std_get_sub_forms($me,$options);

	//THIS IS THE KIDS section
	if(array_key_exists('kids',$options)){

		if($options['if_not_exists']=='create'){
			//we are in a NEW ITEM view, adding stuff, tehrefore, we don't have
			//a current record ID.

			if($options["kids"]=="model"){
				$hbm_fields=$me->hbm_fields(-1,'create');
				#$me->hbm_fields(-1,'create');
				foreach($hbm_fields as $hkey=>$hfield){
					if(array_key_exists($hkey,$options["fields"])){
						$me->error("field _list exists!",'std505');
						exit;
					}
					$me->f->add_field($hfield);
				}

			}else{
				///@todo 129837 allow kids=model to be changed by kids=something else
			}
			
		}else{
			$current_record_id=$dta[0][$me->id];	
			if($options['kids']=='model'){
				$fe=$me->get_kids($current_record_id,$options);
				$me->f->form_end.=$fe;
			}elseif(is_array($options['kids'])){
				$me->f->form_end.=std_get_included_views($me,$options,$current_record_id);
			}
			
		}
	}
	$me->f->border_style=$me->shadow_config['form'];

	//p2($me->f,'red','get_ed() options');
	return($me->f);

}#end get_ed

/** 
 * turns kids=>(mod/ac,mod/ac) into what it means, and it adds restrict where clause automagically too! 
 * */
function std_get_included_views(&$me,$view1,$current_record_id){
	$include_contents = '';
	if(isset($view1['kids'])){//This only makes sense when used in ED context, not in LIST context
		$conditions_array = array(array($me->program_name(),'=',$current_record_id));
		foreach($view1['kids'] as $view_name){
			$mod=explode('/',$view_name);
			$view_name = $mod[1];
			$mod = $mod[0];
			$m = $me->load_file($mod);
			
			$include_contents.= $m->get_view_contents($view_name,
				array(
					'link_options'=>array(),
					'fields'=>array(
						$m->get_connector_field($me->program_name()) => array('restrict'=>$current_record_id)
						)
					)
				);
		}
	}
	return($include_contents);
}

/**
 *
 *
 * THIS DOES NOT WORK
 *
 *
 *
 *
 *
 *
	 * \todo 252 some way of disabling this!
	 *
	 * side effect: write to $me->f
	 * called by: get_ed()
	has many
	the has many array has the following syntax:
	has_many=array(
			'table_name'					//string:requires table name to have a <this.table>_id field
			array('table_name','another_table_name')	
			//array:requires table_name to have a foreign field pointing 
			to this table, an another foreign field (f2), pointing to <another_table> , 
			from where i'll use the another_table->name
			int ->no action taken (compatibility issue)
		)
		\todo 253 which fields?
		@param $options: view struct
		[prefix fix]
		\bug finish this function get_sub_forms()
	 * */

function std_get_sub_forms(&$me,$options){
	///p2($options);
	if(!is_array($options['children'])){
		return(0);
	}
	foreach($options['children'] as $k=>$v){
		if(is_int($v)){
		
		}elseif(is_array($v)){
			//p2($view);
			//TODO \todo a better not so hacky way of doing this! (needs load_file (reflection))

			$myid=$me->table.'_'.$me->id;
			$id=$me->remove_strange_chars($_GET['id']);
			//conection table	r2p
			$foreign_field=$v[0].".id";	
			$my_foreign_field=$v[0].".".$me->table."_id";	
			//description table	role
			$foreign_desc_field=$v[0].".".$v[1].'_id';	

			//'.$foreign_field.' as v0_id ,
		//	$s=$me->q2obj('SELECT * FROM '.$v[0].', '.$v[1]." WHERE $foreign_desc_field = $foreign_field AND  ".$myid." = '$id'");
			$all=$me->q2obj('SELECT * FROM '.$v[1]);
		//	p2($all);
			$mine=$me->q2obj('SELECT * FROM '.$v[0]." WHERE '".$id."' = ".$my_foreign_field);
		//	p2($mine,'red');

			//roles con este privilegio

			//$where=$v[1].'.id = '. $me->sub_implode($s,'id', ' OR '.$v[1].'.id = ');
			//$q=$me->q2obj("SELECT * from ".$v[1].' WHERE '.$where);

			$items_that_are_mine=$me->sub_array($mine,$v[1].'_id');
		//	p2($items_that_are_mine);
			$end=array();
			foreach($all as $k1=>$v1){
				if(in_array($v1['id'],$items_that_are_mine)){
					$end[]=array($v1,'checked');
				}else{
					$end[]=array($v1);
				}
			}
	///		p2($end,'blue');
	//		$me->f

		//	$me->f->add_block('hello world');
		
		}elseif(is_string($v)){
			//a list like: a,b,c,d,e

		}else{
			$me->log('WTF r u trying to do in has_many?','ERROR');
		}
	}
}

?>
