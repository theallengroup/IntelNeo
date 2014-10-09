<?php
/**

Usage:
@code
require_once(INCLUDE_DIR."table_editor.php");
            $t=new table_editor();
            //$data=$this->q2obj('SELECT * from usr');
            $data=array();
            $data[0]=array(1,2,3); 
            $data[1]=array(1,2,3);
            $data[2]=array('<input>',2,3);
            $t->set_data($data);
            $t->set_headers(array('Campo 1','Campo 2','Campo 3')); 
            $t->min_rows=2;
            $t->max_rows=10;
            $t->set_data($data);
            $this->jsc('table_editor');
            $t->shtml();
@endcode
 * a table simple editing system
 * depends on: common
 * */
class table_editor{
	var $data;
	var $style='list';//same as common::table
	var $title;
	var $name;
	var $has_add=1;
	var $has_delete=1;
	var $min_rows=1;
	var $max_rows=-1;///< -1 means no max
	var $add_link='';///< Add link text
	var $headers=array();
	var $on_add_row='void(null)';
	function table_editor($name='default'){
		$this->name=$name;
	}
	function add_row($row){
		$this->data[]=$row;
	}
	function set_headers($headers){
		$this->headers=$headers;
	}
	/** @param $data is a bidimensional array */
	function set_data($data){
		$this->data=$data;
	}
	/**
	 * returns the table
	 * creates: [name]_table and [name]_tbody  [name]_row0 [name]_row1 ... 
	 * */
	function editor(){
		$txt='<table class="'.$this->style.'_table" border=1 cellspacing=0 cellpadding=2 id="'.$this->name.'_table"><tbody><tr class="'.$this->style.'_row">';
		foreach($this->headers as $k=>$header){
			$txt.='<th class="'.$this->style.'_head">'.$header.'</th>';
		}
		if($this->has_delete==1){
			$txt.='<th class="'.$this->style.'_head">&nbsp;</th>';
		}
		$txt.='</tr></tbody><tbody id="'.$this->name.'_tbody" class="'.$this->style.'_tbody">';
		$c=0;
		foreach($this->data as $k=>$row){
			$txt.='<tr id="'.$this->name.'_row'.$c.'" class="'.$this->style.'_eow">';
			$c=0;
			foreach($row as $k1=>$cell){
				$txt.='<td class="'.$this->style.'_cell">'.$cell.'</td>';
				$c++;
			}
			if($this->has_delete==1){
				$txt.='<td class="'.$this->style.'_cell">';
				$txt.='<a class=\'standard_link\' href="#" onclick="std_table_editor_delete_row(this,\''.$this->min_rows.'\');return false;">';
				$txt.=std::get_i18n_text('delete_row')."</a>";
				$txt.='</td>';
			}
				
			$txt.='</tr>';
		}
		$txt.='</tbody><tbody class="'.$this->style.'_foot">
			<tr class="'.$this->style.'_row"><td class="'.$this->style.'_cell" colspan="'.($c+1).'" align=right>';
		if($this->has_add){
			$txt.='<a href="#" onclick="std_table_editor_add_row(\''.$this->name.'_tbody\',\''.$this->max_rows.'\');'.$this->on_add_row.';return false;">'.
				$this->get_add_link().'</a>';
		}
		$txt.='</td></tr></table>';

		return($txt);
	}
	function get_add_link(){
		if($this->add_link==''){
			return(std::get_i18n_text('add_row'));
		}else{
			return($this->add_link);
		}
	}
	function shtml(){
		$c=new common();
		$c->shadow($this->editor());
	}
	function out(){
		return($this->editor());
	}
}

?>
