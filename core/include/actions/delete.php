<?php
	#Calls Menu()

if($this->on_before_delete()){
	$this->disable_headers=array();
	$this->head();
	$this->menu();
	$f=new form();
	$id=$this->remove_strange_chars($_GET['id']);
	$f->add_field(array('type'=>'hidden','name'=>'id','value'=>$id));
	$me = $this->find_by_id($id);
	$rec_info=array(
		'id'=>'Id:'.$id,
		);
	if(isset($me[$this->ifield])){
		$rec_info['name']=$this->fi($this->ifield).':'.$me[$this->ifield];
	}
	$f->confirm_border_style=$this->shadow_config['delete'];
	$msg=$this->fmt($i18n_std['list']['form_confirm'],$rec_info,'_');
	$f->confirm($this->program_name(),'all_delete2',$msg);
	$this->on_after_delete();
	$this->foot();
}else{
	$this->set_cmessage($this->i18n_std('unable_to_delete'));
	$this->on_after_delete();
	header('Location: ?mod='.$this->program_name().'&ac=all_b2l');
}
?>
