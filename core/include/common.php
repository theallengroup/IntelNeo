<?php
/** number of common() & derivaqes instances */
global $common_count;


/**indicates  how many times has the common::table() function has been called.
 * @see common::table()
 *
 * */
$table_call_count=0;
$std_jsc=array();
$std_cssc=array();

/**  \brief everyone subclasses this one.
 * Common functionality, and important function like common::fmt(), and  common::table() are here.
 * */
class common {
	//php5 compat
	function __tostring(){
		return("Object:".get_class($this));
	}

	var $template_error='';
	var $default_style='shadow';		///< default style indicates the kind of borders you want arround some elements @see std::ed()

	/** 
	 * @param $tfile full path to file
	 * @param $t an array
	 * this requires output bufering ON (i think)
	 * */
	function template($tfile,$t=array()){
		if(!file_exists($tfile)){
			die('template file missing: ['.$tfile.'] ');
		}
		ob_start();
		include($tfile);
		$t2 = ob_get_contents();
		ob_end_clean();
		return($t2);
	
	}
	/**
	 * \todo 254 css component, seeks in local, then shared media folder.
	 *
	 * jsc: javascript component
	 * this will put stuff from the core/shared/components in your code, and will not write a component twice 
	 * (because we all know what happens when you re-define stuff, right  (we don't want that))
	 * @patram $compoment the component name (please leave .js off)
	 * example
	 * @code
	 * $this->jsc('combobox');
	 * $this->jsc('combobox');
	 * $this->jsc('combobox');
	 * //causes no error
	 * @endcode
	 * */
	function jsc($component){
		echo($this->get_jsc($component));
	}
	function cssc($component){
		echo($this->get_cssc($component));
	}
	/** 
	 * @see common::jsc()
	 * */
	function get_cssc($component){
		global $std_cssc;
		if(!array_key_exists($component,$std_cssc)){
			$std_cssc[$component]=$component;
			return("\n".'<link rel=stylesheet tyle="text/css" href="'.MEDIA_DIR.$component.'.css'.'" />');
		}else{
			//std::log('do not re-output:'.$component,'WARNING');
			return('');
		}
	
	}
	/** 
	 * @see common::jsc()
	 * */
	function get_jsc($component,$force=0){
		global $std_jsc;
		if(!array_key_exists($component,$std_jsc) || $force==1){
			$std_jsc[$component]=$component;
			return("\n".'<script type="text/javascript" src="'.JS_DIR.$component.'.js'.'"></script>');
		}else{
			//std::log('do not re-output:'.$component,'WARNING');
			return('');
		}
	
	}
	/** 
	 * see form::input_protected()
	 * */
	function save_protected($key,$value){
		if(!array_key_exists('form',$_SESSION)){
			$_SESSION['form']=array();
		}
		$_SESSION['form'][$key]=$value;
	}
	/** 
	 * see form::input_protected()
	 * */
	function protect_unique($text){
		return($text.md5('std_protected'.date('YmdHis').rand(0,10000)));
	}
	/**
		\todo title, tooltip, etc.
		\todo use this EVERYWHERE.
		\todo samples
		@code
		$this->make_link(array('mod'=>$this->table,'ac'=>'b2l'),'hit me','css_class'));
		@endcode

	 */
	function make_link($options,$text,$class='link',$title='',$events=array()){
		#p2($options);
		$js=' ';
		if(is_array($events)){
			foreach($events as $name=>$ev){
				$js.='on'.$name.'=\''.$ev.'\' ';
			}
		}
		$txt="<a class='standard_link make_link $class' href='?";
		$l=array();
		foreach($options as $k=>$v){
			$l[]="$k=$v";
		}
		#echo("return:".$txt.implode("&",$l)."'>".$text."</a>");
		return($txt.implode("&",$l)."'$js>".$text."</a>");
	}


	/**\brief retrieves the user's IP (123.123.123.123)
	 * from base_class.php:get_user_ip()
	 * */
	function get_ip(){
		global $HTTP_SERVER_VARS, $HTTP_ENV_VARS;
		$ip = (!empty($HTTP_SERVER_VARS['REMOTE_ADDR'])) ? $HTTP_SERVER_VARS['REMOTE_ADDR'] : ((!empty($HTTP_ENV_VARS['REMOTE_ADDR'])) ? $HTTP_ENV_VARS['REMOTE_ADDR'] : getenv("REMOTE_ADDR"));
		$ip = preg_replace("/[^\.0-9]+/", "", $ip);
		return substr($ip, 0, 50);
	}
	/** \brief formats a string, and inserts 
	 *
	 * fmt stands for format, 
	 * @param $fmt the format (a [# b ])
	 * @param $arr array of elements to be inserted in $fmt
	 * 
	 * @param $c a character to precede the items in the format string, for instance, if $c=='f', then [# f0 ] matches, 
	 * but [# 0 ] doesn't. this is is useful because you might want to do the string replacing in several steps, or have
	 * the arrays separated in different groups (like, for instance, labels and inputs, if it were an HTML form.)
	 * 
	 * example
	 * \code
	 * echo($this->fmt("<br/>[#1][#2][#1][#0]",array(100,101,102,103)));
	 * \endcode
	 * */
	function fmt($fmt,$arr,$c=""){
		#where it belongs.
		//std::plog(debug_backtrace());
	//	if(!is_array($arr)){
	//		echo('error:['.$arr.'] is not an array');
	//		p2(debug_backtrace());
		//	}


		//any use?
		if($fmt==''){return('');}
		
		if(!is_array($arr)){
			std::log('ERROR: common::fmt() $arr is not array. fmt=['.$fmt.'] c=['.$c.'] log:'.b2(),'ERROR');

		}
		if(array_key_exists('__display',$arr) && $arr['__display']==0){
			return('');
		}
		return(preg_replace("/\[#$c(.*?)\]/e","\$arr[\"\\1\"]",$fmt));/*please, dont re-write so fucking often */
		//return(preg_replace("/[#$c(.*?)]/e","\$arr[\\1]",$fmt));/*please, dont re-write so fucking often */
	}
	/** \brief limits a string to a certain size, and adds "..." if the string is avobe the size.
	 *
	 * will turn VERYLONGTEXT into VERYLONGTE...
	 * @param $txt the string to trim
	 * $len how long will the max string be (not counting the final ...)
	 * */
	function cut($txt,$len){
		if(strlen($txt)>$len){
			$txt=substr($txt,0,$len)."...";
		}
		return($txt);
		}
	/** 
	 * @brief adds the contents of a file into a string 
	 * (much like a server side include, but with a different format)
	 *
	 *
	 * will take a string like "askdhkasjdhkasd[#inc_fuck]asd" 
	 * and include the contents of the file "./templates/fuck.html"  onto it. 
	 *
	 *
	 * @param $fmt the string to be formatted
	 * @param $c use "inc_"
	 * @param $path the path to the files, default to ./templates, needs no trailing slash.
	 * */
	function parse_add_files($fmt,$c='',$path='./templates'){
		return(preg_replace("/\[#$c(.*?)\]/e","(file_exists(\"".$path."/\\1.html\"))?file_get_contents(\"".$path."/\\1.html\"):\"Uknown file:".$path."/\\1.html @ \".getcwd()",$fmt));
	}	
	/**
	 * \brief a minimal templating system, with recursive tree parsing capabilities.
	 *
	 * if something goes wrong, you will be reported trough common::template_error $this->template_error
	 *	cs 1 and cs2 are  '#' and "#\r\n"
	 *	or html comments
	 *	comes from v2.2.2/include/base.common.php
	 *	filters...? handled by the caller (wtf am i thiking of?)
	 *	I want a: array of keys, containing, header, footer, and content: which must be an array of stuff.
	 *	@param $data a tree
	 *	@param $template a (long?) string
	 *	@param $name the name of the MAIN tree entry (usually CONTENT (all caps))
	 *	@param $cs parser start indicator
	 *	@param $cs2 parser end indicator
	 *
	 *	Sample Usage
	 *	@code
	 	$tree=array('isnode'=>1);
		$tree['HEADER']=array();
		$tree['FOOTER']=array();
		$tree['CONTENT']['LOOP']=array(
			'HEADER'=>$this->get_i18n_array();,
			'FOOTER'=>$this->get_i18n_array();,	
			'CONTENT'=>$a,
			'isnode'=>1,
		);
		echo($this->parse_template($tree,$tmpl,'CONTENT','_'));

		@endcode
			sample template 
			@code
			<!--CONTENT_START-->
				<ul>
					<!--LOOP_START-->
						<li>[#_name]
					<!--LOOP_END-->
				</ul>
			<!--CONTENT_END-->
			@endcode
	 */
	function parse_template($data,$template,$name='CONTENT',$char='_',$cs='<!--',$cs2="-->"){

		#$template=$this->fmt($template,'[FILE GOES HERE]','INC_');

		$result='';
		$tmpl=preg_split('('.$cs.$name.'_START'.$cs2.'|'.$cs.$name.'_END'.$cs2.')',$template);
		///dbg echo('<br />just found 3 parts of:'.$name.' .');
		
		$times=0;
		if(count($tmpl)==3){
			$times=1;
		}elseif(count($tmpl)%2==1 && ( count($tmpl) != 1 )){
			#this means the block repeats, multiple times.
			#how on the fucking earth are we onna parse that?
			$times=(count($tmpl)-1)/2;	
			//if 5: 2 times, if 9:4 times., header is taken into consideration, until footer arives.
		#	echo("will parse:$times times");
		}else{
			#Syntax error, something is missing.
			$error_msg='WARNING: '.$name.'_START or '.$name.'_END not found!.';
			$this->template_error=$error_msg;
			return($error_msg);
		}
		//dbg echo('<br />echo the fisrt part of '.$name.'<xmp>'.$tmpl[0].' :: '.print_r($data['HEADER'],1).'</xmp>');


		while($times>0){
			
			//what if HEADER is not an array (or plain nothing, for that matter?)
			if(!is_array($data['HEADER'])){
				$data['HEADER']=array();
			}

			$result.=$this->fmt($tmpl[0],$data['HEADER'],$char);	//header
		
			foreach($data['CONTENT'] as $k=>$v){	

				if(array_key_exists('cases',$data)){
					$all=explode('<!--CASE-->',$tmpl[1]);	//always?:TODO allow #CASE#
					if(count($all)==$data['cases']){
						if(array_key_exists('__case',$v)){
							if(array_key_exists($v['__case'],$all)){
								$txt=$all[$v['__case']];	//special item __case
							}else{
								$error_msg='WARNING: no such case:'.$v['__case'].' at item:'.$k.' of '.$name;
								$this->template_error=$error_msg;
								return($error_msg);
							}	
						}else{
							$this->template_error=$error_msg='WARNING: MISSING __case at item:'.$k.' of '.$name;
							return($error_msg);
							
						}
					}else{

						$this->template_error=$error_msg='WARNING: CASE MISSING:'.$name.' found ';
						return($error_msg);
					}
		
				}else{
					$txt=$tmpl[1];
				}
				if(array_key_exists('isnode',$v)){
					//echo("branching on:".$k);
					$result.=$this->parse_template($v,$txt,$k,$char,$cs,$cs2);	//branch content
				}else{
					$result.=$this->fmt($txt,$v,$char);	//leaf content
				}	
			}
			//dbg echo('<br />echo the LAST part of '.$name.'<xmp>'.$tmpl[2].' :: '.print_r($data['FOOTEr'],1).'</xmp>');
			$times--;
			#shift data, so next "token" is parsed
			#the following removes the first 2 elements, so we can so the same thing over.
			array_shift($tmpl);	
			array_shift($tmpl);	
		}
		#echo("<br />finally, im doing this(there should be a 2 in here):");p2($tmpl);

		#the final element of the array, becomes the 0th element, due to shifting, 
		#dont be fooled, this is the final one:
		
		if(!is_array($data['FOOTER'])){
			$data['FOOTER']=array();
		}

		$result.=$this->fmt($tmpl[0],$data['FOOTER'],$char);	//footer
		return($result);
	}#end parse_template 

	/** \brief writes the contents of $somecontent to $filename and creates it if $create_if_not_exists is set to 1.
	 * 
	 * this is a shortcut, to fopen,fwrite and fclose. since in php4.4 there is no file_put_contents()
	 * @param $mode file write mode, defaults to "w+"
	 * @param $filename the name of the file to be written to/created
	 * @param $somecontent a string with the file contents
	 * @param $silent wether or not to show an error to the user (useful, when a called a LOT of times,and you don't want to display 100 error messages )
	 * you can still check for errors, using the function's return code.
	 * @param $create_if_not_exists when this is set to 1, it will attempt create the file, if it does not exist.
	 * 
	 * \returns 1 on success and 0 on faluire.
	 * */	
	function file_write($filename,$somecontent,$silent=0,$create_if_not_exists=0,$mode='w+'){		#{{{2
		#filename, some content			//$filename = 'a_include/conf.php';//make editable
		//$somecontent = $res;
		if(is_writable($filename)) {
		}else{
			if($create_if_not_exists==1){
				$a=copy(SHARED_MODULES_DIR.'void.txt',$filename);//kinda hacky, TODO fin a better solution, for file creation.
			}else{
				$a=0;
			}
			if(!$a){
				if(!$silent ){  $this->error('Error3' .$filename.'Err4' );}
				return(1);
			}	
		}	
		if(!$handle = fopen($filename, $mode)) {
			if(!$silent ){ $this->error('Error1'."($filename)");}
			return(0);
			}
		if(fwrite($handle, $somecontent) === FALSE) {
			if(!$silent ){  $this->error("Error2($filename)");}
			return(0);
		}
		if(!$silent ){  $this->error('Wrote OK' );}
		return(1);
			
		fclose($handle);
	}#2}}} end file_write

	/**
	 * your old EX2 pal
	 *
	 * turns array(a,b,c) into array(a=>a,b=>b,c=>c),
	 * very useful when used with the input.list widget of form()
	 * @see form::input_list()
	 */
	function aa($arr){
		
		$vs=array();
		if(!is_array($arr)){
			return($arr);
		}
		foreach($arr as $k=>$v){
			$vs["$v"]=$v;
		}
		return($vs);
	}
	/**
	 * DEPRECATED?
	 * goddamn rat!
	 * stop cheating!
	 * this was ridiculously easy to do.
	 */
	function group_by($data,$collumn){

		$result=array();
		foreach($data as $k=>$row){
			if(!array_key_exists($row[$collumn],$result)){
				$result[$row[$collumn]]=array();
			}
			$r2=$row[$collumn];
			unset($row[$collumn]);
			$result[$r2][]=$row;
		}
		return($result);
	}
	/**
	 * this is almost not used...
	goddamn rat!
	stop cheating!
	this was ridiculously easy to do.
	*/
	function group_by2($data,$collumn,$collumn2){

		$result=array();
		foreach($data as $k=>$row){
			if(!array_key_exists($row[$collumn],$result)){
				$result[$row[$collumn]]=array();
			}
			$r2=$row[$collumn];
			unset($row[$collumn]);
			$result[$r2][]=$row[$collumn2];
		}
		return($result);
	}
	/**
	 * fills with zero's in the left an array ($list), with as many as $length zero's
	 * */
	function zero_pad($list=array(),$length=2){
		$na=array();
		foreach($list as $k=>$v){

			$p=str_pad($v,$length,str_repeat('0',$length),STR_PAD_LEFT);
			$na[$p]=$p;
		}
	
		return($na);
	}
	/** \brief sends a nice error messahe to the user, with lots of HTML
	 * \todo LOGGING.
	 *
	 \todo errorlevel, send email, error LOG
	 \todo add to db, file whatever
	 \todo allow error_ class namespace to be customizable
	 */
	function error($description,$code=''){
		global $i18n_std;
		if(DEBUG){
			$dbg2="
				<br><a href='javascript:std_toggle_element(\"error_msg\")'>Trace</a>
				<a href='javascript:std_toggle_element(\"object_msg\")'>Object</a>
				<div style='display:none' id=error_msg>".b2(2)."</div>
			<div style='display:none' id=object_msg>".gp2($this)."</div>";
		}
		if(isset($_GET["__output_type"]) && $_GET["__output_type"]!='HTML'){
			echo("***". $code.':'.$description."***");
			return('');
		}
		if($code==''){
			$header=$i18n_std['error']['header'];
		}else{
			$header=$i18n_std['error']['header_code'].$code;
		}

		$error_img = $this->shadow_config['error_image_location'];
		if($error_img ==''){
			$error_img = 'error.gif';
		}
		$this->shadow($this->fmt($this->get_template_contents('error'),array(
			'cssc'=>$this->get_cssc('error'),
			'header'=>$header,
			'error_img'=>MEDIA_DIR.$error_img,
			'description'=>$description,
			'debug'=>$dbg2,
			),'_'),'round','center');
	}

	/** \brief template inheritance system
	 *
	 * attempt to get a /template/*.html file
	 * followed by an attempt to get a SHARED_DIR/templateS/*.html file
	 * @param $template_name a template file without .html extension.
	 *
	 * */
	function get_template_contents($template_name){
		global $config;
		$template_file=$this->get_skin_folder().'/'.$template_name.'.html';
		#$template_file='./template/'.$template_name.'.html';
		if(!file_exists($template_file)){
			$template_file='./template/'.$template_name.'.html';
			if(!file_exists($template_file)){
			std::log('missing: ./template/'.$template_name.'.html','ERROR');
			$template_file=SHARED_MODULES_DIR.'templates/'.$template_name.'.html';

			}		
		}
		return(file_get_contents($template_file));
	}

	/**
	 * prints the contents of get_msg() @see common:get_msg()
	 * */
	function msg($txt,$title='notitle'){
		#TODO beutify, put in template.
		#STATIC
		global $i18n,$i18n_std;
		echo($this->get_msg($txt,$title));
	}
	function get_warning($text){
		if($text==''){
			return('');
		}else{
			return('<div class="standard_text warning_container warning_text" >'.$text.'</div>');
		}
	}
	/**
	 * returns a nice message to the caller, with an interrogation image on the left, and rounded borders
	 * $title is an optional title
	 * @TODO 9201093 beutify, put in template.
	 * */
	function get_msg($txt,$title='notitle'){
		
		#STATIC
		global $i18n,$i18n_std;
		if($title=='notitle'){
			$title=$i18n_std['msg']['message'];
		}
		//p2($this->shadow_config);
		return($this->get_shadow("<table >
			<tr><td colspan=2 class='standard_text warning_title'>".$title."</td></tr>
			<tr><td><img src='".MEDIA_DIR.$this->shadow_config['warning_location']."'><td class='standard_text warning_text'>".$txt."</td></tr>
			</table>",$this->shadow_config['msg'],'center')
		);

/*		return("<table class='msg_table standard_container cool_container' >
			<tr><td colspan=2 class='standard_text warning_title'>".$title."</td></tr>
			<tr><td><img src='".MEDIA_DIR.$this->shadow_config['warning_location']."'><td class='standard_text warning_text'>".$txt."</td></tr>
			</table>");
 */
	}

	/** 
	 * \todo help count instances that derive from me. 
	 * */
	function common(){
		global $common_count;
		$common_count++;
	//	std::log($common_count.':'.$this->table,'PROFILE');
	//	std::log(b2(),'PROFILE');
		//$this->table(debug_backtrace(),'',array('style'=>'list'))
			
	}

	function i_error($error_string,$error_code,$error_stuff=array()){
		#saves all the boilerplate stuff of i18n
		#and prints an error message.
		#
		global $i18n_std,$i18n;
		
		if(is_array($i18n[$this->table]['error']) && array_key_exists($error_string,$i18n[$this->table]['error'])){
			$es=$i18n[$this->table]['error'][$error_string];

		}elseif(array_key_exists($error_string,$i18n_std["error"])){
			$es=$i18n_std["error"][$error_string];
		}else{
			$es='NOT_FOUND:'.$error_string;
		}

		$this->error($this->fmt($es,$error_stuff,''),$error_code);
	}
	/**
	 * i18n (internationalized) message
	 * */
	function i_msg($message,$message_stuff=array()){
		#saves all the boilerplate stuff of i18n
		#kinda repeated from i_error...
		echo($this->get_i_msg($message,$message_stuff));
	}
	function fmt_msg($message,$message_stuff=array()){
		global $i18n_std;
		if(is_array($i18n[$this->program_name()]['msg']) && array_key_exists($message,$i18n[$this->program_name()]['msg'])){
			$es=$i18n[$this->program_name()]['msg'][$message];
		//old system \todo find and destroy
		}elseif(array_key_exists($message,$i18n_std["msg"])){
			$es=$i18n_std["msg"][$message];

		//new system
		}elseif(array_key_exists('form_'.$message,$i18n_std["list"])){
			$es=$i18n_std["list"]['form_'.$message];
		}else{
			$es=$this->get_i18n_text($message,'msg_');
		}
		return($this->fmt($es,$message_stuff));
	}	
	/**
	 *		//old system \todo find and destroy
	 * */
	function get_i_msg($message,$message_stuff=array()){
		#saves all the boilerplate stuff of i18n
		#kinda repeated from i_error...

		return($this->get_msg($this->fmt_msg($message,$message_stuff)));
	}

	///@defgroup shadow Shadow Utilities
	///@{
	
	/** returns with a slash at the end */
	function get_skin_folder(){
		global $config;
		$f1 = './skins/'.$config['skin_name'];
		if(!file_exists($f1)){
			$f1 = STD_LOCATION.'shared/skins/'.$config['skin_name'];
		}
		return($f1.'/');
	}
	/** \brief html required to start a shadowed section
	 *
	 * \returns the html code required to make an shadwed section.
	 * its basicaly a Table, with images on the corners.
	 * \todo 1130 p10 add support for iframe fix borders.
	 * \todo 1131 p10 fix width handling
	 * \param $width has not any effect.
	 * */
	function get_shadow_start($shadow_folder='shadow',$align='left',$width=''){
		global $config;
		
		if($shadow_folder=='shadow' || $shadow_folder == 'round'){
			$w1=' width=16 ';
			$h1=' height=16 ';
			$wh1=' width=16 height=16 ';
		}else{
			$w1='';
			$h1='';
			$wh1='';
		}

		//skin support
		if($config['skins_enabled']==1){
			$images_folder=$this->get_skin_folder().'/images';
			ob_start();
			include($this->get_skin_folder().'shadow_start.php');
			$s = ob_get_contents();
			ob_end_clean();
			return($s);
		}

/*		//style="width:'.$width.'"
return('<div style="border: 1px solid black;align:'.$align.'">
<table class="shadow" border="0"  cellspacing="0" cellpadding="0" align="'.$align.'" ">
<tr>
<td class="shadow-topl"></td>
<td class="shadow-top"></td>
<td class="shadow-topr"></td></tr>
<tr>
<td class="shadow-l"></td>
<td class="shadow-center">');	
 */		
		//width:'.$width.'
		//style="border-color:black;border-width:0px;border-style:solid"
		if($shadow_folder=='none'){
			return('<'.$align.'>');
		}
		return("<$align>".'
			<!--Shadow Start-->
		<table class="'.$shadow_folder.'" border="0"  
		    cellspacing="0" cellpadding="0" >  
		  <tr>
		    <td '.$wh1.' class="'.$shadow_folder.'-topLeft"></td>
		    <td '.$h1.' class="'.$shadow_folder.'-top"></td>
		    <td '.$wh1.' class="'.$shadow_folder.'-topRight"></td>
		   </tr>
		   <tr>
		     <td '.$w1.' class="'.$shadow_folder.'-left"></td>
		     <td class="'.$shadow_folder.'-center">
		     ');	
		 
		
	}
	function get_shadow_end($shadow_folder='shadow',$align='left',$width=''	){
		global $config;

		
/*return('</td>
<td class="shadow-r"></td></tr>
<tr>
<td class="shadow-botl"></td>
<td class="shadow-bot"></td>
<td class="shadow-botr"></td></tr>
</table></div>');
 */
		if($shadow_folder=='none'){
			return('<'.$align.'>');
		}
		if($shadow_folder=='shadow' || $shadow_folder == 'round'){
			$w1=' width=16 ';
			$h1=' height=16 ';
			$wh1=' width=16 height=16 ';
		}else{
			$w1='';
			$h1='';
			$wh1='';
		}

		//skin support
		if($config['skins_enabled']==1){
			$images_folder=$this->get_skin_folder().'/images';
			ob_start();
			include($this->get_skin_folder().'shadow_end.php');
			$s = ob_get_contents();
			ob_end_clean();
			return($s);
		}

		return('
		     </td>
		     <td '.$w1.' class="'.$shadow_folder.'-right"></td>   
		   </tr>
		   <tr>
		     <td '.$wh1.' class="'.$shadow_folder.'-bottomLeft"></td>
		     <td '.$h1.' class="'.$shadow_folder.'-bottom"></td>
		     <td '.$wh1.' class="'.$shadow_folder.'-bottomRight"></td>
		   </tr>
		</table><!--Shadow End-->'."</$align>");
	}
	function shadow_start($shadow_folder='shadow',$align='left',$width=''){
		//echo(common::get_shadow_start($shadow_folder,$align,$width));

		echo($this->get_shadow_start($shadow_folder,$align,$width));
	}
	function shadow_end($shadow_folder='shadow',$align='left',$width=''){
//		echo(common::get_shadow_end($shadow_folder,$align,$width));
		echo($this->get_shadow_end($shadow_folder,$align,$width));
	}
	function get_shadow($txt,$shadow_folder='shadow',$align='left',$width=''){
		return(
	//		common::get_shadow_start($shadow_folder,$align,$width).
			$this->get_shadow_start($shadow_folder,$align,$width).
			$txt.
//			common::get_shadow_end($shadow_folder,$align,$width)
			$this->get_shadow_end($shadow_folder,$align,$width)
		);

	}
	/** 
	 * you can override the shadow folder, by creating your own shadows inside /your_project/media, and modifying /media/user.css
	 *
	 * */
	function shadow($txt,$shadow_folder='shadow',$align='left',$width=''){
		echo($this->get_shadow($txt,$shadow_folder,$align,$width));
	}

	///@}
	/**
	 * no longer used
	 * creates _ul and _li
	 * */
	function li($str,$class_path='default_menu'){
		#Recieves an array of objects, and turnst them in a List <ul>
		#echo("li got:");p2($str);
		#classopath:css prefix to apply, css generated classes are <class_path>_li , <class_path>_ul , <class_path>_title ,
		$txt="";
		$txt.="<ul class='${class_path}_ul'>";
		foreach($str as $k=>$v){
			if(is_array($v)){
				$txt.=("<li class='${class_path}_li'><b class='standard_title ${class_path}_title'><br />".$k."</b>".$this->li($v,$class_path));
			}else{
				$txt.=("<li class='${class_path}_li'>".$v);
			}
			
		}
		$txt.="</ul>";
		return($txt);
		
	}
	/** 
	 * @see common::gradient_table()
	 * */
	function e_gradient_table($data,$headers='none',$options=array()){
		echo($this->gradient_table($data,$headers,$options));
	}
	/**
	 * @see common:table() for details
	 * sample usage
	  - options.start : an rgb array (10,20,30)
	  - options.end : an rgb array (10,20,30)

	  Sample usage:
	@code 
	 $c->e_gradient_table(array(
		array(1,2,3),
		array(1,2,3),
		));
	@endcode
	  Sample usage:
	@code 
	$c->e_gradient_table($data,array('Proyecto'),array(
		'style'=>'list',
		'end'=>array(0,0,0),
		'start'=>array(255,255,255),
	));
	@endcode
	 * */
	function gradient_table($data,$headers='none',$options=array()){
		/*
		$options['start']=array(250,250,250);
		$options['end']=array(200,200,200);
		 */
		$inc=array(0,0,0);
		if(count($data)==0){return('');}
		
		foreach($inc as $k=>$v){
			$start=$options['start'][$k];
			$end=$options['end'][$k];
			
			if($start<$end){
				$m=-1;
			}else{
				$m=-1;
			}
			$inc[$k]=$m*(($start-$end)/count($data)) ;
		}
	
		$c=0;
		$styles=array();
		foreach($data as $data_key=>$row){
			$gc=array(0,0,0);

			$gc[0]=round($options['start'][0]+$inc[0]*$c);
			$gc[1]=round($options['start'][1]+$inc[1]*$c);
			$gc[2]=round($options['start'][2]+$inc[2]*$c);
			$color="rgb(".implode(',',$gc).')';
			$styles[$data_key]='background-color:'.$color;
			$c++;
		}
		$options['row_styles']=$styles;
		
		return($this->table($data,$headers,$options));
		
		return($d);
	}
	/**
		\todo group_table
		takes an array, options.data, and creates a nice Table.
	*/
	function group_table($op=array()){
		
		$data=$op['data'];
		$headers=$op['headers'];
		$options=$op['options'];
		$child_headers=$op['child_headers'];
		$child_options=$op['child_options'];
		$txt=array();
		foreach($data as $k=>$group){
			$txt[]=array("<h3>".$k."</h3>");
			$txt[]=array($this->table($group,$child_headers,$child_options));
		}
		p2($txt);
		return($this->table($txt,$headers,$options));
	}
	/**
	 * prints a table, 
	 * @see common::table()
	 * */
	function e_table($data,$headers='none',$options=array()){
		echo(common::table($data,$headers,$options));
	}
	/**
	 * 	\brief displays an HTML Table
	 	for the collapsible elements, a global variable, $table_call_count is used.
		@see $table_call_count

	 	@param data : a 2 dimensional array
		@param headers: array of titles

		@param options an array of options, these are the actual parameters, 
		I use an array, instead of overloading, because it's easier to remember the named parameters,
		as oposed to positional parameters, and also because it's easier to improve and extend this function,
		by adding new parameters and not breaking current functionality.

		simpler to 

		-	row_styles array
		-	column_style array
		-	title: Table title
		-	footer
		-	style (list is good)
		-	message
		-	border
		-	bordercolor
		-	cellspacing
		-	cellpadding
		-	collapsible
		-	status
		-	sortable=[1|0] defaults to 0
		-	expand:	a list of items item=>item that indicates ashich collumns are to be expanded.
		-	nr : no records indicates how to handle the non existance of records: if 1, don't show anything


		Styles defined: 
		- <style>_table 
		- <style>_row 
		- <style>_head 
		- <style>_foot 
		- <style>_cell 
		- <style>_title
		- <style>_message
		* */
	function table($data,$headers='none',$options=array()){
		global $table_call_count;
		$table_call_count++;

		$title=		$options['title'];
		$footer=	$options['footer'];
		$style=		$options['style'];
		$border=	$options['border'];
		$message=	$options['message'];
		$cellspacing=	$options['cellspacing'];
		$cellpadding=	$options['cellpadding'];
		$status=	$options['status'];
		$collapsible=	$options['collapsible'];
		$bordercolor=	(isset($options['bordercolor']))?$options['bordercolor']:null;
		$no_records=	$options['nr'];
		$row_styles=	$options['row_styles'];
		$sortable=	$options['sortable'];
		//echo("<h1>EXPAND INFO:".gp2($options["expand"])."</h1>");
		if(!array_key_exists('expand',$options)){$options['expand']=array();}
		if(!array_key_exists('sortable',$options)){$sortable=0;}


		if(!array_key_exists('column_style',$options)){
			$options['column_style']=array();
		}
		if($no_records==1 && count($data)==0){
			$this->log("NR:".$options['title']);
			return('');
		}
		//p2($options,'red');
		#echo("<xmp>");
		#print_r(debug_backtrace());
		#echo(b2());
		//		std::log('TABLE HERE:'.print_r($headers,true),'COMMON');
		if(!is_array($headers) && $headers=='none'&&is_array($data)){
			if(array_key_exists(0,$data)&&is_array($data[0])){
				$headers=array_keys($data[0]);
			}else{//for keyed table info.
				foreach($data as $k=>$v){
					$headers=array_keys($v);

					break;//only once
				}
			}
		}
		if($sortable==1){
			$column_number=0;
			foreach($headers as $k=>$v){
				$headers[$k]=$v.'<span style="padding-left:5px;float:right" onclick="std_sort_column(this,'.$column_number.',\''.$style.'\',\'ASC\')"><img title="CLICK AQUI PARA ORDENAR" style="cursor:pointer" src="'.MEDIA_DIR.'/sortable/sort.png" alt="&lt;&gt;" /></span>';
				$column_number++;
			}
		}
		
//		std::log('TABLE HERE:'.print_r($headers,true),'COMMON');
//		std::log('TABLE HERE:'.print_r($data,true),'COMMON');


		if(!array_key_exists('bordercolor',$options)){
			$bd='';
		}else{
			$bd=' bordercolor="'.$bordercolor.'" ';
		}

		$r='';	
		$h="\n<table ".$bd.' border="'.$border.'" class="'.$style.'_table" cellpadding="'.$cellpadding.'" cellspacing="'.$cellspacing.'" >';
		
		$st9=array();//custom style for this column.
		$hst9=array();//custom style for this column.
		
		$styles=array();
		foreach($data as $k8=>$v8){
			$h.="\n\t<colgroup>\n";
			$c=0;
			
			foreach($v8 as $k81=>$v81){
				if(!isset($options['column_style'][$k81]) || $options['column_style'][$k81]==''){//E_NOTICE
					$st9[$k81]='';
					$hst9[]='';
				}else{
					$st9[$k81]=' style="'.$options['column_style'][$k81].'" ';
					$hst9[]=' style="'.$options['column_style'][$k81].'" ';
				}
				$h.="\n\t\t<col class='".$style."_cell ".$style."_collumn_".$c."' />";
				$c++;

			}
			$h.="\n\t</colgroup>\n";
			break;
		}
		
		if($collapsible==1){
			$title='<a href=\'javascript:std_collapse("_std_table_'.$table_call_count.'")\'>'.$title."</a>";
		}
		if($collapsible==1 && $status==0){
			$p0=" style='display:none;' ";
		}else{
			$p0='';
		}
		if($headers != 0){
			$r.="\n".'<tr class="'.$style.'_row" >'."\n";
			foreach($headers as $k=>$v){
				$r.="\n\t" . '<th '.$hst9[$k].' class="'.$style.'_head standard_text">'."\n\t".$v."\n\t".'</th>';
			}
			$r.="\n</tr>";
		}
		$tmax=1;
		foreach($data as $k=>$v){
			$row_class = $style.'_row';
			$row_style = '';
			
			if(is_array($row_styles) && array_key_exists($k, $row_styles )){
				$row_style='style="'.$row_styles[$k].'"';
			}

			$r.="\n".'<tr class="'.$row_class.'" '.$row_style.'>';
			$c=0;/*for style stuff*/
			if(array_key_exists($k,$options['expand'])){
				

				///\todo allow ful expandion, not just 2.

				$r.="\n\t<!--block:$k-->\n\t<td colspan=100 class=\"".$style.'_cell '.$style.'_collumn_'.$c.'">'.implode("",$v).'</td>';
				$c=count($v);
				
				
			}else{
				foreach($v as $k1=>$field){
					$r.="\n\t<td ".$st9[$k1]." class=\"".$style.'_cell '.$style.'_collumn_'.$c.'">'.$field.'</td>';
					$c++;
				}
				$tmax=max($c,$tmax);
			}
			$r.="\n".'</tr>';
		}
		if($collapsible==1){
			$iid1=" id=\"_std_table_".$table_call_count."\" ";
		}else{
			$iid1='';
		}
		/* no title means no space! */
		if($title!=''){
			$h.="\n".'<tbody><tr><td colspan='.$tmax.' class="standard_title '.$style.'_title" >'.$title.'</td></tr></tbody>';
		}
		$h.="\n".'<tbody><tr><td colspan='.$tmax.'  '.$style.'_message" >'.$message.'</td></tr></tbody>';
		$h.="\n".'<tbody'.$iid1.' '.$p0.'>';
		
		if($footer!=''){
			$r.="\n".'<tr><td class="'.$style.'_foot"  colspan="'.$tmax.'">'.$footer.'</td></tr>';
		}		
		$r.="\n".'</tbody></table>';
		return($h.$r);
	}
	function a2o($a,$c='stdClass'){
		$r = new $c();
		foreach($a as $k=>$v){
			$r->$k=$v;
		}
		return($r);
	
	}
	function noprint($content){
		return('<span class="noprint">'. $content .'</span>');
	}
	/** 
	 * transpose an bidimentional array.
	 * @param $in a bidimentional array.
	 * @returns another array 
	 * */
	function xpose($in){
		$out = array();
		foreach($in as $col_name=>$col_data){
			foreach($col_data as $row_id=>$cell_data)
			$out[$row_id][$col_name]=$cell_data;
		}
		return($out);
	}
	/**
	 * @param $hidden defaults 0 if cero don't display if one display. 
	 * */
	function toggle_block($handle,$block,$hidden=0){
		$map=array(0=>'none','1'=>'block');
		$mapped=$map[$hidden];
		$dx='';
		$cl = "onclick='toggle_block(\"${handle}_block\",\"".MEDIA_DIR."\")'";
		$dx.="".$this->get_jsc("toggle");
		$dx.="<div class='toggle_block'><table><tr>";
		$dx.="<td valign=middle><img valign = center src='".MEDIA_DIR."icons/down_arrow.png' class=clickable_thing $cl id='${handle}_block_img' /></td>";
		$dx.="<td valign=middle><a  class=clickable_thing href='#' $cl ><nobr>$handle<nobr></a></td></tr></table>\n";
		$dx.="<div class='toggle_block_content' style='display:$mapped' id=\"${handle}_block\">$block</div></div>";
		return($dx);
	}

	


}//common


/**
 *	dump the object in a base.class.php :: print_r2() fashon
 	this function is for debugging purposes only (obviously)
	@see d2()
	@param $obj an array of elements, that may have elements that are also arrays.
	@param $color the color of the output (useful, when you need to call this several times, 
	and you need a certain structure to stand out from the others, or simply to differentiate them)
	color-coding stuff is a great wa to work, consider this:
	@code 
	p2($struct,'red');
	@endcode

		to find the function call, just search "red".
		in case you find yourself lost, you can always sue the line numbers, @see $dinfo


	@param $dinfo see http://www.php.net/debug_backtrace for nifo on debug info., 
		0: no info, 1: function call, 2: full backtrace.
		@param $title simple remarks you can put to aid you in development. i.e.
		@code
		$a=array(1,2,3,5,6)
		
		//this

		p2($a,'',0,'the numbers');

		//is more meaningful than this:

		p2($a);

		@code
		This is particulary true when dealing with many p2()

		It is recomended that you *don't* delete p2()'s calls from your code, 
		but rather document them:
		@code
		
		//insightfull comments about spected output
		//p2($a123,'',1,'Items in list')

		@endcode

		or, if you feel more confortable with logging, use this:
		$this->log(gp2($struct)); @see gp2()

		You can also use p2() as a call trace: helper

		@code
		function a(){
			b();
		}

		function b(){
			c();
		}
		function d(){
			a();
		}

		function c(){
			p2(1,'',2,'backtrace');		
		}


		d();
		@endcode
		
		note the use of p2(1), as a simple backtrace enables, you can use 
		p2($anything,'',2) to display the log, or p2($anything) to show the call route.

		the call route follows the following format:

		@ file : function : function_call_line_number -> current_line_number

		i.e.
		@code
		@ std.php:filter_link():1222->2142
		@endcode

		where current line number is the line where the p2() call is, and "function call number" 
		is the line where the current function was called from (useful as hell).

		--


 * */
	function p2($obj,$color='',$dinfo=1,$title=''){
		//echo();
		$dbg=debug_backtrace();
		if($dinfo!=0){
			//':'.$dbg[1]['class'].
			$dinfo="@".basename($dbg[1]['file']).':'.$dbg[1]['function'].'():'.
				$dbg[1]['line'].'->'.$dbg[0]['line']."\n";
		}
		if($dinfo==2){
			$dinfo.=print_r(debug_backtrace(),true);
		}
		$c2='style="color:'.$color.'"';
		if($color==''){
			$color='rgb(50,50,200)';
			$c2='';
		}
		echo("<h3 style='color:$color'>".$title.'</h3><xmp '.$c2.'>'.
		$dinfo.print_r($obj,1)."</xmp>");
	}
	function gp2($obj,$color=''){
		$dx=($color=='')?'':"style='color:$color'";
		return("<xmp $dx>".print_r($obj,1)."</xmp>");
	}
	function __toString(){
		return("IM AN OBJECT!");
	}
	function hdump($a){
		
		if(is_array($a)){
			if(count($a)==0){
				return('Empty')	;
			}else{
				return(	'<b class=standard_link onclick="std_toggle_next(this)">more ('.count($a).')</b>'.
					'<span style="display:none" >'.gp2($a).'</span>');
			}
		}elseif(is_object($a)){
			return('Object:'.get_class($a));
		}else{
			return($a);
		}

	}
	/**	
	 * a *nice* debug_backtrace()
	 *
	 * @see http://www.php.net/debug_backtrace
	 * The current call type. If a method call, "->" is returned. If a static method call, "::" is returned. If a function call, nothing is returned.
	 * @param $trim_levels how much do you not give a damn about?
	 * */
	function b2($trim_levels=0){
		
		$z=debug_backtrace();
		$c = 0;
		foreach($z as $k=>$v){
			foreach($v as $k1=>$v1){
				$z[$k][$k1]=hdump($v1);
			}
			foreach($v as $k1=>$v1){
				$z[$k][0]=basename($z[$k][0]);
			}
			if($c<$trim_levels){
				unset($z[$k]);
			}
			$c++;
		}
	 	return(common::table($z,
			array('Function','Line','File','Class','Object','Type','Args'),
			array('style'=>'list')).
			implode("\n<br/>",get_included_files())
		);
		

//		return(hdump(array('test',1,2,3,4,5)));
	}
	/**
	 * @see d2()
	 * */
	function d2_recursive($obj,$label='',$color='rgb(100,100,100)'){
		if(is_array($obj)){
			$dx="\n<table style='border:1px solid ".$color."' cellspacing=0 cellpadding=2 width=100%>";
			if($label!=''){
				$dx.="\n<tr><td style='background-color:".$color.";border:1px solid ".$color.
					"' colspan=2>".$label."</td></tr>";
			}	
			foreach($obj as $k=>$v){
				$dx.="\n<tr><td class=standard_text style='width:1%;color:white;font-weight:bold;font-family:verdana;".
					"font-size:10pt;background-color:".$color.";border:1px solid ".$color."'>".$k
					.'</td><td style="border:1px solid '.$color.'">';
				$dx.=d2_recursive($v,'',$color)."</td></tr>";
			}
			$dx.="\n</table>";
		}else{
			$dx=$obj;
		}
		return($dx);
	}
	/** \brief nice html object inspector
	 * @see p2()
	 * */
	function d2($obj,$label='',$color='black'){
		echo("\n<!--DUMP:$label START-->\n".d2_recursive($obj,$label,$color)."\n<!--DUMP END-->\n");
	}

?>
