<?php
	/** \brief deletes a record from this table.
	 * @param $id the Id of the record to be deleted.
	 * @see std::set_cmessage
	 * "the record was deleted" or a message stating that "impossible to delete"
	 * */		
if($this->on_before_delete2()){

	if($_GET['answer']=='yes'){
		$id = $_GET['id'];
		$id=$this->remove_strange_chars($id);
		$this->sql('DELETE FROM '.$this->get_table_name()." WHERE ".$this->id." ='$id' ");
		if($this->affected()==1){
			$this->log_event('DELETE',$id);
			$this->set_cmessage($i18n_std['list']['form_delete_ok'].$id);
		}else{
			$this->set_cmessage($i18n_std['error']['delete']." ($id)");
		}
	}else{
		$this->set_cmessage($i18n_std['list']['form_cancelled']);	
	}	
	$this->on_after_delete2();
	if($this->silent==0){
		header('Location: ?mod='.$this->program_name().'&ac=all_b2l');
	}
}
?>
