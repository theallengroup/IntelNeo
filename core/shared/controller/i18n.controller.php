<?php

class i18n_model extends std{
	function ac_diagnose(){
		global $i18n,$i18n_std;
		$this->menu();
		echo('diagnosing...');
		$old_language=std_get_language();
		//2func LANG LIST
		$ll=array();
		foreach(glob(STD_LOCATION.'i18n/*.include.php') as $i18n_file){
			$l=basename($i18n_file,'.include.php');
			$ll[$l]=$l;
		}
		//trash old i18n
		$old_i18n=$i18n;

		//tofunc MAIN i18n
		$my_i18n=array();
		foreach($ll as $l1){
			std_set_language($l1);
			$i18n=array();			
			$i18n_files=$this->get_valid_i18n();
			foreach($i18n_files as $i18n_file){
				require_once($i18n_file);
			}
			$my_i18n[$l1]=$i18n;
		}


		//p2($my_i18n);
		foreach($ll as $l){
			$ll2=$ll;
			unset($ll2[$l]);
			foreach($ll2 as $l2){
				$t=$this->array_flat($my_i18n[$l]);
				$t2=array_keys($t);
				$T=$this->array_flat($my_i18n[$l2]);
				$T2=array_keys($T);
				echo("<h1 class='form_title standard_title'>".$l.' keys not in '.$l2.'</h1>');
				$t3=array_diff($t2,$T2);
				d2($t3);
				echo("<h1 class='form_title standard_title'>".$l2.' keys not in '.$l.'</h1>');
				$t3=array_diff($T2,$t2);
				d2($t3);
			}
		}

		
		//missing Files!
		//missing Values!
		
		//in the end leave everythinh as it was, please!
		std_set_language($old_language);
	}
	/**expat like function that turns a=(a=1,b=(c=1,d=2),f=4) into 
	 /a=1
	 /b/c=1
	 /b/d=2
	 /f

	 pretty damn useful, IMHO

	 */
	function array_flat($a,$st=''){
		$res=array();
		foreach($a as $k=>$value){
			if(is_array($value)){
				$res=array_merge($res,$this->array_flat($value,$st.'/'.$k));
			}else{
				$res[$st.'/'.$k]=$value;
			}
		}
		return($res);
	}
	function i18n_model(){
		$this->std();
	}
	var $use_table = 0;
}
?>
