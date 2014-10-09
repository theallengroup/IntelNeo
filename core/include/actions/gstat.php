<?php
	$this->menu();
	$foreign_fields= array();
	foreach($this->fields as $kfield=>$field){
		if($this->is_foreign_id($field['name'])){
			$foreign_fields[$kfield] =$field; 
		}    
	}
	$this->shadow_start();
	echo("<ul>");
	foreach($foreign_fields as $field){
		#get module i18n
		#$field['name']
		$mod=$this->id2mod($field['name']);
		$title=$this->i18n("table_plural",$mod);
		#p2(array($mod,$title));

		echo('<br><a href ="?mod='.$this->table .'&ac=gstat&table_name='.$field['name'].'">'.$title.'</a>');
	} 
	echo("</ul>");
	if(isset($_GET['table_name'])){
		$tbl=$this->remove_strange_chars($_GET['table_name']);
		if(array_key_exists($tbl,$foreign_fields)){
			#echo('allok');
			$mod_name = $this->id2mod($tbl);
			$m=$this->load_file($mod_name);
			$foreign_items = $this->q2obj("SELECT * FROM ".$m->get_table_name());
			foreach($foreign_items as $k=>$foreign_item){
				#todo:what do you want here5555555
				#echo("<h1>".$foreign_item[$m->ifield]."</h1>");
				$level2 = array($this->id,$this->ifield);
				$q2=$this->q2obj("SELECT " . implode(',',$level2) . " FROM ".$this->get_table_name() ." WHERE ".$tbl." = ".$foreign_item[$m->id] );
				#what format do you want here555
				#which fields555

				#must filter table, add links, add privileges
				$this->e_table(
					$q2,
					$this->get_i18n_list($level2),
					array(
						'style'=>'list',
						'nr'=>'1',
						'title'=>$foreign_item[$m->ifield],
						'border'=>0,
					)
				);
			}
			#$m->e_dsl();
		}else{
			$this->error('invalid key:'.$_GET['table_name']);
		}
	}
	$this->shadow_end();
	
	#p2($foreign_fields);
?>
