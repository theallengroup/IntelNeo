<?php
set_time_limit(10);
	/** 
	 * how do we validate the system not to allow this punks to see ANY record in the system by simpky modifying URLs?????
	 * perhaps seeing relationships between $this->program_name() and other mods untill finding a connection??
	 * security concerns?
	 *
	 * */
	$this->menu();
	if($_GET['path']==''){
		$_GET['path']='/';
	}
	$path=$_GET['path'];
	$tdata=array('title'=>$this->i18n('table_plural'),'links'=>array());
	if($path=='/'){
		foreach($this->find_all() as $rec){
			$tdata['links'][]='<a class="standard_link make_link" href="?mod='.$this->program_name().'&ac='.$this->current_action.'&path='.$this->program_name().'/'.$rec[$this->id].'">'.$rec[$this->ifield].'</a>';
		}
		include(SHARED_MODULES_DIR.'templates/tree_main.php');
		
	}else{
		//resolve path
		$path_parts = explode('/',$path);
		$path_text='<a class="standard_link make_link" href="?mod='.$this->program_name().'&ac='.$this->current_action.'&path=/">Ra&iacute;z</a>';
		$cumulative_path='';
		for($i=0;$i<count($path_parts);$i+=2){
			$mod_name = $path_parts[$i];
			$id = $path_parts[$i+1];
			$mod=$this->load_file($mod_name);
			$rec = $mod->find_by_id($id);
			
			
			$cumulative_path.=$mod_name.'/'.$id.'/';
			$path_text.='/<a class="standard_link make_link" href="?mod='.$this->program_name().'&ac='.$this->current_action.'&path='.substr($cumulative_path,0,strlen($cumulative_path)-1).'">'.$rec[$mod->ifield].'</a>';
		}
		//show the current path
		$tdata=array();
		
		$tdata['navigation_link']=$path_text;
		//show myself (last part in path)
		$mod_name=$path_parts[count($path_parts)-2];
		$id=$path_parts[count($path_parts)-1];
		$mod=$this->load_file(escapeshellcmd($mod_name));
		$tdata['record_info']=$mod->dsr('all',$mod->get_table_name().'.'.$mod->id.' = \''.$this->remove_strange_chars($id).'\'',array(),'table_title');
		$tdata['children']=array();
		//show my kids
		foreach($mod->rel['has'] as $k=>$child){
		
			$c=$this->load_file($child);
			$child_list = $c->find_all($c->get_connector_field($mod->program_name()).'=\''. $id .'\'' ) ;
			$tdata['children'][$k]=array('name'=>"\n".$c->i18n('table_plural').':'.count($child_list),'items'=>array());
			
			foreach($child_list as $rec){
				$tdata['children'][$k]['items'][]=('<a class="standard_link make_link" href="?mod='.$this->program_name().'&ac='.$this->current_action.'&path='.$path.'/'.$child.'/'.$rec[$c->id].'">'.'['.$rec[$c->id].'] '.$rec[$c->ifield].'</a>');
			}

		}
		
		include(SHARED_MODULES_DIR.'templates/tree.php');
	
	}
?>
