<?php

	/** tabs are javascript hidden divs
	 * \todo horizontal, vertical.
	 * to call this, include this file like this:
	 * require_once(INCLUDE_DIR.'std_tab.php');

	USAGE:
	@code
	
	$s = new tab();
	$s->add_tab('tab1 title','tab 1 content');
	$s->add_tab('tab2 title','tab 2 content');
	$s->shtml();
	
	@endcode
	*/
class tab{
	var $shadow_style='shadow';
	var $tabs=array();
	var $name='default';
	var $active;
	var $tab_start='';
	var $link_class='default_link';
	var $tab_separator='&nbsp;';
	var $default_tab=0;
	/**\brief sets the default tab.
	 * @param $tab_id a number, index of the tab array.
	 * */
	function set_default_tab($tab_id){
		$this->active=$tab_id;
	}
	function set_style($style){
		if($style=='nice'){
			$this->tab_start='<table border=0 width="100%" cellspacing=0 cellpadding=0><tr><td align=right >';
			$this->link_class='tab_link';
			$this->tab_separator='</td><td align=right >';
			$this->tab_end='</td></tr></table>';
		}elseif($style=='normal'){
			$this->tab_start='';
			$this->link_class='';
			$this->tab_separator='&nbsp;';
			$this->tab_end='';
		}else{
			std::log('Uknown tab style:'.$style.' should be normal, or nice','TAB');
		}
	}
	function tab($tabs=array(),$name='default',$active=0,$style='nice'){
		$this->tabs=$tabs;
		$this->name=$name;
		$this->active=$active;
		$this->set_style($style);
	}
	/**
	 * @param $title The title of the tab.
	 * \todo allow multiple instances
	 * \todo allow multiple instances, with equal tab names to work.
	 * */
	function add_tab($title,$content){
		$this->tabs[$title]=$content;
	}
	function add_tab_content($title,$content){
		$this->tabs[$title].=$content;
	}

	function out($w=''){
		$dx='';
		$head=common::get_jsc('tab');
		$head.=common::get_cssc('tab');
		$c=0;
		$max=count($this->tabs);
		$headers=array();
		foreach($this->tabs as $k=>$v){
			if($this->active==$c){
				$d='';
				$d6='tab_link_on';
			}else{
				$d6='tab_link_off';
				$d='display:none;';	
			}

			$headers[]="\n"."<div id='std_link_".$this->name."_".$c."'  class='$d6 ".$this->link_class."_span'><a class='standard_link ".$this->link_class." ' href=\"javascript:std_display_tab('".$this->name."','".$c."','".$max."','".$this->link_class."_span');\">".$k."</a></div>";
			$dx.="\n".'<div class="tab_content standard_text standard_container cool_container" style="width:'.$w.';'.$d.';" id="std_'.$this->name.'_'.$c.'">'.$v.'</div>';
			$c++;
		}
		$r=$head."\n".$this->tab_start."\n".implode($this->tab_separator,$headers)."\n".$this->tab_end."\n".$dx;
		return($r);
	}
	function html($w=''){
		echo($this->out($w));
	}
	function shtml($w=''){
//		echo(common::shadow($this->out($w),'shadow','center'));
		$c=new common();
		echo($c->shadow($this->out($w),$this->shadow_style,'center'));
	}
}	

?>
