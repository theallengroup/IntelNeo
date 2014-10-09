var std_current_tab_name=0;
function std_map_id(id,function_name,aditional_parameters){
		var node=document.getElementById(id)
		//GREAT debugging tool
		/*
		node.style.borderStyle='solid';
		node.style.borderColor='yellow';
		node.style.borderWidth='1';
		alert('applying '+function_name+' to '+id);
		*/
		var tag=document.getElementById(id).tagName;
		var all=document.getElementsByTagName(tag);
		for(var i2=0;i2<all.length;i2++){
			if(all[i2].id==id){
				function_name(all[i2],aditional_parameters);
			}
		}
}
function std_tab_hide(node,aditional_parameters){
	node.style.display='none';
	//node.className='tab_off '+aditional_parameters[0];
}

function std_tab_display(node,aditional_parameters){
	node.style.display='';
//	node.className='tab_on '+aditional_parameters[0];

}

function std_tab_off(node,aditional_parameters){
	node.style.display='none';
	node.className='tab_off '+aditional_parameters[0];
}

function std_tab_on(node,aditional_parameters){
	node.style.display='';
	node.className='tab_on '+aditional_parameters[0];

}

function std_tab_link_unhighlight(node,aditional_parameters){
	node.className='tab_link_off '+aditional_parameters[0];
}

function std_tab_link_highlight(node,aditional_parameters){
	node.className='tab_link_on '+aditional_parameters[0];
}

function std_display_tab(group_name,tab_name,tab_max,span_class){
	std_current_tab_name=tab_name;
	//hide all
	for(var i=0;i<tab_max;i++){
		var id='std_'+group_name+'_'+i;
		std_map_id('std_'+group_name+'_'+i,std_tab_hide,[span_class]);
		std_map_id('std_link_'+group_name+'_'+i,std_tab_link_unhighlight,[span_class]);
	}

	std_map_id('std_'+group_name+'_'+tab_name,std_tab_display,[span_class]);
	for(var i=0;i<tab_max;i++){
		var id='std_'+group_name+'_'+i;
		std_map_id('std_'+group_name+'_'+i,std_tab_off,[span_class]);
	}
	std_map_id('std_link_'+group_name+'_'+tab_name,std_tab_link_highlight,[span_class]);
	std_map_id('std_'+group_name+'_'+tab_name,std_tab_on,[span_class]);

}
