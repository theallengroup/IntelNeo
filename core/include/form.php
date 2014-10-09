<?php
/** \brief utilities for the creation of HTML forms.
 * with special datatypes not found in HTML like date, etc
 * \todo fieldset+legend, label for field
 * \todo BLOCKs (field.block?)
 * \todo block show/hide
*/

class form extends common{
	var $use_grid_layout=0;/**< nice addition */
	var $columns=0;/**< the most likely you WANT this. left alone, but if you don't, then good luck. */
	var $separator_break_str="\n<br/>\n";/**< the most likely you WANT this. left alone, but if you don't, then good luck. */
	var $no_use_str=0;/**< use var names as labels */
	var $message='';/**< see get_message and set_message */
	var $on_submit='';
	var $required_scripts=array('form');/**< required_scripts a list of scripts that i'll need for form rendering. this exists because you don't want to load stuff you won't use, right?*/
	var $shadow_object=null;
	var $border_style='shadow';
	var $confirm_border_style='banded';
	var $form_end='';/**< this is what gets shown AFTER the form, possibly after the /form tag, and is used by std::get_kids() */
	var $hidden_types=array('protected','separator','hidden','label');
	var $template='';/**< this is the template HTML string, that will affect how the form is rendered */
	var $in_template=0;/**< wether or not to use the template. */
	var $block_count=0;
	var $submit_options=array();
	var $fields=array();	/**< my list of fields.*/
	var $options=array();
	var $req_message='function(){return("");}';	/** function that returns "" or an error message. */	
	var $style='form';	/**< default form style, may change over time.*/
	var $strings=array();	/**< field descriptors*/
	var $has_ok=1;
	var $btn=array();	/**< the actions array*/
	var $collapsible=0;	/**< this means that you can "click the title" to hide/show the form.*/
	var $status=0;		/**< default status is "collapsed", this is only valid when "collapsible" is set to true. */

	/** 
	 * @page form_valid Valid Input Types
	 * $valid_datatypes is an array that indicates which datatypes are valid,
	 * you can add custom datatypes trough form::register_function()
	 * */
	var $valid_datatypes=array(); 
	/** 
	 * HTTP method, can be get,post,head, etc defaults to GET
	 * */
	var $method='GET';
	/** 
	 * avoid usage, use form::add_submit_button instead
	 * */
	var $action='';

	function get_message(){
		return($this->message);
	}
	function set_columns($c){
		$this->use_grid_layout=1;
		$this->columns=$c;
	}
	function set_message($msg){
		$this->message=$msg;
	}
	/**
	 * the options array may contain [strings]
	 * strings should be already internationlalized!
	 * options.buttons, hav buttons (arrays), of array(label,action)
	 * @param $fields  a \ref field_structure "Field Structure"
	 * @param $options an array of options: style,buttons
	 * */		
	function form($fields=array(),$options=array()){
		global $main;
		$this->valid_datatypes=$this->aa(array(
			'password','date','tree','label','list','glob','textarea','radio',
			'checklist','combobox','upload','boolean','rich_text','number','simple','group'));
		$this->fields=$fields;	
		if(array_key_exists('style',$options)){$this->style=$options["style"];	}
		$this->options=$options;
		$this->strings=$this->options["strings"];
		if(array_key_exists('buttons',$options)){
			foreach($options["buttons"] as $k=>$v){
				$this->add_submit_button($v);
			}
		}
		if(is_subclass_of($main,'std')){
			//make sure we have an "std" object
			$this->set_shadow_object($main);
		}else{
			$this->set_shadow_object(new common());
		}
	}
	
	/**\brief destroys all the fields, 
	 * (useful for form reutilization, so instead of creating a lot of forms, 
	 * you can re-use the first form object, reducing the CPU costs of memory allocation, etc)
	 * */
	function drop_fields(){
		$this->fields=array();
	}
	/**
	 * @brief sets the input's vlue to what the user yout typed.
	 * useful in multi-page forms, where you want to keep the user's data.
	 * @param $l an array of input names
	 * @returns nothing
	 * */
	function restore($l,$method='GET'){
		
		if(!is_array($l)){
			std::error($l.' should be array, but is '.typeof($l));
		}else{

			if($method=='GET'){
				$m=$_GET;
			}elseif($method=='POST'){
				$m=$_POST;
			}
			
			foreach($l as $k9=>$v9){
				$this->add_field(array('type'=>'hidden','name'=>$v9,'value'=>$m[$v9]));
			}
		}
	}
	/** \brief displays a confirm dialog, like the one used in ac_delete.
	 * @param $mod module name where the form goes to NOTE: module and table terms can be used interchangeably
	 * @param $action what you do when you get there
	 * @param $text the question to be displayed to the user
	 * @returns nothing
	 * */
	function confirm($mod,$action,$text){
		global $i18n_std;

		$yes=array('answer'=>'yes');
		$no=array('answer'=>'no');
		$this->add_hidden_field('mod',$mod);
		$this->add_hidden_field('ac',$action);
		$this->add_field(array('type'=>'link','name'=>'yes','value'=>'yes','link_options'=>$yes));
		$this->add_field(array('type'=>'link','name'=>'no','value'=>'no','link_options'=>$no));
		$this->strings=$i18n_std['confirm'];
		$this->strings['_form_title']=$text;
		$this->has_ok=0;
		$this->shadow_object->shadow(
			'<div class=\'confirm_request\'>'.
			$this->out().
			'</div>'
			,$this->confirm_border_style,'center','100%');
	}
	/**
	 * \brief displays the form, surrounded by shadowed borders
	 * */
	function shtml(){
		
		$this->shadow_object->shadow($this->out(),$this->border_style,'center','50%');
	}
	/**
	 * \brief displays the form.
	 * if you want to embed the form, in some place, use form::contents() or form::out() instead.
	 * @see contents 
	 * @see out
	 * */
	function html(){
		echo $this->out();
	}
	
	/**
	 * @defgroup inputs Form::Input Handlers
	 * each widget, form input (or select) tag, is created by one of these
	 * additionally, there are special inputs (and the ability to add even more)
	 * these are governed by a @ref form_valid variable:  
	 * */
	//@{
	/** 
	 * I'll write this in the only way I know how to: recursively.
	 * */
	function input_group($field_data){
		$f = new form();
		$final_ds=array();


		$f3 = $field_data["fields"];
		$first_element = array_shift($f3);
		$items = count($first_element["value"]);
		$row_number = 0;
		###p2($field_data["i18n_text"]);
		###p2($items,'red');
		while($row_number < $items){

			$f->fields=array();
			foreach($field_data["fields"] as $field_name=>$field){
				$field["value"]=$field_data["fields"][$field_name]["value"][$row_number];
				if($field["type"] == 'label' && isset($field_data["fields"][$field_name]["display_value"])){
					$field["display_value"]=$field_data["fields"][$field_name]["display_value"][$row_number];
				}
				$fn =$field["name"];
				$field["name"] =$field["name"].'[]';
				$field["raw_label"] =((isset($this->strings[$field_name]))?$this->strings[$field_name]: (isset($field["i18n_text"])?$field["i18n_text"]:$fn));
				$f->add_field($field);
			}
			$b = $f->bare_fields();	

			$final_ds[] = $b["visible_fields"];
			$headers=$b["labels"];
			$row_number++;
		}
		$group_name='';//nil TODO FIX FIX FIX
		require_once(INCLUDE_DIR."table_editor.php");
		$t=new table_editor();
		#p2($field_data,'red');
		$t->has_delete = $field_data["has_delete"];
		$t->has_add = $field_data["has_add"];
		$t->set_data($final_ds);
		$t->set_data($final_ds);
		$t->set_headers($headers); 
		if(isset($field_data["add_link"])){
			$t->add_link=$field_data["add_link"]; 
		}
		$t->set_headers($headers); 
		$t->min_rows=1;
		$t->name=$field_data["name"];
		$t->max_rows=9999;//todo group max
		$this->required_scripts[]='table_editor';
		$dx = $t->out();
		//TODO: if group_can_add==0, else
		#OBSOLETE:
		#$this->table($final_ds,$headers,array('title'=>$group_name,'style'=>'list','border'=>0));
		return($dx);
	}
	
	/**\brief a HTML SELECT input
	 * \todo optgroup
	 * @param $field_data an array field
	 * @returns the HTML required to create a list
	 * 
	 * special parameters
	 * - list_type: "" or multiple
	 * - size: default to 1
	 * - options: a hash table @see std::aa(), 
	 * - values: possible selected options (only valid when list_type == multiple)
	 * */

	function input_list($field_data){
		global $i18n_std;
		//p2($field_data);
		$f=$field_data;
		if(!array_key_exists('list_type',$f)){$f['list_type']='';}
		if(!array_key_exists('size',$f)){$f['size']='1';}
		if(!array_key_exists('values',$f)){$f['values']=array();}
		if(!array_key_exists('value',$f)){$f['value']=null;}
		if(!array_key_exists('required',$f)){$f['required']=0;}

		$txt="<select size='".$f['size']."' ".$f['cdata'].$f['ev1']." ";
		$txt.=" name='".$f["name"]."' title='".$field_data["help_text"]."' ".$f['list_type']." >";
		if($f['required']==1){
			$txt.="\n\t<option value='**INVALID**' $txt1> ".$i18n_std["form"]['please_select']."</option>";
		}
		if(!is_array($f["options"])){
			std::error('No Options for input:<pre>'.print_r($f,true).'</pre>');
		}else{
			foreach($f["options"] as $k1=>$op){
				if(($f['value']!=null && $k1==$f['value']) || ($f['list_type']=='multiple' && in_array($k1,$f['values']))){$txt1=" SELECTED ";}else{$txt1="";}
				$txt.="\n\t<option value='".$k1."' $txt1> ".$op."</option>";
			}
		}	
		$txt.="</select>";
		return($txt);
	}
	/** this might even work the same for CHECKBOX! */
	function input_radio($field_data){
		global $i18n_std;
		//p2($field_data);
		$f=$field_data;
		$separator='<br/>';
		if(!array_key_exists('value',$f)){$f['value']=null;}
		if(array_key_exists('separator',$f)){$separator=$f['separator'];}

		$txt1=$f['cdata'].$f['ev1'];
		$txt='';
		if(!is_array($f["options"])){
			std::error('No Options for input:<pre>'.print_r($f,true).'</pre>');
		}else{
			foreach($f["options"] as $k1=>$op){
				if($f['value']!=null && $k1==$f['value']){
					$txt1=" CHECKED ";
				}else{
					$txt1="";
				}
				$txt.="\n\t<input id='".$f['name']."[".$k1."]' title='".$field_data["help_text"]."' type=radio name='".$f["name"]."' value='$k1'  $txt1> <label for='".$f['name']."[".$k1."]'>".$op."</label>".$separator;
			}
		}	

		return($txt);
	}
	/**
	 * special: accept
	 * */

	function input_upload($field){
		
		return($this->input_label($field));
		//return("<input type=\"file\" name='".$field["name"]."' accept=\"".$field["accept"]."\" ".$field["cdata"].$field["ev1"]." title='".$field["help_text"]."' value='".$field["value"]."'/>");
	}
	function input_boolean($field){
		$field["type"]='list';
		$field["options"]=array('0'=>'NO',"1"=>'SI');
		return($this->input_list($field));
		/*
		if($field['value']==1){
			$dx='CHECKED';
		}else{
			$dx='';
		}
		return("<input type=checkbox name='".$field["name"]."' value='1' ".$field["cdata"].$field["ev1"]."  $dx  title='".$field["help_text"]."' />");
		 */
	}
	function input_hidden($field){
		return("<input type='".$field["type"]."' name='".$field["name"]."' value='".$field["value"]."'  ".$field['cdata'].$field['ev1']."  >");
	}

	function input_textarea($field){
		if(!array_key_exists('rows',$field)){
			$field['rows']=10;
		}
		if(!array_key_exists('cols',$field)){
			$field['cols']=40;
		}

		return("<textarea rows=".$field['rows']." cols=".$field['cols']." ".$field["cdata"].$field["ev1"]." name='".$field["name"]."' title='".$field["help_text"]."' >".$field["value"]."</textarea>");
	}
	/**
	 * this requires you to previously include Dojo stuff
	 * textarea.rows = 10
	 * */
	function input_rich_text($field){
		if(!array_key_exists('rows',$field)){
			$field['rows']=10;
		}
		if(!array_key_exists('cols',$field)){
			$field['cols']=40;
		}
		return("<textarea 
			items=\"bold;italic;underline;strikethrough;\"
			dojoType=\"Editor\" 
			rows=".$field['rows']." cols=".$field['cols']." ".$field["cdata"].$field["ev1"]." name='".$field["name"]."' title='".$field["help_text"]."' >".$field["value"]."</textarea>");
	}
	/** \brief gets the span that surrounds input labels, with title data, etc.
	 * \todo put a small icon, for help here, when help data is found
	 * whenever a MASK attribute is available replacing % with the actual data will be attempted:
	 *
	 * value=text,mask=" this is a %% "
	 * wil lead to a label like:
	 * this is a test
	 *
	 * it works like a filter, but simpler.
	 * examples:
	 * @code
	 * 'foto'=>array('name'=>'foto','type'=>'label','mask'=>'<img src="%%" />'),
	 * @endcode
	 *
	 * @returns html containing he label, with some aditional data
	 * 
	 * */

	function input_label($f){
		if(array_key_exists('display_value',$f)){
			$lbl=$f['display_value'];
		}else{
			$lbl=$f["value"];
		}
		if(array_key_exists('mask',$f)){
			$lbl=str_replace("%%",$lbl,$f['mask']);
		}
		//".$f['ldata']."
		
		return("<span id='form_label_".$f['name']."' class='form_flabel_data' style='".$f["style"]."' >".$lbl."</span><input type='hidden' id='".$f["name"]."'  name='".$f["name"]."' value='".$f["value"]."' ".$cdata.$ev1."  >");
	}
	/**@brief 
	 * just put stuff there
	 * */
	function input_simple($f){
		return($f['simple']);
	}
	/** \brief a select box with file names.
	
	 special options used:
	- mask: a file mask, like *.php, etc
	- ext file extension to be removed, 
	  i.e. when ".php" is used, the file name sent to the server is still xyz.php, but the info 
	  shown to the user is just "xyz"
	 * */
	function input_glob($field_data){
		#returns a list of files
		$l=glob($field_data['mask']);
		$op=array();
		if(!is_array($l)){
			$op['error']='no files:'.$field_data['mask'];
		}
		foreach($l as $k=>$v){
			$op[$v]=basename($v,$field_data['ext']);
		}
		$field_data['options']=$op;
		return($this->input_list($field_data));
	}
	/* \brief displays a tree
	 *
	 * multiple level select input, useful for country/state, module/action, etc.
	 * ever for stuff like planet/continent/country/state/region/city/neighbourhood/house/room/chair
	 *
	 * anything that depends on a higher hierarchy can/should use one of these.
	 * data is entered trough a tree, field[tree]
	 * that tree contains an array of a/b/c/d/e=>f
	 * keys are in an xpath fashion, values can be anything (hopefully human-readable, and i18n-ized)
	 *
	 * @param $field a field structure
	 * optional: input_separator: what's between the inputs.
	 * @returns an html input.
	 * */

	function input_tree($field){
		global $i18n_std;
		if(!array_key_exists('input_separator',$field)){$field['input_separator']='<br /><br />';}
		if(!array_key_exists('select_label',$field)){$field['select_label']=$i18n_std['form']['please_select'];}
		$fo=$field['options'];
		ksort($fo);
		$dx=$this->glob2tree($fo,0);
		//p2($dx,'green');
		$js=$this->get_jsc('form_tree');
		$js.='<script type="text/javascript" >'."\n".$this->tree2json($dx,'std_'.$field['name']);
		$js.="\n std_divisor='".$field['input_separator']."';";
		$js.="\n std_please_select='".$field['select_label']."';";
		$js.="\n std_display_tree('".$field['name']."_tree','std_".$field['name']."',std_".$field['name'].",'input_".$field['name']."');</script>";
		$dz=$this->tree2html($dx);
		//echo('<xmp>'.$js.'</xmp>');
		$out='<input 
			type="hidden" 
			id="input_'.$field['name'].'" 
			value="'.$field['value'].'" 
			name="'.$field['name'].'" >';
		$out.='<span id="'.$field['name'].'_tree"></span>'.$js;
		return($out);
	}
	/**
	 * style info: %_check_list_item _check_list_label
	 *
	 * \todo 1001 gender check_all
	 * \todo 1002 custom text check_all
	 * values is a list of valid options, that will be checked TRUE.
	 * f has: options, and values.
	 *
	 *
	 * options, can be used as the output from q2op, or the output of aa()
	 * value can be the output of q2a , or any array.
	 * */
	function input_checklist($f){
		global $i18n_std;
		$max=count($f["options"]);
		$out="<ul>";
		$out="";
		$all=1;
		$dx='';
		$c2=0;
		
		$table_data = array();

		foreach($f["options"] as $k1=>$op){
			if(is_array($f["values"]) && in_array($k1,$f["values"])){
				$cc=" checked";
			}else{
				$cc="";
				$all=0;
			}
			$table_data[]="<span class='".$this->style."_check_list_item'>
				<label class='standard_text ".$this->style."_flabel ".$this->style."_check_list_label'>
				<input 
				
				type=checkbox 
				id='".$f["name"]."_".$c2."' 
				name='".$f["name"]."[]' 
				value='".$k1."' 
				$cc ".$f['cdata'].$f['ev1']." 
				title='".$f["help_text"]."'
				onclick='check_complete(\"".$f["name"]."\",$max)'
				>&nbsp;".$op."</label></span>";
			$c2++;
		}
		//Columns
		if(isset($f["columns"]) && 1 != $f["columns"]){
			require_once(STD_LOCATION.'include/grid_layout.php');
			$cols = round(count($f["options"])/$f["columns"]);
			$gl  = new grid_layout($cols);	
			foreach($table_data as $item){
				$gl->add($item);
			}
			$o = $gl->out();
			$o = $this->xpose($o);
			$dx.=$this->table($o,array(),array("style"=>'list','border'=>0));

		}else{
			$dx = "<br/>".implode("<br/>",$table_data);
		}

		#if($f["name"]=='diagnostico_list'){p2($table_data);}
			
		//class='".$this->style."_check_list_item'
		if(array_key_exists('check_all',$f) && $f["check_all"]!=0){
			
			$cc2=($all)?" checked":" ";
			$dy="<label class='standard_text ".$this->style."_flabel ".$this->style."_check_list_label'><input $cc2 
				".$f['cdata'].$f['ev1']."  
				type='checkbox' 
				id='".$f["name"]."_all' 
				onclick='check_all(\"".$f["name"]."\",\"".$max."\")' title='".$f["help_text"]."'>&nbsp;".$i18n_std['list']['form_check_all'].'</label>';
		}
		$out.=$dy;
		$out.=$dx;
		$out.="";
///dbg			p2($f['options']);			p2($f['values']);
		return($out);
	}
	/**
	 * \brief a combobox
	 *
	 * \todo 1115 allow multpple cocurrences on a form
	 * \todo 1116 allow multiple forms.
	 * \todo 1117 remove ", '\n and any weird char that JS might find extraneous
	 * \todo 1118 disable input.enter
	 * @param $f a \ref field_structure "Field Structure"
	 * */
	function input_combobox($f){
		$this->required_scripts[]="combobox";
		$aname="std_options_".$f['name'];
		
		$out=$this->get_jsc('combobox');
		$out.="<script type='text/javascript'>;\nvar $aname=new Array();\nstd_mask[\"my_id\"]='';\n";
		foreach($f['options'] as $value=>$text){
			$out.=$aname."['".$value."']='".$text."';\n";
		}
		$out.="</script>";

		$out.="<input type=hidden name='".$f['name']."_value' id='".$f['name']."_value'  />\n";
		$out.="<div style='border-width:1px;border-style:solid;border-color:black;min-width:150px;max-width:300px'>";
		$out.="<input type=text name='".$f['name']."_text' id=".$f['name']." 
			onkeyup = \"std_change_option('".$f['name']."',event,$aname,'$aname','".$this->name."');return false\" 
			onfocus=\"std_form_disable()\"	
			onblur=\"std_form_enable()\"	
			/>
			<div id=\"".$f['name']."_options\"></div>
			</div>	";
		return($out);
	}
	function input_password($f){
		return("<input type='password' name='".$f["name"]."' value='".$f["value"]."' ".$f["cdata"].$f["ev1"]." title='".$f["help_text"]."'  >");
	}
	/** fixes small opera incompatibility, someday, well be anle to use HTML5 stuff
	 * and there will be an Input type=number
	 * for now, its just wishful thinking
	 * */
	function input_number($f){
		return("<input type='text' name='".$f["name"]."' value='".$f["value"]."' ".$f["cdata"].$f["ev1"]." title='".$f["help_text"]."' >");
	}
	/**
	 * MUST GIVE HIS OWN ID (for JS purposes)
	 * \brief a date input with Month, day and Year
	 *
	 * special keys:
	 * today: indicates that a link must be shown, default is 1.
	 * year_range: indicates the date range
	 * 	year_range=>range(2000,2010)
	 *
	 * \todo 1113 showdate ymd
	 * \todo 1114 a small calendar in JS
	 *
	 * 
	 * \todo 294 form_name!!!!
	 * \todo 294 ,\"'.$field_data['form_name'].'\"
	 * */
	//,$cdata,$ev1
	function input_date($field_data){
		global $i18n_std;
	//	p2($field_data)	;			p2($this,'red');
		$txt="<nobr>";
		$d=explode(' ',$field_data['value']);
		$d1=explode('-',$d[0]);
		$d2=explode(':',$d[1]);

		if(!array_key_exists('year_range',$field_data)){
			$field_data["year_range"]=range(1900,2099);
		}
		if(substr($field_data['name'],strlen($field_data['name'])-2,2)=='[]'){
			$field_data['name']=substr($field_data['name'],0,strlen($field_data['name'])-2);
			$field_append = "[]";
		}else{
			$field_append='';
		}
		$txt.=$this->input_list(
			array(
				'name'=>$field_data['name'].'_year'.$field_append,
				'type'=>'list',
				'options'=>$this->aa($field_data["year_range"]),
				'value'=>$d1[0],
				'cdata'=>$field_data['cdata'].' id="'.$field_data['name'].'_year'."\" ",
			));

		$txt.=$this->input_list(
			array(
				'name'=>$field_data['name'].'_month'.$field_append,
				'type'=>'list',
				'options'=>$i18n_std['months'],
				'value'=>$d1[1],
				'cdata'=>$field_data['cdata'].' id="'.$field_data['name'].'_month'."\" ",
			));

		$txt.=$this->input_list(
			array(
				'name'=>$field_data['name'].'_date'.$field_append,
				'type'=>'list',
				'options'=>$this->aa(range(1,31)),
				'value'=>$d1[2],
				'cdata'=>$field_data['cdata'].' id="'.$field_data['name'].'_date'."\" ",
			));
		if((!array_key_exists('today',$field_data)) ||$field_data['today']!=0){
			$txt.='&nbsp;<a class="today_link standard_link" href="#" onclick="std_set_today(\''.$field_data['name'].'\')">'.$i18n_std['today'].'</a>';
		}
		return($txt.'</nobr>');
	}		
	//@}
	function is_path($txt){
		///echo('<br />searching:'.$txt.'/'.strpos($txt,'/'));
		return(strpos($txt,'/')===false);
	}
	function nodir($txt){
		$dirname=explode('/',$txt);
		if(count($dirname)>=2){//its a a/b
		}	
		return(substr($txt,strlen($dirname[0])+1,strlen($txt)));
	}
	function dn($txt){
		$dirname=explode('/',$txt);
		if(count($dirname)>=2){//its a a/b
		}
		return($dirname[0]);
	}
	/**
	 * @defgroup tree_functions Form::Tree Functions
	 * these functions are required by form::input_tree()
	 * */
	//@{
	function glob2tree($fo){
		$dy=array();
		$mr=array();
		foreach($fo as $k=>$v){
			///echo('<br />'.$v);
			if($k!='__name'){
				if(!$this->is_path($k)){//it's NOT a root element
					//echo('<br />dn is:'.$this->dn($k));
					if(!$this->is_path($this->nodir($k))){
						//Must Recurse
						$mr[$this->dn($k)]=$this->dn($k);	
					}
					$dy[$this->dn($k)][$this->nodir($k)]=$v;
				}else{	//root element
					$dy[$k]=array('__name'=>$v);
				}
			}	
		}
		//p2($mr,'yellow');
		foreach($mr as $k=>$v){
			
			$dy[$v]=$this->glob2tree($dy[$v]);
			$dy[$v]['__name']=$fo[$k];
			
		}
		return($dy);
	}

	function tree2html($tree,$level=0){
		$dx='';
		if(is_array($tree)){
			if(!array_key_exists('__name',$tree)){
				if($level==0){
					//no action taken, i'm root!.					
					$dx.='';
				}else{
					//minor error due to lack of parent item information, use they key.
					$dx.='unnamed-section';
				}	
			}else{
				$dx.=$tree['__name'];
			}
			$dx.='<ul>';

			foreach($tree as $k=>$v){
				if($k != '__name'){
					$dx.='<li>'.$this->tree2html($v,$level+1);
				}	
			}
			$dx.='</ul>';
		}else{
			$dx=$tree;
		}
		return($dx);
	}
	/**\brief turns a PHP array into it's JSON representation for simple JS handling
	 * @param $tree a PHP tree
	 * @param $path the path of the js array, like the root of the array, something like X, or X["y"]
	 * @param $level tabbing lelevl (unused but useful, we'll use this eventually)
	 * @returns javascript
	 * \todo 293 tree2json: make this [optionally] bandwidth-friendly!
	 * a{b:c,d:{g:h}};
	 * */
	function tree2json($tree,$path,$level=0){
		
		if(is_array($tree)){
			$dx="\n".$path.'=new Array();';
			foreach($tree as $k=>$v){
				$dx.=$this->tree2json($v,$path.'["'.$k.'"]',$level+1);
			}
			return($dx);
		}else{
			//useless tabbing
			//str_repeat("\t",$level)
			return("\n".$path.' = "'.$tree.'";');
		}
		
	}
	//@}
	/**
	 * \page widget_overloading
	 * permits input overloading
	 * \todo 1119 example required
	 *
	 * */
	
	function register_function($function_name,$function_callback,$obj,$objf){
		$this->valid_datatypes[$function_name]=
			array(
			'fc'=>$function_callback,
			'obj'=>$obj,
			'objf'=>$objf,
		);
	}
	/**\brief returns the fields in the array, but no table, as an array.
	 * @returns the fields, in an array, with no surrounding table.
	 * \todo 1112 use this in lots of places, like out(), and re_out() //templated_output
	 * returns array(visible_fields, labels,hidden_fields,head,foot,buttons,title)
	 *
	 *
	 * head 		: string (form  start)
	 * visible_fields 	: a list of fields: [field_name]=>value
	 * labels help_texts 	: a list of fields: [field_name]=>value
	 * hidden_fields 	: a list of fields: [field_name]=>value
	 * expand		: a list of rows: name - > name (which will be expanded) to the full width of the table.
	 * title
	 * message 
	 *
	 * \todo 1111 allow this to test: footer,header AND separators.
	 *
	 * css 			: styles, i think
	 * cdata 		: styles,
	 * ev1 			:events (js)
	 * buttons 		:string: a html bunch of buttons
	 * foot 		: string: form end
	 * title		: the form's title.
	 */
	function bare_fields(){
		global $i18n,$i18n_std;
		$ff='';
		foreach($this->fields as $k=>$f){
			if($f["type"]=='file' || $f["type"]=='upload'){
				$ff=' enctype="multipart/form-data" ';
				break;
			}
		}
		$r=array();

		//onsubmit = \"return do_x()\"
		//				<script>;function do_x(){alert(1);return false;};</script>
		//		\todo 292 js validation
		//		\todo 291 super-duper input blocks submission on enter press, but when you leave, it goes back to normal!
		//

		if($this->on_submit!=''){
			$onsubmit='&&'.$this->on_submit;
		}else{
			$onsubmit='';
		}
		


		$r["visible_fields"]=array();
		$r["expand"]=array();
		$r["labels"]=array();
		$r["help_texts"]=array();
		$r["hidden_fields"]=array();
		$first_field='';
		$required_fields = array();	
		foreach($this->fields as $k=>$f){
			
			if(!isset($f['css'])){$f['css']='';}//E_NOTICE
			if(!isset($f['lcss'])){$f['lcss']='';}//E_NOTICE
			$me=$f['name'];

			if($first_field == ''){$first_field=$me;}

			//ALL OK
			$str=$this->strings[$me];

			if(!is_array($this->strings)){
				std::log('no_fields:frm002',"ERROR");
				$this->strings=array();
				#break;
			}
			if(!in_array($f['type'],$this->hidden_types) && !array_key_exists('help_'.$f["name"],$this->strings)){
				if(array_key_exists('i18n_text',$f)){
					//in this case,, the API user garantizes that the string used are i18n 
					//compatible, and that he/she got them from a valid i18n repository, or 
					//database, this is very important, since breaking this rule very easily 
					//breaks i18n compatibility, so be advised.
					$str=$f['i18n_text'];
				}else{
					$str=$f['name'];
					#std::log('nohelp4input name:'.$f['name'].' key:'.$k.'frm001'.b2(),"ERROR");
				}	
				#p2($f);
			}
			$help_text='';
			if(array_key_exists('i18n_help',$f)){
				$help_text=$f['i18n_help'];
			}else{
				if($f['type']=='hidden'){
					$help_text='';//NO HELP AVAIABLE IS OK SINCE ITS A HIDDEN FIELD.
				}else{
					$help_text=$this->strings['help_'.$f["name"]];
				}
				
			}

			if(array_key_exists('events',$f)){
				$ev=array();
				foreach($f['events'] as $k11=>$event){
					$ev[]="on".$k11." = '" . $event . "'";
				}
				$ev1=implode(' ',$ev);
			}else{
				$ev1=' ';	
			}
			$cdata=' class="standard_text '.$this->style.'_finput" style="'.$f["css"].'" ';
			//minor hack to allow no id set to allow himself to set the id to allow multiple ids to allow TODAY js button
			//sorry
			if($f['type']!='date'){
				$cdata.=' id="'.$f['name'].'" ';
			}
			//Fix cdata bug
			$dx2 = '';

			if($help_text!=''){
				$ldata=' class="standard_text '.$this->style.'_flabel '.$this->style.'_label_help" style="'.$f["lcss"].'" ';
				$dx2=' onclick=\'alert("'.$help_text.'")\' ';
			}else{
				$ldata=' class="'.$this->style.'_flabel" style="'.$f["lcss"].'" ';
				$help_text=' ';
			}
			$f['ldata']=$ldata;

			$r["help_texts"][$me]=$help_text;
			if(isset($f["raw_label"])){
				$r["labels"][$me]=$f["raw_label"];
			}else{
				if(isset($f["i18n_text"])){
					$str=$f["i18n_text"];
				}
				if(isset($f["required"])&&$f["required"]==1){
					$str.="*";
					$help_text.="\n Este campo es requerido";
				}
					$r["labels"][$me]='<span '.$dx2.$ldata." title='".$help_text."'>".$str.":</span>";
			}
			

			$r['cdata']=$cdata;
			$r['ev1']=$ev1;
			$r['form_name']=$me;
			//4 everyone
			#$f['help_text']=$help_text;

			//4 glob
			
			$f['cdata']=$cdata;
			if(isset($f["expand"])){
				$r["expand"][$me]=$me;
			}
			$f['ev1']=$ev1;


			#p2($f);
			if(isset($f["required"]) && $f["required"]==1){

				##echo($f["name"] ." is required");
				$required_fields[$f["name"]]=$f["name"];
			}


			//! \todo TODO 1110 selected item $f["value"]
			#IS THIS: is in hidden types?
			if($f["type"]=="hidden"||$f['type']=='protected'){
				/*HIDDEN, uses no i18n string.*/

				//changes protected	
				if($f['type']=='protected'){
					$f['type']='hidden';
					//sort of unique.
					$key=$this->protect_unique('_std_form:'.$f['name'].'_');
					$value=$f['value'];
					$f['value']=$key;
					$this->save_protected($key,$value);
				}

				unset($r["visible_fields"][$me]);


				$r["hidden_fields"][$me]=$this->input_hidden($f);
				

			}elseif($f["type"]=="link"){
				//input_link
				$ff=array();
				$link_options=array();
				foreach($this->fields as $kk=>$ff){	
					//ADD ALL VALUES that are HIDDEN
					if($ff['type']=='hidden'){
						$link_options[$ff['name']]=$ff['value'];
					}	
				}

				$lo=array_merge($link_options,$f['link_options']);
				
				$r["visible_fields"][$me]=$this->make_link($lo,$str,$this->style."_link",$help_text);
				$r["labels"][$me]='&nbsp;';/*@left:nothing*/

			}elseif($f["type"]=="separator"){
				$r["labels"][$me]="<span title='$help_text'>".$f['value']."</span>";
				$r["visible_fields"][$me]="&nbsp;";
			}elseif($f["type"]=="block"){
				$r["labels"][$me]=$f['value'];
				$r["visible_fields"][$me]='';
				$r["expand"][$me]=$me;

			}elseif(array_key_exists($f["type"],$this->valid_datatypes)){	


				#aha


				//'form::input_'.
				//"form::input_".$f["type"]
				//$t=$this->valid_datatypes[$f["type"]]['fc'];
				//echo("used callback:".$t.' on datatype:'.$f['type']);
				//$sm='';
				//TODO why value NULL?
				//$ec="\$sm=$t(".var_export($f,true).");";
				//eval($ec);
				
				if(is_array($this->valid_datatypes[$f["type"]])){
					$obj=$this->valid_datatypes[$f["type"]]['obj'];
					$objf=$this->valid_datatypes[$f["type"]]['objf'];
					std::log("used callback:".$objf.' on datatype:'.$f['type'].' obj type:'.get_class($obj),'FORM');
					$r["visible_fields"][$me]=$obj->$objf($f);
				}else{
					$fn='input_'.$f["type"];
					$r["visible_fields"][$me]=$this->$fn($f);
				}
				//ADD BEFORE AND AFTER
				$r["visible_fields"][$me]=$f['before'].$r["visible_fields"][$me].$f['after'];
				
				
				//"<input ".$cdata.$ev1." title='$help_text' type='".$f["type"]."' name='".$f["name"]."' value='".$f["value"]."'>";
			}else{	/*text,radio,checkbox,etc*/
				if(isset($f['size'])){
					$fs=' size=\''.$f['size'].'\'';
				}else{
					$fs='';
				}
				
				if(isset($f['disabled'])){
					$disabled_flag=' DISABLED ';
				}else{
					$disabled_flag='';
				}

				if(isset($f['autocomplete'])){
					$autocomplete_flag=' autocomplete="'.$f['autocomplete']."\"";
				}else{
					$autocomplete_flag='';
				}
				
				$r["visible_fields"][$me]="<input type='".$f["type"]."' name='".$f["name"]."' value='".$f["value"]."'  ".$autocomplete_flag.$disabled_flag.$fs.$cdata.$ev1." title='$help_text' >";

				//ADD BEFORE AND AFTER
				$r["visible_fields"][$me]=$f['before'].$r["visible_fields"][$me].$f['after'];
				
			}
		}
		#p2($this->fields,'red');
		#p2($required_fields);
		//field validation
		if(count($required_fields)>0){
			$req_script='std_validate_required_fields('.$this->req_message.')';
			$req_js = '<script>;var std_required_fields=["'.implode('","',$required_fields).'"];';
			$req_js .="\nvar std_required_fields_labels=[];\n";
			foreach($required_fields as $fname){
				$flabel = strip_tags($r['labels'][$fname]);
				$flabel=str_replace("&aacute;","á",$flabel);
				$flabel=str_replace("&eacute;","é",$flabel);
				$flabel=str_replace("&iacute;","í",$flabel);
				$flabel=str_replace("&oacute;","ó",$flabel);
				$flabel=str_replace("&uacute;","ú",$flabel);
				$flabel=str_replace("&Aacute;","Á",$flabel);
				$flabel=str_replace("&Eacute;","É",$flabel);
				$flabel=str_replace("&Iacute;","Í",$flabel);
				$flabel=str_replace("&Oacute;","Ó",$flabel);
				$flabel=str_replace("&Uacute;","Ú",$flabel);
				$flabel=str_replace("&ntilde;","ñ",$flabel);
				$flabel=str_replace("&Ntilde;","Ñ",$flabel);
				$req_js .= "\n".' std_required_fields_labels["'.$fname.'"]="'.str_replace(array(":","*","\n","\r","\t",'"',"'",';'),"",$flabel).'";';
			}
			$req_js .= "\n</script>";
			$req_js_click_event=' onclick="std_set_submit_button(1)" ';
		}else{
			$req_js_click_event='';
			$req_js='';
			$req_script = ' true ';
		}

		$r["head"]="\n<!--FORM START-->
			$req_js
			<form  onsubmit='return ($req_script && std_form_check()$onsubmit) ' action='".$this->action."' $ff method='".$this->method."'>";


		//SOMETHING WILL EVENTUALLY BREAK HERE< IF YOU SEE NO BUTTONS< BLAME IT ON THIS GUY< DEPRECIATION COMPLETE
		//p2($this->has_ok);p2($this->btn);

		if($this->has_ok==1){
			//can't click everywhere, wont do a BG due to space limitations, 
			//because if text grows larger we are screwed TODO figure out a way of doing this
			//$foot=$this->get_shadow("<input class='".$this->style."_ok' type='submit' name='submit' value='".$this->strings["ok"]."'> ",'round','center');

			if(count($this->btn)==0){
				std::log("Deprecated Style, use add_submit_button() instead.",'WARNING');
			//	$this->add_submit_button(array('label'=>$this->strings["ok"],'action'=>'none'));
			}
			//<br />
			$r["buttons"]="\n<!--btn start-->\n<div  style='display:inline-block'><table width='100%'><tr>";
			foreach($this->btn as $k1=>$button){
				$this_btn_js_click_event = $req_js_click_event;
				if(count($required_fields)>0){
					#don't care much for valdiations, if the user is returning to the previous screen...
					#but only for this button
					if(preg_match("/b2l/",$button['action'])|| (isset($button["validate"]) && $button["validate"]==0)){
						$this_btn_js_click_event=' onclick="std_set_submit_button(0)" ';
					}
				}
				
				$r["buttons"].="\n<td ><input type='submit' value='".$button['label']."' name='submit'  $this_btn_js_click_event class='".$this->style."_ok' ></td>";
			}
			$r["buttons"].="</table></div>\n<!--btn end-->\n";
			
		}else{
			$r["buttons"]='<br />';
		}	
		//determine first element
		//THIS ONLY WORKS IF THERE IS ONLY 1 FORM, on more fors, it will definately break
		//so please use only one form.
		$r["foot"]="<script>;std_form_focus_first();</script>";
		$r["foot"].="\n</form>\n<!--END OF FORM-->";
		if($this->strings["_form_title"]!=''){
			$r["title"]="<span title='".$this->strings["help__form_title"]."'>".$this->strings["_form_title"]."</span>";
		}
		$m=$this->get_message();
		if($m!=''){
			$r["message"].="<div class='standard_text warning_container warning_text'>".$m."</div>";
		}
		#p2($r,'blue');
		foreach($this->required_scripts as $k=>$script){
			$r["head"].=$this->get_jsc($script,1);
			//"\n<script type='text/javascript' src='../shared/components/".$script.".js'></script>"
		}
		///\todo allow multiple forms!
		//TODO onEnter()
		
		if($this->use_grid_layout==1){
			$r["head"].="\n<style> \n .form_collumn_0 {text-align:left} \n </style>";
		}
		$r["head"].="\n<script>;var std_mask=new Array();</script>";

		return($r);
	}

	/**\brief the form's HTML representation.
	 *
	 this function allows form serialization, so you can put the whole form inside your page, concatenating 
	 to some string var.
	 if you want to put a form inside another form, use form::contents() instead.
	 @see form::contents()

	\todo allow multiple formats (JSON?,XML?)
	\todo save to file, form cacheing.
	\todo allow groups
	\todo reuse Xform framework. ???
	
	Styles defined:
	- _check_list_label (only for form::input_checklist())
	- _check_list_item (only for form::input_checklist())
	- _ok (buttons)
	- _flabel (left text)
	- _finput (right input)
	- _f (not a clue (does this work?))
	- _link (just for form::confirm())

	field:
		css	input style
		lcss	label style
		type
	
		WARNING dont assign event click to checklist, since they have its own handling.
		\todo add checklist events so that they are run before/after my evts

	* */		
	function out(){
		$o=$this->contents();
		return($o['head'].$o['contents'].$o["hidden"].$o["foot"].$this->form_end);
	}
	function group_repack(){
		//we must pack grid info back into place
		$new_fields=array();
		
		foreach($this->fields as $k=>$f){
			if(isset($f["repeat"])&&$f["repeat"]==1){
				$group_name = 'default_group';
				if(isset($f["group"])){
					$group_name=$f["group"];
				}
				$has_delete = 1;
				if(isset($f["has_delete"])){
					$has_delete=$f["has_delete"];
				}
				$has_add = 1;
				if(isset($f["has_add"])){
					$has_add=$f["has_add"];
				}
				if(!isset($new_fields[$group_name])){

					$new_fields[$group_name]=array(
						'name'=>$group_name,
						'i18n_text'=>'',
						'has_delete'=>$has_delete,
						'has_add'=>$has_add,
						'i18n_help'=>'',
						'type'=>'group',
						'expand'=>1,
						'fields'=>array(),
					);
				}
				//the VALUE property gets a special treatment, if the value is not an array, I'll make it into one!
				//that way, if there is only ONE value, everything is FINE, if the value is "" or empty string (whatever you wanna call it)
				//then ALSO everything is fine
				//part of the nice post-processing one would spect 
				//instead of getting non-existant grids out of null values (uncalled for!)

				if(!isset($f['value'])||$f['value']==''||(!is_array($f['value']))){
					$f['value'] = array($f['value']);
				}

				//avoid unnecessary recursion 
				unset($f["repeat"]);
				unset($f["group"]);
				$new_fields[$group_name]['fields'][$k]=$f;
				if(isset($f["group_label"])){
					$new_fields[$group_name]["i18n_text"]=$f["group_label"];
				}
				if(isset($f["group_add_link"])){
					$new_fields[$group_name]["add_link"]=$f["group_add_link"];
				}

			}else{
				$new_fields[$k]=$f;
			}
		}
		
		$this->fields = $new_fields;


	}
	/**
	 * \brief this will get you the form's contents, in a small array so you can nest forms.
	 *
	 * the beauty of it, cannot be expresed in words, it must be seen in action.

	 * this will allow you to get the form's table, but no <form> tag, that way you can easily 
	 * include one form in another form.

	 @returns an array with the following keys:
	- head
	- contents
	- hidden	(all the hidden fields in a nice string.)
	- foot
	  */
	function contents(){
		global $config;
		$this->group_repack();
		
		$d=$this->bare_fields();
		$expand=$d['expand'];	
		$o=array();
		if($this->use_grid_layout == 1 || (isset($config["enable_grid_layout"]) && $config["enable_grid_layout"]==1)){
			require_once(STD_LOCATION.'include/grid_layout.php');
			//META: 
			if(isset($config["columns"]) || $this->use_grid_layout == 1){
				$cols = $config["columns"];
			}else{
				$cols = 2;//lousy default, I know.
			}
			if($this->use_grid_layout == 1){
				$cols = $this->columns;
			}
			if($cols == 0){
				$cols = 2;
			}
			
			$gl  = new grid_layout($cols);
			
			
			$dta=array();
			foreach($d["visible_fields"] as $k=>$f){
				if(isset($this->fields[$k]["expand"]) && $this->fields[$k]["expand"]==1){
					$dta["{$k}_sep"]="[BREAK]";
				}
				$dta[$k]=$d["labels"][$k].$this->separator_break_str.$f;
			}
			//p2($dta,'orange','CAMPOS');
			$gl->add($dta);
			//WARNING, At This Point, column labels get lost.
			$dta = $gl->out();
			//d2($dta,'purple');
			$expand = $gl->get_expand();
			
		}else{
			$dta=array();
			foreach($d["visible_fields"] as $k=>$f){
				$dta[$k]=array($d["labels"][$k],$f);
			}
		}
		
		$o["head"]=$d["head"];
		///std::log('we are in template:'.$this->in_template,'FORM');
		
		if($this->in_template==1){
			$str=array();
			$str['title']=$d["title"];
			$str['buttons']=$d['buttons'];
			
			foreach($d['visible_fields'] as $k=>$v){
				$str['input_'.$k]=$v;
				$str['help_'.$k]=$d['help_texts'][$k];
				$str['label_'.$k]=$d['labels'][$k];
			}
			std::log('template for form:'.$d["title"].'<br/>'.implode(' ,<br/> ',array_keys($str)),'FORM');
			$o["contents"]=$this->fmt($this->template,$str,'_');

		}else{
			$o["contents"]=$this->table($dta,array(),
				array(
					'expand'=>	$expand,
					'collapsible'=>	$this->collapsible,
					'status'=>	$this->status,
					'title'=>	$d["title"],
					'message'=>	$d["message"],
					'footer'=>	$d["buttons"],
					'style'=>	$this->style,
					'border'=>	0,///RFC you can put 1 here if you feel you need to check the layout
					'cellspacing'=>	0,
					'cellpadding'=>	0
				));
		}	
		///std::log("<xmp>".$d["buttons"]."</xmp>",'FORM');
		$o["hidden"]="\n<!--hidden fields-->\n".implode("\n",$d["hidden_fields"])."\n<!--end of hidden fields-->\n";
		$o["foot"]=$d["foot"];
		
		return($o);

	}
	/**\brief adds a submit button to the form
	 * forms can have many submit buttons, that lead to various actions, this is all handled by the std engine
	 * so you don't have to worry about it.
	 *
	 @param button an array with keys: 
	 - label
	 - action 
	when each buttn is added a hidden field is created, giving its mapping into: label->action
	this complicates a litle bit the execution model, but it allows us to
	have form with multiple exit points
	such exit point management must be done, of couse, by the user.
	\todo button's help
	*/
	function add_submit_button($button){
		#std::log("action in button:".$button["label"]." :: ".$button["action"],'FORM');
		$this->btn[]=$button;	
		$this->add_submit_option($button);
	}
	/** \brief submit option: a item in the _submit array.
	 * each form can be submitted to multiple "actions",
	 * actions are defined  by  AC and MOD, ac is the action, and mod is the module, both together 
	 * intruct the program on which part of it must be executed.
	 * actions map directly into functions that are defined
	 * in the modules (./controller/)
	 *
	 * */
	
	function add_submit_option($button){
		$this->submit_options[]=$button;
		$this->fields[]=array(
			'name'=>'_submit['.$button['action'].']',
			'type'=>'hidden',
			'value'=>$button["label"]
		);
	}
	function get_submit_options(){
		return($this->submit_options);
	}

	/**
	 * \page field_structure Field Structure
	 * \brief adds a field to the form
	 *
	 * @param $field an array thatshould have at least these keys:
	 - name
	 - type: the datatype, usually something like an html datatype, used in the html INPUT element,
	 the "type" attribute.
	 type can also have some custom types, like: date, checklist, glob, separator, etc
	 in theory you can add more datatypes, altough this hasn't been done yet.
	 
	 You can have a lot more optional values, like:
	 - disabled
	 - value
	 - options (valid only when the input is a list, a checklist, etc)
	 - values  (valid only when the input is a list, a checklist, etc)
	 - mask (when the input is a glob) @see form::input_glob()
	 - ext same as avobe.
	 \todo required
	 \todo range
	 \todo 

	 REQUIRES: $field.name to exists.
	 */
	
	function add_field($field){
		if(!array_key_exists('values',$field)){
			$field["values"]=array();
		}
		$this->fields[$field['name']]=$field;
	}
	function set_title($title){
		$this->strings['_form_title']=$title;
	}
	function set_field_label($field,$text,$help=''){
		$this->strings[$field]=$text;
		$this->strings['help_'.$field]=$help;
	}
	function add_text_field($name,$value=''){
		$this->add_field(array('name'=>$name,'type'=>'text','value'=>$value));
	}
	function add_block_field($contents){
		$this->add_field(array('type'=>'block','value'=>$value));
	}
	function add_hidden_field($name,$value=''){
		$this->add_field(array('name'=>$name,'type'=>'hidden','value'=>$value));
	}
	function add_textarea_field($name,$value=''){
		$this->add_field(array('name'=>$name,'type'=>'textarea','value'=>$value));
	}
	/**\brief adds some spacing vertically between inputs.
	 * @param $txt the text to be separated.
	 * this text appears on the left section, if you want text on the right sectin, use label instead.
	 * */
	function add_separator($txt='&nbsp;'){
		#for aestetic purposes only.
		$this->fields[]=array('type'=>'separator','value'=>$txt,'name'=>'separator_'.rand(0,10000).'');
	}
	/**
	 * in order to access the blocks, in the template, you can use something along the lines of:
	 * [#___blockX]
	 * where X is the number of the block, starting by 0
	 *
	 *
	 * you can use:*/
	function add_block($txt){

		$this->fields[]=array(
			'i18n_help'=>' ',
			'i18n_text'=>' ',
			'name'=>'__block'.$this->block_count,
			'type'=>'block',
			'value'=>$txt
		);
		$this->block_count++;
	}
	function set_shadow_object($object){
		$this->shadow_object=$object;
	}
}
?>
