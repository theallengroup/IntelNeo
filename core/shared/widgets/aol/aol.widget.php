<?php

class aol_widget extends default_widget {
	function aol_widget(){
		$this->wlist=$this->load_widget('xor');//basic_select
		$this->wlist->set_name($this->get_name());
		//$this->wlist->set_label('Modo');
		$this->wlist->add_field('AND');
		$this->wlist->add_field('OR');

		$this->default_widget();
	}
}
?>
