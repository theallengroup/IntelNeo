<?php
class nav_model extends std{
	function ac_tables(){
		global $config;
		$this->menu();
		$this->shadow_start("round");
		if(!isset($_GET['table'])){
			$q=$this->table_list();
			echo("<h1 class='standard_title form_title' >Navegador SQL</h1>");
			$ws=array();
			$c=0;
			foreach($q as $table){
				$d = $table["Tables_in_".$config["database_name"]];
				$ws[$c]=array();
				$ws[$c]['Datos']=("<a href='?mod=nav&ac=tables&table=$d&ac2=lista'>$d</a>");

				//record count
				$c1=$this->q2op('select 0 as id,count(*) as name from '.$d,'id','name');
				$ws[$c]['Total Registros']=(" {$c1[0]} ");

				$ws[$c]['Estructura']=("<a href='?mod=nav&ac=tables&table=$d&ac2=descripcion'> Estructura</a><br>");
				$c++;
			}
			$this->e_table($ws,'none',array('style'=>'list'));

		}elseif($_GET["ac2"]=="lista"){
			echo("<a href='?mod=nav&ac=tables'> Volver a la Lista</a>");
			$_GET["table"]= escapeshellcmd($this->remove_strange_chars($_GET["table"]));
			$m2 = $this->aa($this->get_valid_modules());
			if(isset($m2["./controller/".$m.".controller.php"])){
				$m = $this->load_file($_GET["table"]);
				$m->e_dsl();
			}else{
				$this->e_table($this->q2obj("SELECT * FROM ".$_GET["table"]),'none',array('title'=>$_GET["table"]));
			}
			
		}elseif($_GET["ac2"]=="descripcion"){
			echo("<a href='?mod=nav&ac=tables'>Volver a la Lista</a>");
			$q = $this->describe($_GET["table"]);
			echo("<h1 class='standard_title form_title'>".$_GET["table"]."</h1>");
			$this->e_table($q,array_keys($q[0]),array('style'=>'list'));

		}
		$this->shadow_end("round");

	}
	function nav_model(){

		$this->std();
	}
	var $use_table = 0;
}
?>
