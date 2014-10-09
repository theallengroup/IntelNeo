	function std_log(txt){
		//do nothing...
		//document.getElementById("log").innerHTML+="<br>"+txt;
	}
	function std_option_up(id,op){
		//nothing
	}
	function std_highlight_item(id,i,class_name){
		if(document.getElementById(id+"_options_"+i)){
			document.getElementById(id+"_options_"+i).className=class_name;
		}else{
			std_log("cant find:"+id+"_options_"+i);
		}	
	}
	function std_highlight_option(id,i,op){
		var i2=null;
		for(i2 in op){
			if(op[i2].match(new RegExp("^"+std_mask[id],"i"))){
				std_highlight_item(id,i2,"option_normal");
			}
		}
		std_highlight_item(id,i,"option_ontop");

	}
		

	function std_option_scroll(id,op,direction){
		//document.title="down";
		//selected option
		var current=document.getElementById(id).value;
		var input=document.getElementById(id+"_value");
		var i=null;
		if(current!=std_mask[id]){
			//this means it's one of the elements of the list, but haven't decide which one, its choosing
			//so we go Down.
			var previous_element=null;
			var is_next=0;
			for(i in op){
				var same=new RegExp("^"+current+"$","i");
				var alike=new RegExp("^"+std_mask[id],"i");
				if(op[i].match(alike)){
					if(is_next==1){//this element must be selected
							std_set_option(id,i,op[i]);
							std_highlight_option(id,i,op);
							var is_next=2;
							break;
					}else{
						
						if(op[i].match(same)){
							//we found the element
							if(direction=="up"){
								window.status="prev is:"+previous_element;
								//go back, be done.
								//not the first one
								if(previous_element!=null){
									std_set_option(id,previous_element,op[previous_element]);
									std_highlight_option(id,previous_element,op);
									break;
								}
							}else{
								is_next=1;
							}	
						}
					}		
					if(is_next==1){
						//we are at the last option, do nothing
					}
					previous_element=i;

				}
			}
		}//current != null
	

		if(input.value == "__new"){	//at this point, current==mask
			//it's the first option
			//find the next matching element, and set it to me.
			//
			//if there are no matching element, nothing happens
			for(i in op){
				var like=new RegExp("^"+std_mask[id],"i");
				if(op[i].match(like)){
					//at this point, current != mask, meaning we can go up,or down, in the list.
					std_highlight_option(id,i,op);
					std_set_option(id,i,op[i]);
					break;
				}
			}
		}

	}
	function std_change_option(id,ev,op,op_var_name,form_name){
		var key=ev.keyCode;
		//window.status=ev.keyCode;

		var current=document.getElementById(id).value;

		if(key==40){		
			//down
			std_option_scroll(id,op,"down");
			
		}else if(key==38){
			//up
			std_option_scroll(id,op,"up");	
			
		}else if(key==10||key==13){
			//enter
			ev.cancelBubble=true;
		//	document[form_name].onsubmit="return false"
			std_clear_options(id);
			std_mask[id]=current;
			return(false);
		}else{

			var i=null;
			//mask reset
			std_mask[id]=current;
		///	document.title="now mask is:"+current;

			var input=document.getElementById(id+"_value");
			var found=0;
			var alike=new Array()
			for(i in op){
				var reg=new RegExp("^"+current+"$","i");
				var like=new RegExp("^"+current+"","i");
				if(op[i].match(like)){
					alike[i]=op[i];
				}
				if(op[i].match(reg)){
					std_set_option(id,i,op[i]);
					found=1;
					break;
				}
			}
			if(found==0){
				var ophtml="";
				for(i in alike){
					///this.className=\"option_ontop\"
					ophtml+="\n<div id='"+id+"_options_"+i+"' ";
					ophtml+="onmouseover='std_highlight_option(\""+id+"\",\""+i+"\","+op_var_name+")' ";
					ophtml+="onmouseout='this.className=\"option_normal\"' class=option_normal ";
					ophtml+=" onclick=\"std_option_select('"+id+"','"+i+"','"+alike[i]+"');\" >";
					ophtml+='<nobr>'+alike[i]+"</nobr></div>";
				}
				///alert(ophtml);
				document.getElementById(id+"_options").innerHTML=ophtml;
				input.value='__new';
			}
		}
	}
	function std_option_select(id,value,text){
	//	alert("clicked "+id+" value:"+value+" text:"+text);
		std_set_option(id,value,text);
		std_clear_options(id);
		std_mask[id]=text;
		document.getElementById(id).focus();
	}
	function std_set_option(id,value,text){
		document.getElementById(id+"_value").value=value;
		document.getElementById(id).value=text;
	
	}
	function std_clear_options(id){
		document.getElementById(id+"_options").innerHTML="";
	}

//deprecated
//input onblur
//onblur  = "std_clear_options('my_id');"
