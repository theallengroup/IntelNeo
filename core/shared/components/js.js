var std_global_form_enabled=true;
/** language array.*/
std_i18n=new Array();

function std_form_enable(){
	std_global_form_enabled=true;
}
function std_form_disable(){
	std_global_form_enabled=false;
}
function std_form_check(){
	return(std_global_form_enabled);
}
/**
returns the number of properties of an object, this is usefukl sin .length returns 0 on non-numeric arrays (hash tables)
*/
function count(obj){
	var x=0;
	for (i in obj){
		x++;
	}
	return(x);
}
/**
debugging facility: allows the inspection of an object (non-recursive)
*/
function desc1(obj,txt2){
	var txt=''+txt2+' typeof:'+typeof(obj)+' length:'+obj.length+' count:'+count(obj);
	var j=null;
	for(j in obj){
		txt+='\n'+j+':'+obj[j];
	}
	alert(txt);
}
/**
displays, or hides an element
*/
function std_toggle_element(id){
	var w=document.getElementById(id).style.display;
	if(w!='none'){w='none';}else{w='';}	
	document.getElementById(id).style.display=w;
}
/** 
	for debug backtrace @see b2()
*/
function std_toggle_next(dom_obj){
//	desc1(dom_obj)
//	alert(dom_obj.tagName)
	var d1=dom_obj.parentNode;
	var s=d1.getElementsByTagName('SPAN');
	//alert(s[0].tagName)
	std_toggle_object(s[0]);

}
function std_toggle_object(object1){
	var w=object1.style.display;
	if(w!='none'){w='none';}else{w='';}	
	object1.style.display=w;
}
function std_collapse(id){
	std_toggle_element(id);
}
/**
checks if a given list of elements, are checked.
*/
function check_complete(id,max){
	var x=0;
	var allok=1;
	while(x<max){
		var e=document.getElementById(id+"_"+x)
		if(!e.checked){allok=0;break;}
		x++;
	}
	if(allok==1){
		//document.getElementById(id+"_all").checked=true;
		document.getElementById(id).checked=true;
	}else{
		//document.getElementById(id+"_all").checked=false;
		document.getElementById(id).checked=false;
	}
}
/**
	\todo masc/fem
		selects all checkbox elements from a checklist input 
	\todo fix bug at generator
*/
function check_all(id,max){
	var setvalue='nothing';
	if(arguments.length==3){
		setvalue = arguments[2];
	}

	var i=0;
	var checked_count=0;
	for(i=0;i<max;i++){
		var e=document.getElementById(id+"_"+i)
	
		if(e==null){
			alert("Uknown element:"+id+"_"+i);
		}else{
			if("click" in e){
				if(e.checked==true){
					if(setvalue==0){
						e.click();
					}
					checked_count++;
				}else{	
					e.click();
				}
			}else{
				alert('no click() in e:('+id+'_'+i+')');
				desc1(e)
			}
		}
	}
	if(checked_count==max && setvalue != 0 ){

		check_all(id,max,0);
	}
	if(checked_count==max ){
		//document.getElementById(id+"_all").checked=false;
		document.getElementById(id).checked=false;
	}else{
		//document.getElementById(id+"_all").checked=true;
		document.getElementById(id).checked=true;
	}
	
}
/**
* @param select_id an input id
* value a value.
*/
function std_set_select_value(select_id,value){

	var s=document.getElementById(select_id);
	if(!s){
		alert("no such input:"+select_id);
	}
	var found=0;
	for(var i=0;i<s.options.length;i++){
		var v=s.options[i].value;
		if(v==value){
			s.selectedIndex=i;
			found=1;
			break;
		}
	}
	if(found==0){
		alert('JS error: no:'+value+': in:'+select_id);
	}
}
/**
* cannonical input name.
*/
function std_set_today(input_name){
	var now=new Date();
	std_set_select_value(input_name+"_year",now.getFullYear());
	std_set_select_value(input_name+"_month",1+now.getMonth());
	std_set_select_value(input_name+"_date",now.getDate());
//	document[form_name][input_name+"_month"]=now.getMonth();
//	document[form_name][input_name+"_date"]=now.getDate();

}


function keyC(e){
	if(e.keyCode==75 && e.shiftKey==1){
		document.getElementById('debug_info').style.display='';
	}
};
	
function std_highlight_row(me,high){
	var p= me.parentNode;
	if(p.tagName=='TD'){
	}
	var tr=p.parentNode;
	if(tr.tagName=='TR'){
		var d = tr.className;
		var d6=d;		
		if(d.split(" ").length>=2){
			d6=d.split(" ")[0];//means 2 classes
		}
		if(d6.split("_").length==2){
			var d1=d6.split("_")[0];
			if(high==true){
				tr.className=tr.className+" "+d1+"_highlight"	//list
			}else{
				tr.className=d6;
			}
		}else{
			alert(d6+' is invalid format');
		}
	}

}
/**
* for table views
*/
function std_select_row(me){
	// is it on?
	var c= me.checked;
	//desc1(me);
	var p= me.parentNode;
	//alert(p.tagName);
	if(p.tagName=='TD'){
		//we are inside a table.
	}
	var tr=p.parentNode;
	if(tr.tagName=='TR'){
		//this is the row object.
		var d = tr.className;
		var d6=d;		
		if(d.split(" ").length>=2){
			d6=d.split(" ")[0];//means 2 classes
		}
		//just one class
		//get the part before _ , list_row becomes list
		//alert(d6);
		if(d6.split("_").length==2){
			var d1=d6.split("_")[0];
			if(c==true){
				tr.className=tr.className+" "+d1+"_highlight"	//list
			}else{
				//remove _highlight
				//by letting just the first class.
				tr.className=d6;
			}
		}else{
			alert(d6+' is invalid format');
		}
	}
}
function std_getv(input_name){
	if(document.getElementById(input_name)){
		return(document.getElementById(input_name).value);
	}else{
		if(document.getElementById(input_name+"_year")&&document.getElementById(input_name+"_month")&&document.getElementById(input_name+"_date")){
			var y = document.getElementById(input_name+"_year").value;
			var m = document.getElementById(input_name+"_month").value;
			var d = document.getElementById(input_name+"_date").value;
			if((""+m).length<2){m="0"+m;}
			if((""+d).length<2){d="0"+d;}

			var date_value = y+"-"+m+"-"+d;
			//alert(date_value+". is a date");
			return(date_value);
		}else{
			alert("ERROR: INPUT: "+input_name+" NO EXISTE");
			return "";
		}
	}
}
function std_colorize(field_name,color){
	if(document.getElementById(field_name)){
		document.getElementById(field_name).style.backgroundColor=color;
	}else if(document.getElementById(field_name+"_year")&&document.getElementById(field_name+"_month")&&document.getElementById(field_name+"_date")) {
		document.getElementById(field_name+"_year").style.backgroundColor=color;
		document.getElementById(field_name+"_month").style.backgroundColor=color;
		document.getElementById(field_name+"_date").style.backgroundColor=color;
	}else{
		alert("Error el campo:"+field_name+" no existe.");
	}
}
/**
 * validate required fields in form
 *
 * */
function std_validate_required_fields(func){
	//alert(std_validate_form_flag);
	if(std_validate_form_flag==0){
		return true;
	}
	
	//desc1(submit_button);
	var bad_list=[];
	
	for(var i=0;i<std_required_fields.length;i++){
		var field_name = std_required_fields[i];
		var field_value = std_getv(field_name);
		if(field_value==''||field_value=='1900-01-01'||field_value=='2000-01-01'||field_value=='**INVALID**'){
			bad_list[bad_list.length]=field_name;
			std_colorize(field_name,'#FFDDDD');

		}else{
			std_colorize(field_name,'#FFFFFF');

		}
	
	}
	var labeled_bad_list=[];
	var f=null;
	try{
		f=func(bad_list);
	}catch(e){
		alert(e)
	}
	if(f===false){
		return(false);
	}
	if(f!='' && f!==true){
		labeled_bad_list[labeled_bad_list.length]=f;
	}
	if((bad_list.length>0) || (labeled_bad_list.length>0)){
		for(i in bad_list){
			labeled_bad_list[labeled_bad_list.length]=std_required_fields_labels[bad_list[i]];
		}
		alert("Los siguentes campos son requeridos:\n\n * "+labeled_bad_list.join("\n * "));
		try{
			document.getElementById(bad_list[0]).focus();
		}catch(e){
			//whatever, if it does not exist, 
			//that is not my problem
			//since it could not exist
			//and that is ok too
		}

		return(false);
	}else{
		//everything validates.
		return(true);
	}
}
var std_validate_form_flag=1;
function std_set_submit_button(validate_value){
	std_validate_form_flag=validate_value;
	return true;
}
function std_html2data(tbody){
	var i=0;
	var contents=[];
	var headers=[];
	var row_number=0;
	var column_number=0;
	var in_headers=0;
	for(var i=0;i<tbody.children.length;i++){
		var tr=tbody.children[i];
		if(tr.nodeType==1 && (""+tr.nodeName).toUpperCase()=='TR'){
			contents[row_number]=[];
			column_number=0;
			for(var c=0;c<tr.children.length;c++){
				var td=tr.children[c];
				if(td.nodeType==1 && (""+td.nodeName).toUpperCase()=='TH'){
					in_headers=1;
					headers[headers.length]=td.innerHTML;
					column_number++;
				}
				if(td.nodeType==1 && (""+td.nodeName).toUpperCase()=='TD'){
					contents[row_number][column_number]=td.innerHTML;
					column_number++;
					in_headers=0;	//DONT MIX TH AND TD IN THE SAME BLOCK!
				}
				
			}
			if(in_headers!=1){
				row_number++;
			}
		}
	}
	//alert(contents.join("\n"));
	return({'headers':headers,'contents':contents});
}

/**
 * sort_direction: ASC, DESC
 * */
function std_order_by(data, field,sort_direction){
	var mul={'ASC':1,'DESC':-1};//multiplies.
	var m=mul[sort_direction];
	var data1=data;
	data1.sort(function(a,b){return((a[field].toLowerCase() < b[field].toLowerCase()) ? (m*-1) : ((a[field].toLowerCase() > b[field].toLowerCase()) ? (m*1) : 0));});
	return(data1);
}
/**
 * column_number is used to fix the sort link.
 * */
function std_ds2table(ds,style,column_number,direction){
	var dmap={'ASC':'DESC','DESC':'ASC'};
	new_direction=dmap[direction];//swap direction.
	var dx='\n<tr class="'+style+'_row">';
	for(var i in ds.headers){
		if(i==column_number){
			ds.headers[i] = (""+ds.headers[i]).replace("'"+direction+"'","'"+new_direction+"'")
		}
		dx+="\n\t<th class='"+style+"_head'>"+ds.headers[i]+"</th>";//missing classes.
	}
	dx+='\n</tr>';
	for(var i in ds.contents){
		var row=ds.contents[i];
		dx+="\n<tr class='"+style+"_row'>"
		for(var c in row){
			dx+="\n\t<td class='"+style+"_cell'>"+row[c]+"</td>";
		}
		dx+="\n</tr>"
	}
	return(dx);
}
/** 
 * style: std: list
 * column_number
 * span_object: the litle link.
 * */
function std_sort_column(span_object,column_number,style,direction){
	var th = span_object.parentNode;
	var tr= th.parentNode;
	var tbody= tr.parentNode;

	var ds = std_html2data(tbody);
	var c = ds.contents;
	std_order_by(c,column_number,direction);
	ds.contents = c;
	var table_html = std_ds2table(ds,style,column_number,direction);
	tbody.innerHTML=table_html;

	//222.585
	//oficina salazar
	//0.11
	tbody.children[column_number].span_object.onclick="std_sort_column(this,'.$column_number.',\''.$style.'\',\'ASC\')"

}


