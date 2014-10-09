<?php

class usr2session_model extends onboarding_base{
	function get_tutorial($activity_name){
		$q =$this->q2obj("select * from user_tutorial where name='$activity_name' and usr_id='".ssid()."'");
		if(count($q)>= 1){
			return "<!--USER-HAS-SEEN-TUTORIAL-BEFORE-NO-TUTORIAL-HERE-->";
		}else{
			$this->sql("INSERT INTO user_tutorial(name,usr_id) VALUES('". $this->remove_strange_chars($activity_name) ."','". ssid() ."')");
		}

		$tutorial_images = glob("./media/tutorial/".escapeshellcmd($activity_name)."/*.png");
		$data = array();
		$screen_width = 480;

		foreach($tutorial_images as $file){
			$data[]=array(
				'filename'=>$file,
				'width'=>$screen_width,
				'pages'=>count($tutorial_images),
			);
			#$images['help_text']='bienvenido al tutorial';
		}
		#echo("hi");die();
		$old_ct = $this->current_tutorial;
		$this->set_current_tutorial("");
		$tt = $this->s_template('tutorial',$data,"");
		$this->set_current_tutorial($old_ct);
		#p2($data);
		return($tt);

	}
	function usr2session_model(){
		$this->onboarding_base();
	}

	var $id="id";
	var $ifield='name';
	var $mod_get_kids_fields='all';	

}
?>
