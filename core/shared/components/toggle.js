function toggle(id){
	//alert(typeof(document.getElementById(id)));
	if(document.getElementById(id).style.display != 'block'){
		document.getElementById(id).style.display='block';
	}else{
		document.getElementById(id).style.display='none';
	}
}
function toggle_block(id,icons_location){
	//var cpath = location.href.split("?")[0];
	var cpath=icons_location
	
	//alert(document.getElementById(id+"_img").src );
	/*
	if(document.getElementById(id+"_img").src == cpath + 'icons/down_arrow.png'){
		document.getElementById(id+"_img").src = cpath + 'icons/right_arrow.png'
	}else{
		document.getElementById(id+"_img").src = cpath + 'icons/down_arrow.png'
	}

	alert("TEST:"+document.getElementById(id+"_img").src +" == "+cpath + 'icons/down_arrow.png');
	alert("VALUE:"+document.getElementById(id+"_img").src );
	*/
	toggle(id);
	var dx = document.getElementById(id).style.display;
	var map=[];
	map["block"]=cpath + 'icons/right_arrow.png';
	map["none"]=cpath + 'icons/down_arrow.png';
	map[""]=map["block"];

	document.getElementById(id+"_img").src = map[dx];
}
