<?php
	/** 
	 * this deletes the items that were marked for deletition, in the list.
	 * calls menu()
	 * \todo click on "id" to "check all", 
	 * \todo confirm page
	 * */
	if($this->on_before_delete_selected()){
		
		$errors='<div style="text-align:left">';
		$errlist=array();
		if(isset($_GET['item']) && is_array($_GET['item'])){
		
			foreach($_GET['item'] as $k=>$id){
				$this->sql('DELETE FROM '.$this->get_table_name()." WHERE ".$this->id." ='".$this->remove_strange_chars($id)."'");
				if($this->affected()==0){
					$errlist[]=$this->get_i18n_text('cannot_delete').$id;
				}else{
					$this->log_event('DELETE',$id);
					$errlist[]=$this->get_i18n_text('delete_ok').$id;
				}
			}
			$this->set_cmessage($errors.implode('<br/>',$errlist)."</div>");
		}else{
			$this->set_cmessage($this->i18n_std('no_records_selected'));
		}
		
		
	}else{
		$this->set_cmessage($this->i18n_std('unable_to_delete'));
	}
	$this->on_after_delete_selected();
	$this->send_b2l_headers();

?>
