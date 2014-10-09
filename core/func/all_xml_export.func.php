<?php

function a2x($tag_name,$a,$ident_level=0){
	$ident_string = str_repeat("\t",$ident_level);
	$ident_string1 = str_repeat("\t",$ident_level+1);
	$dx="\n$ident_string<$tag_name>";
	foreach($a as $k=>$v){
		if(is_array($v)){
			$dx.=a2x($k,$v,$ident_level+1);
		}else{
			$dx.="\n$ident_string1<$k>".htmlentities($v)."</$k>";
		}
	}
	$dx.="\n$ident_string</$tag_name>\n";
	return($dx);
}
function std_xml_export(&$me,$kids,$ids,$record_id){
	$l = $me->q2obj("select * from ".$me->get_table_name()." WHERE ".$me->id." ='".$me->remove_strange_chars($record_id)."'");
	$l=array('fields'=>$l[0]);

	if($kids==1){
		//$l['kids']=array();
		foreach($me->rel["has"] as $kid){
			$m =$me->load_file($kid);
			$c=0;
			foreach($me->q2obj("SELECT * from ".$m->get_table_name()." WHERE ".$me->program_name()."_id = '".$record_id."'") as $r){
				$l["kids"][$kid][$kid.$c]=std_xml_export($m,$kids,$ids,$r["id"]);
				$c++;
			}
		}
	}
	return($l);
}
function std_xml_export2(&$me,$kids,$ids,$record_id){
	$dx = ("<xmp>");
	$dx .= ("<?xml version=\"1.0\">\n");
	$dx .= (a2x($me->get_table_name(),std_xml_export($me,$kids,$ids,$record_id)));
	$dx .= ("</xmp>");
	return($dx);
		
}
function std_all_xml_export(&$me,$argv){
	global $mydir;
	$options=$argv[0];
	$me->menu();

	if(isset($_GET["ids"])){

		echo(std_xml_export2($me,$me->remove_strange_chars($_GET["kids"]),$me->remove_strange_chars($_GET["ids"]),$me->remove_strange_chars($_GET["id"])));

	}else{
		$s=new form();

		$s->strings=array(

			'kids'=>'Exportar Registros Anidados',
			'help_kids'=>' ',
			'ids'=>'Exportar IDs',
			'help_ids'=>' ',
			'id'=>'ID',
			'help_id'=>' ',

		);
		$s->set_title("Exportar XML");
		$s->add_field(array('name'=>'id','type'=>'label','value'=>$me->remove_strange_chars($_GET["id"])));
		$s->add_field(array('name'=>'kids','type'=>'list','options'=>array(0,1)));
		$s->add_field(array('name'=>'ids','type'=>'list','options'=>array(0,1)));
		$s->add_field(array('name'=>'mod','type'=>'hidden','value'=>$me->program_name()));
		$s->add_submit_button(array(
			'label'=>"Exportar XML",
			'action'=>$me->current_action,
		));
		$s->shtml();
	}
}

?>
