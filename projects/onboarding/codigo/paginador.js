function show_section(section_id){
	if(document.getElementById("nav_pager")){
		var s = parseInt(section_id.split("_")[1],10);
		document.getElementById("nav_pager").innerHTML = s + "/" + question_count;
		console.log("EXISTS: PROGRESS DISPLAY");
	}else{
		console.log("DOESN'T EXISTS: PROGRESS DISPLAY");
	}
	if(document.getElementById(current_section)){
		document.getElementById(current_section).style.display = "none";
		console.log("HIDE:" + current_section);
	}else{
		console.log("CANNOT HIDE:" + current_section);
	}

	if(document.getElementById(section_id)){
		document.getElementById(section_id).style.display = "block";
		console.log("SHOW:" + section_id);
	}else{
		console.log("CANNOT SHOW:" + section_id);	
	}

	current_section = section_id;
}