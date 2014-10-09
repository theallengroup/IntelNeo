<?php
	/** \defgroup search Search
	 * \todo find a way to load this OPTIONALLY
	 * */
	//@{

	/**
	 * also to ml()
	 * simple search function aid.
	 * */
function std_parse_field(&$me,$f,$qfields){

	//what you are looking for
	$lookup='';
	
	$s=array();
	$found=0;

	$search_field='*';// * means all

	if($c=strpos($f,':')!==FALSE){
		$r=explode(':',$f);
		if(count($r)==2){
			$field_name=strtolower($r[0]);
			/** @see doc/struct.txt
			 * */
			foreach($qfields as $k=>$vfield){
				if($field_name==strtolower($k)){
					$s[]=$vfield['d']." LIKE '%".$r[1]."%'";
					$search_field=$vfield['q'];//table_fieldname
					$lookup=$r[1];
					$found=1;
					break;
				}
			}
		}else{
			// looking for a:b:c
		}	
	}
	//if the field is not found tin the previous try, ill try to search for the whole thing.
	//which allows me to search stuff like a:b
	//when a is not a field, and I want literally a:b.

	if($found==0 || $c=strpos($f,':')===FALSE){
		//no colon, search ni all fields.
		foreach($qfields as $k=>$v){
			$s[]=$v['d']." LIKE '%".$f."%'";
		}
		$lookup=$f;
	}
	if(count($s)>0){
		return(array(
			'sql'=>"\n\t (\n\t".implode(" \n\t\tOR\n\t\t ",$s)."\n\t)\n\t ",
			'lookup'=>$lookup,
			'field'=>$search_field,
		));
	}
	$me->log('SEVERE@std_parse_field()','ERROR');
	return(array('sql'=>'','lookup'=>$lookup,'field'=>$search_field));
}
	/**
	 * \todo 258 this goes in ml()
	 * 
	 * recieves a string like ' a "b" c' and returns an array: 0: format string 'a [#f0] c' and 1: an 
	 * array: array(0=>'b')
	 * 
	 * this is useful in searching, since we might ignore spaces, but no spaces that are inside quotes.
	 * test with:
	 \code
	 p2($this->get_quoted_string_struct('! this is a "simple" test of string parsing et al "nah""nah" "nah"'));
	 \endcode
	 * 
	 * */


function std_get_quoted_string_struct(&$me,$a,$c='f'){
	$str=array();
	$QUOTE_CHARACTER='"';
	$format='';
	$matches=0;
	if(strpos($a,$QUOTE_CHARACTER)===FALSE){
		return(array('format'=>$a,'strings'=>array(),'reformat'=>0));
	}
	//remove slashes (dangerous)
	
	
	$l=strlen($a);
	for($i=0;$i<$l;$i++){
		if($a[$i]==$QUOTE_CHARACTER){
			if($in_str==0){
				$in_str=1;
				$start=$i;
			}elseif($in_str==1){
				$in_str=0;
				$end=$i;
				$str[]=substr($a,$start+1,$end-$start-1);
				$format.="[#".$c.$matches."]";

				$matches++;
			}
		}else{
			if($in_str==0){
				$format.=$a[$i];
			}
		}
	}
	if($in_str==1){
		$me->log('format error, missing "','PARSER');
	}
	return(array('format'=>$format,'strings'=>$str,'reformat'=>1));
}
	/**
	 * returns SQL
	 * so s/you/I/ can highlight them
	 *
	 * */

function std_parse_search_string(&$me,$argv){
	$st=$argv[0];
	$qfields=$argv[1];
	//check for ',', and ""
	//tokens: text, field, comparision operator, boolean operator.
	$st=trim($st);
	if($st==''){
		return(array('sql'=>'','words'=>array()));
	}
	$txt=array();
	foreach($qfields as $k=>$v){
		$txt[strtolower($me->fi($k))]=$k;
	}
	
	$st=str_replace(array_keys($txt),array_values($txt),$st);


	$ts=std_get_quoted_string_struct($me,$st,'f');
	$search_string=$ts['format'];
	$quoted_strings=$ts['strings'];
	$l=strlen($search_string);
	$l2=preg_split("/\s+/",$search_string);

	/*remove duplicates*/
	$l21=array();
	foreach($l2 as $k=>$v){
		if(in_array(strtoupper($v),array('OR','||',"O"))){
			$l21[]='OR';
		}else{
			$l21[$v]=$v;
		}
	}
	$words=array();
	$op_ok=0;
	$tokens='';

	foreach($l21 as $k=>$v){
		/*is operator*/	
		if(in_array(strtoupper($v),array('OR','||',"O"))){
			if($op_ok==0){
				$me->log('syntax error: token spected:','PARSER');
			}else{	
				//op ok==1
				
				$op_ok=0;
				$tokens[]='OR';
			}
		}else{
			if($op_ok==1){
				$tokens[]='AND';
			}
			$op_ok=1;
				
			$p=std_parse_field($me,$v,$qfields);
			$tokens[]=$p['sql'];
			$words[$p['lookup']]=array(
				'word'=>$p['lookup'],
				'field'=>$p['field'],
			);
		}
	}

	if(in_array($tokens[count($tokens)-1],array('OR','||',"O"))){
		//last is OP, remove it
		unset($tokens[count($tokens)-1]);
	}

	//\todo
	//THIS 	MUST BE MADE SOMEHOW NOT TO ITERATE HERE< BUT SOMEWHERE ELSE< SO FMT IS NOT CALLED 20 TIMES.
	
	foreach($words as $k=>$v){
		$words[$k]['word']=$me->fmt($v['word'],$quoted_strings,'f');
	}
	//end

	$end=$me->fmt(implode("\n\t",$tokens),$quoted_strings,'f');

	return(array('sql'=>' AND '.$end,'words'=>$words));
}
	///@}

?>
