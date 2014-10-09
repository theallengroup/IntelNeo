var all_questions = {};
var all_answers = {};
var all_curpos = {};
var q_count = 0;

var current_word = "";
var current_question = 0;
var letter_count = 20;
function shuffle_text(text){
	var p = 0;
	var new_text = text.split("");
	var temp;
	for(var i=0;i<100;i++){
		var p1 = Math.min(text.length-1,Math.floor(Math.abs(Math.random() * text.length)));
		var p2 = Math.min(text.length-1,Math.floor(Math.abs(Math.random() * text.length)));
		temp = new_text[p1];
		new_text[p1] = new_text[p2];
		new_text[p2] = temp;
	}
	return new_text.join("");
}
function scramble_word(word){
	return shuffle_text(cut(combine(shuffle_text(word), shuffle_text(alphabet())),letter_count));
}
function cut(text,length){
	return text.substring(0,length);
}
function alphabet(){
	return 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
}
function combine(a,b){
	return a+b;
}
function letter_clicked(a){ 
	alert("A");
}

/* user interface */
function upper_keyboard_clicked(obj, pos, letter, qid){
	//return caret to writing position
	var ii=all_answers[qid].length;
	if(pos > all_answers[qid].length-1){
		pos = all_answers[qid].length;
	}
	all_curpos[qid] = pos;

	console.log("up:" + letter);
}

function lower_keyboard_clicked(obj,pos, letter, qid){
	if(!(qid in all_answers)){
		alert("no such question code:" + qid);
		return;
	}
	console.log("low:" + letter);
	var cpos = all_curpos[qid]; 
	if(all_questions[qid] === ""){
		alert("ERROR: words cannot be empty!");
	}
	//disabled: check upper bound
	if(cpos >= all_questions[qid].length){
		cpos = all_questions[qid].length - 1;
	//	//correct! maybe!
	//	skip_problem(qid);
	//	return;
		console.log("CARET RETURN. Way too much: " + cpos + ">" +  all_answers[qid].length + "("+all_answers[qid]+") qid=" + qid);
		all_curpos[qid]--;

	}

	//write bytes to screen, ram
	var path = "letter_box_font_" + qid+"_"+cpos;
	if(!document.getElementById(path)){
		console.log("ERROR: path does not exist:"+path );
		return;
	}
	document.getElementById(path).innerHTML = "<p style='color:black'>" + letter+"</p>";
	all_curpos[qid]++;
	all_answers[qid] += letter;//always add at the end.
	document.getElementById("option["+qid+"]").value = all_answers[qid];
	//check win condition
	if(all_answers[qid].length === all_questions[qid].length){
		if(all_answers[qid] === all_questions[qid]){
			alert("Correct!");
		}else{
			alert("Sorry, the correct answer was:" + all_questions[qid]);
		}
		skip_problem(qid);
	}	
}

function get_keyboard(word,class_name,function_name,qid){
	var kb ="";
	for(var i=0;i<word.length;i++){
		var letter = word[i];
		if(word[i] == " "){
			letter = "&nbsp;";
		}
		var param = function_name + "(this,'"+i+"','"+word[i]+"','"+qid+"')";
		kb += '<div id="letter_'+class_name+'_'+qid+"_"+i+'" class="box_font" title="'+param+'" onclick="'+param+'" style="color:black"><p style="color:black">'+letter+'</p></div>&nbsp;';
	}
	return kb;
}

function setup_keyboard(word, class_name, function_name, div_id, qid){
	if(document.getElementById(div_id)){
		document.getElementById(div_id).innerHTML = get_keyboard(word,class_name,function_name, qid);
	}else{
		console.log("ERROR021: CANNOT SETUP KEYBOARD:" + div_id + " DESTINATION CONTAINER DOES NOT EXIST.");
	}
}

function setup_upper_keyboard(word,qid){
	setup_keyboard(word, "box_font", "upper_keyboard_clicked", "upper_" + qid,qid);	
}

function setup_lower_keyboard(word, qid){
	setup_keyboard(word, "box_font", "lower_keyboard_clicked", "lower_" + qid, qid);	
}

function setup_all_keyboards(){
	for(var i in all_questions){
		var l = "";
		for(var i1=0;i1<all_questions[i].length;i1++){
			l += " ";
		}
		setup_upper_keyboard(l,i);
		setup_lower_keyboard(scramble_word(all_questions[i]),i);
	}
}

function spin_init(){
	setup_all_keyboards();
}

function reset_word(qid){
	console.log("reset:" + qid);
	all_answers[qid] = "";
	all_curpos[qid] = 0;
	var l = "";
	for(var i1=0;i1<all_questions[qid].length;i1++){
		l += " ";
	}
	
	setup_upper_keyboard(l,qid);
}

function skip_problem(qid){
	console.log("skip:" + qid);
	//advance to next question.
	//alert("clicked!");
	var s = get_next_section();
	if(s == 999){
		document.forms[0].submit();
	}else{
		show_section("section_" + s);
	}
}

function remove_letter(qid){
	console.log("remove_letter:" + qid);

}

