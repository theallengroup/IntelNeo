/**
Standard Divisor, you might want to use &nbsp; or something like that.
*/
var std_divisor="";
var std_please_select="---";


/**
This displays the Next level, if available, or leaves the thing alone.
this is also responsible for setting the actual inputr's value (a hidden one).

*/
function std_tree_changed(container,spath,item,input_id){
	
	var path=std_remove_slashes(spath);
	var parent_tree=eval(path);
	var child_path=path+'[\''+item+'\']';
//	var child_tree=eval(child_path);
	var child_tree=parent_tree[item];

	//alert(item in parent_tree);alert(item);
	if(item!=-1){
		if(document.getElementById(input_id)){
			document.getElementById(input_id).value=item;	

		}else{
			alert('Error: no input:'+input_id+' in form.');
		}

		if(typeof(child_tree)=='object'){
		//alert('you selected an object:'+item+' on:'+path+' accessing child_path:'+child_path);
		//var tree=eval(path);
		//alert(tree[item]);
			if(item in parent_tree){
				/// dbg alert('elements in :'+child_path+' : '+count(child_tree));

				//this test for the end .
				//the leaf, shich is an object with just a property: __name.
				if(count(child_tree)==1 && ('__name' in child_tree)){
					std_remove_child(container);
				}else{
					std_display_tree(container+'_child',child_path,child_tree,input_id);

				}

			}else{
			
				alert('error:'+item+' is not in path:'+path+', that has '+count(child_tree)+' items.');
			}
		}else{
			//alert('you selected:'+item);
			std_remove_child(container);
		}
	}else{
		//void option below
		document.getElementById(input_id).value='-1';//TODO default option.
		std_remove_child(container);
	}

//	document.getElementById(path+'_child').innerHTML=('selected '+item+' on path '+path);
}
function std_remove_child(container){
	document.getElementById(container+"_child").innerHTML='';
}


/** puts the select on the tree*/
function std_display_tree(container,path,mytree,input_id){
	if(document.getElementById(container)){
		document.getElementById(container).innerHTML=std_get_select(container,path,mytree,input_id);
		//document.getElementById(input_id).value=document.getElementById(container).selectedIndex
	}else{
		alert('Error: no such item:'+path);
	}
}

/** returns the html required for the next level of the input.*/

function std_get_select(container,path,mytree,input_id){
	/// dbg alert('this :'+path+' inside container:'+container+' has:'+count(mytree)+' elements');

	var out=std_divisor+"<select name=\""+path+"\" onchange = \"std_tree_changed('"+container+"','"+std_slashes(path)+"',this.value,'"+input_id+"')\">";
	out+='\n<option value="-1">'+std_please_select;//+tree["__name"]

	var i=null;
	/// dbg desc1(mytree,'mytree@'+path);
	for(i in mytree){
		var dbgst=' ('+i+')';
		dbgst='';	//remove me if debugging

		if(typeof(mytree[i])=='object' && '__name' in mytree[i]){
			out+='\n<option value="'+i+'">'+mytree[i]['__name']+dbgst;
		}else if(typeof(mytree[i])=='object'){
			out+='\n<option value="'+i+'">Error: Uknown __name:'+i+dbgst;
			//a leaf beneath me, do nothing.
		}else if(i == '__name'){
			//Do nothing, we don't displays the name.

		}else{
			out+='\n<option value="'+i+'">'+mytree[i]+dbgst;
		}
	}
	out+='</select>\n<span id="'+container+'_child"></span>';
	/// dbg alert(out);
	return(out);
}
/**
a small hack

changes ' to ::: , so you can have those damn things inside the Quoting Hell.
*/
function std_slashes(path){
	var p = path.replace(/'/gi,':::');
	return(p);
}

/**
inverse of 
*/
function std_remove_slashes(path){
	var p = path.replace(/:::/gi,"'");
	return(p);
}
