<?php
function tree_ui($ui,$hierarchy='@none',$title='@auto',$is_kid=0){
	$c = new common();
	if($hierarchy!='@none'){
		$all = explode("/",$hierarchy);
	$hierarchy_depth = count($all);
	$level = array_shift($all);
	$all_ok=implode("/",$all);
	if(count($all) == 0){//last level reached.
		$all_ok='@none';
	}
	$new_data = array();
	//group data
	foreach($ui as $row=>$data){
		$dl = $data[$level];
		if(!isset($data[$level])){
			return("<font color=red>WARNING: NO SUCH COLUMN:$level IN DATASET:".implode(",",array_keys($data))."</font>");
		}
		unset($data[$level]);
		$new_data[$dl][] = $data;
	}
	$initial_length=0;
	//recurse
	foreach($new_data as $row=>$line_group){
		$d = tree_ui($line_group,$all_ok,$title,1);
		$initial_length = $d["length"]+1;	//get kid length, sum one to it.
		$new_data[$row]=$d['data'];
	}
	if($is_kid==0){
		#dbg echo("depth:".$hierarchy_depth);
		if($title=='@auto'){
			$title='Reporte por '.$level.','.(implode(",",array_map('ucwords',$all)));
		}
		return("<h1>$title</h1>".tree2html($new_data,$hierarchy_depth,$initial_length));
	}

	return(array(
		'length'=>$initial_length,
		'data'=>$new_data
	));
	
}else{
	//at final point
	foreach($ui as $c){
		$length = count($c);
		break;
	}
}

return(array(
	'length'=>$length,
	'data'=>$ui,
));

}
/**
* @param $initial_length the size of the colspan to be used (decreases with recursion)
* @param $hierarchy_depth the recursions that must be made left (decreases un til zero: detail level is reached)
* @param $original_length -1 means use initial length, does not change trough recursion, 
* @param $original_depth -1 means use hierarchy_depth, does not change trough recursion, 
*
* $depth: original - initial
* is used for spacing purposes.
* 
* */
function tree2html($data,$hierarchy_depth,$initial_length,$original_length=-1,$original_depth=-1,$is_kid=0){
$dx='';

//KEY IS DEPTH, VALUE IS pt
$font_sizes=array(

/*		0=>36,
	1=>32,
	2=>30,
	3=>24,
	4=>22,
	5=>20,
	5=>18,
	6=>14,
*/
	0=>22,
	1=>20,
	2=>18,
	3=>14,

);
if($is_kid==0){
	//we are at the MAIN level
	$original_length = $initial_length;
	$original_depth = $hierarchy_depth;
	$dx.="<table border=0>";
}
/*
	echo("DATA:<br/>
		is_kid:$is_kid<br/>
		hierarchy_depth =$hierarchy_depth<br/>
		initial_length = $initial_length<br/>
		original_length = $original_length <br/>
		original_depth = $original_depth<br/>
		");
 */
$depth = $original_length - $initial_length ;
 //DEBUG
 

$new_font_sizes=array_slice($font_sizes , -$original_length ,$original_length);

if($hierarchy_depth == 0){
	//get headers
	foreach($data as $row_title=>$row_data){
		$headers = array_map('ucwords',array_keys($row_data));
		break;
	}
	//change _ by " " so its cleaner, less work for me.
	foreach($headers as $hid=>$header){
		$headers[$hid] = str_replace('_',' ',$header);
	}
	
	//Print HEADER Spacer.
	$dx.="\n<!--HEADER-->\n<tr>";
	foreach(range(0,$depth-1) as $c){
		$dx.="<th  >&nbsp;&nbsp;</th>";
			//0&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			///0&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		}
		//headers AT DETAIL
		$dx.="\n\t<th style='border:1px solid black'>".  implode("</th>\n\t<th style='border:1px solid black'>",$headers)."</th>";
		$dx.="</tr>\n<!--END-OF-HEADER-->\n";
	}	
	foreach($data as $row_title=>$row_data){
		$dx.=("\n\n<tr>");

		//TITLE LEVEL Spacer.
		//ONLY PRINT WHEN NOT IN TOP LEVEL
		if(($depth)>0  && $hierarchy_depth != 0){
			$dx.="<th  colspan='".($depth)."'>&nbsp;</th>";
		}


		if($hierarchy_depth == 0){
			//we are at the lowest level DETAIL LEVEL

			
			$dx.=("\n<!--DETAIL-->\n<tr>");//NEW LINE DETAIL BREAK


			//Print Spacer.
			foreach(range(0,$depth-1) as $c){
				$dx.="<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>";
				//&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			}
			
			//actual cell content
			foreach($row_data as $cell=>$value){
				if(is_numeric($value)){
					$align = 'right';
				}else{
					$align = 'left';
				}
				$dx.="\n\t<td style='text-align:$align;border:1px solid rgb(220,220,220)'>".$value."</td>";
			}
			$dx.="</tr>\n<!--END-OF-DETAIL-->\n";
		}else{
			//we are at the title level
			//cell title

			$dx.="<th style='border-bottom: 1px solid black;font-size:". $new_font_sizes[$depth] .";text-align:left' colspan='".$initial_length."'>".ucwords($row_title)."</th></tr>";
			//$dx.='<tbody style="display:none">';
			$dx.=tree2html($row_data,$hierarchy_depth-1,$initial_length-1,$original_length,$original_depth,1);
			//$dx.='</tbody>';

		}
		
	}
	if($is_kid==0){
		$dx.="\n</table>";
	}	
	return($dx);
}

?>
