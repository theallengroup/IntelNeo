<?php	
/**
 * \todo 268 excel like partitioning:
 * take a group of data and part it equally in X parts, display the results in different colors.
 * this is an excelent module.
 * \todo 270 calculated fields?
 * \todo 275 calculated fields in VIEWS in general?
 * */
class report extends db{
	var $options;
	var $caller;
	function display_report(){
		$me=&$this->caller;	//ok
		///p2($this->options);

		$resume_fields=array();
		$resume_tables=array($me->get_table_name());
		$resume_joins=array('1=1');//\todo restrict
		$group_col='@none';//for now, just one level, FUTURE: multiple levels
		$headers=array();
		if($this->options['report_level']=='resume'){
			//find resume fields
			foreach($this->fields as $k=>$field){
				$headers[$k]=$me->fi($k);
				if(array_key_exists('group_level',$field)){
					$group_col=$field['name'];
					//what if the field is foreign?
					//a list?
					if($me->field_is_deportable($field)){
						$field=$me->field_deport($field);
						///\todo 269 if exists: alias, etc

						//p2($field,'red');
						$f3=$me->explain_foreign_field($field['foreign']);
						$resume_tables[]=$f3['table'];
						$resume_fields[]=$f3['table'].'.'.$f3['field'];

						$foreign_id='id';//FIX
						$resume_joins[]=$f3['table'].'.'.$foreign_id.' = '. $me->table.'.'.$field['name'] ;
					}
					#$resume_fields[]=$field['name'];
				}
				if(array_key_exists('resume',$field)){
					//resume options: are SQL functions, like sum, avg, etc
					//\todo 273 use just one $resume_option, make multiple
					foreach($field['resume'] as $k1=>$resume_option){
						$sql_expression=$resume_option.'('.$me->get_table_name().'.'.$field['name'].')';
						$resume_fields[]=$sql_expression;
						$this->fields[$k]['sql_expression']=$sql_expression;
						//count(id) becomes table name
						///\todo 271 prepend total, sum count, etc
						if($field['name']==$me->id){	
							$headers[$k]=$me->i18n('table_plural');
						}
					}
				}
			}
		}
		$fs=$me->foreign_select(array(
			'fields'=>$this->fields,
			'get_fid'=>0,
			'group_field'=>$group_col,
		));
	
		///\todo 274 if actions  are available, then get fid=1, and make links!
		///\todo

		///p2($fs);
		//all the foreign_select stuff goes here, i guess.

		if($group_col=='@none' && $this->options['report_level']=='resume'){
			echo('ERROR: no group field selected.');
			return(0);
		}

		#deprecated:
		#$gsql='SELECT '.implode(',',$resume_fields).' FROM '.implode(',',$resume_tables).' WHERE '.implode(' AND ',$resume_joins).' GROUP BY '.$group_col ;

		$gsql=$fs['sql'];
		$data=$this->q2obj($gsql);
		//$data=$me->find_all();
			
		///p2($headers);
		$this->shadow(
		$this->table($data,$headers,array(
			'title'=>$me->i18n($this->options['title']),
			'style'=>'list'
		)));
		//,'negative'

	}
	function report(&$me,$options){
		$this->caller=&$me;//NOT THIS ONE
		$this->options=$options;
		$this->fields=$this->options['fields'];//shorthand
		$this->db();
	}
}
