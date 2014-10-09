/**
max is inclusive
*/
function std_table_editor_add_row(tbody_id,max){
	var tbody=document.getElementById(tbody_id);

	//tbody.childNodes(0);
	//tbody.insertBefore(last child, first child)

	var kid=tbody.childNodes[0];
	var new_kid=kid.cloneNode(true);
	//change ID

	var c=std_table_editor_row_count(tbody);
	if(c<=max){
		tbody.insertBefore(new_kid,null);
	}else{
		//no warning
	}

	
}
function std_table_editor_delete_row(element,min){
	
	var p=element.parentNode;//td
	var p2=p.parentNode;//tr
	//alert(p2.tagName);

	var tbody=p2.parentNode;
	var c=std_table_editor_row_count(tbody);
	if(c>min){
		p2.parentNode.removeChild(p2);
	}else{
		//no warning
	}


//	var row=document.getElementById(row_id);	
//	row.parentNode.removeChild(row);
}
function std_table_editor_row_count(element){
	var c=0;
	for(var i=0;i<element.childNodes.length;i++){
		if(element.childNodes[i].nodeType==1){
			c++;
		}
	}
	return(c);
}
