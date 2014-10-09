<?php
	
class skin_model extends std{
	/** @todo allow #RRGGBB notation */
	function parse_color($color){
		return(explode(',',str_replace(array('rgb(',')'),'',$color)));
	}
	function apply_mask($color,$mask){
		list($r,$g,$b) = $this->parse_color($color);
		$mask = $this->parse_color($mask);
		$r+=$mask[0];
		$g+=$mask[1];
		$b+=$mask[2];
		return('rgb('.$r.','.$g.','.$b.')');
	}
	function ac_preproccessor(){//preproccessor
		global $config;
		if($config['skins_enabled']==1){
			$skin = $this->get_skin_array();

			//apply mask stuff:
			if(isset($_GET['table'])){
				#$m = $this->load_file($_GET['table']);
				#$m->mask
				foreach($skin as $sk=>$sv){
					if(substr($sk,0,strlen('model_'))=='model_'){
						$a = isset($skin['model_'.$_GET['table']])?$skin['model_'.$_GET['table']]:array();
						foreach($a as $k=>$v){
							$skin[$k] = $this->apply_mask($skin[$k],$v);
						}
						unset($skin[$sk]);//se don't want THESE in the final output
					}
				}
			}

			extract($skin);

			include($this->get_skin_folder().'style.css.php');
		}
		
	}
	function skin_model(){
		$this->table='none';
		$this->use_table=0;
		$this->disable_header('preproccessor');
		$this->std();
	}

	var $restrictions=array(
		'allow'=>array('ac_preproccessor'),
	);
	
}
?>
