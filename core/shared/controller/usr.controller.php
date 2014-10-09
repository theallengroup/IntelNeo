<?php
	/**
	 * Hand Edit
	 * */

	#Title: Untitled App	
	#Author: f3l	
	#Generated Date: 2006-05-16 15:52:06	
	#Description: f3l	
	#Generator Version: 0.2	
	
class usr_model extends std{
	var $nono=0;
	var $login_message='';
	function set_login_message($msg){
		$this->login_message=$msg;
	}
	function get_login_message(){
		return($this->login_message);
	}
	/**
	 * display profile info
	 * \todo 3100 show also roles available
	 * */
	function ac_edit_profile(){
		//$this->menu();
		$_GET['id']=ssid();
		$this->current_view='edit_profile';
		$this->ac_view();
	}
	function ac_not_authorized(){
		global $i18n;
		$this->enable_header($this->current_action);//this helps head() to work, in other cases, it wouldn't
		$this->head();
		$this->set_login_message($i18n['usr']['texts']['not_authorized'].'<br/>usr001');
		$this->ac_login();
	}
	function ac_register(){
		$this->current_view='register';
		$this->ac_view();

//		$this->current_view='register';
//		$this->ac_view();
	}
	function ac_register_ok(){
		global $config;
		$_GET['login_count']=0;
		$_GET['last_login']='1970-01-01 00:00:00';
		$_GET['last_login_month']='01';
		$_GET['last_login_date']='01';
		$_GET['last_login_year']='1970';
		$_GET['created_date']=date('Y-m-d H:i:s');
		$_GET['created_date_month']=date('Y');
		$_GET['created_date_date']=date('m');
		$_GET['created_date_year']=date('d');
		$_GET['last_ip']=$this->get_ip();

		$this->silent=1;	
		if($this->ac_new2()){
			$lid=$this->last_id();
			$r=$this->q2obj('SELECT * FROM '.$this->table_prefix.'role WHERE name='."'".$config['default_user_role']."'");
			$rid=$r[0]['id'];
			$this->sql('INSERT INTO '.$this->table_prefix.'usr2role VALUES(\'\',\''.$lid.'\',\''.$rid.'\')');
			if($this->affected()!=1){
				$this->error('unable to create','usr005');
			}
			$this->msg($this->i18n('register_sucess')."<br/>".$this->make_link(array('mod'=>'usr'),$this->i18n('click2login')));

		}
		
	}
	/** 
	 * automagic login, no question asked
	 * user is md5()'d
	 * so is pwd
	 * */
	function ac_auto(){
		$this->validate_user(array('blob'=>1));
		//no md5
	}
	
	/**
	 * validates user in list.
	 * get.blob=md5(username)+password
	 * or post.email & post.password
	 */
	function validate_user($options=array()){
		//http://localhost/project0.2.7.1/projects/system4/?blob=63a9f0ea7bb98050796b649e854818450d107d09f5bbe40cade3de5c71e9e9b7
		//63a9f0ea7bb98050796b649e854818450d107d09f5bbe40cade3de5c71e9e9b7
		global $mydir,$std_usr_data,$main,$config;

		//echo('<h1>validate_user()</h1>');


		if($options['blob']==1){
			$blob=$this->remove_strange_chars($_GET['blob']);
			$email=substr($blob,0,32);
			$password=substr($blob,33,64);
			$sql="SELECT * from ".$this->get_table_name()." WHERE email=md5('".$email."') AND password='".$password."' ";
		}else{
			$email=$this->remove_strange_chars($_POST['email']);
			$password=md5($this->remove_strange_chars($_POST['password']));
			$sql="SELECT * from ".$this->get_table_name()." WHERE email='".$email."' AND password='".$password."' ";
		}
		#p2($sql,'red');
		$us=$this->q2obj($sql);

		if(std_get_language()==''){
			std_set_language('ES');
		}

		if(count($us)==0){
			$_SESSION[$mydir]["inside"]=0;
			$_SESSION[$mydir]['usr']['id']=1;
			$this->log_event('LOGIN FAILED:  '.$_SERVER['REMOTE_ADDR'].' '.$email);
			$_SESSION[$mydir]['usr']['id']=0;
			$this->ac_not_authorized();
			//$this->ac_login();
				//load privileges
		}else{
			$_SESSION[$mydir]['usr']=$us[0];	/*all the session data, first record only.*/
			$_SESSION[$mydir]["inside"]=1;

			//load profile's language
			if(isset($config['enable_profile']) && $config['enable_profile']==1){
				std_set_language('ES');//Spanish is Default, just in case profile() fails.
				$m=$this->load_file('profile');
				$m->set_user_language();
			}

			$this->sql("UPDATE ".$this->get_table_name()." SET login_count=login_count+1 , last_login='".date('Y-m-d H:i:s')."' , last_ip='".$this->get_ip()."' WHERE ".$this->id."='".ssid()."' ");
			
			
			$first_letter=$us[0]["email"][0];
			$fn="./data/".$first_letter."/".$us[0]["email"]."/".$us[0]["email"].".rc.php";
			if(file_exists($fn)){
				include($fn);
				$this->log("Preferences file Loaded:".$fn,'MODULE');
				$_SESSION[$mydir]['usr']['field_presets']=$std_usr_data['views'];

			}else{
				$this->log("the user has no default options file, nothing is loaded:".$fn,'MODULE');
			}
			//before log = make sure we have a LANG defined
			if(std_get_language()==''){
				std_set_language('ES');
			}
			$this->log_event('LOGIN:'.$email." IP:".$_SERVER['REMOTE_ADDR']." USER_AGENT:".$this->remove_strange_chars($_SERVER["HTTP_USER_AGENT"]).' ');


			header('Location: ?');

			/*
			if(!isset($_POST['uri'])){
				//echo('go to /');
				//die();
				header('Location: ?');
			}else{
				$this->privilege_manager->rebuild_privileges();
				//echo('go to '.$_POST['uri']);
				//die();


				//The System may not get all the way here
				header('Location: '.$_POST['uri']);
			}
			 */

		}
	
	}
	function ac_login2(){
		$this->validate_user(array());	
	}
	
	/**
	 *\todo SET: 
	 NOTE: there is a system to enable the user to enter exactly 	 the place he was on, 
	 but that system unfortunately doesn';t quite work (and it might never work), so it is deleted.
	 perhaps in 0.2.6.4 ?
	 * */

	function ac_login(){
		global $i18n,$config,$mydir;
		$_SESSION[$mydir]["inside"]=0;
		//echo("<h1>Login()</h1>");
		//echo(b2());

		$a=array('email','password');
		$ac='login2';
		if($this->login_redirect==1){

			$uri=$this->redirect_uri;//$_SERVER['REQUEST_URI'];
			$this->fields['uri']=array('name'=>'uri','value'=>$uri,'type'=>'hidden','i18n_text'=>'Redir','i18n_help'=>' ');
			$a[]='uri';
		}
		
		echo("</td><td style='text-align:center;' >");

		echo($this->get_logo());
		echo("<div class=login_box style=''>");
		$f = $this->get_form_from_fields($ac,$a,
			array(
				'method'=>'POST',
				'width'=>"40%",
				'title'=>$i18n['apptitle'].' :: '.$this->get_i18n_text('login2'),
				)
			);
		if(preg_match("/iPhone/",$_SERVER['HTTP_USER_AGENT'])){
			$f->set_columns(1);
		}
		$h1="<span onclick='location.href=\"?mod=usr&ac=forgot_password\"' style='cursor:pointer;color:rgb(132,132,132);text-decoration:underline' class=standard_link onclick=''>Forgot Password?</span>";
		$f->add_field(array('name'=>'forgot_password','i18n_text'=>$h1,'type'=>'simple','simple'=>'&nbsp;'));
		$f->set_message($this->get_login_message());
		$f->shtml();
		echo("</div>");

	}
	/** 
		user interface for forgetful users (aren't we all?)
	*/
	function ac_forgot_password(){
		global $i18n;
		$f=new form();
		if(!isset($_GET['email'])){
			$f->add_hidden_field("mod",$this->program_name());
			$f->add_field(array('name'=>'email','i18n_text'=>$this->fi('email'),''));
			$f->add_submit_button(array('action'=>'login','label'=>'Back to Login'));
			$f->add_submit_button(array('action'=>$this->current_action,'label'=>'Request new password'));
		
			$f->set_title($i18n['apptitle']." :: Forgot Password?");
			$f->shtml();
		}else{
			$back_link='<br/> <a href="?mod=usr&ac=login">Back to Login</a>';
			if($_GET['email']=='root'){
				$this->msg("ERROR A00: Invalid User.".$back_link);
				return("");
			}
			if(strpos($_GET['email'],"@")===FALSE){
				$this->msg("ERROR A01: Invalid User.".$back_link);
				return("");
			}
			$q=$this->q2obj("select * from ".$this->get_table_name()." WHERE email='".$this->remove_strange_chars($_GET['email'])."'");
			if(count($q)>0){
				$nc=substr(md5('salecita'.date("Y-m-d H:i:s")),rand(0,5),8);
				#echo($nc);
				$this->sql("UPDATE ".$this->get_table_name()." SET password='".md5($nc)."' where id='".$q[0]['id']."'");
				mail($q[0]['email'],"[".$i18n['apptitle'].'] Clave Olvidada?','Hello, the new password for the user: '.$q[0]['email'].' is '.$nc,'From: soporte@auditor400.com');
				mail('soporte@auditor400.com',$i18n['apptitle'].": user ".$q[0]['email'].' has been reset password.','User: '.$q[0]['email'].' has changed password. ','From: soporte@auditor400.com');
				$this->msg("The password has been sent to your email. <a href='?mod=usr&ac=login'>Login</a>");

			}else{
				$this->msg("ERROR A02: Invalid User.".$back_link);
				return("");
			}
		}
	}
	function ac_need_login(){
		global $mydir,$main;
		if($_SESSION[$mydir]["inside"]==1){
			//$this->menu();
			//This si the default execution point where the logo is clicked!
			$main->ac_main();
		}else{
			//echo('<h1>you are currently not inside</h1>');
			$this->ac_login();
		}
	}

	function ac_change_password2(){
		$this->menu();
	
		$_GET['oldpassword']=$this->remove_strange_chars($_GET['oldpassword']);
		if($_GET['newpassword']==$_GET['password2'] ){
			if(count($this->q2obj('SELECT password FROM '.$this->get_table_name().' WHERE password = \''.md5($_GET['oldpassword']).'\' AND id=\''.ssid().'\' '))==1){
				$this->sql('UPDATE '.$this->get_table_name().' SET password = \''.md5($_GET['password2']).'\' WHERE id=\''.ssid().'\'');
				if($this->affected()==1){
					$this->i_msg('update_ok');
				}else{
					$this->i_msg('no_update');
				}
			}else{
				$this->i_error('password_error','usr002');
			}
		}else{
			$this->i_error('password_error','usr003');
		}
		
	}
	/**
	 * become root again (only enabled, if you previously impersonated someone else
	 * */
	function ac_su(){
		global $mydir;
		$_GET["id"] = $_SESSION[$mydir]['usr']["old_uid"];
		$this->allow_old_link=0;
		$this->ac_all_impersonate();
	}
	/** 
	 * become any user (for simplyfing admin tasks)
	 * */
	function ac_all_impersonate(){
		global $mydir,$std_usr_data,$main,$config;		
		$old_name = ssname();
		$old_uid = ssid();
		$uid = $this->remove_strange_chars($_GET["id"]);
		$sql="SELECT * from ".$this->get_table_name()." WHERE id='".$uid."' ";
		$us=$this->q2obj($sql);

		$this->log_event("USER SWITCH:".$us[0]['id'].':'.$us['email']." BY ADMIN USER :".$old_uid.':'.$old_name,'MODULE');

		#$_SESSION["OLD_DATA"]=$sql.':'.gp2($us);
		$_SESSION[$mydir]=array();//CLEAR PREVIOUS DATA
		$_SESSION[$mydir]['usr']=$us[0];	/*all the session data, first record only.*/
		$_SESSION[$mydir]['usr']["old_uid"]=$old_uid;	
		$_SESSION[$mydir]["inside"]=1;
		#$this->privilege_manager->rebuild_privileges();
		#if($this->allow_old_link==1){
		#	$_SESSION[$mydir]["usr"]["_roles"]['ADMIN']='ADMIN';
		#	$p_obj=array(
		#		'action'=>'usr/su',
		#		'role_name'=>'ADMIN',
		#		'privilege_name'=>'SU ADMIN'
		#	);
		#	$this->privilege_manager->add_privilege($p_obj);
		#	$_SESSION[$mydir]["usr"]["_privileges_menu"][]=$p_obj;
		#}
		header('Location: ?');
	}
	function ac_all_admin_change_password(){
		$this->cpform(0,'all_admin_change_password2');
	}
	function ac_all_admin_change_password2(){
		$this->menu();
		$_GET['newpassword']=$this->remove_strange_chars($_GET['newpassword']);
		$_GET['uid']=$this->remove_strange_chars($_GET['uid']);

		$this->sql('UPDATE '.$this->get_table_name().' SET password = \''.md5($_GET['newpassword']).'\' WHERE id=\''.$_GET['uid'].'\'');
		if($this->affected()==1){
			$this->i_msg('update_ok');
		}else{
			$this->i_msg('no_update');
		}
	}
	function cpform($confirm,$ac){
		global $i18n;
		$this->menu();
		$f=new form();
		$f->strings=$this->i18n('change_pass_form');

		$f->add_field(array('name'=>'mod','value'=>$this->program_name(),'type'=>'hidden'));
		if($confirm){
			$f->add_field(array('name'=>'oldpassword','type'=>'password'));
		}
		$f->add_field(array('name'=>'newpassword','type'=>'password'));
		if($confirm){
			$f->add_field(array('name'=>'password2','type'=>'password'));
		}else{
			$f->add_field(array('name'=>'uid','type'=>'hidden','value'=>$_GET['id']));
		}
		$f->add_submit_button(array('label'=>$this->i18n('change_pass'),'action'=>$ac));
		$f->shtml();
		//$this->privilege_manager->ap('change_password2');

	}
	function ac_change_password(){
		$this->cpform(1,'edit_profile_change_password2');
	}
	/**
	 * adds a "role" list so you can define right away who this guy is.
	 * */
	function ac_all_add_user(){
		global $i18n;
		$this->menu();
		$this->run_view('new_user');
	}
	function usr_model(){
		$this->disable_header('login2');
		$this->disable_header('new2');
		$this->disable_header('all_impersonate');
		$this->disable_header('su');
		$this->std();
	}
	/** Aliases */
	function ac_edit_profile_change_password(){
		$this->ac_change_password();
	}
	function ac_edit_profile_change_password2(){
		$this->ac_change_password2();
	}
	
	var $default_action='need_login';
	var $table="usr";
	var $module_name="usr";
	var $id="id";
	var $ifield='name';
	var $allow_old_link=1;
	var $restrictions=array(
		'allow'=>array(
			'ac_forgot_password',
			'ac_need_login',
			'ac_register',
			'ac_auto',
			'ac_login',	//why wasn't this here before?
			'ac_login2'
			),
	);
}
?>
