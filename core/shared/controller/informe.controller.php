<?php 
#Generated on 2009-05-11 12:42:20 by 1:Administrator
#
# como hacer interfaz de drill trough?
#
class informe_model extends std {
	function ac_all_run(){
		$this->menu();
		$s = "";
		$q = $this->q2obj("select 
			informe.descripcion as descripcion_informe,
			informe.titulo as informe,
			columna.titulo as columna,
			campo,
			valor,
			campo_fuente,
			informe.fuente_de_datos,
			columna.calculado,
			columna.visible
		       	from fuente,informe,columna where columna_id = columna.id and informe_id = informe.id AND informe.id='".$this->remove_strange_chars($_GET["id"])."' order by columna.orden");
		#$this->e_table($q);
		$l = extract($q[0]);
		//$titulos=array($grupo=>$grupo);
		//$titulos=$this->q2op("select campo_de_agrupamiento.id , campo_de_agrupamiento.nombre from campo_de_agrupamiento where informe_id = '".$this->remove_strange_chars($_GET["id"])."' ORDER BY posicion ","id",'nombre');
		
		$campos_de_agrupamiento=$this->q2obj("select * from campo_de_agrupamiento where informe_id = '".$this->remove_strange_chars($_GET["id"])."' ORDER BY posicion ");
		$f = new form();
		$f->set_title("Filtros de Consulta");
		$c=0;
		$ff=array();
		#p2($campos_de_agrupamiento);
		foreach($campos_de_agrupamiento as $k=>$row){
			$titulos[$row["id"]]=$row["nombre"];
			if($row["es_filtrable"]=='Y'){
				$c++;
				$op=$this->q2op("SELECT distinct(".$row["nombre"].") FROM ".$fuente_de_datos." ORDER BY ".$row["nombre"],$row["nombre"],$row["nombre"]);
				if(isset($_GET[$row["nombre"]])){
					$vf = $_GET[$row["nombre"]];
				}else{
					$vf = array_keys($op);
				}
				$f->add_field(array('i18n_text'=>ucwords($row["nombre"]),'i18n_help'=>'', 'name'=>$row["nombre"],'type'=>'checklist','check_all'=>1,'options'=>$op,'values'=>$vf));
				$ff[$row["nombre"]]=$row["nombre"];
			}
		}
		if($c>0){
		
			$f->add_submit_button(array("action"=>$this->current_action,'label'=>"Filtrar"));
			$f->add_hidden_field("mod",$this->program_name());
			$f->add_hidden_field("id",$this->remove_strange_chars($_GET["id"]));
			echo("<div style='width:400px'>");
			$f->shtml();
			echo("</div>");
			$csql= array();
			$c1 = 0;
			foreach($ff as $nombre_campo){
				if(isset($_GET[$nombre_campo])){
					$c1++;
					$csql[]= "$nombre_campo IN ('".implode("' , '",$_GET[$nombre_campo])."')";
				}
			}
			if($c1>0){
				$csql = implode(" AND ",$csql);
			}else{
				$csql='2=2';
				
			}
		}else{
			$csql='1=1';
		}
		#$csql = '1=1';
		#echo("ESTE ES:".$csql);

		$grupos=$titulos;		

		if(count($grupos)==0){
			$this->msg("No hay campo de agrupamiento.");
			return("");
		}

		$fields=array();
		$f4 = array();
		$visibility=array();
		$conds = array();
		foreach($q as $row){
			$titulos[$row["columna"]]=$row["columna"];
			$f4[$row["columna"]]=$row["calculado"];
			$visibility[$row["columna"]]=$row["visible"];
			if($row["calculado"]==1){
				$fields[$row["columna"]].="\n\t ".$row["campo_fuente"]." ";
			}else{
				$fields[$row["columna"]].="\n\t + case when(lcase(trim(".$row["campo"].")) = lcase(trim('".$row["valor"]."'))) then ".$row["campo_fuente"]." else 0 end";
			}

		}
		
		
		$f2 = $fields;
		//dont filter on calculatable results
		foreach($fields as $kf=>$vf){
			if($f4[$kf]==0){
				$f2[$kf] = $fields[$kf];
			}
		}

		$f2 = $fields;
		//efectua suma
		foreach($fields as $kf=>$vf){
			if($f4[$kf]==0){
				$fields[$kf] = "sum(".$fields[$kf].")";
			}
		}

		//reemplaza 
		//post processing, replace [a] with a's content
		//
		
		$kf2 = array_keys($fields);
		foreach($fields as $kf=>$vf){
			foreach($kf2 as $test){
				$fields[$kf] = str_replace("[".$test."]","(".$fields[$test].")",$fields[$kf]);
			}
			//$fields[$kf] = "(".$fields[$kf].")";
		}
		

		foreach($fields as $k=>$v){
			if($f4[$k]==0){
				$conds[$k]="((".$f2[$k].") <> 0)";//ONLY THOSE RECORDS THAT ARE GREATER THAN CERO
			}
			$fields[$k]="concat('<div style=\"text-align:right\">',format($v,2),'</div>') as `$k`";
			//$fields[$k]="format($v,2) as `$k`";
		}
		//process visibility
		foreach($fields as $k=>$v){
			if($visibility[$k]==0){
				unset($fields[$k]);

			}
		}
		

		$g2=$grupos;
		foreach($g2 as $k=>$v){
			$g2[$k] = "concat('<nobr>',$v,'</nobr>') as `$v`";
		}
		$sql = "SELECT \n\t".implode(",",$g2).",\t\n".implode(",\n\t",$fields)."\n FROM ".$fuente_de_datos."  WHERE (".implode(" OR ",$conds) .") AND (".$csql.") GROUP BY ".implode(",",$grupos);	
		#echo($sql);
		$this->privilege_manager->add_privilege(array(
			'action'=>$this->program_name().'/all_b2l',
			'privilege_name'=>$this->program_name(),
		));
		
		echo($this->b2l_link()."<br/><br/>");
		echo("<!--".htmlspecialchars($sql)." -->");
		$this->shadow_start();
		$data = $this->q2obj($sql);
		//$this->e_table($data,$titulos,array("title"=>$informe."<br/><br/><span style='font-size:10pt;font-weight:normal'>fecha y Hora del Informe:".date("Y-m-d H:i")."</span><br/>",'border'=>0,'style'=>'list'));
		
		$title = $informe."<br/><br/><span style='font-size:10pt;font-weight:normal'>Fecha y Hora del Informe:".date("Y-m-d H:i")."<br/>".nl2br($descripcion_informe)."</span><br/>";
		include(STD_LOCATION."include/tree.ui.php");
		if(count($grupos)>=2){
			array_pop($grupos);
		}
		echo(tree_ui($data,implode("/",$grupos),$title));
		 
		$this->shadow_end();

	}
	function informe_model(){
		$this->std();
	}
	var $default_trim_length=200;

	var $table='informe';
	var $id='id';
	var $ifield='nombre'; 
}
?>
