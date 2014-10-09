
/**
 * http://stackoverflow.com/questions/2264072/detect-a-finger-swipe-through-javascript-on-the-iphone-and-android
 * http://stackoverflow.com/questions/5736398/how-to-calculate-the-svg-path-for-an-arc-of-a-circle
 * */
var question_count = 4;
var is_hit = 0;
var current_angle = 0;
var current_sp_section = "";
var TEST = 0;
var text_deg_offset = 0;//degrees
//text arrangements
var fixed_angles = [-1,-90,90,-330,0,340,330,320,315,310,310,305,300];
var fc = 11;
var snd = null;

var xDown = null;
var yDown = null;


function handleTouchStart(evt,div_id,count) {
    xDown = evt.touches[0].clientX;
    yDown = evt.touches[0].clientY;
}

function handleTouchMove(evt,div_id,count) {
	if ( ! xDown || ! yDown ) {
		return;
	}

	var xUp = evt.touches[0].clientX;
	var yUp = evt.touches[0].clientY;

	var xDiff = xDown - xUp;
	var yDiff = yDown - yUp;
	var doit = 0;
	var cc = 1;
	if ( Math.abs( xDiff ) > Math.abs( yDiff ) ) {/*most significant*/
		if ( xDiff > 0 ) {
			/* left swipe */
			doit = 1;
			cc = 1;
		} else {
			/* right swipe */

			doit = 1;
			cc = 0;
		}
	} else {
		if ( yDiff > 0 ) {
			/* up swipe */
			doit = 1;
			cc = 1;
		} else {
			/* down swipe */
			doit = 1;
			cc = 0;
		}
	}
	if(doit == 1){
		var section = div_id.replace("spinner", "");
		hit_spin_the_wheel(document.getElementById(div_id),section, cc);
		//alert(div_id);
	}
	/* reset values */
	xDown = null;
	yDown = null;
}


function spinner_clicked(event){
	//make_spinwheels();
}
function deg2rad(deg){
	return Math.PI  * deg / 180;
}
function make_spin_setup(){
	snd = new Audio("./media/sounds/ruleta.mp3");
	for(var i=0;i<20;i++){
		if(document.getElementById("spinner" + i)){
			make_spinwheel("spinner" + i,question_count);
		}
	}

	snd.onload = function(){
		console.log("Audio Finally Loaded.");
	};
	snd.onerror = function(){
		alert("ERROR: could not load sound.");

	};
}
function make_spinwheel(div_id, count){
	var t_width = 250;
	var t_height = 250;
	var distance_between_border_and_circle = 10;
	var t_size = t_width/2 - distance_between_border_and_circle;
	//var count =  3;
	var data = [];
	var slice_size = 360 / count;
	for(var i=0;i<count;i++){
		data[data.length] = i * slice_size;
	}
	//console.log(data);
	var colors = ["rgb(253,184,19)", "rgb(255,218,0)", "rgb(166,206,57)", "rgb(29,174,235)", "rgb(0,113,197)", "rgb(6,68,126)"];
	var slices = [];
	for(i=0;i<data.length;i++){
		var val = i*50 + 100;

		slices[slices.length] = {'start' : data[i],'end':data[i] + slice_size,'value':val,'color':colors[i % colors.length]};
	}
	//console.log(slices);

	var dx = "<svg width='"+t_width+"' height='"+t_height+"'>";
	dx += '<defs>';
	dx += '  <style type="text/css">';
	dx += '    @font-face {';
	dx += '      font-family: IntelBold;';
	dx += '      src: url(./fonts/IntelClear_Bd.ttf) format("truetype");';
	dx += '    }';
	dx += '  </style>';
	dx += '</defs>';

	dx += '  <circle cx="'+(t_width/2)+'" cy="'+(t_height/2)+'" r="'+(t_size)+'" stroke="white" stroke-width="20" fill="red" />';

	// center
	var cx = t_width / 2;
	var cy = t_height / 2;
	var radius = t_size;
	var slices_dx = "";
	var texts_dx = "";
	for(i in slices){
		var slice = slices[i];

		var p1x = cx + radius * Math.cos(deg2rad(slice["start"]));
		var p1y = cy - radius * Math.sin(deg2rad(slice["start"])); // minus sign because coords are inverted in svg space
		var p2x = cx + radius * Math.cos(deg2rad(slice["end"]));
		var p2y = cy - radius * Math.sin(deg2rad(slice["end"]));
		//console.log("("+p1x+","+p1y+" "+p2x+","+p2y+")");
		var path = "M"+cx+","+cy+" L" + p1x + " "+p1y + "  A"+(t_width/2-distance_between_border_and_circle) + " "+ (t_height/2-distance_between_border_and_circle) + ",0,0,0, "+p2x+" "+p2y+" Z";// L"+cx+","+cy;
		slices_dx += '<path d="'+path+'"  fill="'+slice["color"]+'" />';

		var v_value = slice["value"];
		var v = (""+v_value);//.split("").reverse().join("");
		//console.log("v" + v);
		var fixed_distances = [35,57,80];
		var apperture_size = (slice["end"]-slice["start"]);
		var angle_deg_offset = -(360 / count )/2 ; //- apperture_size/2;
		//console.log("ado:" + angle_deg_offset);
		//console.log("Start:" + slice["start"] + ", end:" + slice["end"] + ", bis:" + angle_deg_offset);

		for(var j=0;j<3;j++){
			var d = fixed_distances[j];
			var tx = d * Math.cos(deg2rad(slice["start"] + angle_deg_offset )) ;
			var ty = d * Math.sin(deg2rad(slice["start"] + angle_deg_offset )) ;
			text_angle = - (slice["end"] +slice["start"])/2-TEST + fixed_angles[count];
			var fill = "white";
			//if(i%2==0){
			//	fill = "black";
			//}
			texts_dx += '<text x="'+cx+'" y="'+cy+'" fill="'+fill+'" transform="translate('+tx+','+ty+') rotate('+(-text_angle)+' '+cx+','+cy+')" font-size="20pt" font-family="IntelBold" font-weight="bold">'+v[2-j]+'</text>';
		}


	}
	dx += slices_dx;
	dx += texts_dx;
	dx += '  <circle cx="'+(t_width/2)+'" cy="'+(t_height/2)+'" r="'+20+'" stroke="white" stroke-width="9" fill="rgb(187,187,187)" />';

	dx += "</svg>";

	document.getElementById(div_id).innerHTML = dx;
	document.getElementById(div_id).addEventListener('touchstart', function(evt){handleTouchStart(evt,div_id,count);}, false);
	document.getElementById(div_id).addEventListener('touchmove', function(evt){handleTouchMove(evt,div_id,count);}, false);
}

/*
 * this happens when the item is hit or swiped, it can go clockwise
 * or counter clockwise.
 * */
function hit_spin_the_wheel(obj,section,direction){
	window.onscroll = function (event){
		window.scrollTo(0,0);
		//document.title=window.pageYOffset
	}


	if(is_hit == 1){
		//nop
		return;
	}
	snd.play();
	current_sp_section = section;
	is_hit = 1;
	var n = Math.abs(Math.round(Math.random()*10000));
	if(direction == 1){//counterclockwise;
		n *= -1;
	}else{//clockwise;
		n *= 1;//nop
	}

	// the picker is rotated 90 degrees with respect to the
	//horizontal angle of 0 degrees, therefore,
	// we add 90.
	var offset = 0;

	//we like POSITIVE angles.
	while(current_angle < 0){
		current_angle += 360;
	}
	// but small ones.
	current_angle = (n + offset) % 360;

	obj.style.webkitTransform="rotate("+n+"deg)";
	obj.style.mozTransform="rotate("+n+"deg)";
	obj.style.oTransform="rotate("+n+"deg)";
	obj.style.msTransform="rotate("+n+"deg)";
	obj.style.transform="rotate("+n+"deg)";
	console.log("angle=" + current_angle + ", wager=" + deg2wager(current_angle));
	setTimeout(advance_to_question, 8 * 1000 + 1000);
}
/** brute force testing. */
function test_deg2wager(){
	for(var i=0;i<360;i++){
		console.log(i+" = " + deg2wager(i));
	}
}
function deg2wager(degrees){
	return sector2wager(deg2sector((degrees) % 360));
}
function deg2sector(degrees){
	var question_size = 360 / question_count;
	return Math.floor(degrees / question_size);
}
function sector2wager(s){
	return 100 + ((question_count - 1) - s) * 50
}
function nop(){
	/*
	//current_angle += 180;
	//var d = Math.floor((  (360-degrees+question_size)%360  ) / question_size);
	degrees %=360;
	//offset to keep in account the fact that
	//questions are "one off". in negative or clockwise angles.
	degrees -= question_size;
	if(degrees < 0){
		degrees += 360;
	}
	var datatable = [];
	for(var i=0;i<question_count;i++){
		datatable[datatable.length] = {'s':i*question_size,'e':(1+i)*question_size,'value': i * 50 + 100};
		if(degrees >= i*question_size && degrees <= (1+i)*question_size){
			return i * 50 + 100;
		}
	}
	return -1;
	*/
	degrees = 360 - degrees;
	var d = Math.floor( ( degrees + question_size ) / question_size);
	return d  * 50 + 100;
}

function advance_to_question(){
	is_hit = 0;
	/* fixed??*/
	var wager =deg2wager(current_angle);
	console.log("angle=" + current_angle + ", wager=" + wager);
	document.getElementById("wager_" + current_sp_section).innerHTML = wager;
	document.getElementById("wager[" + current_sp_section+"]").value = wager;
	document.getElementById("wheel_" + current_sp_section).style.display = "none";
	document.getElementById("question_" + current_sp_section).style.display = "block";
	//fix scrolling
	window.onscroll = function (event){
	};
}
function spin_option_selected(){
	//alert("clicked!");
	var s = get_next_section();
	if(s == 999){
		document.forms[0].submit();
	}else{
		show_section("section_" + s);
	}
}


document.body.onload = make_spin_setup;

