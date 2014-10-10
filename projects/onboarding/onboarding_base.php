<?php
define('CT_TEXT', 1);
define('CT_WATCH', 2);

define("AS_READY", 1);
define("AS_COMPLETE", 2);

define("AT_POLL", 1);
define("AT_QUIZ", 2);
define("AT_QUIZ_WC", 10);//WORD CLOUD QUIZ
define("AT_WAGER", 3);
define("AT_SPIN", 4);
define("AT_ACRONYM", 5);
define("AT_READ", 6);
define("AT_SCAVENGER", 7);
define("AT_PHOTO", 8);
define("AT_QR", 9);

class onboarding_base extends std{
	var $current_tutorial = "";
	function get_sessions(){
		return $this->q2op("select id,name from session","id","name");
	}

	//usr2session
	function s_header(){
		echo($this->s_tmpl("header"));
	}

	function s_footer(){
		echo($this->s_tmpl("footer"));
	}

	function s_tmpl($template, $template_name=-1){
		$t = '';
		if( strpos($template, '.php') !== false ) {
			ob_start();
			require($template);
			$t = ob_get_clean();
		}
		else {
			if( !file_exists("./template/$template.html") ) {
				return '';
			}
			$t = file_get_contents("./template/$template.html");
		}

		if($template_name !== -1){
			if(strstr($t, "[#template_name]") !== FALSE){
				$main = file_get_contents("./template/s_main.html");
				$main = str_replace("[#template_name]", $template_name, $main);
				$t = str_replace("[#main]", $main, $t);
			}
		}

		#add navigation
		if(strstr($t, "[#navigation]") !== FALSE){
			$nav = file_get_contents("./template/activity_navigation.html");
			$t = str_replace("[#navigation]", $nav, $t);
		}

		#add notification count
		if(strstr($t, "[#notification]") !== FALSE) {
			$unread_count = $this->get_unread_message_count();
			$icon = file_get_contents("./template/notification_icon.html");
			$t = str_replace("[#notification]", $icon, $t);
			$t = str_replace("[#unread_count]", $unread_count, $t);
		}
		return($t);
	}

	function user_exists($email){
		return count($this->q2obj("select * from usr where email='$email'"));
	}

	function get_rank_by_score($score){
		$q = $this->q2obj("select id from rank where score_start <= '$score' and score_end >='$score'");
		if(count($q) == 0){
			if($score > 0 ){
				$q = $this->q2obj("select max(id) as id from rank ");
			}
			if($score <= 0 ){
				$q = $this->q2obj("select id from rank where id=1");
			}
		}
		return $q[0]["id"];
	}

	function get_rank_by_rank_value($rank_value){
		$q = $this->q2obj("select * from rank where rank_value = '$rank_value'");
		return $q;
	}

	function get_score(){
		$a = $this->q2obj("select score from usr_status where usr_id='".ssid()."'");
		return $a[0]["score"];
	}

	/*
	 * returns 1 if rank changed and
	 * 0 if ranks stayed the same.
	 * */
	function earn_points($points){ 
        $old_rank = $this->get_rank_by_score($this->get_score());
		$this->sql("update usr_status set score = score + '".((int)$this->remove_strange_chars($points))."' where usr_id='".ssid()."'");
		$new_rank = $this->get_rank_by_score($this->get_score());
		$this->set_rank($new_rank);
		if($old_rank != $new_rank){
			return 1;
		}else{
			return 0;
		}
	}
    
    function earn_team_points($points,$teamid){
        $this->sql("update team set score = score + '".((int)$this->remove_strange_chars($points))."' where id='".$teamid."'");
    }

	function set_rank($rank){
		$this->sql("update usr_status set rank_id = '$rank' where usr_id='".ssid()."'");
	}

	function get_unread_message_count() {
		$query = "SELECT COUNT(*) as count FROM message WHERE source='".ssid()."' AND view_date = '2000-01-01' and respuesta='YES'";
		$res = $this->sql($query);

		if($this->query_failed==1 || $res =='FAILED' ){
			// TODO: log error
			return 0;
		}

		$row = $this->fetch();
		return $row['count'];
	}

	function get_messages($message_id=0){
		$s="";
		if($message_id != 0){
			$message_id = $this->remove_strange_chars($message_id);
			$s = "and message.id='$message_id'";
		}
        
		$messages =  $this->q2obj("SELECT
				message.*, message.answer mensaje,
				usr.name as source,
				(case
					when view_date = '2000-01-01' and respuesta = 'YES' then
					'unread'
					else
					'read'
				end) as class_name
				from
					message,
					usr
				where
					usr.id = message.source and message.respuesta <> 'n/a'
					and source='".ssid()."' $s
				order by id desc ");
        
		foreach($messages as $k=>$message){
			if( $message['anonymous'] == 1 ) {
                $messages[$k]["body"] = 'Anonymous';
                //$messages[$k]["source"] = 'Anonymous';
			}
			$messages[$k]["creation_time"] = date('g:i A', strtotime($message["creation_date"]));
		}
        
		return $messages;
	}

	function get_read($message_id){
		$s="";
		if($message_id != 0){
			$message_id = $this->remove_strange_chars($message_id);
			$s = "and message.id='$message_id'";
		}
		$messages =  $this->q2obj("SELECT question.*,question.name,  question.description
			FROM  `question`
			WHERE  id = $message_id  ");

		return $messages;
	}

	/* r_write r_photo_op */
	function ac_r_photo_op(){
		$this->s_header();
		$this->s_slider_menu();

		if(isset($_POST["sent"]) && $_POST["sent"] == '1'){
			//save the photo.
			print_r($_FILES);
		}else{
			echo($this->s_tmpl("photo_op"));
		}
		$this->s_footer();
	}

	function ac_r_photo(){
		$this->s_header();
		$this->s_slider_menu();
		if(isset($_POST["body"])){
			$usrid=ssid();
			$photo=$_POST["body"];
			$this->sql("INSERT INTO photo (`id_photo`,`id_usr`, `foto`) VALUES('',$usrid,'$photo')");
			echo("<script>;location.href='?mod=usr2session&ac=r_message_sent';</script>");
		}
		echo($this->s_tmpl("photo_upload"));
		$this->s_footer();
	}

	function ac_r_photo_upload(){
		$this->s_header();
		$this->s_slider_menu();

		echo($this->s_tmpl("photo_upload"));
		$this->s_footer();
	}

	function ac_r_take_photo(){
		$this->s_header();
		$this->s_slider_menu();

		//print_r($_SESSION);
		//echo "-------------------------";
		//echo $_SESSION[usr][id];
		//echo $_SESSION ['onboarding']['usr']['id'];

		echo($this->s_tmpl("take_photo"));
		$this->s_footer();
	}

	function ac_r_take_photo_choose(){
		if(isset($_GET['shared'])){
			$activ = $this->get_activity($_REQUEST["activity_id"]);
			$score= $activ[0]['activity_value'];
			$rank = $this->earn_points($score);
			$res = array('result'=>'success');
			if($_GET['shared'] == 1){
				$res['doRedirect'] = '1';
				$all_correct = '1';
				$rank_changed = $rank;
				$total_score  = $user_score = $score*2;
				$total_questions = 4;
				$res['query_string'] = "&mm={$all_correct}&rank_changed={$rank_changed}&total_score={$total_score}&user_score={$user_score}&questions={$total_questions}";
			}
			header('Content-Type: application/json');
			echo(json_encode($res));exit;

		}
		$this->s_header();
		$this->s_slider_menu();


		echo($this->s_tmpl("take_photo_choose"));
		$this->s_footer();
	}

	function ac_r_settings(){
		if(isset($_GET['is_muted'])){
			/*$sql = "UPDATE `usr` set is_muted='1' WHERE id=" .ssid();
				$res = $this->sql($sql);

				if($res) $msg = array('result'=>'success');*/
			$msg = array('is_muted'=>$_GET['is_muted']);
			header('Content-Type: application/json');
			echo(json_encode($msg));exit;
		}
		$this->s_header();
		$this->s_slider_menu();
				$rUs = $this->q2obj("SELECT usr.*, usr.email
										FROM  `usr`
										WHERE  `id` ='".ssid()."'
										LIMIT 0 , 1");
				$r = $this->s_tmpl("settings");
				$r = $this->add_score_data_to_template($t);
				$r = $this->s_template("settings", $rUs, "", $r);

		echo($r);
		$this->s_footer();
	}

	/* Scavenger Camera */
	function ac_r_scavenger_camera(){
		$this->s_header();
		$this->s_slider_menu();

		echo($this->s_tmpl("scavenger_camera"));
		$this->s_footer();
	}

	/* Scavenger */
	function ac_r_scavenger(){
		$this->s_header();
		$this->s_slider_menu();

		echo($this->s_tmpl("scavenger"));
		$this->s_footer();
	}

	/* r_write */
	function ac_r_write(){
		if(isset($_REQUEST["body"])){
			$source = ssid();
			$destination = "my message" . session_id() . '_' . ssid();
			$view_date = '2000-01-01';
			$creation_date = date('Y-m-d H:i:s');
			$body = $this->remove_strange_chars($_REQUEST["body"]);
			$anon = isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'SUBMIT ANONYMOUS' ? 1 : 0;

			# TODO: update message.destination from int(100) to varchar or something
			$this->sql("INSERT INTO message(source, destination, anonymous, origen, answer, respuesta, view_date, creation_date, body)
				VALUES('$source', '$destination', '$anon', '$source', 'n/a', 'n/a', '$view_date', '$creation_date', '$body')");

			$host  = $_SERVER['HTTP_HOST'];
			$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
			$extra = '?mod=usr2session&ac=r_message_sent';
			header("Location: http://$host$uri/$extra", true, 301);
			exit;
		}

		$this->s_header();
		$this->s_slider_menu();
		echo($this->s_tmpl("write"));
		$this->s_footer();
	}

	/* r_view_message */
	function ac_r_view_message(){
		$this->s_header();
		$this->s_slider_menu();
		$mid = $this->remove_strange_chars($_REQUEST["message_id"]);
		$messages = $this->get_messages($mid);
		$this->sql("UPDATE message set view_date='".date('Y-m-d H:i:s')."', respuesta='READED' WHERE id='".$mid."' and source='".ssid()."' and view_date = '2000-01-01' ");
		echo($this->s_template("view_message", $messages));
		$this->s_footer();
	}

	/* r_message_sent */
	function ac_r_message_sent(){
		$this->s_header();
		$this->s_slider_menu();
		echo($this->s_tmpl("message_sent"));
		$this->s_footer();
	}

	/* r_questions_and_comments */
	function ac_r_questions_and_comments(){
		$this->s_header();
		$this->s_slider_menu();
		$messages = $this->get_messages();
        
		$count = 1;
		foreach($messages as $k=>$message){
            // check if string have more than 40 characters then replace the rest of string with dots.
            if (strlen($message["body"]) > 40) {
                $messageFromName = substr($message["body"],0,40)."...";
            }
            else {
                $messageFromName = $message["body"];
            }
            
            $message["body"] = $messageFromName;

//            $messages[$k]["body"] = substr($message["body"],0,40)."...";
            // $messages[$k]["source"] = substr($message["body"],0,40)."...";
			// $messages[$k]["klass"] = $message["view_date"] == '2000-01-01 00:00:00' ? 'unread' : 'read';
			// $messages[$k]["creation_date"] = $this->time_elapsed_string($messages[$k]["creation_date"]);
		}

		echo($this->s_template("inbox", $messages));

		$this->s_footer();
	}

	function ac_r_logout(){
		sset('inside',0);
		sset('usr',array());#delete session data
		$this->ac_r_();
	}

	
	/* r_enviar_mensaje*/
	// Nostradamus knew what $var1, $var2 and $var3 means. I don't. :)
	function enviamensaje($var1,$var2,$var3) {
		$num = md5(time());

		//MAIL BODY
		$body = "
		<html>
		<head>
		<title>INTEL NEO:  ".$var2."</title>
		</head>
		<body style='background:#EEE; padding:30px;'>
		<h2 style='color:#767676;'>INTEL NEO</h2>";

		//Cambiar URL imagen del correo de Bienvenida
		 $body .= "
		 <strong style='color:#0090C6;'><img src='http://outsourcing.pressstart.co/PJCTS/NewIntelNEO/project0.2.7.6/projects/onboarding/skins/welcome_intel_neo.jpg'></strong>
		 <span style='color:#767676;'></span>";

		$body .= "</body></html>";

		$_name=$_FILES["filead"]["name"];
		$_type=$_FILES["filead"]["type"];
		$_size=$_FILES["filead"]["size"];
		$_temp=$_FILES["filead"]["tmp_name"];

		if( strcmp($_name, "") ) { //FILES EXISTS
			$fp = fopen($_temp, "rb");
			$file = fread($fp, $_size);
			$file = chunk_split(base64_encode($file));

			// MULTI-HEADERS Content-Type: multipart/mixed and Boundary is mandatory.
			$headers = "From: Monitoreo Grupo Bedoya <rhfat24@gmail.com>\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: multipart/mixed; ";
			$headers .= "boundary=".$num."\r\n";
			$headers .= "--".$num."\n";

			// HTML HEADERS
			$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
			$headers .= "Content-Transfer-Encoding: 8bit\r\n";
			$headers .= "".$body."\n";
			$headers .= "--".$num."\n";

			// FILES HEADERS
			$headers .= "Content-Type:application/octet-stream ";
			$headers .= "name=\"".$_name."\"r\n";
			$headers .= "Content-Transfer-Encoding: base64\r\n";
			$headers .= "Content-Disposition: attachment; ";
			$headers .= "filename=\"".$_name."\"\r\n\n";
			$headers .= "".$file."\r\n";
			$headers .= "--".$num."--";
		}
		else { //FILES NO EXISTS
			// HTML HEADERS
			$headers = "From: INTEL NEO <rhfat24@gmail.com> \r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
			$headers .= "Content-Transfer-Encoding: 8bit\r\n";
		}

		// SEND MAIL
		$sent = mail($var1, "INTEL NEO" , $body, $headers);
		
		//**********************************************
		// El mensaje
		//$mensaje = "Línea 1\r\nLínea 2\r\nLínea 3";

		// Enviarlo
		//mail('rhfat24@gmail.com', 'Bienvenido', $mensaje);
		echo " ";
		return;
	}

	/* r_write_setting */
	function ac_r_write_setting(){
		echo " ";
		return;
	}

	function ac_r_register2(){
		$session_id = $this->remove_strange_chars($_POST["session_id"]);
		$name = $this->remove_strange_chars($_POST["name"]);
		$email = $this->remove_strange_chars($_POST["email"]);
		$password = $this->remove_strange_chars($_POST["pass"]);
		$pass2 = $this->remove_strange_chars($_POST["pass2"]);

		$_POST["email"] = $email;
		$_POST["password"] = $password;

		if($password != $pass2){
			echo("Error, passwords must match.");
			return;
		}
		$password = md5($password);
		if($this->user_exists($email)){
			echo("User exists.");
			return;
		}
		$this->sql("INSERT INTO usr(name,email,password,login_count,last_login,created_date,last_ip)
		VALUES('$name','$email','$password','0','2000-01-01','".date("Y-m-d H:i:s")."','".$_SERVER["REMOTE_ADDR"]."')");
		$uid = $this->last_id();
		$DEFAULT_ROLE = $this->s_get_default_role();	//FRONT END ROLE.
		$this->sql("INSERT INTO usr2role (usr_id,role_id) VALUES('$uid','$DEFAULT_ROLE')");
		$this->sql("INSERT INTO usr2session (usr_id,session_id) VALUES('$uid','$session_id')");
		$rank_id = $this->get_rank_by_score(0);

		$this->sql("INSERT INTO usr_status (usr_id,score,rank_id) VALUES('$uid','0','$rank_id')");

		$emailus=$email;
		$PassUsr=$password;

		$enviado24 = $this->enviamensaje($emailus,$name,$PassUsr);
		echo(" ");//do not delete this line.
		//autologin
		ob_start();
		echo("<div style='display:none'>");
		$m =$this->load_file("usr");
		$m->validate_user(array());
		$this->menu();//force privilege draw.
		echo("</div>");
		ob_end_clean();
		$this->s_header();
		echo($this->s_template("register_welcome",array()));
		echo("<script>;
			setTimeout(function(){
				location.href='?mod=usr2session&ac=r_firstwellcome';
			},3000);
		</script>");
		$this->s_footer();
	}

	function ac_r_login(){
		$this->s_header();
		echo($this->s_tmpl("login"));
		$this->s_footer();
	}

	function ac_r_home(){
		$this->s_header();
		$this->s_slider_menu();
		
		// WHOAAA...?! "$incomplete_arr" means complete and "$completed_arr" means incomplete (active).
		// I bet somebody was drunk when wrote this code.
		$incomplete_arr = $this->get_current_activities2(); // Completed activities, believe me.
		$completed_arr = $this->get_current_activities(); // Active activities.
		
        //echo '<pre>';var_dump($completed_arr[4]);exit;
		$this->set_current_tutorial("Start");

		$container = $this->s_tmpl('activities');
		$incomplete = $this->s_template('../template/activities_incomplete_tab', $incomplete_arr);
		$completed = $this->s_template('../template/activities_completed_tab', $completed_arr);

		$container = str_replace("[#incomplete]", $incomplete, $container);
		echo("<script>;
			setTimeout(function(){
				$('#tabs').animate({'bottom':'0%'},'slow');
			},1000);
		</script>");
		$container = str_replace("[#completed]", $completed, $container);
		echo("<script>;
			setTimeout(function(){
				$('#tabs').animate({'bottom':'0%'},'slow');
			},1000);
		</script>");
		echo $container;
		$this->s_footer();
	}

	function ac_r_firstwellcome(){
		$this->s_header();
		$this->s_slider_menu();
		echo($this->s_tmpl("slider_tutorial"));
		$this->s_footer();
	}

	function ac_r_welcome(){
		$this->s_header();
		$this->s_slider_menu();
		echo($this->s_tmpl("get_started"));
		$this->s_footer();
	}

	// show user score
	function ac_r_score_read(){
		$this->s_header();
		$t = $this->s_tmpl("score_read");
		$t = $this->add_score_data_to_template($t);
		echo($t);
		$this->s_footer();
	}

	function add_score_data_to_template($t){
		$images = array(
			'NONE',
			'medal01',
			'medal02',
			'medal03',
			'medal04',
			'medal05',
			'trophy01',
			'trophy02',
			'trophy03',
			'trophy04',
			'trophy05',
		);

		$user_data = $this->q2obj("select
				rank.name as rank,
				rank_value,
				score,
				score_start,
				score_end
				from usr_status, rank where rank.id = rank_id and usr_id = '".ssid()."'");

		$user_score = $user_data[0]["score"];
		$user_rank = $user_data[0]["rank"];
		$user_start = $user_data[0]["score_start"];
		$user_end = $user_data[0]["score_end"];
		$rv = $user_data[0]["rank_value"];
/*echo '<pre>';
var_dump($user_data[0]); exit;*/
		//$user_percentage = round(100 * ($user_score - $user_start) / ($user_end - $user_start),0);
        $user_percentage = round(100 * ($user_score/$user_end),0);
		$user_percentage = min(100, $user_percentage);

		$current = $this->get_rank_by_rank_value($rv);
		$current["score_end"];

		$t = str_replace("[#score]", $user_score, $t);
		$t = str_replace("[#rank]", $user_rank, $t);
		$t = str_replace("[#score_start]", $user_start, $t);
		$t = str_replace("[#score_end]", $user_end, $t);
		$t = str_replace("[#score_percentage]", $user_percentage, $t);
		$t = str_replace("[#ssname]", ssname(), $t);
		$t = str_replace("[#current_rank_image_name]", $user_rank, $t);//$images[$rv], $t);
		$t = str_replace("[#next_rank_image_name]", $images[$rv + 1], $t);
		$t = str_replace("[#rank_changed]", $this->remove_strange_chars($_REQUEST["rank_changed"]), $t);


		if($_REQUEST["mm"] == '1'){
			$correct_message = "Great Job!";
		}else{
			$correct_message = "";
		}
		if($_REQUEST["rank_changed"] == '1'){
			$correct_message = "New Rank Achieved!";
			//todo show screen!.
		}
		$t = str_replace("[#all_correct]", $correct_message, $t);
		$trophies = $this->q2obj("SELECT rank.*	FROM rank where rank_value + 5 >'$user_rank' and rank_value -5 < '$user_rank' ");

		foreach($trophies as $k=>$trophy){
			$trophies[$k]["file_name"] = $images[$trophy["rank_value"]];
			if($rv == $trophy["rank_value"]){//$trophy["rank_value"]){
				$trophies[$k]["class_name"] = "current";
			}elseif($rv > $trophy["rank_value"]){
				$trophies[$k]["class_name"] = "before";
			}elseif($rv < $trophy["rank_value"]){
				$trophies[$k]["class_name"] = "after";
			}else{
				$trophies[$k]["class_name"] = "error";
			}
		}
		#p2($user_rank,'red');
		#p2($trophies);
		$trophies_html = $this->s_template("trophies",$trophies);
		$t = str_replace("[#trophies]", $trophies_html, $t);
		return $t;
	}

	function ac_r_leader_board(){
		#echo(ssname());
		$this->s_header();
		$this->s_slider_menu();
		$ranking = $this->q2obj("
			SELECT
				usr_status.score AS person_score,
				usr.id AS usr_id,
				usr.name,
				usr2session.session_id
			FROM
				usr_status
			INNER JOIN
				usr ON usr_status.usr_id = usr.id
			LEFT JOIN
				usr2session ON usr2session.usr_id = usr.id
			WHERE
				usr2session.session_id = (SELECT u2s.session_id FROM usr2session AS u2s WHERE u2s.usr_id = " . ssid() . ")
			ORDER BY person_score DESC
		");

		$c=1;

		foreach($ranking as $r=>$row){
			if(ssid() == $row["usr_id"] ){
				$ranking[$r]["ucolor"] = "highlight";
			}
			$ranking[$r]["number"] = $c;
			$c++;
		}
		$t = $this->s_tmpl("leaderboard");
		$t = $this->add_score_data_to_template($t);

		$t = $this->s_template("leaderboard", $ranking, "", $t);
		echo($t);
		$this->s_footer();
	}

	function get_poll_results($activity_id) {
		$q = $this->q2obj("
			SELECT
				ques.id AS question_id,
				qo.id AS question_option_id,
				ques.name AS question_name,
				qo.name AS question_option,
				qo.is_correct,
				(SELECT COUNT(*) FROM answer a2 WHERE a2.question_id=ques.id AND a2.question_option_id=qo.id) AS chosen,
				(SELECT COUNT(*) FROM answer a3 WHERE a3.question_id=ques.id) AS total_choices,
				act.activity_type_id,
				(CASE
					WHEN ans.question_option_id = qo.id THEN 'selected'
					ELSE ''
				END) as question_selected
			FROM
				activity act,
				question ques,
				question_option qo
				LEFT JOIN answer ans ON ans.question_option_id=qo.id AND ans.usr_id=".ssid()."
			WHERE
				act.id=$activity_id
				AND ques.activity_id=act.id
				AND qo.question_id=ques.id
			GROUP BY qo.id
		");

		foreach($q as $k => $question) {
			if( $q[$k]['activity_type_id'] == AT_WAGER || $q[$k]['activity_type_id'] == AT_QUIZ ) {
				# if there are right and wrong answers, show red and green
				$q[$k]['ring_color'] = $q[$k]['is_correct'] == 'Y' ? 'lightgreen' : 'red';
			}
			else {
				# for open polls, show this orange color
				$q[$k]['ring_color'] = '#fdb813';
			}
			$q[$k]['percentage'] = round($question['chosen'] / $question['total_choices'] * 100, 0);
		}
		$q = $this->group_by($q, "question_id");

		$count = 1;
		foreach($q as $question_name => $questions){
			$q[$question_name]["question_number"] = $count;
			$q[$question_name]["question_name"] = $questions[0]['question_name'];
			$op = $this->s_template("show_question", $questions);
			$q[$question_name]["question_content"] = $op;
			$cloud_opts = '';


			$qchosen	   =  array_map(function($v){return $v['chosen'];}, $questions);
			$has0     = (count(array_filter($qchosen)) < count($qchosen)) ?  true : false;
			if($has0){
				$ch  = array_map(function($v){return $v+1;},$qchosen);
			}
			$areLessThan9 = (max($qchosen) < 9) ? true : false;


			foreach($questions as $k=>$question){
				$chose = ($areLessThan9) ? round($ch[$k] *3) : $ch[$k];
				$opt   = $question['question_option'];
				$cloud_opts .= "<span data-weight='{$chose}'>{$opt}</span>";
			}
			$q[$question_name]["cloud_opts"] = $cloud_opts;
			$count++;
		}

		return $q;
	}

	function get_acronym_results($activity_id) {
		$a = $this->q2obj("
			SELECT
				q.activity_id,
				q.id AS question_id,
				q.name AS question_name,
				qo.id AS question_option_id,
				a.answer AS user_answer,
				qo.name AS right_answer
			FROM
				answer a,
				question q,
				question_option qo
			WHERE
				a.usr_id=".ssid()."
				AND q.id = a.question_id
				AND q.activity_id = $activity_id
				AND qo.question_id = q.id
		");

		$count = 1;
		foreach( $a as $n => $acronym ) {
			$needle = preg_split("/[- ]/", $acronym['user_answer']);
			$haystack = preg_split("/[- ]/", $acronym['right_answer']);
			$match = array();

			$i = 0;
			for($i = 0; $i < count($haystack); $i++ ) {
				$class = strtolower($needle[$i]) == strtolower($haystack[$i]) ? 'match' : 'miss';
				$match[] = '<p class="' . $class . '">' . $haystack[$i] . '</p>';
			}

			$a[$n]['question_number'] = $count;
			$a[$n]['question_content'] = join('', $match);
			$count++;
		}

		return $a;
	}

	function show_activity_results($activity_id){
		$activity_id = $this->remove_strange_chars($activity_id);
		$activity = $this->get_activity($activity_id);

		$results = array();
		switch($activity[0]['activity_type_id']) {
			case AT_POLL:
			case AT_QUIZ:
			case AT_WAGER:
				$results = $this->get_poll_results($activity_id);
				break;
			case AT_ACRONYM:
				$results = $this->get_acronym_results($activity_id);
				break;
			case AT_QUIZ_WC:
				$results = $this->get_poll_results($activity_id);
				echo($this->s_template("show_word_cloud", $results));
				$this->s_footer();
				return;
		}

		echo($this->s_template("show_quiz", $results));
		/*echo '<pre>';
		var_dump($results); exit;*/
		//echo($this->s_template("show_word_cloud", $results));
		$this->s_footer();
	}

	function ac_r_answers(){
		$this->s_header();
		$this->s_slider_menu();

		//see one
		if(isset($_REQUEST["activity_id"])){
			$this->show_activity_results($_REQUEST["activity_id"]);
			return;
		}

		//see all
		$activities= $this->q2obj("
			SELECT
				com.activity_id, act.name, com.activity_date,
				(
					SELECT count(*)
					FROM question ques, answer ans
					LEFT JOIN question_option qopt ON ans.question_option_id=qopt.id
					WHERE
						ans.usr_id = com.usr_id
						AND ques.activity_id=com.activity_id
						AND ans.question_id = ques.id
						AND (is_correct='Y' OR is_correct IS NULL)
				) AS correct,
				(
					SELECT COUNT(*)
					FROM question ques
					WHERE ques.activity_id = act.id
				) AS total
			FROM activity act, completed_activity com
			WHERE
				com.activity_id=act.id
				and act.activity_type_id != " . AT_READ . "
				AND com.usr_id='". ssid() ."'");

		echo($this->s_template("answers", $activities));
		$this->s_footer();
	}

	function ac_r_main(){
		$this->ac_r_();
	}

	function ac_r_(){
		global $mydir;
		#if($_SESSION[$mydir]["inside"]==1){
		#	$this->enable_header("r_");
		#	$this->head();
		#	$this->ac_main();
		#	$this->foot();
		#}else{

		$this->s_header();
		echo($this->s_tmpl("index","-"));
		$this->s_footer();
		#}
	}

	function get_session_id(){
		$q = $this->q2obj("select * from usr2session where usr_id='".ssid()."'");
		if(count($q) == 0){
			return 0;
		}else{
			return $q[0]["session_id"];
		}
	}

	function ac_r_watch(){
		$this->display_content(CT_WATCH, "watch", "watch_one", "Watch");
	}

	// text content
	function ac_r_read(){
		$this->display_content(CT_TEXT, "read", "read_one", "Read");
	}

	function display_content($content_type, $list_template, $detail_template, $template_title){
		#$this->menu();
		$sid = $this->get_session_id();
		if($sid == 0){
			echo("ERROR NO SESSION");
		}
		$ss = "";
		if(isset($_REQUEST["content_id"])){
			$cid = $this->remove_strange_chars($_REQUEST["content_id"]);
			$ss .= " and content.id ='$cid'";
		}

		$s= "select * from content where session_id= '".$sid."' and content_type_id = '".$content_type."'  $ss";
		#echo($s);
		$contents = $this->q2obj($s);
		if(!isset($_REQUEST["content_id"])){
			foreach($contents as $k=>$v){
				$contents[$k]["body"] =substr($v["body"] ,0,50)."...";
			}
		}
		$this->s_header();
		$this->s_slider_menu();
		if(isset($_REQUEST["content_id"])){
			$this->e_template($detail_template, $contents,$template_title);
		}else{
			$this->e_template($list_template, $contents,$template_title);
		}
		$this->s_footer();
	}

	function s_slider_menu(){
		$t = $this->s_tmpl("slider_menu");
		$user_data = $this->q2obj("select rank.name as rank, score from usr_status, rank where rank.id = rank_id and usr_id = '".ssid()."'");
		$user_score = $user_data[0]["score"];
		$user_rank = $user_data[0]["rank"];
		$t = str_replace("[#score]", $user_score, $t);
		$t = str_replace("[#score]", $user_score, $t);
		$t = str_replace("[#rank]", $user_rank, $t);
		$t = str_replace("[#ssname]", ssname(), $t);
		echo($t);
	}

	function e_template($template_name, $data, $template_title){
		echo($this->s_template($template_name,$data,$template_title));
	}

	function s_template($template_name, $data, $template_title="", $raw_template=""){
		$activity_id = $this->remove_strange_chars($_REQUEST["activity_id"]);
		$activity = $this->get_activity($activity_id);
        
		$h = array(
			'activity_id' => $activity_id,
            'activity_count' => count(array_keys($data)),
			'activity_name' => $this->get_activity_name($activity_id),
            'activity_value' => $this->get_activity_value($activity_id),
			'puntaje' => $activity[0]['puntaje'],
			'activity_type' => $this->underscore($this->get_activity_type($activity_id)),
			'activity_type_id' => (!empty($activity)) ? $activity[0]['activity_type_id'] : '',
			'team_id' => $this->remove_strange_chars($_REQUEST["team_id"]),
			'template_title' => $template_title,
			'tutorial' => $this->get_current_tutorial(),
		);
        
		$tree = array('isnode' => 1);
		$tree['HEADER'] = $h;
		$tree['FOOTER'] = $h;

		$tree['CONTENT']['LOOP'] = array(
			'HEADER' => $h,
			'FOOTER' => $h,
			'CONTENT' => $data,
            'activity_count' => count(array_keys($data)),
			'isnode' => 1
		);



		if($raw_template == ""){
			$tmpl = $this->s_tmpl($template_name, $template_title);
		} else {
			$tmpl = $raw_template;
		}
		/** For Testing!! To be Removed **/
		if(!isset($_SESSION['onboarding']['usr']['is_muted'])){
			$_SESSION['onboarding']['usr']['is_muted']='1';
		}
		/********/
		if(strstr($tmpl, "[#is_muted]") !== FALSE){
			$tmpl = str_replace("[#is_muted]", $_SESSION['onboarding']['usr']['is_muted'], $tmpl);
		}
		return($this->parse_template($tree, $tmpl, 'CONTENT', ''));
	}

	function ac_r_register(){
		#$this->menu();
		$this->s_header();
		$t = $this->s_tmpl("register");
		$s = $this->get_sessions();
		$sessions = "";
		foreach($s as $id=>$name){
			$sessions .= "<option value='$id'>$name</option>\n";
		}
		$t = str_replace("[#sessions]", $sessions,$t);
		echo($t);
		$this->s_footer();
	}

	function s_get_default_role(){
		return 4;
	}

	function ac_main(){
		$r = $this->q2obj("select * from usr2role where usr_id='".ssid()."'");
		if(count($r) == 1 && $r[0]["role_id"] == $this->s_get_default_role()){
			echo("<div style='diplay:none'>");
			#$this->menu();
			echo("</div>");

			echo("...<script>;location.href='?mod=usr2session&ac=r_welcome';</script>");
		}else{
			#echo(123);
			$this->menu();
		}
	}

	function get_activity($aid){
		$aid = $this->remove_strange_chars($aid);
		$s = $this->q2obj("select
			activity.*,
			activity_value,
            puntaje,
			activity_type.name as activity_type_name
			from
				activity,
				activity_type
			where
				activity_type_id = activity_type.id and
				activity.id='$aid' ");
		return $s;
	}

	function get_activity_name($aid) {
		$activity = $this->get_activity($aid);
		return $activity[0]['name'];
	}

	function get_activity_value($aid) {
		$activity = $this->get_activity($aid);
		return $activity[0]['puntaje'];
	}

	function get_activity_type($aid) {
		if( !$aid ) {
			return '';
		}

		$activity = $this->get_activity($aid);
		$type = $this->q2obj("select name from activity_type where id=".$activity[0]['activity_type_id']);
		return $type[0]['name'];
	}

	//Actividades completadas
	function get_current_activities2(){
		$s = $this->q2obj("
			SELECT
				activity. * , activity_type.activity_value AS valor_ac,
				activity_status.name AS STATUS ,
				activity_type.name AS cnomb
			FROM activity, activity2session, activity_type, activity_status, completed_activity
			WHERE activity_status_id = activity_status.id
				AND activity_type_id = activity_type.id
				AND activity2session.activity_id = activity.id
				AND activity.id = completed_activity.activity_id
				AND completed_activity.usr_id='".ssid()."'
				AND activity2session.session_id = ". $this->get_session_id() ."
			ORDER BY activity_status_id"); #fix NEO-20
			# activity_status_id='".AS_READY."'

		return $s;
	}

	//*************************************************************************************
	function get_current_activities(){
		$s_cond = "1=1";
		if(isset($_GET["filter"])){
			$f = $this->remove_strange_chars($_GET["filter"]);
			if($f == 'read'){
				$s_cond = "activity_type_id=6";
			}
			if($f == 'quizzes'){
				$s_cond = "activity_type_id<>1 and activity_type_id<>6";
			}
			if($f == 'survey'){
				$s_cond = "activity_type_id=1";
			}
		}
		$s = $this->q2obj("
			SELECT activity.*,
					activity.puntaje as activity_value,
					activity_status.name as status,
					activity_type.name as cnomb
			FROM activity, activity2session, activity_type, activity_status
			WHERE activity.activity_status_id = activity_status.id
				AND activity.activity_type_id = activity_type.id
				AND activity2session.activity_id = activity.id
				AND activity2session.session_id ='".$this->get_session_id()."'
				AND activity.id NOT IN (
					SELECT activity.id
					FROM activity, activity2session, activity_type, activity_status, completed_activity
					WHERE activity.activity_status_id = activity_status.id
						AND activity.activity_type_id = activity_type.id
						AND activity2session.activity_id = activity.id
						AND activity.id = completed_activity.activity_id
						AND activity2session.session_id ='".$this->get_session_id()."'
						AND completed_activity.usr_id ='".ssid()."'
						ORDER BY activity_status_id
				)
                ORDER BY activity.order ASC"

		); #fix NEO-20	# activity_status_id='".AS_READY."'
        foreach($s as $k => $act){
            $share = '<ul class="hideshare-list"><li><i class="fa fa-facebook-square fa-1x"></i></li><li><i class="fa fa-twitter-square fa-1x"></i></li><li><i class="fa fa-pinterest-square fa-1x"></i></li>'
                    .'<li><i class="fa fa-google-plus-square fa-1x"></i></li><li><i class="fa fa-linkedin-square fa-1x"></i></li></ul>';
            $s[$k]['share'] = ($act['activity_type_id'] == '8') ? trim($share) : '';
        }
		return $s;
	}
	//*************************************************************************************

	function save_activity_answers($activity){

		if(!is_array($_REQUEST["option"])){
			return;
		}

		# TODO: put this in transaction, otherwise a failed answer insert will prevent re-attempt

		$wdbg = 0; # make it 1 if you want to see debugging data

		$usr_id = ssid();
		$team = $this->remove_strange_chars($_REQUEST["team_id"]);
        
		//insertar en actividad completada actividad poll
		$this->sql("INSERT INTO completed_activity (usr_id, activity_id, activity_date) VALUES('".ssid()."','".$activity["id"]."', '".date("Y-m-d H:i:s")."')");

		#p2($_REQUEST);
		$questions = $this->get_activity_questions($activity["id"]);

		$all_correct = 1;
		$c = 0;
		if($wdbg == 1){
			p2($questions,$red);
			p2($_REQUEST,'red');
		}

		$total_score = 0;
		$total_questions = count($questions);
		foreach($questions as $question_row) {
			$question_id = $question_row['id'];
			//get question options.

			if($_REQUEST['question_type'][$question_id] == 'multiple_answers') {
				$options_arr  = $this->remove_strange_chars($_REQUEST["option"][$question_id]);
			} else {
				if($activity["activity_type_id"] == AT_QUIZ){
					$option_value = $this->remove_strange_chars($_REQUEST["option"][$question_id][0]);
				} else {
					$option_value = $this->remove_strange_chars($_REQUEST["option"][$question_id]);
				}
				$question_option_id = $option_value; //caso poll, wager, spin
			}

			if($activity["activity_type_id"] != AT_ACRONYM ){
				if(!isset($_REQUEST["option"][$question_id])){
					//no response
					echo("This question continues: $question_id");
					continue;
				}
			}

			if($wdbg == 1){
				echo("<br/>The Option Value is: $option_value");
			}

			$question_id = $this->remove_strange_chars($question_id);
			$answer = $this->remove_strange_chars($option_value);
			$score = 0;

			if( in_array($activity["activity_type_id"], array(AT_READ, AT_SCAVENGER)) ) {
				# open ended question, there are no correct answers (options).  score is ignored
				$question_option_id = 0;
				$score = 1;
			}
			else {
				if($activity["activity_type_id"] == AT_QUIZ && $_REQUEST['question_type'][$question_id] == 'multiple_answers') {
					$correct_ops = $this->get_quiz_correct_answers($activity['id'], $activity['activity_type_id'], $question_id);
					if($options_arr == $correct_ops) {
						$score = 1;
					} else {
						$score = 0;
					}
				} else {
					$score = $this->question_is_correct($activity, $question_row, $option_value, $wdbg);
				}

				if( $activity["activity_type_id"] == AT_ACRONYM ) {
					$question_option_id = 0;
					if( $score == 0 ) {
						# TODO: how to get a point even if you're wrong
					}
				}
				else if ($activity['activity_type_id'] == AT_SPIN ) {
					$correct_options = $this->get_correct_answers($activity['id'], $activity['activity_type_id'], $question_row['id']);
					$question_option_id = $score == 1 ? key($correct_options) : 0;
				}
			}

			/*if($activity["activity_type_id"] == AT_WAGER){
				$dbg_old_score = $score;
				//wager people will give a value
				var_dump('In wager if');exit;
				$bet_value = (int)$this->remove_strange_chars($_REQUEST["wager_value"][$c]);
				$answer = $bet_value;
				//you win as much as you betted.
				//if you betted a lot, and got a wrong answer, you get: zero
				//if you get it right and bet a lot, you win a lot!
				$score = $score * $bet_value;
				if($wdbg == 1){
					echo("<br/>Wager Calculated Score is: $dbg_old_score * $bet_value = $score ");
				}

			}*/

			if($activity["activity_type_id"] != AT_WAGER){
				$all_correct *= $score;
				$total_score += $score;
			}else{ //WAGER ONLY
				//var_dump($_REQUEST['wager_total']); exit;
				if(!isset($_REQUEST['wager_total'])) $_REQUEST['wager_total'] = '360';
				$score = $_REQUEST['wager_total'] / $total_questions;
			}

			if( $activity["activity_type_id"] != AT_READ ) {
				$this->sql("INSERT INTO answer (question_option_id, question_id, usr_id, score, answer)
					VALUES('$question_option_id', '$question_id', '$usr_id', '$score', '$answer')");
			}

			$c++;
		}

		# activity value for spin is random
		$activity_value = $activity["activity_type_id"] == AT_SPIN
			? $this->remove_strange_chars($_REQUEST["value"])
			: $activity["puntaje"];

		$user_score = 0;
		if( in_array($activity["activity_type_id"], array(AT_READ, AT_POLL, AT_SCAVENGER)) ){
			$user_score = $activity_value;
		}else if($activity["activity_type_id"] == AT_WAGER) {//WAGER ONLY
			$user_score = $total_score = $_REQUEST['wager_total'];
		}else{
			$user_score = round($activity_value * ($total_score / $total_questions));
		}

		#echo("Total Processed: $c");
		if(isset($_GET['team_id'])) {
            $this->earn_team_points($user_score,$_GET['team_id']);
        }
        
        $rank_changed = $this->earn_points($user_score);
		if($wdbg == 1){
			echo("The GET:");
			p2($_REQUEST);
		}
		if($wdbg == 1){
			echo("total_score=$total_score<br/>
				user_score=$user_score<br/>
				questions=$total_questions<br/>");
			echo("<a href='?mod=usr2session&ac=r_score_read&mm=$all_correct&rank_changed=$rank_changed&total_score=$total_score&user_score=$user_score&questions=$total_questions&activity_val=$activity_value'>continue</a>");
		}else{
			echo("<script>;location.href='?mod=usr2session&ac=r_score_read&mm=$all_correct&rank_changed=$rank_changed&total_score=$total_score&user_score=$user_score&questions=$total_questions&activity_val=$activity_value';</script>");
		}

		//for debugging purposes only:
	}

	/**
	 * just a lookup table
	 * moved to a function because I didn't want to repeat the code
	 * in two places.
	 * */
	function question_is_correct($activity, $question_row, $question_option_id, $wdbg=0){
		$correct_options = $this->get_correct_answers($activity['id'], $activity['activity_type_id'], $question_row['id']);

		$needle = is_array($question_option_id) ? array_map('strtolower', $question_option_id) : strtolower($question_option_id);
		$haystack = array_map('strtolower', array_map('trim', array_values($correct_options)));
		$search_result = in_array($needle, $haystack);

		if($wdbg == 1){
			p2($needle, 'purple');
			p2($haystack, 'red');
			var_dump($search_result);
		}

		if( $search_result !== FALSE ){
			if($wdbg == 1){
				echo("<br/>Question: $question_option_id is correct.");
			}
			return 1;
		}else{
			if($wdbg == 1){
				echo("<br/>Question: $question_option_id is NOT correct.");
			}
			return 0;
		}
	}

	function set_current_tutorial($t){
		$this->current_tutorial = $t;
	}

	function get_current_tutorial(){
		if($this->current_tutorial == ''){
			return "<!--NOTUT-->";
		}
		return $this->get_tutorial($this->current_tutorial);
	}

	function ac_r_run_activity(){
		if(!isset($_REQUEST["activity_id"])){
			echo("error: no activity selected.");
			return;
		}

		if($_REQUEST["activity_id"] == ""){
			echo("error: no activity cannot be empty.");
			return;
		}

		$s = $this->get_activity($_REQUEST["activity_id"]);
		if(count($s) == 0){
			echo("No READY Activities");
			return;
		}

		$completed = $this->q2obj("select * from completed_activity where activity_id='".$s[0]["id"]."' and usr_id='".ssid()."'");
		if(count($completed) != 0){
			echo("<script>;location.href='?mod=usr2session&ac=r_answers&activity_id=".$s[0]["id"]."';</script>");
			return;
		}

		#if is it a team activity?
		if($s[0]["is_team_activity"] == 'Y' && (!isset($_REQUEST["team_id"]))){
			//show team selector.
			$this->s_header();
			$this->s_slider_menu();
			$teams = $this->q2obj("select * from team");

			$t = $this->s_tmpl("team_selector");
			$t = str_replace("[#template_name]", $this->remove_strange_chars($_REQUEST["activity_id"]),$t);
			$t = $this->s_template("team_selector",$teams,'',$t);
            
			echo($t);
			$this->s_footer();
			return;
		}

		if(isset($_REQUEST["pf"])){
			#p2($_REQUEST);
			$this->save_activity_answers($s[0]);
			return;
		}
		#p2($s);

		$at = $s[0]["activity_type_id"];
		$this->set_current_tutorial($s[0]["activity_type_name"]);

		if($s[0]["activity_status_id"] != AS_READY){
			echo("<script>;location.href='?mod=usr2session&ac=r_home';</script>");
			return;
		}

		$this->s_header();

		# slider menu removed for all activities
		#$this->s_slider_menu();

		if(AT_POLL == $at || AT_QUIZ_WC == $at){
			$this->render_poll($s[0]);
			//ya no es POLL.
			// $this->render_shuffle_quiz($s[0]);
		}
		if(AT_QUIZ == $at){
			$this->render_shuffle_quiz($s[0]);
		}
		if(AT_WAGER == $at){
			$this->render_wager($s[0]);
		}
		if(AT_SPIN == $at){
			$this->render_spin($s[0]);
		}
		if(AT_ACRONYM == $at){
			$this->render_acronym($s[0]);
		}
		if(AT_READ == $at){
			$this->s_slider_menu();
			$this->render_read($s[0]);
		}
		if(AT_SCAVENGER == $at){
			$this->render_scavenger($s[0]);
		}
		if(AT_PHOTO == $at){
			$this->s_slider_menu();
			$this->render_photo_op($s[0]);
		}
		if(AT_QR == $at){
			$this->s_slider_menu();
			$this->render_qr($s[0]);
		}
		$this->s_footer();
	}

	function render_photo_op($activity) {
		$this->render_something($activity, "photo_op","photo_op");
	}

	function render_qr($activity) {
		$this->render_something($activity, "scan_code","scan_code");
	}

	function render_navigation_list($questions){
		$i = 1;
		foreach($questions as $k=>$question){
			//navigation logic
			$questions[$k]["display"] = "none";
			$questions[$k]["d_previous"] = "block";
			$questions[$k]["d_end"] = "none";

			if($i==1){
				$questions[$k]["display"] = "block";
				$questions[$k]["d_previous"] = "none";
			}

			if($i==count($questions)){
				$questions[$k]["d_next"] = "none";
				$questions[$k]["d_end"] = "block";
			}

			$questions[$k]["previous"] = $i-1;
			$questions[$k]["current"] = $i;
			$questions[$k]["next"] = $i+1;
			$i++;
		}
		return $questions;
	}

	function get_activity_questions($activity_id){
		$q = $this->q2obj("select question.*, category.name as category from question,category where category_id = category.id and activity_id='$activity_id' order by id");
		foreach($q as $k=>$row){
			$q[$k]["description"] = preg_replace("/\[([^\]]+)]/", "<img style='max-width:100%' src='\\1' alt='BBCODE'>", $q[$k]["description"]);
            $q[$k]["image"] = $row["imagen"] ? '<img src="' . $row['imagen'] . '" border="0">' : '';
		}
		return $q;
	}

	function get_correct_answers($activity_id, $activity_type_id, $question_id) {
		$search_term = in_array($activity_type_id, array(AT_ACRONYM, AT_SPIN)) ? 'name' : 'id';

		$select = "select question_option.id, question_option.name from question_option, question ";
		$where = "where question_id = question.id and activity_id = '".$this->remove_strange_chars($activity_id)."'";

		if( $activity_type_id == AT_SPIN ) {
			$where = "where question_id = question.id and question.id = " . $this->remove_strange_chars($question_id);
		}

		$query = "$select $where and question_option.is_correct = 'Y'";

		return $this->q2op($query, 'id', $search_term);
	}

	function get_quiz_correct_answers($activity_id, $activity_type_id, $question_id) {
		$query = 'SELECT qo.* FROM question_option AS qo LEFT JOIN question AS q on qo.question_id = q.id WHERE qo.question_id = ' . $this->remove_strange_chars($question_id) . ' AND q.activity_id = ' . $this->remove_strange_chars($activity_id) . " AND qo.is_correct = 'Y'";
		$ca = $this->q2op($query, 'id', 'id');

		$result = array();
		foreach($ca as $k => $a) {
			$result[] = $a;
		}

		return $result;
	}

	function render_poll($activity) {
		$this->render_something($activity, "single_question","poll");
	}

	function render_something($activity, $single_template, $list_template) {
		
		// echo '<pre>';
		// print_r($activity);
		// echo '<pre>';
		// exit;
        
		$aid = $activity["id"];
		$questions = $this->get_activity_questions($aid);
        
		$q_count = count($questions);
		$n = 1;
		//var_dump($questions); exit;
		foreach($questions as $k=>$question){
            
			$qid = $question["id"];
			$ops = $this->q2obj("select question_option.* from question_option where question_id='$qid'");
			//var_dump($ops);
            $questions[$k]["activity_count"] = $q_count;
			$questions[$k]["activity_value"] = $activity["activity_value"];
			$questions[$k]["puntaje"]        = $activity["puntaje"];
            $questions[$k]["question_number"] = $n;
//			$questions[$k]["question_number"] = $question['question_number'];
			$questions[$k]["timer"] = $activity["timer"];

			if($single_template == "single_shuffle"){
				//only for shuffle
				$question_height = 64;//this may change
				$ball_padding = 10;
				$left_padding = 20;
				$ball_width = 50;
				$offset = 480; //top height
				$questions[$k]["left"] = $left_padding + ($n-1) * ($ball_width + $ball_padding);
				$questions[$k]["top"] = $offset;
			}

			$op_number = 1;

			$balls = array();

			foreach($ops as $k_op=>$op_row) {
				if($ops[$k_op]["is_correct"] == 'Y') {
					array_push($balls, $ops[$k_op]);
				}
			}

			foreach($ops as $k_op=>$op_row) {
				$ops[$k_op]["op_number"] = $op_number;
				$ops[$k_op]["row_height"] = 82 / count($ops);

				if(count($balls)>1) {
					$ops[$k_op]["op_input_type"] = 'checkbox';
				} else {
					$ops[$k_op]["op_input_type"] = 'radio';
				}

				$op_number++;
			}
			#p2($ops);

			// echo '<pre>';
			// print_r(array_count_values(array_column($ops, 'is_correct')));
			// echo '</pre>';
			// exit;

			$op = $this->s_template($single_template, $ops, "");
			$ba = $this->s_template("single_ball", $balls, "");

			if(count($balls)>1) {
				$questions[$k]["question_type"] = 'multiple_answers';
			} else {
				$questions[$k]["question_type"] = 'single_answer';
			}

			$questions[$k]["options"] = $op;
			$questions[$k]["balls"]   = $ba;
			$n++;
		}
        
		$questions = $this->render_navigation_list($questions);

		echo($this->s_template($list_template, $questions, $activity["name"]));
	}

	function render_shuffle_quiz($activity) {
		$this->render_something($activity, "single_shuffle", "shuffle_quiz");
	}

	function render_wager($activity) {
		if( isset($_REQUEST['id']) && isset($_REQUEST['value']) ) {
			return $this->render_something($activity, "single_wager", "wager_answers24");
		}

		$this->render_something($activity, "single_wager", "wager");
	}

	function render_spin($activity) {
		if( isset($_REQUEST['value']) ) {
			return $this->render_something($activity, "single_spin", "./pregunta.php");
		}
		$this->render_something($activity, "single_spin", "spin_quiz");
	}

	function render_acronym($activity) {
		$this->render_something($activity, "single_acronym","acronym");
	}

	function render_read($activity) {
		$this->render_something($activity, "single_read", "read");
	}

	function render_scavenger($activity) {
		$this->render_something($activity, "single_scavenger","scavenger");
	}

	function onboarding_base(){
		$this->std();
		$a = array("r_engage",
			"r_run_activity",
			"r_read",
			"r_watch",
			"r_home",
			"r_login",
			"r_register",
			"r_register2",
			"r_",
			"r_main",
			"r_answers",
			"r_score_read",
			"r_leader_board",
			"r_logout",
			'r_questions_and_comments',
			'r_write',
			'r_message_sent',
			'r_view_message',
			'r_settings',
			'r_scavenger_camera',
			'r_scavenger',
			"r_welcome",
			"r_firstwellcome",
			'r_photo_op',
			'r_take_photo',
			'r_take_photo_choose',
			'r_photo_upload');

		if(!is_array($this->restrictions)){
			$this->restrictions = array(
				'allow'=>array(),
				'deny'=>array()
			);
		}
		if(!is_array($this->restrictions["allow"])){
			$this->restrictions["allow"]=array();
		}

		foreach($a as $ac){
			$this->disable_headers[$ac]=$ac;
			$this->restrictions["allow"]["ac_$ac"]="ac_$ac";
		}
		$this->add_public_module("usr2session");
		$this->add_public_module("usr");
		$this->add_public_module("skin");
		#$this->default_module='usr2session';
		#$this->default_action='r_';

		#$this->add_public_module("usr2session");
		$this->current_tutorial = "";
	}

	/*
	 * http://stackoverflow.com/questions/1416697/converting-timestamp-to-time-ago-in-php-e-g-1-day-ago-2-days-ago
	 * */
	function time_elapsed_string($datetime, $full = false) {
		#date_default_timezone_set('America/Los_Angeles');
		$now = new DateTime();
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		#$diff->w = floor($diff->d / 7);
		#$diff->d -= $diff->w * 7;

		$string = array(
			'y' => 'year',
			'm' => 'month',
			#'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}

	/**
	* Transforms an under_scored_string to a camelCasedOne
	*/
	function camelize($scored) {
		return lcfirst(
			implode('',
				array_map('ucfirst',
					array_map('strtolower',
						explode('_', $scored)))));
	}

	/**
	* Transforms a camelCasedString to an under_scored_one
	*/
	function underscore($cameled) {
		return implode('_',
			array_map('strtolower',
				preg_split('/([A-Z]{1}[^A-Z]*)/', $cameled, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY)));
	}
}

?>