<?php 
#Generated on 2009-05-18 17:13:28 by 1:Administrator
class drill_down_report_model extends std {
	function xml_record($table,$where_statement,$exclude_fields=array(),$close_tag='/'){
		$q = $this->q2obj("select * from $table where $where_statement");
	
		foreach($q as $row){
			echo("<$table ");
			foreach($row as $name=>$value){
				if(in_array($name,$exclude_fields)){
					continue;
				}
				echo(" ".$name.'="'.$value.'"');
			}
			echo("$close_tag>\n");
		}
	}
	function xml2array_object($xml){
		$a=array();
		foreach($xml as $k=>$v){
			$a[$k]=$v;
		}
		return $a;
	}
	
	function ac_all_export(){
		$this->menu();
		$id = $this->remove_strange_chars($_GET['id']);
		$q = $this->q2obj("select * from drill_down_report where id='$id'");
		if(count($q)==0){
			$this->msg("Error, no existe el registro.");
			return("");
		}
		$exclude_fields=array('id','drill_down_report_id');
		echo("<textarea rows=10 cols=80>");
		echo("<?xml version=\"1.0\" ?>\n");
		$this->xml_record('drill_down_report',	"id='$id'",$exclude_fields,"");
		$this->xml_record('data_field',		"drill_down_report_id='$id'",$exclude_fields,"/");
		$this->xml_record('dimension_field',	"drill_down_report_id='$id'",$exclude_fields,"/");
		echo("</drill_down_report>");
		echo("</textarea>");


		//import
		$f=new form();
		$f->set_title("Importar XML");
		$f->add_field(array('type'=>'textarea','name'=>'xml','value'=>'','i18n_text'=>'Digite el XML'));
		$f->add_hidden_field('mod',$this->program_name());
		$f->add_hidden_field('_postback',1);
		$f->add_hidden_field('id',$id);
		$f->add_submit_button(array('label'=>'Volver a la lista de Resultados','action'=>'all_b2l'));
		$f->add_submit_button(array('label'=>'Importar XML','action'=>$this->current_action));
		$f->shtml();
		if(isset($_GET['_postback'])){
			$xml = simplexml_load_string(str_replace("\\","",$_GET['xml']));
			//p2($xml->attributes());
			$a=$this->xml2array_object($xml->attributes());
			$this->sql("INSERT INTO drill_down_report (id,".implode(',',array_keys($a)).") VALUES (0, '" .implode("','",$a)."')" );
			$ddrid=$this->last_id();
			foreach($xml->children() as $c) {
				$tag = $c->getName();
				$a=$this->xml2array_object($c->attributes());
				$this->sql("INSERT INTO $tag (id,drill_down_report_id,".implode(',',array_keys($a)).") VALUES (0,$ddrid, '" .implode("','",$a)."')" );
				//echo("\n<br>");
			}
			//echo("<xmp>".$_GET['xml'].'</xmp>');
			echo("Cargado OK");
		}

	}
	function ac_all_run(){
		global $config;
		$this->menu();
		$id = $this->remove_strange_chars($_GET["id"]);
		$r = $this->q2obj("select * from ".$this->get_table_name()." where id='".$id."'","name","description");
		$di = $this->q2obj("select dimension_field.name, dimension_field.label,chart_type.name as chart_type_name from dimension_field,chart_type where drill_down_report_id='".$id."' and chart_type_id = chart_type.id ORDER BY dimension_field.id");
		$da = $this->q2op("select * from data_field where drill_down_report_id='".$id."'","label","expression");

		$rp = array('preserve'=>array('report_name','mod','ac','id'));
		foreach($r as $row){
			$rp["title"]=$row["name"];
			$rp["table"]=$row["table_name"];
		}
		foreach($di as $row){
			$rp["dimension_fields"][$row["name"]]=array(
				'label'=>$row["label"],
				'chart_type'=>$row["chart_type_name"],
				);
		}
		foreach($da as $label=>$expression){
			$expression=str_replace("#","'",$expression);
			$is_money_here=1;
			if(strpos($expression,'!')!==FALSE){
				$expression=str_replace("!","",$expression);
				$is_money_here=0;
			}

			if(strpos(strtolower($expression),'count(')!==FALSE||strpos($expression,'sum(')!==FALSE){
				$alias=1;
				$alias_name=str_replace(array('(',')','*'),'_',$expression);
			}else{
				$alias_name='';
				$alias=0;
			}
			$rp["data_fields"][$expression]=array(
				'use_alias'=>0,
				'label'=>$label,
				'is_money'=>$is_money_here,
				'value'=>$expression,
			);
			if($alias==1){
				$rp["data_fields"][$expression]['alias']=$alias_name;
				$rp["data_fields"][$expression]['use_alias']=1;
			}
		}

		if(isset($config["userfilter"]) && $config["userfilter"]==1){
			$uf = $this->load_file("userfilter");
			$rp["filter"]=$uf->get_user_filter($rp["table"]);
		}


		require(SHARED_MODULES_DIR."/lib/report.lib.php");
		#p2($rp);
		show_report($rp);
		//done.




	}
	function drill_down_report_model(){$this->std();}
		var $table='drill_down_report';
	var $id='id';
	var $ifield='name'; 
}
?>
