<?php
/**
 * xor(select(basic_date),range(basic_date,basic_date))
 * */
class expr_widget extends default_widget {
	var $expr='';

	function user_interface(){
		//echo($this->expr);
		require_once(STD_LOCATION.'include/abstract_parser.php');

		$at = new abstract_parser();
		$ast = $at->parse_expr($this->expr);
	//	p2($ast);
		$w = $this->ast2widget($ast,'aol',$this->get_name());
		//$w->set_name($this->get_name());//Direct override
	//	p2($w,'blue');
		return($w->user_interface());
	}
	function ast2widget($ast,$widget_type,$name){
		$result=array();
		//p2($ast,'red');

		$widget_name_list = explode('[',$name);
		$parent = $this->load_widget($widget_name_list[0]);
		$parent->set_name($name);

		foreach($ast as $widget_id => $expr1){
			if(is_array($expr1)){
				if(isset($expr1['name']) && is_array($expr1['value'])){
					$parent->add_field($this->ast2widget($expr1['value'],$expr1['name'], $expr1['name'].'['.$widget_id.']' ));
					//$parent->add_field($this->ast2widget($expr1['value'],$expr1['name'], $expr1['name'].'/'.$widget_id ));
				}
			}else{
				$ww = $this->load_widget($expr1);
				$ww->set_name($name.'['.$widget_id.']');
				//$ww->set_name($name.'/'.$widget_id);
				$parent->add_field($ww);
			}
		}
		//p2($parent,'blue');		
		return($parent);
	}
	function set_expr($expr){
		$this->expr = $expr;
	}
	function get_expr(){
		return($this->expr);
	}
	function prepare(){
	
	}
	function expr_widget(){
		$this->default_widget();
	}
}
?>
