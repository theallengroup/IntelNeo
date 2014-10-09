/** 
* stupid IE fix, won't focus a hidden field.
*/
function std_form_focus_first(){
	for(var i=0;i<document.forms[0].elements.length;i++){
		if(document.forms[0].elements[i].type!='hidden'){
			try{
				document.forms[0].elements[i].focus()
			}catch(e){
				//couldn't do it? no problem, 
				//it was just for usability purposes.
				//no biggie.
			}
			break;
		}
	}
}
