<?php
class grid_layout {
	var $items = array();
	var $columns=1;
	function grid_layout($columns  = 1){
		$this->set_columns($columns);
	}
	function set_columns($columns){
		$this->columns = $columns;
	}
	/** 
	 * colspan is 0 for normal and 1 for everything.
	 * you can toss an array to item, but colspan is used for everyone as the same.
	 * */
	function add($item,$colspan=1){
		if(!is_array($item)){
			$item=array($item);
		}
		foreach($item as $one){
			$this->items[] = array('contents'=>$one,'colspan'=>$colspan);
		}
	}
	/** 
	 * outputs an array, buddy.
	 * */
	function out(){
		$border=0;
		$style = $this->style;
		$cellspacing = 0;
		$cellpadding=0;
		$out_array=array();
		$c = 0;
		$current_row = 0;
		$current_col = 0;
		$this->expand = array();//TODO
		$just_broke=0;
		$dz=0;//la ultima ves fue BREAK?
		foreach($this->items as $item){
			if($item["contents"]=='[BREAK]'){
				//ADVANCE TO THE NEXT ONE
				$current_col=0;	
				$just_broke=1;
				$current_row++;
				$this->expand[$current_row]=1;
				$dz=1;//Sí, el ultimo campo es de Break
			}else{
				//solo en caso de que el ultimo campo no haya sido de tipo BREAK.
				if(!($dz) && ((($c ) % $this->columns ==0)&& $c !=0)){//I'm done here, next row please
					$current_row++;
					$current_col=0;
				}
				$dz=0;
				$out_array[$current_row][$current_col] = $item['contents'];
				$current_col++;
				//ADVANCE TO THE NEXT ONE
				if($just_broke == 1){
					//break again.
					$current_row++;
					$current_col=0;	
				}else{
					$c++;
				}
			}
		}
		return($out_array);
	}
	function get_expand(){
		return($this->expand);
	}
	function set_expand($x){
		$this->expand=$x;
	}	
}
?>
