<?php
/**
 * @page foreign_select Foreign Select
 * foreign select returns a SQL statement of all the fields, joined.
 * it allows just a few fields, and not all of them, an array of keys
 *
 *
 *
 * IF you store the output of this function in this.fs_options you'll play nicely with filters like filter_money()
 * so watch out!
 *
 * \todo 272 foreign_select() explain this better
 *
 *
 * WARNING: use get_fid=0 for better results!
 *
 recieves:
 sort_field,	:can take comma saperated values, a,b,c ???
 sort_direction,
 fields,
 restrict,
 group_field	:can take comma saperated values, a,b,c
 get_fid		(get foreign id: default to 1)
 in group statements, CSQL makes no sense, s don't use it

 @returns
 it returns an array with the following keys:

 sql:		the join statement
 sfields:	an array with the fully qualified names of the fields.

 qfields:	the whole mess.

 qfields.d tblname.fieldname

 --was added
 --foreign_id	

 csql: 		a sql statement of count() (useful for your Pagination scheme,
	 c sql retusnr the count() of your designated ID field ($this->id) as the "c" field.
	 that's right, it is YOU who implentents it.)
	 sql_fields	id=>table_id		
	 restrict:	a SQl where clause, so you can trim down your results, to only the relevant set.

	 fields may have a display_id==1

	 \todo 267 allow restrictions on foreign fields, restrict. (a lot of editown goes here, right?)
	 \callgraph


	 example
	 @code
	 $fs=$this->foreign_select(array(
		 'fields'=>$this->fields,
		 'sort_field'=>$this->table.'.'.$this->id,
		 'sort_direction'=>'ASC',
		 'get_fid'=>0,	//most likely what you want
	 ));
example2
@code
$fs=$this->foreign_select($view_structure);
@endcode
	* */

function std_foreign_select(&$me,$argv){
	$options=$argv[0];

	global $i18n,$i18n_std;
	$me->log('FSCALLED','MODULE');

	//name, tablename.name alias.name alias_name table_name

	$qfields=array();

	$sort_field=$options['sort_field'];

	if(!array_key_exists('group_field',$options)){
		$group_statement=0;
	}else{
		$group_statement=1;
		$group_field=$options['group_field'];
	}
	$sort_direction=$options['sort_direction'];
	$which_fields=$options['fields'];


	//this is LAME!
	$restrict=$options['restrict'];
	if($restrict==''){$restrict='1=1';}

	$fields=array();
	$sfields=array();	//all fields in a.b notation
	$sql_fields=array();	//all fields in a_b notation

	//the array of tables, that will appear on the FROM clause
	//such array usually starts with THIS table (that is, you want stuff from THIS table, right?)
	//and has usually components from other (foreign) tables,
	//that hold the descriptive of the foreign fields, for instance, usr_id.
	//where usr is a foreign table, and _id is the foreign key.

	$tables=array($me->get_table_name());

	#$table_aliases=array($me->get_table_name()=>$me->get_table_name());
	$join=array('1 = 1');/// \todo 1101 do I need this?
	$final=array();
	#echo("ST:");p2($which_fields,'red');
	$na=$me->need_alias($which_fields);
	$ialias=0;
	$sort_field_valid=0;
	foreach($which_fields as $k=>$field){
		


		$qfields[$k]=array(
			'simple_name'=>$field['name']
		);

		#$is_foreign=array_key_exists('foreign',$field);
		if($me->field_is_foreign($field)){	/*field is foreign, join tables*/

			//				if($field["display_id"]==1){
			if(!array_key_exists('get_fid',$options) || $options['get_fid']==1){
				#Standard behaviour states that we just want the Foreign data, 
				#but not the actual IDs, here, I''m telling the program that I 
				#also want the IDs, so I can do operations with them.

				//@todo is this legitimate use of >table ????
				$sfields[$me->table.".".$k]=$me->table.".".$k;

				$qfields[$k]['s']=$me->table.".".$k;
				#BLOCK##
				$sql_alias1 = $me->table."_".$k;
				if(isset($field["sql_alias"])){
					$sql_alias1 = $field["sql_alias"];
				}
				#ENDBLOCK##

/*f*/				$fields[]=$me->get_table_name().".".$k." AS ".$sql_alias1;
/*f*/				$qfields[$k]['sql']=$me->get_table_name().".".$k." AS ".$sql_alias1;

				$qfields[$k]['was_added']=1;
				$qfields[$k]['foreign_id']=$me->table."_".$k;
			}

			//				}
			$field=$me->field_deport($field);

			$f3=$me->explain_foreign_field($field['foreign']);
			$modname=$f3['table'];
			$foreign_field=$f3['field'];

			if($modname!=''){
				$fm=$me->load_file($modname,'light');
			}else{

			}
			//p2($f3);

			if($f3['has_special_connector']==0){
				//need mod id
				$foreign_connection_id=$fm->id;
			}else{
				$foreign_connection_id=$f3['id'];
			}
			//echo('<br/>alias is:'.$foreign_connection_id);
			$qfields[$k]['i18n']=$fm->get_i18n_text($fm->default_edit_action).'&nbsp;'.$fm->i18n('table_title');
			$qfields[$k]['link']=$fm->table.'/'.$fm->default_edit_action;

			///echo($fm->ifield);					
			if($na==1){
				$tblalias=$fm->get_table_name().'_alias_'.$ialias;	#generates aliases like:whatever_alias_0 ,etc	
				$tables[]=$fm->get_table_name().' AS '.$tblalias;
				$qfields[$k]['is_aliased']=1;
				$qfields[$k]['alias_path']=$ialias;
				$qfields[$k]['alias_add']='_alias_'.$ialias;
			}else{
				//echo($k.' was here ');
				$qfields[$k]['is_aliased']=0;
				$qfields[$k]['alias_path']='';
				$qfields[$k]['alias_add']='';
				$tables[]=$fm->get_table_name();
				$tblalias=$fm->get_table_name();
			}
			#$table_aliases[$fm->table]=$tblalias;
			//Join is incorrect
			//Links are broken
			$join[]=$tblalias.'.'.$foreign_connection_id." = ".$me->get_table_name().".".$k;

			$sql_alias2 = $tblalias."_".$foreign_field;
			if(isset($field["sql_alias"])){
				$sql_alias2 = $field["sql_alias"];
			}

			$fields[$k]=$tblalias.".".$foreign_field." AS ".$sql_alias2;
			$sfields[$fm->table.".".$foreign_field]=$fm->table.".".$foreign_field;
			$qfields[$k]['s']=$fm->table.".".$foreign_field;
			$qfields[$k]['table']=$fm->table;
			$sql_fields[$k]=$tblalias."_".$foreign_field;
			$qfields[$k]['q']=$tblalias."_".$foreign_field;

			//used in parse_search_string
			$qfields[$k]['d']=$tblalias.".".$foreign_field;

			$qfields[$k]['aliased_table']=$tblalias;
			$qfields[$k]['is_foreign']=1;

			$expr = $tblalias.'.'.$foreign_connection_id;

			$ialias++;		
		}else{	/*field is NOT foreign, add to list*/
			$sql_fields[$k]=$me->table."_".$k;
			$qfields[$k]['q']=$me->table."_".$k;
			$qfields[$k]['d']=$me->get_table_name().".".$k;
			$qfields[$k]['table']=$me->table;
			$qfields[$k]['aliased_table']=$me->table;
			$qfields[$k]['is_foreign']=0;
			$qfields[$k]['is_aliased']=0;
			$qfields[$k]['alias_path']='';
			$qfields[$k]['alias_add']='';
			$sfields[$me->table.".".$k]=$me->table.".".$k;
			$qfields[$k]['s']=$me->table.".".$k;
			#the ones used in the actual SQL expression returned
			#BLOCK##
			$sql_alias1 = $me->table."_".$k;
			if(isset($field["sql_alias"])){
				$sql_alias1 = $field["sql_alias"];
			}
			#ENDBLOCK##
			$fields[$k]=$me->table.".".$k." AS ".$sql_alias1;
			if(!array_key_exists('sql_expression',$field)){
	/*f*/			$field['sql_expression']=$me->get_table_name().".".$k;	//raw and simple: table_name.field_name
			}
			$fields[$k]=$field['sql_expression']." AS ".$sql_alias1;
			$expr = $field['sql_expression'];

		}

		if(isset($field['restrict'])){
			$join[]=$expr." = '".$field['restrict']."'";
		}
	//	if($qfields[$k]['simple_name']==$sort_field){
	//		$sort_field_valid=1;
	//	}else{
	//		//echo("<br>".$sort_field.' != '.$qfields[$k]['simple_name']);
	//	}
	}

	if($options['search_flag']==1){
		/** \this returns  a part of the WHERE clause, in the LIST views (mod/view:list_all et al).
		 * @param $sql_fields array of fields, with aliases (very important, since the SQL we are 
		 * trying to generate, must be properly aliased.)

		 //initial: a:b, later a:b,c:e e:f -g:h (note -g:h removes the pattern)
		//also use a<b, c>d, h>=j, etc
		//also use && and || and AND and OR and Y and O,
		//also perenthesize
		//we are basically parsing this and generating safe sql (isn't that FUN!)
		 * */

		/// \todo 236 memorize/cache function output,fcache()?

		$st = $me->remove_strange_chars($me->remove_html($me->restore_value('__search_term')));
		$dl=$me->parse_search_string($st,$qfields);
		$restrict.=$dl['sql'];
	}

	$fw=	"\n FROM \n\t".implode(",\n\t",$tables).
		"\n WHERE \n\t( ".implode(" AND\n\t",$join)." ) AND ($restrict)";


	if($sort_field!=''){
		//AUTOMATIC FIELD FIX.

		if($sort_field==$me->id){
			//that means you want metablename.id
			$sort_field = $me->get_table_name().'.'.$me->id;
			//there, std076 is now gone!
		}

		//validate sort field
		$sort_field_ok=0;
		##p2($qfields);die();

		foreach($qfields as $k12=>$qfield){
			if($sort_field==$qfield['d']){//	not q $sort_field==$qfield['q'] || 
				$sort_field_ok=1;
				break;
			}

		}
		if($sort_field==''){//its ok to have no sotr field
			$sort_field_ok=1;
		}
		if($sort_field_ok==0){
			$me->log('SORT USED FIELD INVALID='.$sort_field.', USING ID.','VIEW');
			$me->set_cmessage($me->fmt($i18n_std['error']['invalid_sort_field'],array('sort_field'=>$sort_field)));
			$sort_field=$me->get_table_name().'.'.$me->id;
		}else{
			#nunca se cumple?
			if($me->get_cmessage()==$i18n_std['error']['invalid_sort_field']){
				$me->set_cmessage($i18n_std['error']['invalid_sort_field']);
			}
			//$me->log('SORT USED FIELD VALID='.$sort_field.'. ','VIEW');
		}


		$order_by_clause="\n ORDER BY ".$sort_field .' '.$sort_direction;
	}else{
		$order_by_clause='';
	}
	if($group_statement==1){
		$sql_group="\n GROUP BY ".$group_field;
	}else{
		$sql_group='';
	}
	return(array(
		#'table_aliases'=>$table_aliases,
		'sql_fields'=>$sql_fields,
		'qfields'=>$qfields,
		'words'=>$dl['words'],
		'sfields'=>$sfields,
		'sql'=>"SELECT \n\t".implode(",\n\t",$fields).$fw.$sql_group.$order_by_clause,
		'csql'=>"SELECT \n\tcount(".$me->get_table_name().".".$me->id.") as c ".$fw.$sql_group,
		//error fixef fix fix fix this.name
	));
}
?>
