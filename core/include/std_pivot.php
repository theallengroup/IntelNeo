<?php
include(dirname(__FILE__).'/datatree.php');
class std_pivot{
	var $show_totals=1;
	var $restrictions=array();//SQL WHERE STATEMENTS
	var $parameters=array();
	var $data_source='';
	var $row_fields=array();
	var $column_fields=array();
	var $data_fields=array();
	var $only_columns_greater_than_zero=1;

	function std_pivot(){
	}
	function set_data_source($name){
		$this->data_source=$name;
	}
	function add_row_field($name,$label=''){
		$this->add_field('row_fields',$name,$label);
	}
	function add_field($field_type,$name,$label=''){
		if($label==''){$label=$name;}
		$c = new common();

		//PHP BUG WORK-ARROUND
		//SHOULD BE THIS:
		//$this->$field_type=$c->a2o(array('name'=>$name,'label'=>$label));;

		$d = $this->$field_type;
		$d[] = $c->a2o(array('name'=>$name,'label'=>$label));
		$this->$field_type=$d;
	}
	function add_column_field($name,$label=''){
		$this->add_field('column_fields',$name,$label);
	}
	function add_data_field($name,$label='@none'){
		if($label=='@none'){
			$label = $name;
		}
		//COUNT
		if($label=='1'){
			//SORRY i18n
			$label='Cantidad';
		}
		$this->add_field('data_fields',$name,$label);
	}
	function add_restriction($restriction){
		$this->restrictions[]=$restriction;
	}
	function r2sql(){
		$d=new db();
		if(count($this->parameters)>0){
			$f = new form();
			$f->set_title("Mostrar Registros Coincidentes.");
			//preprocess params
			$params_where_clause=array();
			$old_params=$this->parameters;
			$new_params=array();

			foreach($this->parameters as $param){
				$ll =explode("/",$param);
				if(count($ll)>=2){
					$parent = $ll[0];
					$child  = $ll[1];
					$new_params[]=$parent;
					//only display parent once set.
					if(isset($_GET["param"][$parent])){
						$params_where_clause[$child] = "and  $parent IN ('".implode("','",$_GET['param'][$parent])."') ";
						$new_params[]=$child;
					}else{
						$params_where_clause[$child]='and 7 = 7 ';
					}
				}else{
					$params_where_clause[$param]='and 8 = 8 ';
					$new_params[]=$param;
				}
			}
			$this->parameters = $new_params;
			#p2($new_params);
			#p2($params_where_clause,'red');
			foreach($this->parameters as $param){

				$sql3 = '0=0';
				if(count($this->restrictions)>0){
					$sql3 = " ( ".implode(" and ",$this->restrictions )." ) ";
				}
				
				$p = $d->q2op("SELECT distinct $param FROM ".$this->data_source." WHERE $sql3  {$params_where_clause[$param]} ORDER BY $param LIMIT 500 ",$param,$param);
				//If nothing is given assume all is given
				if(!isset($_GET['param'][$param])){
					$_GET['param'][$param]=$p;
				}
				$f->add_field(array(
					'name'=>'param['.$param.']',
					'type'=>'checklist',
					'options'=>$p,
					'i18n_text'=>$this->humanize($param),
					'i18n_help'=>'',
					'check_all'=>1,
					'values'=>$_GET["param"][$param],
				));
			}
			$f->add_hidden_field('mod',$_GET["mod"]);
			$f->add_hidden_field('id',$_GET["id"]);
			$f->add_submit_button(array('action'=>$_GET["ac"],'label'=>'OK'));
			ob_start();
			echo("<div style='width:500px'>");
			$f->shtml();
			echo("</div>");
			$param_html = ob_get_contents();
			ob_end_clean();
			$c = new common();
			echo($c->toggle_block("Par&aacute;metros", $param_html));
		}
		$sql2=array("1=1");
		if(is_array($_GET['param'])){
			foreach($_GET['param'] as $pname=>$p){			
				$v1 =array();
				foreach($p as $v){
					$v1[]="'$v'";
				}
				$sql2[] = $pname." IN (".implode(",",$v1).")";
			}
		}

		if(count($this->restrictions)>0){
			$sqlstr = " ( ".implode(" and ",$this->restrictions )." ) ";
			return($sqlstr." and (".implode(" AND ",$sql2).")");
		}else{
			return("(".implode(" AND ",$sql2).")");
		}
	}
	function get_or_expr(){
		if($this->only_columns_greater_than_zero==1){
			$or_expr=array();
			foreach($this->data_fields as $dfield){
				$or_expr[]=$dfield->name." > 0";	//@TODO FIXME FIXME FIXME DANGEROUS, SHOULD BE PARAMETRIZABLE!
			}
			$or_expr = "(".implode(" OR ",$or_expr).")";
			return($or_expr);
		}else{
			return("1=1");
		}
	}
	function get_columns_tree($a=NULL,$parent_element=NULL,$value='@NOTHING',$r="1=1",$prefix='',$main_restrict=NULL){
		$d = new db();
		if($main_restrict==NULL){
			$main_restrict =$this->r2sql();
			$this->main_restrict = $main_restrict;
		}
		if($a===NULL){
			$a = $this->column_fields;
			$parent_element='ROOT';
		}
		if(count($a)>0){
			$current = array_shift($a);
			$csql='SELECT DISTINCT '.$current->name.' FROM '.$this->data_source.' WHERE '.$this->get_or_expr().' AND ('. $r .') and ' . $main_restrict.' ORDER BY '.$current->name;
			$cols = $d->q2op($csql ,$current->name,$current->name);
			
			#if(count($cols)==0){
			#	echo("NO SE ENCONTRARON REGISTROS PARA: $value! ");
			#	die();
			#	$out["Error"]="'No se encontraron registros'";
			#}
			#echo("\n".$csql);
			#p2($cols);
			flush();
			ob_flush();
			$out=array();
			foreach($cols as $col){
				$pr=$prefix.$col."///";
				$out[$col] = $this->get_columns_tree($a,$current,$col,$r.'  and  '. $current->name ."='$col' ",$pr,$main_restrict);
			}
		}else{
			//INCLUDE ALL DATA FIELDS AS COLUMNS AT THE END OF THE TREE
			//DAtA COLUMNS HERE ARE OBJECTS
			$out = array();
			foreach($this->data_fields as $dfield){
				$addr = $prefix.$dfield->label;	//$value.
				$out[$dfield->name]=$d->a2o(array(
					"sql"=>"\nsum(case when ".$r." then {$dfield->name} else 0 end)  as ".$d->sql_quote($addr),
					'name'=>$dfield->name,
					'label'=>$dfield->label,	//to replace 1 with " "
					'addr'=>$addr,
					#'restrict'=>$r,
					'value'=>$value,
				));
			}
			#p2($ret);die();

		}
		if(count($out)==0){
			$s=new std();
			$s->msg("No se encontraron Registros.");
			$out["Mensaje"]=$d->a2o(array(
				"sql"=>"'0' as Mensaje",
				'name'=>'Mensaje',
				'addr'=>'Mensaje',
				'value'=>'No se encontraron registros'
			));

		}
		return($out);		
	}
	function leafs($node){
		$l=array();
		if(is_array($node)){
			foreach($node as $name=>$a){
				if(is_array($a)){
					$l = array_merge($l,$this->leafs($a));
				}elseif(is_object($a)){
					//wtf.
					$l[$a->addr]=$a;
				}else{
					echo("WTF1:");
				}
			}
		}elseif(is_object($node)){
			$l=array($prefix.$node->value=>$node);
		}else{
			echo("WTF2:");
		}
		return($l);
	}

	#function get_hierarchical_column_list
	function get_pivot_sql(){
		$d = new db();
		//get COLS
		//FIXME only one col for now

		$cols_sql=array();
		$data_sql=array();
		foreach($this->data_fields as $data_field){
			$data_sql[$data_field->label]='sum('. $data_field->name .')';	
		}

		//WHEN NO COLS ARE GIVEN; USE DATAFIELDS
		if(count($this->column_fields) == 0){
			foreach($this->data_fields as $data_field){
				$cols_sql[$data_field->label]='sum('. $data_field->name .')';	
			}
		}
		$ct = $this->get_columns_tree();
		#p2($ct,'blue');
		$this->ct=$ct;

		$leafs = $this->leafs($ct);
		#p2($leafs);
		foreach($leafs as $name=>$obj){
			#foreach($data_sql as $slabel=>$sfield){
				$cols_sql[]=$obj->sql;
				//"\ncase when ".$obj->restrict." then $sfield else 0 end  as `".$name."///$slabel`";
			#}
		}

		/*
		foreach($this->column_fields as $column_field){
			$cols = $d->q2op('SELECT DISTINCT '.$column_field->name.' FROM '.$this->data_source.' WHERE '. implode(' and ',$this->restrictions).' ORDER BY '.$column_field->name ,$column_field->name,$column_field->name);
			foreach($cols as $col){
				foreach($data_sql as $slabel=>$sfield){
					$cols_sql[]="\ncase when ".$column_field->name." ='".$col."' then $sfield else 0 end  as `$col//$slabel`";
				}
			}
		}
		*/
		$cols_select_sql = implode(',',$cols_sql);

		//get ROWS
		$row_names=array();
		foreach($this->row_fields as $row_field){
			$row_names[]=$row_field->name;

			//THIS IS USEFUL??
			//$rows = $this->q2op('SELECT DISTINCT '.$row_field->name.' FROM '.$this->data_source,$row_field->name,$row_field->name);

		}
		$row_names_sql=implode(',',$row_names);
		$group_fields= $row_names;

		foreach($this->column_fields as $column_field){
			$group_fields[]=$column_field->name;
		}
		$group_sql=implode(',',$group_fields);


		/*
		 * should generate something like:
		 *
		select familia,sum(valor) from aa_olap3 group by familia;
		select familia,
		case when nombre ='Mantesa Sur' then sum(valor) else 0 end  as   `Mantesa Sur`,
		case when nombre ='Planta Tocancipa' then sum(valor) else 0 end  as   `Planta Tocancipa`
		from aa_olap3 group by familia;
		*/
		$sql="SELECT \n".$row_names_sql.','.$cols_select_sql . "\n FROM " . $this->data_source." \nWHERE ".$this->main_restrict."\nGROUP BY $row_names_sql ORDER BY $group_sql";
		#$sql="\n UNION \n ";
		//DBG 		#p2($sql);	
		return($sql);	
	}
	function get_pivot_data(){
		$d=new db();
		return($d->q2obj($this->get_pivot_sql()));
	}
	function treecount($a=NULL){
		if($a==NULL){
			$a = $this->ct;
		}
		$c = 0;
		if(is_array($a)){
			foreach($a as $kid){
				$c+=$this->treecount($kid);
			}
		}elseif(is_object($a)){
			$c=1;
		}else{
			$c=999;
		}
		return($c);
	}
	function headers2html($a=NULL,$depth=0){
		if($a==NULL){
			$root=1;
			$a = $this->ct;
		}else{
			$root=0;
		}
		$dx=array();
		if(is_array($a)){
			foreach($a as $kname=>$kid){
				if(is_array($kid)){
					$dx[$depth].='<td class="std_pivot_cell std_pivot_header" colspan='.($this->treecount($kid)).">".$this->humanize($kname)."</td>";	//"(".$this->treecount($kid).")".
					$in = $this->headers2html($kid,$depth+1);
					foreach($in as $level=>$contents){
						$dx[$level].=$contents;
					}
				}elseif(is_object($kid)){
					//This is the last leaf, of the tree, therefore, data_field, 
					//therefore, we are allowed and encouraged to use label names
					$dx[$depth+1].="<td class='std_pivot_cell std_pivot_header'>".$this->humanize($kid->label)."</td>";
				}else{
					echo("WTF");
				}
			}
		}elseif(is_object($a)){
			$dx[$depth].="<td>".$a->value."</td>";
		}else{
			echo("WTF4");
		}
		if($root==1){
			$dx1="<tr>";
			foreach($this->row_fields as $f){
				$dx1.="<td  class='std_pivot_cell std_pivot_header'  rowspan=".count($dx).">".$this->humanize($f->label)."</td>";
			}
			return($dx1.implode("</tr>\n<tr>",$dx)."</tr>");
		}
		return($dx);
	}
	function array_subtotal($data){
		#require_once(STD_LOCATION.'include/tree.ui.php');
		$l=array();
		foreach($this->row_fields as $rf){
			$l[]=$rf->name;
		}
		#$t = tree_ui($data,implode("/",$l),'@auto',1);

		$dt = new datatree();
		$dt->show_totals=$this->show_totals;

		if(count($l)!=1){
			array_pop($l);//REMOVE LAST ELEMENT AT THE END, SO DETAIL IS AND NOT ALWAYS SUMMING ONE ROW.
		}
		$dt->load_from_table($data,$l);
		#p2($dt);
		return($dt->tohtml());
	}
	function humanize($str){
		return(str_replace("_"," ",ucwords($str)));
	}
	function out(){
		$dx='';
		$data = $this->get_pivot_data();
		$this->data_cache = $data;
		$h =  $this->headers2html();
	#	echo("TREE COUNT IS:".$this->treecount());
		$dx.=("<style> 
			.std_pivot_header {
				font-weight:bold;
				background-color:rgb(250,250,250);
				}
			.std_pivot_group {
				font-weight:bold;
				border:1px solid black;
			}
			.std_pivot_cell {
				vertical-align:top;
				border:1px solid rgb(200,200,200);
				border-top:0px;
				border-left:0px;
				padding:4px;

				white-space: nowrap;
				font-family:verdana;
				font-size:10pt;
			}
			.std_pivot_total {
				background-color:rgb(240,240,240);
				font-weight:bold;
				/*text-align:right;*/
			}
			.std_pivot_table {
				border:1px solid black;
				width:100%;
			}
			
			.std_pivot_cell_left {text-align:left;} 
				.std_pivot_cell_right {
					text-align:right;
			}
			</style>");
		$dx.=("<h1 class='stardand_text standard_title form_title'>".$this->title."</h1><table class=std_pivot_table cellpadding=2 cellspacing=0>");
		$dx.=($h);

		$dx.=$this->array_subtotal($data);
		$dx.=("</table>");
		#common::e_table($data);
		return($dx);
	}
	function shtml(){
		$c = new common();
		return($c->shadow($this->out()));
	}
}
?>
