<?php
global $std_chart_count;
class std_chart{
	//canvasBaseColor="F3D6D0" canvasBgColor="E8D9D3" divlinecolor="D49B8B" limitsDecimalPrecision='0' divLineDecimalPrecision='1'

	var $type='bars';//add more later
	var $data=array();
	var $title='Untitled Chart';
	var $name='untitled_chart';//UNIQUE IDENTIFIER
	var $xtitle='X';
	var $ytitle='Y';
	var $width='800';
	var $height='600';
	var $background_color='FFFFFF';// a hex without the #
	var $divlinecolor='202020';
	var $alternate_hgrid_color='F0F0F0';
	var $default_colors=array('AFD8F8','F6BD0F','8BBA00','FF8E46','008E8E','D64646','8E468E','588526','B3AA00','008ED6','9D080D','A186BE');
	var $flash_file='';
	var $headers=array();
	var $type2file=array(
		#'bars'=>'FCF_Column3D',
		'bars'=>'Column3D',
		'line'=>'Line',
		'pie'=>'Pie3D',
		'multiple.series.bars'=>'MSColumn3D',//FCF_MSColumn3D
		'multiple.series.3d'=>'MSCombi3D',
	);
	function set_headers($headers){
		$this->headers=$headers;
	}
	function get_headers(){
		return($this->headers);
		/* i was drunk, sorry
		$r = array();
		foreach($this->data as $row){
			if($this->axis_row=='@first'){
				foreach($row['value'] as $k1=>$cell){
					$r[] = $cell;
					break;
				}
			}else{
				$r[] = $row['value'][$this->axis_row];
			}
		}
		return($r);
		 */
	}
	function get_swf_file(){
		return($this->type2file[$this->type]);
	}
	/** 
	 * supported types: bars, multiple.series.bars 
	 * */

	function set_type($type_name){
		$this->type = $type_name;
	}
	function get_xml($nobreaks=1){
		ob_start();
		$t=$this;
		include(STD_LOCATION.'shared/templates/charts/chart.'.$this->type.'.template.xml.php');
		$c = ob_get_contents();

		if($nobreaks==1){
			$c=str_replace("\r",'',$c);//remove line breaks
			$c=str_replace("\n",'',$c);//remove line breaks
			$c=str_replace("\"","'",$c);
		}
		ob_end_clean();

		return($c);
	}
	function out(){
		global $std_chart_count;
		ob_start();
		$t=$this;
		$flag=0;
		if($this->name == 'untitled_chart'){
			if(!isset($std_chart_count)){$std_chart_count=0;}
			$this->name ='untitled_chart'.$std_chart_count;
			$std_chart_count++;
			$flag=1;
		}
		include(STD_LOCATION.'shared/templates/charts/chart.'.$this->type.'.template.php');
		$c = ob_get_contents();
		
		ob_end_clean();
		#p2($c);
		if($flag==1){//return to original state so it can continue to behave the same (autonumber)
			$this->name='untitled_chart';
		}
		return($c);
	}
	function html(){
		echo($this->out());
	}
	function shtml(){
		$c=new common();
		echo($c->shadow($this->out()));
	}
	function add_entry($name,$value,$color='default'){
		$this->add_set(array(
			'name'=>$name,
			'value'=>$value,
			//iterare over default colors when none is given
			'color'=>($color=='default')?$this->default_colors[count($this->data)%count($this->default_colors)]:$color,
			));
	}
	function add_set($set){
		$this->data[]=$set;
	}
	function set_data($data){
		$this->data = $data;
	}
	function std_chart(){
	
	}
}

?>
