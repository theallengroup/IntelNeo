<?php

	
$this->menu();
$image_fields=array();
foreach($this->fields as $k=>$f){
	if($f["type"]=='upload'){
		$image_fields[$k]=$f;
	}
}

if(isset($_POST["post_back"]) && $_POST["post_back"]==1){
	foreach($image_fields as $field_name=>$field ){
		$if = "./uploaded_images/".escapeshellcmd($_POST["old_$field_name"]);
		if(file_exists($if) && $_POST["old_$field_name"]=''){
			unlink($if);
		}
		$ext="jpg";
		if(isset($config["thumbnail_ext"])){
			$ext = $config["thumbnail_ext"];
		}
		//unlink("./uploaded_images/".$_POST["old_$field_name"].'.thumbnail.jpg');
		$thumb_name = md5($_FILES[$field_name]['name'].date('Y-m-d H:i:s').rand(0,10000)).'.'.$ext;//TODO fix this

		if(!copy($_FILES[$field_name]['tmp_name'],'./uploaded_images/'.$thumb_name)){
			echo("error on copy()");
		}

		if(isset($this->fields[$field_name]["thumb"])){
			$tf = $this->fields[$field_name]["thumb"];
			
	
			$width='@default';
			$height='@default';
		
			if($width=='@default'){
				$width=$config["images_width"];
			}
			if($height=='@default'){
				$height=$config["images_height"];
			}
			chdir("uploaded_images");
			if(PHP_OS=='Darwin'){
			
				$ccmd='/opt/local/bin/convert';
			}else{
				$ccmd='convert';
			}

			$d = passthru($ccmd.' '.$thumb_name.' -thumbnail '.$width.'x'.$height.' '.$thumb_name.'.thumbnail.'.$ext.' > log.txt 2>&1 ');
			$d = passthru('echo '.$thumb_name.' -thumbnail '.$width.'x'.$height.' '.$thumb_name.'.thumbnail.'.$ext.' > cmd.txt 2>&1 ');
			chdir("..");

			$this->sql("UPDATE ".$this->get_table_name().' SET '.$tf.'=\''.$thumb_name.'.thumbnail.'.$ext.'\' WHERE id=\''.$this->remove_strange_chars($_POST["id"]).'\'');
		}

		$this->sql("UPDATE ".$this->get_table_name().' SET '.$field_name.'=\''.$thumb_name.'\' WHERE id=\''.$this->remove_strange_chars($_POST["id"]).'\'');
		$m='';
		if(!$this->affected()==1){
			$m='NO changes were made.';
		}else{
			$m=$i18n_std["uploaded_ok"];
		}
		$this->msg($m."<br/>".$this->b2l_link());
	}

}else{
	$s=new form();
	$s->method='POST';
	$s->action='?mod='.$this->program_name().'&ac='.$this->current_action;

	$s->add_field(array('name'=>'mod','type'=>'hidden','value'=>$this->program_name()));
	$s->add_field(array('name'=>'post_back','type'=>'hidden','value'=>1));
	$s->add_field(array('name'=>'id','type'=>'label','value'=>$_GET["id"]));
		$s->strings=array(
			'id'=>'Id',
			'help_od'=>' ',
		);
	
	$s->set_title($this->i18n('table_title').': '.$i18n_std["change_picture"]);
	$r  = $this->q2obj("select * from ".$this->get_table_name()." where id = '".$this->remove_strange_chars($_GET["id"])."'");
	$r = $r[0];

	foreach($image_fields as $field_name=>$field ){
		$s->add_field(array('name'=>'old_'.$field_name,'type'=>'hidden','value'=>$r[$field_name]));
		$s->strings[$field_name]=$this->fi($field_name);
		$s->strings['help_'.$field_name]=$this->fh($field_name);
		$s->add_field(array('name'=>$field_name,'type'=>'file'));
	}

	$s->add_submit_button(array(
		'label'=>$i18n_std["change_picture"],
		'action'=>$this->program_name().'/'.$this->current_action));
	$s->shtml();
}

?>
