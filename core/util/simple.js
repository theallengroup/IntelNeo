function tagged(tag){
	return(document.getElementsByTagName(tag));
}


function apply_styles(){
	var l = ["table","form",'input[@type=text]','textarea','label'];
	var l1 = ["out","left","right","top","bottom","top-left","top-right","bottom-left",'bottom-right','center'];
	var prefix = 'shadow-';
	for(var i in l){
		for(var j in l1){
			$(l[i]).wrap("<div class='" + prefix + l1[j] + "'></div>");
		}
	}
}

window.onload = apply_styles;
