<?php
/*
class abstract_node {
	var $name;
	var $kids;
	var $node_type=0; //1:node, 0=leaf
	function append($node){
		$this->kids[]=$node;
	}
}
 */

/**
 *
xor(select(basic_date),range(basic_date,basic_date))

@text.widget.php:user_interface():29->11
Array
(
    [0] => Array
        (
            [name] => xor
            [value] => Array
                (
                    [0] => Array
                        (
                            [name] => select
                            [value] => Array
                                (
                                    [0] => basic_date
                                )

                        )

                    [1] => Array
                        (
                            [name] => range
                            [value] => Array
                                (
                                    [0] => basic_date
                                    [1] => basic_date
                                )

                        )

                )

        )

)

*
 * */
class abstract_parser {
	function parse_expr($e){
		$c = 0;
		$pcount = 0;
		$result = '';
		$word = '';
		$paren_start=0;
		$paren_end=0;
		
		#echo("<br><h2>parsing:[".$e.']</h2>');
		
		$map = array();
		$map_id = 0;
		//remove useless parens
		if($e[0]=='(' && $e[strlen($e)-1]==')'){
			$e = substr($e,1,strlen($e)-2);
			#echo('<br/>e changed!:'.$e);
		}

		$str=$e;
		

		for($c=0;$c<strlen($e);$c++){//REDUCE PARENS
			$c1=$e[$c];
			if($c1 == '('){
				if($pcount == 0){
					$paren_start=$c;	
				}			
				$pcount++;

			}elseif($c1 == ')'){
				
				$pcount--;
				if($pcount == 0){
					$paren_end=$c;
					$dx = substr($e,$paren_start,($paren_end+1)-$paren_start);//no paren
					$dx2 = substr($e,$paren_start,($paren_end+1)-$paren_start);//with paren

					$oldstr = $str;
					#echo("<br>pstart= ".$paren_start.' '.$e[$paren_start]);
					#echo("<br>pend = ".$paren_end.' '.$e[$paren_end]);
					#echo("<br>Dx (saved in MAP) = ".$dx);
					#echo("<br>Dx2 (REMOVED)= ".$dx2);
					#echo("<br><b>changing STR FROM = ".$str);
	
					$str=str_replace($dx2,':[#'.$map_id.']',$str);

					#echo("<br>TO = ".$str.'</b>');

					$map[$map_id]=$dx;//saves no paren to avoid infinite recursion
					
					#echo("<br>MAP IS NOW: ".gp2($map));

					$map_id++;
				}
			}else{
				if($pcount == 0){
					$word.=$c1;
				}
			}
		}
		if($pcount!=0){
			die('<br>expr failed to parse:'.$e."pcount is $pcount");
		}
		#echo("<br>STR is now = ".$str);
		$all=explode(',',$str);
		$r=array();
		foreach($all as $token){
			list($name,$value) = explode(':',$token);
			#echo("<br/>name = ".$name);
			#echo("<br/>value = ".$value);
			foreach($map as $mid => $m){
				$value=str_replace('[#'.$mid.']',$m,$value);
			}
			#echo("<br/>value is now:".$value);
			//if(strpos($value,'(')===FALSE){
			if(strlen($value)==0){
				$r[]=$name;
			
			}else{
				$r[]=array(
					'name'=>$name,
					'value'=>$this->parse_expr($value),
				);
			}
		}
		return($r);
	}
}

?>
