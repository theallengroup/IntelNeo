<?php
	/** \brief shows a view
	 * a view is a list of fields, handlers and links,
	 * examples of views, ca vbe: the Edit form, the Add form, and the table, where many elements are shown.
	 *
	 calls std::Menu().
	
	 if you call this function, from an custom defined action, make shure you define the key 
	 "current_action"  -> your action, so links will work, on the view structure

	This system allows user defined handlers, 
	so you can have a view type XXX and define a handler for it.!
	that way you can extend the functionality, in a STILL generic way, cool! 
	(and you get to override view handlers, and go nuts on your own.)
	
	also, this will ALLOW() the user to all of its actions, so be careful.

	@param $argv
	its an array:
	key 0: view name
	key 1 override array to merge with (pretty cool!,eh?)
	
	 */
function std_get_view_contents(&$me,$argv){
	global $std_views,$i18n_std,$i18n;	
	$ret='';//nothing!
	if($me->on_before_view()){
		$nomenu=0;
		if(isset($argv[0]) && $argv[0]!=''){
			$nomenu=1;
			$me->current_view = $argv[0];
		}

		$me->log('ac_view loads view:'.$me->current_view,'VIEW');
		$view1=$me->load_view($me->program_name(),$me->current_view);
		$view1 = $me->parse_view($view1);
		//please allow me to TOTALLY trash wharever you where doing.
		//thanks.
		if(isset($argv[1])){
			$view1=	$me->inherit($view1,$argv[1]);
		}
	
		if(!array_key_exists('no_menu',$view1) && $nomenu==0){
			$me->menu();	
			
		}
		$me->on_before_view_display();

		if($view1==0 || count($view1)==0 ||!is_array($view1)){
			$me->i_error('no_such_view','std075',array('view'=>$me->current_view,'mod'=>$me->program_name()));
			echo(b2());
			return(0);
		}

		#p2($std_views);
		#
		#
		
		$vh="vh_".$view1["type"];
		if(!in_array($vh,get_class_methods($me))){
			$me->log("no view handler:".$vh,'ERROR');
			$me->i_error('no_view_handler','std050');
		}else{
			#add privs, so everything HERE is automagically possible, and OK.
			#please be careful.
			#p2($view1);
			$p=array(); #all my privileges.
			$modifiers=array(
				'side_actions',
				'down_actions',
				'actions'
			);

			$me->whoami=$me->get_role_name($me->mod_and_ac());

			foreach($modifiers as $k2=>$ac_list){
				if(array_key_exists($ac_list,$view1)){
					foreach($view1[$ac_list] as $k3=>$priv1){
						if(is_array($priv1)){
							$lbl=$priv1['label'];
							$act=$priv1['action'];
						}else{
							$lbl=$priv1;
							$act=$priv1;
						}
						//p2(array($priv,$act));
						$p[]=array(
							'action'=>$me->mod_and_ac($me->program_name(),$act),
							'privilege_name'=>
							$i18n[$me->program_name()]['table_plural']." : ".
							$me->get_i18n_text($lbl),

							#whomever i am NOW, must be what i run these babys on
							/** \todo handle GUEST case.*/
							'role_name'=>$me->whoami,
						);
					}#end for
				}	
			} 
			/** 
			 * only allow for views to define automatic link following on specific cases, when the view is defined to do so
			 * using the auto_privilege keyword.
			 * this gives us security, and backwards compatibility/practicality
			 * */
			if(isset($view1["auto_privilege"]) && $view1["auto_privilege"]==1){
				foreach($p as $k1=>$rec){
					$me->privilege_manager->add_privilege($rec);
				}
			}
			
			/*if current_actions id not defined, and the view was called from an custom action, fix the links:*/
			if(('view:'.$me->current_view != $me->current_action) 
				&& ($me->current_action != 'view') && 
					(array_key_exists('current_action',$view1)==0)){
				$view1['current_action']=$me->current_action;
		
				$me->log('view links to:'.$view1['current_action'],'VIEW');
			}
			$me->log('CV:'.$me->current_view.' CA:"'.$me->current_action.'"','VIEW');


			#run view
			$view1["fields"]=$me->view_expand($view1["fields"]);
			if(!array_key_exists("column_style",$view1)){
				$view1["column_style"]=array($me->program_name().'_'.$me->id=>'width:4%','__actions'=>'max-width:10%;width:10%');
			}
			$ret=$me->$vh($view1);
		}
		$ret.=$include_contents;
		$ret.=$me->on_after_view();

	}
	return($ret);
}
?>
