<?php
	/**
	 * Exports table to Excel	
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
		//$fn=STD_LOCATION.$this->xls_path.$this->get_table_name().date('Y_m_d__H_i_s').md5('youcantgetme__'.date('Y_m_d__H_i_s')).'.xls';
		$date1=$i18n_std['generated_on'].date('Y-m-d H:i:s');
		
		include('Spreadsheet/Excel/Writer.php');
		
		$workbook = new Spreadsheet_Excel_Writer();

		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();

		$format_title =& $workbook->addFormat();
		$format_title->setBold();
		$format_title->setColor('black');
		$format_title->setPattern(1);
	//	$format_title->setSize(24);
	//	$format_title->setFgColor('#e0e0e0');
		// let's merge
	//	$format_title->setAlign('merge');


	//	$format_title2 =& $workbook->addFormat();
	//	$format_title2->setBold();
		// let's merge
	//	$format_title2->setAlign('merge');

		$worksheet =& $workbook->addWorksheet();
		$rownum=2;
		$colnum=0;
		foreach($this->get_headers() as $cell){
			$worksheet->write($rownum+2, $colnum, $cell,$format_bold);
			$colnum++;
			$worksheet->write(0, $colnum, "", $format_title);
			$worksheet->write($rownum, $colnum, "", $format_title2);
		}
		$worksheet->write(0, 0, $this->i18n('table_plural'), $format_title);
		$worksheet->write($rownum, 0, $date1, $format_title2);

		$rownum=5;
		foreach($a as $row){
			$colnum=0;
			foreach($row as $cell){
				$worksheet->write($rownum, $colnum, $cell);
				$colnum++;
			}
			$rownum++;
		}
		$workbook->send($this->i18n('table_plural').'.xls');
		$workbook->close();
		die();//I don't want any aditional output!

		/*
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
		p2($_GET);
		//header("Location: /");
		 */
	}
?>
