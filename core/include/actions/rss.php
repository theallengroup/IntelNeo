<?php
	/**
	 * #Exports table to Excel	
	 * \todo :remove old exports
	 * \todo export just what you just saw, people might not have permission to see it all, but just some records.
	 * also, redirect using header:location.
	 * */

	global $i18n_std,$i18n;
	if($this->on_before_xls()){
		$fs=$this->foreign_select(array(
			'fields'=>$this->fields,
			'sort_field'=>$this->get_table_name().'.'.$this->id,
			'sort_direction'=>'ASC',
			'get_fid'=>0,
		));

		$a=$this->q2obj($fs['sql']);
//		$i18n[$this->table]['table_plural']
		$fn=STD_LOCATION.$this->xls_path.$this->get_table_name().date('Y_m_d__H_i_s').md5('youcantgetme__'.date('Y_m_d__H_i_s')).'.xls';
		$date1="<b>".$i18n_std['generated_on'].date('Y-m-d H:i:s')."</b>";
		$this->file_write($fn,
		$this->table(
			$a,
			$this->get_headers(),
			array(
				'border'=>1,
				'title'=>"<h1>".$this->i18n('table_plural')."</h1>".$date1,
				'footer'=>$date1
			)),1,1);
		$this->set_cmessage("<a class=standard_link href='".$fn."'>".$i18n_std['download'].$this->i18n('table_plural')."</a>");
		$this->ac_b2l();
		$this->on_after_xls();
	}
?>
