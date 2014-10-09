/**
global var: std_tree_selected_node indicates the ID of the currently selected node.
*/
var std_tree_selected_node=null;

/** this removes a DOM node
 * @param node a string with the name of the _row_  (str_tree_row_*) node to be deleted. 
 */
function std_tree_remove(node){
	if(confirm(std_i18n['tree_confirm'])){
		var n=document.getElementById(node);
		if(!n){
			alert('uknown node: '+node+' ! stdjs002');
		}else{
			n.parentNode.removeChild(n);
		}
		
	}else{

	}
}

/**
gets a/b/c returns a/b
@param node a string with a path name, like root/node1/part0
*/
function std_dirname(node){
	var x=node.split('/');
	return(node.substr(0,node.length- x[x.length-1].length -1));
}
/**
 * @param node any
 */
function std_basename(node){
	var x=node.split('/');
	return(x[x.length-1]);
}

/** clones the given node and returns the new node's DOM object. 
 * @param node the _row_ node 
 */
function std_tree_clone(node){
	var n=document.getElementById(node);
	if(!n){
		alert('uknown node: '+node+' ! stdjs001');
	}else{
		var nn=n.cloneNode(true);
		n.parentNode.insertBefore(nn,n.nextSibling);
		return(nn.id);
		//alert(new_nodename);
	}
}


/**
 * @param node an ID of a DOM node, like root/a/b/c or something like that.
 * highlighting will take place for a few seconds (3)
 */
function std_tree_highlight(node){
	document.getElementById(node).style.backgroundColor='rgb(255,255,200)';
	setTimeout("document.getElementById('"+node+"').style.backgroundColor='transparent';",3000);
}

//todo
//remains to be seen how the fuck am I going to do this litle mosnter...
function std_rename_node(node){

}
/**
* \todo instead of this, we could use a small form, to appear next to the mouse?
* 
*/
function std_tree_add(last_node,str_texts){
	var name1=std_i18n['tree_name']
	var type1=std_i18n['tree_type']
	var is_array1=std_i18n['tree_is_array']
	var value1=std_i18n['tree_value']
	
	var name=prompt(name1,'new_node');
	var is_array2=confirm(is_array1);
	if(is_array2){
		type1='array';
		value1=[];

	}else{
		var type=prompt(type1,'text');
		var value=prompt(value1,0);
	}

	var row=document.createElement("tr");
	var cell=document.createElement('td');
	var me=std_dirname(last_node)+'/'+name;

	row.id="std_tree_label_"+me;

	cell.id="std_tree_row_"+me;

	var cell2=document.createElement('td');
	cell2.id="std_tree_input_"+me;

	row.appendChild(cell);
	row.appendChild(cell2);

	cell.style.verticalAlign='top';
	cell2.style.verticalAlign='top';
	cell.innerHTML='le';
	cell2.innerHTML='ri';
	var n=document.getElementById(last_node);
	n.parentNode.insertBefore(row,n);



//	alert(n.parentNode.getElementsByTagName('input').length);

	//alert(last_node);
}

/**
 * this wil remove the selected node.
 */
function std_tree_delete_selected_node(){
	std_tree_remove("std_tree_row_"+std_tree_selected_node);
}

function std_tree_clone_selected_node(){
//	var new_name=prompt(std_i18n['tree_new_name'],std_i18n['tree_copy']+std_basename(std_tree_selected_node));
	
	var nn2=document.getElementById("std_tree_row_"+std_tree_selected_node);
	if(!nn2){
		alert(std_i18n['tree_please_select']);
		return(void(null));
	}
	var nn=std_tree_clone("std_tree_row_"+std_tree_selected_node);

	std_tree_rename_selected_node();

	/*node swaping*/
	var x=nn2.id
	var y=nn2.nextSibling;
	var tnext=nn2.nextSibling;
	var tparent=nn2.parentNode;
	//x=y
	y.parentNode.replaceChild(x,y);
	tparent.insertBefore(y,tnext);

}
function std_tree_append_item2_selected_node(){

}
function std_tree_append_tree2_selected_node(){

}

/**
	@param all2 what you get from getElementsByTagName
	@param prop a property name, like for instance, the ID
	@param new_node the new property name
	@param old_node the old property name
	@param prefix the part that remains intact, and is common among the properties, like std_row_

	this will recurse trrough all the nodes of a DOM element, and change a property, like the ID, for instance.
	
	Usage Example
	\code
	std_tree_recurse_change(node.getElementsByTagName('tr'),'id','a/b/c/d','some_node_name',"std_tree_row_");
	\endcode

*/
function std_tree_recurse_change(all2,prop,new_node,old_node,prefix){
	for(var i=0;i<all2.length;i++){
		if(0 && all2[i][prop]==prefix+old_node){
			//all2[i][prop]=new_node;
		}else if(all2[i][prop].match(new RegExp("^"+prefix+old_node))){

			
			//DO NOT USE THE NEXT LIKNE (its buggy)
			//all2[i][prop] = prefix+ new_node + all2[i][prop].substring(prefix+old_node.length , all2[i][prop].length);
			all2[i][prop] = all2[i][prop].replace(old_node,new_node)
		}else{
			alert(all2[i][prop]+"\ndoes not match:"+prefix+old_node+", when trying to change \n"+old_node+"\n to\n "+new_node+"\n on prop:"+prop);		
		}
	}
}
/**

*/
function std_tree_rename_selected_node(){
	///alert('Selected Node:'+std_tree_selected_node);

	if(std_tree_selected_node==null){
		alert(std_i18n['tree_please_select']);
		return(void(null));
	}
	var node=document.getElementById('std_tree_row_'+std_tree_selected_node);
	if(node==null){
		alert('Severe error: the node does not exists!');
		return(void(null));
	}
	//new node name?
	var new_name=prompt(std_i18n['tree_rename'], std_basename(std_tree_selected_node));
	if(new_name==null || new_name==''){
		return(void(null));
	}
	if(new_name==std_basename(std_tree_selected_node)){
		//no changes were made
		return(void(null));
	}
	//check existance
	
	//	

	var full=std_dirname(std_tree_selected_node)+"/"+new_name;

	//TDs
	var all=node.getElementsByTagName('td');
	var all1=node.getElementsByTagName('tr');
		//trs:id
		// tds:id
		//a.href
		//select.name
		//input.name
		//textarea.name
		// 
	var links=node.getElementsByTagName('a');
	
	//changes for TD
	//id moves from: std_tree_label_root/abc to  std_tree_label_root/xyz
 
	var new_label="std_tree_label_"+full;
	var new_input="std_tree_input_"+full;
	var old_label="^std_tree_label_"+std_tree_selected_node
	var old_input="^std_tree_input_"+std_tree_selected_node
	var old_label2="std_tree_label_"+std_tree_selected_node
	var old_input2="std_tree_input_"+std_tree_selected_node
	//td
	for(var i=0;i<all.length;i++){
		//Direct kids, recieve different treatments from their children
		//

		//my kids
		if(all[i].id==old_label2){
			all[i].id=new_label;
		}else if(all[i].id==old_input2){
			all[i].id=new_input;
		//my grand-chldren
		}else if(all[i].id.match(old_label)){
			//alert('its a label');
			all[i].id = new_label+"/"+all[i].id.substring(old_label.length , all[i].id.length);
		}else if(all[i].id.match(old_input)){
			all[i].id = new_input+"/"+all[i].id.substring(old_input.length , all[i].id.length);
			//alert('its an input');
		}else{
			//U can't touch this (na na na-na)
			alert(all[i].id+":wtf???");
		}
		//alert(all[i].id);
	}
	//tr
	//test?
	std_tree_recurse_change(node.getElementsByTagName('tr'),'id',full,std_tree_selected_node,"std_tree_row_");
	std_tree_recurse_change(node.getElementsByTagName('table'),'id',full,std_tree_selected_node,"std_tree_table_");
/*	
	var new_tr="std_tree_row_"+full;
	var old_tr="^std_tree_row_"+std_tree_selected_node
	for(var i=0;i<all1.length;i++){
		if(all1[i].id.match(old_tr)){
			all1[i].id = new_label+all1[i].id.substring(old_tr.length , all1[i].id.length);
		}else{
			alert(all1[i].id+":wtf???");		
		}
	}
*/
	//INPUTS
	var taglist=new Array('textarea','select','input');
	for(var i=0;i<taglist.length;i++){
		std_tree_recurse_change(node.getElementsByTagName(taglist[i]),'name',full,std_tree_selected_node,"");
	}

	//LINKS
	var old_href=std_tree_selected_node;
	var ch=0;
	for(var i=0;i<links.length;i++){

		//change name for primary link
		if(ch == 0 && links[i].innerHTML == std_basename(std_tree_selected_node)){
			links[i].innerHTML=new_name;
			ch = 1;//ch means, this only happens once: (@ the beginning, the first link, MY link.)
		}		
		links[i].href = links[i].href.replace(new RegExp(old_href,''),full);
		//alert("new href:"+links[i].href);
	}

	//finally
	node.id = "std_tree_row_"+full;
	//alert("new node:"+node.id);
//	alert(node.innerHTML);
	//set the OLD node to this node (so that highlight won't break)
	std_tree_selected_node=full;
	//select this node (since now it's the currently selected node)
	std_tree_select_node(full);
}
function std_tree_paint(node,color){
	if(node!=null && document.getElementById("std_tree_row_"+node)!=null){
		document.getElementById("std_tree_row_"+node).style.backgroundColor=color;
	}
}
function std_tree_root(node){
	return(node.split("/")[0]);
}
function std_tree_select_node(node){
	std_tree_paint(std_tree_selected_node,'transparent');
	std_tree_selected_node=node;
	document.getElementById("std_tree_status_"+std_tree_root(node)).innerHTML=node;
	std_tree_paint(node,'rgb(255,255,200)');
}

