<?php

class datatree {
	var $show_totals = 1;			//wheter or not to shoe totals.
	var $max_depth=0;			//	
	var $kids=array();			//	list of datatrees
	var $rows=array();			//	list of rows
	var $has_kids=0;			//	wether or not
	var $kid_count=0;			//	a(b(1,2,3),4,c(5,6)) = 3

	var $kid_totals_record=array();		//	(t1=100,t2=100)
	var $depth = 0;				//	"level" within the structure
	var $grouping_col='General';		//-	debugging info
	var $grouping_value='General';		//-	dispalying info
	var $first_kid_id=0;			//
	var $last_kid_id=0;			//
	var $style='';				//	no style: css stuff.

	function calculate_recursive_kid_count(){
	
	}
	function calculate_recursive_first_and_last(){
	
	}
	function calculate_recursive_depth(){
	
	}
	function calculate_recursive_totals(){
	
	}
	function apply_recursive_style(){
	
	}
	function add_kid($kid){
		$this->kids[]=$kid;
		$this->has_kids=1;
		$this->kid_count++;
	}
	function add_records(){
	
	}
	function calculate_total(){
	
	}
	function remove_zeros($r){
		foreach($r as $k=>$v){
			if($v=='0.00'||$v=='0'){
				$r[$k]='&nbsp;';
			}
		}
		return($r);
	}
	function row2html($row,$class='std_cell'){
		$dx='';
		foreach($row as $k=>$cell){
			$dx.=("\n\t\t<td title='$k' class='".$class."'>$cell</td>");
		}
		return($dx);
	}
	/** 
		var $recursive_kid_count = array();	//	a(b(1,2,3),4,c(5,6)) = 6 NOT USED
       		*
	 * */
	function rowspan(){
		$x=0;
		if($this->has_kids){
			foreach($this->kids as $kid){
				$x+=$kid->rowspan();
			}
			$x+=1;//para el total de los hijitos
		}else{
			$x+=count($this->rows)+1; //ME FALTA EL MAS1, si HAY KIDS.  +1 means +1 at the end + total row
		}
		return($x);
	}
	/** 
	 * array 2 currency 
	 * $t an array
	 * */
	function array2currency($t){
		foreach($t as $k=>$value){
			if(is_numeric($value)){
				if($value<>round($value)){
					$d=2;
				}else{
					$d=0;
				}
				$t[$k]=number_format($value, $d, '.', ',');
			}else{
			
			}
		}
		return($t);
	}
	function get_totals(){
		$t=array();
		if(count($this->rows)>0){
			foreach($this->rows as $row){
				foreach($row as $cellname=>$cellvalue){
					if(is_numeric($cellvalue)){
						$t[$cellname]+= $cellvalue;
					}else{
						#don't even bother.
						$t[$cellname]='Total '.$this->grouping_value;//Total
					}
				}
			}
		}else{
			
			foreach($this->kids as $k){
				$kid_totals = $k->get_totals();
				foreach($kid_totals as $kcell=>$kvalue){
					if(is_numeric($kvalue)){
						$t[$kcell]+=$kvalue;
					}else{
						$t[$kcell]=" - ";
					}
				}
			}
		}
		return($t);
	}
	
	function get_grouping_cell_html(){
		return('<td class="std_pivot_cell std_pivot_cell_left std_pivot_group" valign=top rowspan="'.$this->rowspan().'" title="'.$this->grouping_col.'.'.$this->rowspan().'">'.$this->grouping_value."</td>");
	}
	/** your usual e_table implementation goes here */
	function tohtml(){
		$dx='';
		if($this->has_kids){
			if($this->depth>0){//Depth=0 is removed so I don't get a NONE col that groups everything and moves everything to the right a litle bit...
				$dx.="\n<!--GROUP:{$this->grouping_value}-->\n";
				#$dx.="<tr>";			
				$dx.=$this->get_grouping_cell_html();
				#$dx.="</tr>";
			}		
			$first=1;
			$group_totals = array();
			foreach($this->kids as $kid){
				if($first==0){
					$dx.=("<tr>");
				}
				$dx.="\n".$kid->tohtml();
				$kt = $kid->get_totals();
				#p2("TOTALS:");
				#p2($kt);
				foreach($kt as $kid_total_cellname=>$kid_total_cellvalue){
					if($kid_total_cellname !=  $kid->grouping_col){//FUCK
						if(is_numeric($kid_total_cellvalue)){
							$group_totals[$kid_total_cellname]+=$kid_total_cellvalue;
						}else{
							$group_totals[$kid_total_cellname]=' &nbsp; ';
						}
					}
				}
				if($first==1){
					$first=0;
				}
			}
			
			#if($this->depth>0){
				#p2($group_totals);
				#p2($this);
			#die();

			#ESTE ES UN GRAN TOTAL AGRUPADOR
			#if($this->max_depth==1){
			#	$dd=1;
			#}else{
			#}
			
			$dd = ($this->max_depth - $this->depth);
			
			$dx.="\n<!--GROUP TOTAL md = {$this->max_depth} - td = {$this->depth} = $dd-->";
			$dx.="\n<tr>";
			$dx.="\n<td  colspan='".$dd."' class = 'std_pivot_cell std_pivot_total std_pivot_group'>Total {$this->grouping_value}</td>";
			$dx.=$this->row2html($this->array2currency($this->remove_zeros($group_totals)),'std_pivot_cell std_pivot_cell_right std_pivot_total');
			$dx.="\n<!--END GROUP TOTAL-->";

			#}
		}else{
			//Total goes on TOP.

			$dx.=("\n<!--detail start-->");
			#if($first==0){//AL PRIMERO NO SE LE PONE
			

			#}
			$dx.=$this->get_grouping_cell_html();	
			#$dx.=("</tr>\n");
			$first=1;//AL PRIMERO NO SE LE PONE <tr>!  MISMO NOMBRE DE VARIABLE; DIFERENTE USO
			foreach($this->rows as $row){
				$dx.=("\n<!--DETAIL-->\n");
				if($first==0){
					$dx.="<tr>";
				}else{
					$first=0;
				}
				//Primer Dato: ex texto.
				$first_element=array_shift($row);
				$dx.=$this->row2html($this->remove_zeros(array($first_element)),'std_pivot_cell std_pivot_cell_left');
				$dx.=$this->row2html($this->remove_zeros($this->array2currency($row)),'std_pivot_cell std_pivot_cell_right');
				$dx.="</tr>";
			}
			$dx.=("\n<!--TOTAL OF:{$this->grouping_value}-->\n<tr>");
			#offset total to the right as many as max-1-cur positions.
			#$c = $this->max_depth -$this->depth+1;
			#$dx.=str_repeat("<td> <font color=red>". $c ."</font> </td>",$c);

			#ESTE ES UN PEQUEÑO TOTAL; DE UNAS FILAS
			#
			$gt  = $this->get_totals();
			$first_total_element=array_shift($gt);
			if($this->show_totals){
				$dx.=$this->row2html(array($first_total_element),'std_pivot_cell std_pivot_total std_pivot_cell_left');
				$dx.=$this->row2html($this->array2currency($this->remove_zeros($gt)),'std_pivot_cell std_pivot_cell_right std_pivot_total');
			}
			$dx.="</tr>";
			
		}
		#
		return($dx);
	}
	/** allok */
	function add_row($row){
		$this->rows[]=$row;
	}
	function tods(){
	
	}
	function datatree(){
	
	}
	/** 
	 * table is a 2d ds 
	 * */
	function load_from_table($table,$cols){
		$this->max_depth = count($cols) + $this->depth;
		if(count($cols)>0){
			$col = array_shift($cols);
			//simple grouping
			$ndta=array();
			foreach($table as $id=>$row){
				$r2 = $row;
				unset($r2[$col]);#remove col, so kids don't give a damn about it.
				$ndta[$row[$col]][]=$r2;
			}
			foreach($ndta as $gval=>$group){
				$dt = new datatree();
				$dt->show_totals = $this->show_totals;//inherit
				$dt->depth = $this->depth+1;
				$dt->grouping_col=$col;
				$dt->grouping_value=$gval;
				$dt->load_from_table($group,$cols);
				$this->add_kid($dt);
			}
		}else{
			//at the end of it
			foreach($table as $row){
				$this->add_row($row);
			}
			
		}
	}
}

?>
