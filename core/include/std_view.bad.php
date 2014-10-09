<?php

/**
 * \todo 300 consider moving get_i18n_text to common, consider table problems, if any
 * a view object
 * \todo 301 explain what the caller must implement: [WHICH]/all std functions
 *
 * all the functions in this class, if they want to modify the caller (which MUST inherit or be a instance of STD @see std::std)
 * must have as the first line something like:
 @code 
 $me=&$this->caller;
 @endcode
 you can use the $me object freely, call methods, properties,a dn edit if you want as well.

 invoke Method
@code
	 include(INCLUDE_DIR.'std_view.php');
	$v=new view($this,$view);
@endcode

 * */

class view extends db{
	/**
	 * \todo allow user settings to override $i18n_std['list']	
	 * */
	var $help;
	var $options;
	var $caller;
	var $ss=null;
	var $ss2=null;
	/**
	 * \todo 301 _GET soft direction must be changes, to allow multiple MLs in a form.
	 * Which Sort Direction: GET, default VIEW, or simply ID, in that order.
	 * 
	 * ASC,DESC are sql but might become not so?
	 *
	 * */
	function valid_sort_direction($sd){
		
		if(""==($sort_direction=$this->ss[$this->remove_strange_chars($_GET['sort_direction'])])){
			if($sd==''){
				$sort_direction=$this->ss['up'];
			}else{
				$sort_direction=$sd;
			}
		}
		return($sort_direction);
	}
	/** 
	 * Which Sort field: GET, default VIEW, or simply ID, in that order.
	 * */
	function valid_sort_field($sf){
		$me=&$this->caller;	//ok
		$sort_field=$this->remove_strange_chars($_GET['sort']);
		if(""==$sort_field){
			if($sf==''){
				if($sort_field==''){
					$sort_field=$me->get_table_name().'.'.$me->id;
					$me->log('sort ID is: default','VIEW');
				}
			}else{
				$sort_field=$sf;
				$me->log('sort ID is: '.$sf,'VIEW');
			}
		}
		$me->log('FINAL sort field is: '.$sort_field,'VIEW');
		return($sort_field);
	}
	function valid_page(){
		$page=$this->remove_strange_chars($_GET['page']);
		if($page==""){$page=0;}
		if($page<0){$page=0;
		/** \todo 302 log minor hack attempt? */
		}	
		return($page);
	}

	/**
	function test1(){
		$me=&$this->caller;
		$me->wow=1;
	}
	 */
	function li18n($key){
		global $i18n_std;
		return($i18n_std['list'][$key]);
	}
	function view(&$me,$options){
		$this->caller=&$me;//NOT THIS ONE YOU FUCKING STUPID!
		$this->options=$options;
		$this->table='STD_VIEW';
		$this->ss=array('up'=>'ASC','down'=>'DESC');
		$this->ss2=array('ASC'=>'up','DESC'=>'down');
		$this->db();
	}

	///@defgroup ml Record Listing
	///@{

	//all these belong in ML(),. removed from there for the sake of clarity.


	/**
	 * \todo allow per/app another template file
	 *
	 * this function is wrong.
	 *
	 * */
	function get_field_info($fields){
		$me=&$this->caller;
		global $i18n;
		$tmpl=file_get_contents(SHARED_MODULES_DIR.'templates/field_info.html');
		$a=array();
		$c=0;
		foreach($fields as $k=>$field){
			if($me->id!=$k){
				$a[$c]=array();

				$a[$c]['field_help']="<span class='standard_text'>".str_replace('\\n','<br/>',$me->fh($field['name']))."</span>";
				if($a[$c]['field_help']==''){
					$a[$c]['field_help']=$me->get_i18n_text('no_field_help_available');
					//$a[$c]['field_help']='';
				}
				$a[$c]['field_name']=$me->fi($field['name']);
				$a[$c]['field_sql']=$field['name'];
				$c++;
			}
		}
		$dww=$me->get_i18n_array();

		$tree=array('isnode'=>1);
		$tree['HEADER']=array('title'=>$me->get_i18n_text('help'));
		$tree['FOOTER']=array();
		$tree['CONTENT']['LOOP']=array(
			'HEADER'=>$dww,
			'FOOTER'=>$dww,	
			'CONTENT'=>$a,
			'isnode'=>1,
		);
		$me->log("Available keys to the Template:".implode(" , ",array_keys($a)),'TEMPLATE');
		$ret=$me->parse_template($tree,$tmpl,'CONTENT','_');
		
		return($ret);
	}
	/**
	 * this adds the /doc/{lang}/{table_name}.html to the mix
	 *
	 *
	 * so view has a field called 'help', and help is a i18n_key from the module, which indicates
	 * the .....@todo wtf?
	 *
	 * */
	function get_info($i18n_key,$view_fields){
		$me=&$this->caller;
		$b=$me->parse_add_files($me->i18n($i18n_key),'inc_','./doc/'.std_get_language());
		$a=$me->fmt($b,array('field_info'=>$this->get_field_info($view_fields)),'_');
		return($a);
	}

	///@}


	/**

		@todo 4003 logo means main->menu()!
		to run a view use:
		run_view()
		@todo 4002 view functions

		parameters (pretty much everything you would see on the View()
	)
	options.kids : with kids?
	 kids==model use model info | else: use the kids array as info.

	 -options.nomenu disables the menu
	 (in case you need some more space, make sure you provide the user the way of returning to a menu 
	 displaying view!)

	- options.filter
	- options.restrict
	- options.fields
	- options.shortcuts
		an array of ("search_expression" =>i18n_key)
				
					 
	- options.column_style array
	- options.current_action 	if the view is included from somewherelse, you must put the action that will handle the thing

	 at the end of the table, multiple actions are permitted, over many items (selected trough checkbox on the left)
	 this allows us to do stuff like "delete multiple items" button

	 if results are more than $config.pagination_limit, the results are paginated, so that 
	 no more than $config.pagination_limit are shown per page.
	*controls that allow you to go to the Next page, and the PRevious page, are also set.

	 if a collumn belongs to another table (i.e  is foreign) it is identified so, and information from
	 the other tables is brought (using SQL Join), using the SQl relationships information.

	 this function replicates functionality from base_class.my_list(), with many improvements.
	
	\todo 1101 ALLOW SESSION DATE TO BE FIRST OVER the other data.
	\todo 1102  search
	\todo 1103  send to template
	\todo 1104  allow customization, @ app level OK , mod level OK , & view level NO (kindof, using view.type: view handlers)
	\todo 1105  checkbox for selecting multiple items
	\todo 1106  security? wtf is this about?
	\todo 1107  multiple filters, separated by ,
	
	\callgraph
	\todo 9876
	\todo allow serialization: get_ml()
	* */

	function ml(){
			
		$me=&$this->caller;	//ok
		$options=$this->options;	//ok
		$use_kids=1;
		if(!array_key_exists('current_action',$options)){$options['current_action']='view:'.$me->current_view;}
		if(!array_key_exists('kids',$options)){$options['kids']=array();$use_kids=0;}
		/** Options.check is enabled by default, but can be explicitely disabled.*/
		if(!array_key_exists('check',$options)){$options['check']=1;}
		
		$restrict=$options["restrict"];if($restrict==''){$restrict='1=1';}
		$title=$me->i18n($options["title"]);//fixed check if works
		$view_fields=$options["fields"];
		#p2($view_fields,'red');

		$sort_field=$this->valid_sort_field($options['sort_field']);
		$sort_direction=$this->valid_sort_direction($options['sort_direction']);
		$page=$this->valid_page();

		if($options["template"]==""){
			$in_template=0;
		}else{
			$in_template=1;
			$tmpl=file_get_contents("./templates/".$options["template"].".html");
		}
		///
		
		foreach($view_fields as $k=>$view_field){
			$view_fields[$k]['show_me']=1;
		}

		$temp1=$this->get_preset_fields($view_fields);
		$current_fields=$temp1['current_fields'];
		$cff=$temp1['cff'];
		
		///p2($temp1,'red','Fields to be displayed:<br/>');
		

		//fields presets now contains info
		$q=$me->foreign_select(array(
			'search_flag'=>1,
			'fields'=>$cff,
			'sort_field'=>$sort_field,
			'sort_direction'=>$sort_direction,
			'restrict'=>$restrict,
		));
		//What Foreign Select gives you
		//d2($q,'FOREIGN SELECT');

		#gets the pagination query
		$qmax=$me->q2obj($q['csql']);
		$max=$qmax[0]["c"];
		#retrieve the pagination scheme.
/* damage*/
		$pghtml=$this->pagination_scheme(
			array(
				'view_title'=>$title,
				'page'=>$page,
				'max'=>$max,
				'link'=>array(
					'mod'=>$me->program_name(),
					'view_id'=>$me->current_view,
					'__search_term'=>$this->remove_html($me->restore_value('__search_term')),
					'ac'=>$_GET["ac"],
					'sort'=>$sort_field,
					'sort_direction'=>$this->ss2[$sort_direction],
				)
			)	
		);

		$sfields=$q['sfields'];
		if(count($sfields)==0){
			$this->i_error('no_fields_error','VIEW120');//ok
			return(0);
		}
		
		/**
		 * \todo if @ last page, and there are previous pages, and there are no records, 
		 * then go back one page
		 *
		 */	

		$got_headers=$me->get_headers();
		$headers=array();

		#header info, and sort stuff
		/**
		 * @todo 3010 sadly, i must replicate functionality described @ foreign_select, fix that (somehow...)
		 * */
		$ialias=0;
		/**
		 * @todo 3020 MUST SOMEHOW GET THE HEADERS DATA FROM FOREGN_SELECT
		 * */
		
		$na=$me->need_alias($view_fields);#FIXME
		
		$mk=array(
			'mod'=>$me->program_name(),
			'ac'=>$options['current_action'],
			//'view:'.$me->current_view
			//\todo2 must be in options: current view
			'page'=>$page,
		);

		//old viewfields
		
		foreach($view_fields as $k=>$field){
			if(!array_key_exists($k,$current_fields)){
				continue;
			}
			/*
			$fname=$sfields[$k];
			
			$is_foreign=array_key_exists('foreign',$field);
			
			if($me->field_is_foreign($field)){	
										#field is foreign, join tables,
										#why do i do this here too? 
				if($is_foreign){
					$e=explode('.',$field["foreign"]);
					$tname=$e[0];

					$f1name=$e[1];								
				}else{
					//we don't want the whole thing
					$fm=$me->load_file($me->id2mod($k),'light');
					$tname=$fm->get_table_name();
					$f1name=$fm->ifield;
				}
				
				if($na==1){//use alias
					$tblalias=$tname.'_alias_'.$ialias;
				}else{//don't use alias
					$tblalias=$tname;
				}
				$fname=$tblalias.'_'.$f1name;			
				
				#OLD:
				#$fname=$q['table_aliases'][$fm->table].".".$fm->name;
				
				$ialias++;
			}else{
				$fname=$me->get_table_name().".".$k;
			}
			*/
			
			//p2($q['qfields']);

			$fname=$q['qfields'][$k]['d'];
			//echo("<br>sd = $sort_direction  testing=".$sort_field.' VS '.$fname.' test='.($sort_field == $fname ) );
			if($sort_field == $fname  && $this->ss2[$sort_direction] == 'up'){//asc2up
				$dir='down';
			}else{
				$dir='up';
			}

			/**
				\todo allow customization of list in link, so you can have different views right there.
				\todo add help here:link.
				
			 */

			$mk['sort']=$fname;
			$mk['view_id']=$me->current_view;
			$mk['sort_direction']=$dir;
			$mk['__search_term']=$this->remove_html($me->restore_value('__search_term'));

			//broken	
			$headers[]=$me->make_link($mk,$got_headers[$k],'list_link',$me->fh($field['name']));
		}


		//fetch data from database.
		$l=$me->q2obj($q['sql'].$this->db_paginate_sql($page));
		//\todo
		//a more efficient way?

		//HEADERS SECTION

		$headers[]=$this->li18n('actions');


		$this->set_search_words($q['words']);		
		//does nothing, originally
		///\todo allow multiple filters, separated by , or if filter is array, etc
		//\todo when link filter enabled 9876
		//\todo TEST 

		if(is_array($options['side_actions']) && count($options['side_actions'])>0){
			$me->default_edit_action=$options['side_actions'][0];//['action']
		}
		$me->ml_options=$options;
		$me->fs_options=$q;
		$l=$me->filter_trough($l,array(
			$me->default_filter,
			$me->default_money_filter,
			$me->default_highlight_filter,
			$me->mask_filter,
			$me->date_filter,
			$options["filter"],
			$me->link_filter,
		)
		);

		//allow foreign links
		//d2($q['qfields']);
		//UNSAFE
		/*	
		foreach($q['qfields'] as $k=>$field){
			if($field['was_added']==1){
				///echo('<br/>added field:'.$k);
				$me->privilege_manager->add_privilege(array(
						'privilege_name'=>$field['i18n'],
						'role_name'=>$me->whoami,
						'action'=>$field['link']));
			}
		}
		 */


		//do what?

		$c=0;
		foreach($l as $k=>$row){
			
			$final[$c]=array();
			$mid=$me->table."_".$me->id;
			$myid=$row[$mid];
			
			foreach($row as $k1=>$field){
				$final[$c][$k1]=$field;
			}
			#WARNING
			if($options['check']){
				//SHOULD WE SHOW THE ID?
				if(isset($me->meta["display_id"]) &&$me->meta["display_id"]==1){
					$append = $myid;
				}else{
					$append='';
				}
				$final[$c][$mid]='<input onclick="std_select_row(this)" type=checkbox name="item[]" value="'.strip_tags($myid).'" title="'.strip_tags($myid).'" style="" class="form_finput">'.$append;
			}

			$dxx='';
			$me->make_link($mk,$got_headers[$k],'list_link',$me->fh($field['name']));

			$link_options=array('mod'=>$me->program_name());
				
			foreach($options["side_actions"] as $k9=>$v9){
				$title1=$this->li18n('help_form_'.$v9);//\bug //ok
				if(is_array($v9)){
					$link_options["ac"]=$v9['action'];
					$label=$v9['label'];
				}else{
					$link_options["ac"]=$v9;
					$label=$me->get_i18n_text($v9);
				}
				$link_options["id"]=strip_tags($myid);
				$dxx.=$me->make_link($link_options,$label,'action_link',$title1).'&nbsp;';
			}
			
			$final[$c]['__actions']=$dxx;
			$c++;
		}


		
		//remove the hidden foreign id fields, leave just the foreign name fields

		//who is foreign?
		$foreign_fields_list=array();
		foreach($q['qfields'] as $k=>$field){
			if($field['was_added']==1){
				if(is_array($final)){
					foreach($final as $fi=>$record){
						//removes <table>_<foreign_id> from the record, 
						//so that when we show the damn thing, it doesn't show the ID's.
						unset($final[$fi][$me->table.'_'.$k]);
					}
				}
			}
		}
		///The data sent to table() :
		///p2($final,'violet');		



/*		
		foreach($final as $k=>$v){
			foreach($v as $k1=>$r){
				if($q['qfields'])
			}
		}
 */

		$final_actions="<div align=left class='standard_text page_container' >$pghtml</div>";
		$t6=new form();//contains all the buttons
		$t6->strings=array();
		/* 
		 * Select Collumns section
		 * */
		//WARNING: adding a field, is too much work, there's got to be an easier way !
		/*
		*/

		$t6->add_hidden_field('mod',$me->program_name());
		$t6->add_hidden_field('view_id',$me->current_view);
		/** 
		 * \todo ADD hidden inputs for state, like sort field, sort ac, and search term. to t6
		 * */

		if(is_array($options["down_actions"]) || count($options["down_actions"])!=0){
			foreach($options["down_actions"] as $k=>$v){
				if(is_array($v)){
					$t6->add_submit_button(array('label'=>$me->get_i18n_text($v['label']),'action'=>$v['action']));
				}else{
					$t6->add_submit_button(array('label'=>$me->get_i18n_text($v),'action'=>$v));
				}
			}
		}

		$form_struct=$t6->bare_fields();
		#p2($form_struct);
		if(count($final)==0 && !array_key_exists('page',$_GET)){
			$pghtml='';
		}
		$ending='<table width=100%><tr><td class=list_footer>'.
					"<div align=left class='page_container list_footer'>$pghtml</div>".
					"</td><td valign=bottom>".
					$form_struct["buttons"]."</td></tr></table>";

		$success=1;
		$envelope=1;
		$level9=$form_struct["head"];
		$no_items=0;
		if(count($final)==0){
			$no_items=1;
			//OLD STYLE
			//$t=$this->get_i_msg('no_items',array('name'=>$me->i18n('table_plural'),'ac'=>$ending));	

			$t=$me->get_i_msg('no_items',array('name'=>$me->i18n('table_plural'),'ac'=>$ending));	
			$success=0;
			$envelope=0;
		}elseif($in_template==0){
			/** \todo 1005 allow CHECKBOX in templates (for now it's only in table())*/
			#old fashoned table()
			$t=$this->table($final,$headers,
				array(//$o2["c"]
					///'title'=>$title,//TODO FIXME FIX 
					'footer'=>$ending,
					'column_style'=>$options['column_style'],
					'style'=>'list',	/** \todo 1004 own style, LIST*/
					'border'=>'0',
					'cellspacing'=>0,
				));
		}else{
			return($this->table2template(array(
				'paging'=>$pghtml,
				'data'=>$final,
				'template'=>$tmpl,
				)));
		}
		if($in_template==0){
			#level9 means almost out
		
			$level9.=$t;
			$level9.=implode("\n",$form_struct["hidden_fields"]);
			$level9.=$form_struct["foot"];
	//		if($envelope==1){
	//			$this->shadow($level9,'shadow','center','60%');	
	//		}else{
	//			echo($level9);
	//		}
			require_once(INCLUDE_DIR.'std_tab.php');
			$tabs=new tab();
			$tabs->set_style('nice');

			//BAD idea, we don't know if he's searching for something

			if($no_items==0 || "" != $_GET['__search_term']){
				//there are items available:
				$ssf=$this->get_simple_search_form();
			}else{
				$ssf='';
			}
			if('table_plural' != $this->options['title']){
				$view_title1 = $me->i18n($this->options['title']);
			}else{
				$view_title1 =$me->i18n('table_plural');
			}
			$tabs->add_tab($view_title1 ,
				$ssf.
				$me->get_warning($me->pop_cmessage()).
				$level9);

			//THIS IS DISABLED UNTIL WE FIND A BETTER WAY:
			#	$tabs->add_tab($me->get_i18n_text('advanced_search'),
			#	$this->get_advanced_search_form($view_fields,$restrict));
				///\todo allow JUST certain fields to be "searchable", to be define on the FORM.
				///default_search ?
			//view fields tab
			$tabs->add_tab($me->get_i18n_text('fields_view'),$this->get_presets_form($view_fields,'default'));
			
			if(array_key_exists('help',$options)){
				$h=$this->get_info($options['help'],$view_fields);
			}else{
				$h=$this->get_field_info($view_fields);
			}
			$tabs->shadow_style='none';		
			$tabs->set_default_tab(0);

			$me->tab_see_also_links($tabs);
			if(isset($options["tabs"])){
				foreach($options["tabs"] as $tab_name=>$tab_view_name1){
					$tabs->add_tab($me->i18n($tab_name),$me->get_view_contents($tab_view_name1));
				}
			}

			$tabs->add_tab($me->get_i18n_text('help'),$h);
			$me->parse_tab_info($tabs);
	
			return($me->get_shadow($tabs->out(),'shadow','center'));
			#return($tabs->out());
		}

	}#end ml

	/**\brief this function prints 4 buttons: first, next, last, previous, when they make sense.
	 *
 		Parameters: 
		options.max		: maximun number of pages
		options.page		: current Page
		options.link		: link array options, that allow us to set: mod, ac, etc, in a make_link fashon.
		options.view_title	: the title of the view.

		\todo 10928 multiple pagination schemes.
		\todo 10289 what if im beyond, or below the limit?
		@send2ml
	 * */


	function pagination_scheme($options){
		$me=&$this->caller;

		global $config,$i18n_std,$i18n;
		$max=$options["max"];
		$page=$options["page"];
		$page_limit=$config["pagination_limit"];

		$link_options=$options["link"];
		#	p2($link_options);
		#what are the remainding pages (final pat that does not amount to page_limit.)
		
		$remainder= $max % $page_limit;
			
		#substract remainder from max so its perfectly divisible by page_limit, 
		#and gives us the maximum number of pages available

		$max_pages= ($max - $remainder) / $page_limit ;

		$m2=10;

		if($remainder==0){
			#We are in the LAST page, go back
			$max_pages--;
		}
		
		$txt="";	#output buffer

		#section 1, simple navigation
		
		if($max_pages!=0){
			if($page!=0){
				#first page link appears only when im not on it.
				$a=$link_options;
				$a["page"]=0;
				$txt.=$this->make_link($a,$i18n_std["pagination"]["first"],"page_link")."&nbsp;&nbsp;";
				if($page!=1){
					$a["page"]=$page-1;
					$txt.=$this->make_link($a,$i18n_std["pagination"]["previous"],"standard_link page_link")."&nbsp;&nbsp;";
				}	
			}
			if($page!=$max_pages){
				#LAST, and NEXT page links appear only when i'm not on the last page.
				$a=$link_options;
				if($page!=($max_pages-1)){
					$a["page"]=1+$page;
					$txt.=$this->make_link($a,$i18n_std["pagination"]["next"],"standard_link page_link")."&nbsp;&nbsp;";
				}			
				$a["page"]=$max_pages;
				$txt.=$this->make_link($a,$i18n_std["pagination"]["last"],"standard_link page_link")."&nbsp;&nbsp;";		
			}
			#s3ction 2, each page of the results, for the "pagination lawyers"

			$txt.="<br />";
			$txt.=$i18n_std["pagination"]["go"].":";	
			$pick = '';
			
			if($max_pages>$m2){
				//this means it has more than m2(10) pages
				//so we must show 1 .. x-m2/2,x-2,x,x+1,x+2,x+m2/2,last


				$d=$page - $m2/2;//=5
				$max_d  = $page+$m2/2;
				if($d<0){
					//$page>$m2/2
					$max_d+=-$d;
					$d = 0;
				}
				if($max_d >= $max_pages){
					$d = $max_pages - $m2;
					$max_d = $max_pages;
				}


				while($d<=$max_d){
					$new_range[$d]= $d;
					$d++;
				}
				
			}else{
				$new_range = range(0, $max_pages);
			}
			//$txt.=" / page=$page ".gp2($new_range);
			foreach($new_range as $k=>$v){
				//damage
				//if($v>$m2){break;}

				if($page==$v){
					#actual link, is useless.
					$txt.="&nbsp;<a class='standard_link current_page_link' href=#>".(1+$v)."</a>";
				}else{
					$txt.="&nbsp;&nbsp;";
					$a=$link_options;
					$a["page"]=$v;
					$txt.=$this->make_link($a,1+$v,"standard_link page_link")."&nbsp;";
			
				}	
			}
		}else{
			#there's only one page, no need for all this stuff.
		}
		#setcion 3, display information
		#
		$txt.="<br />";
			
		if($max_pages!=0){
			#gives something like: Page X of Y 
			$txt.=	$i18n_std["pagination"]["page"].":".
				"<b>".(1+$page)."</b>".$i18n_std["pagination"]["of"].
				"<b>".(1+$max_pages)."</b>"."&nbsp;&nbsp;|&nbsp;&nbsp;";

		}
		#$i18n_std["pagination"]["total"]." ".

		$txt.=	$options["view_title"].": ".
			"<b>$max</b> &nbsp;";
		
		return($txt);
	}

	/**
	 * this will set an array to the currently highlited search.
	 * that is, if you *want* highlighted search.
	 * \todo disable search term highlighting (and searching)
	 *
	 * \todo improve this, use class
	 *
	 * recieve
 [0] => Array
        (
            [word] => 0
            [field] => *
        )

    [ar] => Array
        (
            [word] => ar
            [field] => nombre
        )

    [por] => Array
        (
            [word] => por
            [field] => estado_id
        )

    [def] => Array
        (
            [word] => def
            [field] => proyecto_id
        )
	 * 
	 * */
	function set_search_words($words){
		$me=&$this->caller;
		$me->current_search["words"]=array();
		$me->current_search["high"]=array();
		$me->current_search["fields"]=array();
		foreach($words as $k=>$v){
			$me->current_search["words"][$k]="/".str_replace('/','\/',$v['word'])."/i";
			$me->current_search["fields"][$k]=$v['field'];
			//$me->current_search["high"][$k]='<span class="hst">'.$v['word'].'</span>';
			$me->current_search["high"][$k]='{{'.$v['word'].'}}';
		}
	}

	/** \brief turns data into a template.
	 * @param $options[template] the name of the template
	 * @param $options[data] 2-dimensional array with data.(q2obj's output)
	 * @param $options[paging] The pagination widget.

	\notes
	Everything that you put on i18n, will be visible from here, how's that...

	905
	i18n prefix is added so we dont have any compatibility problems.

	This section will draw the information, using a template.
	hopefully, this is not too complex, if so don't worry, its not that hard (i think)

	debugging
	use (at te end):
	\code
	###echo("<h1>template</h1>");p2($tmpl);echo("<h1>result</h1>");p2($tree);
	#p2($tree);		
	\endcode
	* */
	function table2template($options){
		global $i18n;
		$me=&$this->caller;
		$pghtml=$options["paging"];
		$final=$options["data"];
		$tmpl=$options["template"];

		$i18n[$me->table]["pagination_control"]=$pghtml;
		foreach($final as $kt1=>$vt1){
				
			foreach($i18n[$me->table] as $kt=>$vt){
					//905
					$final[$kt1]["i18n_".$kt]=$vt;
			}		
		}		
			
		$tree=array('isnode'=>1);
		$tree['HEADER']=array();
		$tree['FOOTER']=array();
		$tree['CONTENT']['LOOP']=array(
			'HEADER'=>$i18n[$me->get_table_name()],	
			'FOOTER'=>$i18n[$me->get_table_name()],	
			'CONTENT'=>$final,
			'isnode'=>1,
		);
		
		$tmpl=$this->parse_add_files($tmpl,'INCLUDE_');
		std::log("Available keys to the Template:".implode(" ,<br/> ",array_keys($final[0])),'TEMPLATE');
		return($this->parse_template($tree,$tmpl,'CONTENT','_'));
	}
	/**
	 * @brief returns a simple search box, with a search text input , and a [search button]
	 *
	 *
	 * calls restore_value(),
	 * seems read only
	 * */
	function get_simple_search_form(){
		global $i18n_std;
		$me=&$this->caller;
		$f=new form();
		$f->template=file_get_contents(SHARED_MODULES_DIR.'templates/simple_search.html');//magic
		$f->in_template=1;
		///$f->style='';
		$f->strings=$i18n_std['simple_search'];
		///\todo 19273 a combo box with all previous searches.
		//FIX ME FIXME p2($_GET);
		$f->add_field(array('name'=>'__search_term','type'=>'text','value'=>str_replace("\\",'',$this->remove_html($me->restore_value('__search_term')))));
		$f->add_field(array('name'=>'mod','value'=>$me->program_name(),'type'=>'hidden'));
		$f->add_hidden_field('view_id',$me->current_view);

		//shortcuts
		$dx1='';

		if(is_array($this->options['shortcuts'])){
			foreach($this->options['shortcuts'] as $search=>$text){
				$dx1.=	$me->mkl(array(
					'__search_term'=>$search
				),$me->get_i18n_text($text,'')).'&nbsp;&nbsp;&nbsp;';
			}
		}
		//.'&nbsp;&nbsp;'
		$f->add_field(array('name'=>'links','simple'=>$dx1,'type'=>'simple'));
		
		$f->add_field(array('name'=>'ac','value'=>$_GET['ac'],'type'=>'hidden'));
		$f->add_submit_button(array('action'=>$_GET['ac'],'label'=>$me->get_i18n_text('search_ok')));
		//p2($f);
		return($f->out());
	}

	/**\brief This function returns a form, that's used in the ML() "advanced options" section.
	 * @param $available_fields a list of fields 
	 * @param $view name the name of the view
	 * \todo multiple views get saved.
	 * field select
	 * */

	/** 
	 * @brief simplifies session access 
	 * from SESSION
	 * this spects an array, and saves it's keys.
	 * */
	function set_presets($ar,$udf_view='default'){
		global $mydir;
		$me=&$this->caller;
		$_SESSION[$mydir]['usr']['field_presets'][$me->program_name()][$udf_view]=array_keys($ar);
	}

	function get_presets_form($available_fields,$view_name='default'){
		global $mydir;
		$me=&$this->caller;
		///\todo if nothing is selected, create the selection
		
		$first_run=0;
		if(!array_key_exists('field_presets',$_SESSION[$mydir]['usr'])){
			$_SESSION[$mydir]['usr']['field_presets']=array();
		}	
		if(!array_key_exists($me->program_name(),$_SESSION[$mydir]['usr']['field_presets'])){
			$_SESSION[$mydir]['usr']['field_presets'][$me->program_name()]=array();
		}
		if(count($_SESSION[$mydir]['usr']['field_presets'][$me->program_name()]['default'])==0 || 
			!array_key_exists('default',$_SESSION[$mydir]['usr']['field_presets'][$me->program_name()])){

			$pfields=array();
			//remove those that have default_display==0 from default form.
			foreach($available_fields as $k=>$field){
				if(array_key_exists('default_display',$field)){
					if($view_field['default_display']==0){
						
					}else{
						$pfields[$k]=$field;
					}
				}else{
					$pfields[$k]=$field;
				}
			}
			//1,2,4
			$this->set_presets(array_keys($pfields),"default");
			//TODO: allow multiple view presets.	
		}
		//my_op
			
		$presets=$this->get_presets($view_name);
		$fs=new form();
	//	$fs->style='show_fields';
		
		///$fs->collapsible=1;
		$fs->strings=array();///array('_form_title'=>$me->get_i18n_text('fields_view'));

		$op4=array();
		$value_fields=array();
	//	if($first_run){
	//		$value_fields=$pfields;
	//	}else{
			$value_fields=$available_fields;	//really?
	//	}
		foreach($value_fields as $k=>$v){
			if($k!=$me->id){
				$op4[$k]=$me->fi($k);
			}	
		}
		///\todo validate.

		$fs->add_field(array('type'=>'hidden','name'=>'__search_term','value'=>$this->remove_html($me->restore_value('__search_term'))));
		$fs->add_field(array('type'=>'hidden','name'=>'mod','value'=>$me->program_name()));
		$fs->add_hidden_field('view_id',$me->current_view);
		$fs->add_field(array('type'=>'hidden','name'=>'page','value'=>$_GET['page']));
		$fs->add_field(array('type'=>'hidden','name'=>'sort','value'=>$_GET['sort']));
		$fs->add_field(array('type'=>'hidden','name'=>'sort_direction','value'=>$_GET['sort_direction']));


		$fs->add_field(array(
			'check_all'=>1,
			'i18n_text'=>$me->get_i18n_text('fields'),
			'i18n_help'=>$me->get_i18n_text('help_fields'),
			'name'=>'fields',
			'type'=>'checklist',
			'options'=>$op4,
			'values'=>$presets));
		$fs->add_submit_button(
			array(
				'label'=>$me->get_i18n_text('fields_btn'),
				'action'=>$_GET["ac"])
			);
		return($fs->out());
	}
	/**
	 * array_do($list,'me',123);
	 * */
	function array_do($array,$key,$value,$action='kill'){
		$a=array();
		foreach($array as $k=>$line){
			if(array_key_exists($key,$line) && $line[$key]==$value){
				//action
				if($action=='kill'){
					//don't add
				}else{
					$a[$k]=$line;
				}
			}else{
				$a[$k]=$line;
			}
		}
		return($a);
	}
	/**	
	* THESE FIELDS ARE THE ONES BEING SHOWN AT THE MOMENT (by default, ID is always visible.)
	* TODO: add this to VIEW, so you can have "hidden" fields!.
	*
	* @param $view_fields a fields struct
	* @returns an array with keys: cff,current_fields
	* me is readonly
	*/
	function get_preset_fields($view_fields){
		global $mydir;
		//cff
		$me=&$this->caller;	
		$current_fields=array($me->id=>$me->id);
		//|| count($cff)==1

		//retrieve old fields selected
		//..
		if(!isset($_GET["fields"])){
			$_GET["fields"] = $this->get_presets();
		}
		//in the session:
		
		if(count($_GET["fields"])==0){
			//meaning Every field is OUT
			//\todo 6.2 except those that are marked as NOT in view by default, on the view marker
			$view_fields=$this->array_do($view_fields,'default_display',0);
			$cff=$view_fields;
			
			$current_fields=$this->aa(array_keys($view_fields));
			//save this:
			$this->set_presets($current_fields);
		}else{
			//people should not be able to remove the ID, since it contains the checkbox Functionality!.
			if(isset($me->fields[$me->id]) ){
				$cff=array($me->id=>$me->fields[$me->id]);
			}else{
				$me->error("EL CAMPO:".$me->id." (this.id) NO ESTÁ EN LA LISTA DE CAMPOS.");
				return(array());
			}
			foreach($view_fields as $k=>$field){
				if(in_array($k,$_GET["fields"])){
					$current_fields[$k]=$k;	
					$cff[$k]=$field;
				}
			}
		}
		return(array('current_fields'=>$current_fields,'cff'=>$cff));
	}

	/** \defgroup presets Presets Utilities
	 * this functions allow the user to have "custom" views, based on standard views
	 * \todo views have fields that show ONLY on list, not by default.
	 *
	 * */
	//@{	
		
	/**
	 * from SESSION, or user data, whatever comes first.
	 * \todo 901 validate $_GET
	 * \todo 902 validate fields in range (in available)
	 * \todo 903 save that to .rc.php file
	 * ME READONLY
	 * */
	function get_presets($udf_view='default'){
		global $mydir;
		$me=&$this->caller;
		if(isset($_GET["fields"])){
			#$available_fields=$_GET["fields"];
			$_SESSION[$mydir]['usr']['field_presets'][$me->program_name()][$udf_view]=$_GET["fields"];
		}

		return($_SESSION[$mydir]['usr']['field_presets'][$me->program_name()][$udf_view]);
	}

	//@}
	/**
	 * \todo turn dates into date-ranges: [ymd] becomes [ymd to ymd]
	 * \todo add option [exact match/contains/starts with/ends with]
	 * \todo define what you want: custom fields
	 * on a view:
	 * field can Appear like this:
	 * on view
	 * on search thingie
	 * on view_fields mini-form.
	 * is default display.
	 * \bug WARNING: reads from get	 *$_GET['sort_direction'], et al	 please fix
	 * 
	 * @param $fields a field list structure
	 * @param $restrict an SQL where clause (usually inherited from the view's one)
	 * @returns HTML representing an advanced search form.
	 * */
	function get_advanced_search_form($fields,$restrict='1=1'){
		global $i18n;
		$me=&$this->caller;
		
		$fields=$this->fields_fill($fields);

		foreach($fields as $field_name=>$field){
			$fields[$field_name]['value']=$this->remove_strange_chars($_GET['search'][$field_name]);
			if(in_array($field['type'],array('list','glob','checklist'))){
				if(is_array($this->remove_strange_chars($_GET['search'][$field_name]))){
					$fields[$field_name]['values']=$this->remove_strange_chars($_GET['search'][$field_name]);
				}else{
					$fields[$field_name]['values']=array($this->remove_strange_chars($_GET['search'][$field_name]));
				}
			}
		}
		foreach($fields as $k=>$field){
			$f->fields[$k]['name']='search['.$field['name'].']';
			$f->fields[$k]['name']='search['.$field['name'].']';
			$f->fields[$k]['i18n_text']=$me->fi($k);
			$f->fields[$k]['i18n_help']=$me->fh($k);
		}

		$fields=$this->fields_any($fields);
		
		include(STD_LOCATION.'include/advanced_search.php');


		$fields[$me->id]['type'] = 'number';
		$fields[$me->id]['size'] = 3;
		///$fields[$me->id]['options'] = $me->q2op('select id,id as name from '.$me->get_table_name().' WHERE '.$restrict);


		//FIX: LABELS
		foreach($fields as $k=>$field){
			//$f->fields[$k]['name']='search['.$field['name'].']';
			$fields[$k]=$me->field_make_foreign($field);
			if($field['type']=='textarea'){
				$ffields[$k]['type']='text';
			}
			$fields[$k]['name']='search['.$field['name'].']';
			$fields[$k]['i18n_text']=$me->fi($k);
		}
		
		include(STD_LOCATION.'include/widget_init.php');
		$ad=new advanced_search();

		//p2($fields,'red');
		$ad->set_fields($fields);
		if(isset($_GET['search'])){
			$ad->set_values($_GET['search']);
		}else{
			//use default values
		}

		$ui = $ad->user_interface();
		$f=new form();
		$f->set_title($me->get_i18n_text('advanced_search'));
		$f->add_submit_button(array('action'=>$me->current_action,'label'=>$me->get_i18n_text('search_ok')));
		$f->add_hidden_field('mod',$me->program_name());
		$f->add_hidden_field('page',$_GET['page']);
		$f->add_hidden_field('sort',$_GET['sort']);
		$f->add_hidden_field('sort_direction',$_GET['sort_direction']);
		$e = $f->bare_fields();
		$t = array();
		$t['head']=$e['head'];
		$t['foot']=$e['foot'];
		$t['hidden_fields']=$e['hidden_fields'];
		$t['buttons']=$e['buttons'];
		$t['form_content']=$ui;
		$ui2 = common::template(STD_LOCATION.'shared/templates/advanced_search_container.php',$t);
	
		return($ui2);
	}
	/** me readonly */
	function fields_any($fields){
		$me=&$this->caller;
			
		foreach($fields as $k=>$v){
			if(in_array($fields[$k]['type'],array('list','glob','checklist'))){
				$fields[$k]['options']=array_merge(array('__any_value'=>$me->get_i18n_text('any_value')),$fields[$k]['options']);
			}else{
				//$fields[$k]['value']='not a list';
			}
		}
		
		return($fields);
	}
		
	/** 
	 * me is readonly
	 * */
	function fields_fill($fields){
		$me=&$this->caller;
		foreach($fields as $k=>$field){
			//if is select, or if is foreign add a optino:any
			$fields[$k]=array_merge($me->fields[$field['name']],$field);
		}
		return($fields);
	}

}

?>
