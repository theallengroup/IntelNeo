var xDown = null;
var yDown = null;
var penultimate_page = pages-1;
var pick = 450;   
var current_page = 0;
var cleft = 0;
var screen_size = 480;
console.log(screen_size);
function move_left(){
	scroll_add(screen_size*(-1));
	console.log("el incremento sera de"+ screen_size);
}
function move_right(){
	scroll_add(screen_size);
	console.log("el incremento sera de"+ screen_size);
}
function scroll_add(amount){
	if(current_page == 0 && amount > 0 ){
		//nope
		console.log("cowardly refusing to scroll right.");
		return;
	}
//	if (current_page == 1){
	cleft += amount;
	document.querySelector(".modal").style.left =cleft+"px";
	//window.scrollTo(pick,0);
	if(amount < 0){
		current_page+=1;
	}else{
		current_page-=1;
	}
	console.log("Showing:" + current_page + " of " + pages);
	if(current_page >= pages){
		document.querySelector(".modal").style.opacity = 0;
		setTimeout(function(){
			document.querySelector(".modal").style.display = "none";
			console.log("The tutorial Div is no more.");
			window.onscroll = function(){};
			window.onmousewheel = function(){};

		},500);
	}

//	}else if(current_page >= 1 && current_page<= penultimate_page ){
//		pick = current_page * 450;
//		current_page+=1;
//		//window.scrollTo(pick,0);
//	}else{
//		document.getElementById('container').style.display='none';
//	}
}
function handleTouchStart(evt) {
    xDown = evt.touches[0].clientX;
    yDown = evt.touches[0].clientY;
}

function handleTouchMove(evt) {
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
			move_left();
		} else {
			/* right swipe */
			move_right();
		}
	}
	/* reset values */
	xDown = null;
	yDown = null;
}
