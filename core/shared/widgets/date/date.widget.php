<?php
class date_widget extends default_widget {
	function prepare(){

		$this->xorw=$this->load_widget('xor');
		$this->xorw->set_name($this->get_name());

		$this->rangew=$this->load_widget('range');
		$this->rangew->set_name($this->get_name().'[range]');
		$this->date1=$this->load_widget('basic_date');
		$this->date1->set_label('Desde');
		$this->date1->set_name($this->get_name().'[from]');
		$this->date1->set_fs(array(
			'name'=>$this->date1->get_name(),
			'value'=>'2001-01-01',
			));

		$this->date2=$this->load_widget('basic_date');
		$this->date2->set_label('Hasta');
		$this->date2->set_name($this->get_name().'[to]');
		$this->date2->set_fs(array(
			'name'=>$this->date2->get_name(),
			'value'=>'2002-01-01',
		));

		$this->date0=$this->load_widget('basic_date');
		$this->date0->set_name($this->get_name().'[in]');
		//$this->date0->set_label('En:');
		$this->date0->set_fs(array(
			'name'=>$this->date0->get_name(),
			'value'=>'2001-01-12',
		));


		$this->rangew->add_field($this->date1);
		$this->rangew->add_field($this->date2);

		$this->xorw->add_field($this->date0);
		$this->xorw->add_field($this->rangew);
		//temporally disabled
		$this->xorw->set_default($this->get_name().'[in]');
	
	}
	function user_interface(){
		$this->prepare();
		return($this->xorw->user_interface());
	}
	function date_widget(){
		
		$this->default_widget();
	}
}
?>
