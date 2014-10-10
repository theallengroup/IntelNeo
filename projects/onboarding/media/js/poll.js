var POLL_OBJ = {};

POLL_OBJ.navScore         = '#nav_score';

POLL_OBJ.submitPollId 	  = '#id_poll_submit';
POLL_OBJ.submitPollButton = '#id_poll_submit a';

var current_section = "section_1";

$(document).ready(function(){
	// Add a specific class on selected answer.
	$(document).on('change', '.principal_container li input', function(){
		var $radioButton = $(this);
		POLL_OBJ.content_shuffle  = $radioButton.parents('.content_shuffle');
		
		if ($radioButton.is(':checked')){
			POLL_OBJ.content_shuffle.find('li').each(function(){
				var $crtLi = $(this);
				$crtLi.removeClass('selected');
			});
			var $liParent = $radioButton.parents('li');
			$liParent.addClass('selected');
					
			$(POLL_OBJ.submitPollId).show();			
		}
		
		// Mark wager correct answer.
		if ($radioButton.parents('.principal_container.wager').length > 0){
			$('.principal_container.wager .content_wager li').each(function(){
				if ($(this).find('.q_label').hasClass('is_correct-Y')){
					$(this).find('.q_label').addClass('correct-answer');
				}
			});
		}
		
	});
    
    // ----- current question number
    var questionNumber = $('.js-questionNumber').val();
    // ----- show/hide left arrow.
    showHideArrow(questionNumber);
});

function show_previous_section() {
	var previousSectionNum = get_previous_section();
	var act_type= $('#act_nav').attr('data-act_type_id');

	show_section('section_' + previousSectionNum);

	// If current activity type is Read...
	if ( act_type == '6' ) {
		update_read_score(previousSectionNum, true);
	}
}

function show_next_section()
{
	// if we have not answered the current question, do nothing
	// TODO: do something
    var $parentContainer = $('.principal_container');
	var section = '#' + current_section;
	var act_type= $('#act_nav').attr('data-act_type_id');

	var num_check = $(section + ' input[type="radio"], ' + section + ' input[type="checkbox"]');
	var is_not_checked    = num_check.size() > 0 && num_check.filter(':checked').size() < 1;

	var num_text          = $(section + ' textarea, ' + section + ' input[type="text"]');
	var is_not_filled     = num_text.size() > 0 && num_text.filter(function(n) { return $(this).val() == ''; }).size() > 0;
	var is_not_submitted  = ((act_type == '2' || act_type == '1') && !$(section).hasClass('submitted'));
	var wager_was_scored  = (act_type == '3' && $(section).hasClass('scored'));	
	
	if( is_not_checked || is_not_filled || is_not_submitted ) {
		return false;
	}

	var s = get_next_section();
    console.log(s);
	
    if(s == 999){
        update_wager_score(s-1,section);
		document.forms[0].submit();
	} else {
        //---- If is current activity is TLA delay (2000 ms) showing next section.
        if ($parentContainer.hasClass('js-tla-activity'))
        {
            var sectionID = "section_" + s;
            setTimeout(function(){
                show_section(sectionID);
            }, 2000);
        } else {
           show_section("section_" + s);
        }
        
		// If current activity type is Poll...
		// if ( $(section).hasClass('content_poll') ) {
		if ( act_type == '1') {
			update_score(s-1);
		}
		// current activity is WAGER
		if (act_type == '3' && !wager_was_scored){
			update_wager_score(s-1,section);
		}
		// If current activity type is Spin Quiz...
		if ( act_type == '4' ) {			
			update_spin_score(s-1);
		}
		// If current activity type is Read...
		if ( act_type == '6' ) {
			update_read_score(s-1);
		}
	}

	return false;
}

function show_section(section_id) {
    
	if(document.getElementById("nav_pager")){
		var s = parseInt(section_id.split("_")[1],10);
		document.getElementById("nav_pager").innerHTML = s + "/" + question_count;
        $('.js-questionNumber').val(s);
        
        // ----- current question number
        var questionNumber = $('.js-questionNumber').val();
        // ----- show/hide left arrow.
        showHideArrow(questionNumber);
		// console.log("EXISTS: PROGRESS DISPLAY");
	} else {
		// console.log("DOESN'T EXISTS: PROGRESS DISPLAY");
	}

	if(document.getElementById(current_section)){
		var ball_id = $('#'+current_section).data('ball');
		$(ball_id).hide();
		document.getElementById(current_section).style.display = "none";
		//$('#'+current_section).removeClass('active-section');
		// console.log("HIDE:" + current_section);
	} else {
		// console.log("CANNOT HIDE:" + current_section);
	}

	if(document.getElementById(section_id)) {
		//var ball_id = $('#'+section_id).data('ball');
		//$(ball_id).show();
		document.getElementById(section_id).style.display = "block";
		//$('#'+section_id).addClass('active-section');
	} else {
	}

	//show hide shuffle_quiz submit button
	if($('.principal_container.shuffle').length > 0) {
		if($('#' + section_id).hasClass('submitted')) {
			$('#id_shuffle_submit').hide();
		} else {
			if($('#' + section_id).find('.shuffle_ob .select_sh').length == 0) {
				$('#id_shuffle_submit').show();
			}
		}
	}

	current_section = section_id;
    
    
    
}

function send_quiz(){
	if(!document.getElementById("quiz")){
		alert("ERROR: no quiz object found");
		return;
	}
	document.getElementById("quiz").submit();
}

function get_previous_section() {
	var n = parseInt(current_section.split("_")[1], 10);
	n = n - 1;
	if( n < 1 ) {
		return 1;
	}
	else {
		return n;
	}
}

/* lets activities auto-advance. */
function get_next_section(){
	var n = parseInt(current_section.split("_")[1], 10);
	n = n + 1;
	if(n > question_count){
		return 999;
	}else{
		return n;
	}
}

var time_left = 0;
var timer_obj = null;
var ever_run = 0;

function zp(a){
	if(("" + a).length <2){
		return "0" + a;
	}else{
		return a;
	}
}

function update_timer(){
	//clearTimeout(timer_obj);
	if(ever_run === 0 && time_left == 0){
		alert("time is unset.");
		return;
	}
	if(document.getElementById("timer_display")){
		if(time_left > 0 ){
			time_left--;
		}else{
			alert("Time is up.");
			document.forms[0].submit();
		}
		var seconds = time_left % 60;
		var minutes = (time_left - seconds) / 60;
		var time = zp(minutes)+":" + zp(seconds);
		document.getElementById("timer_display").innerHTML = time;
	}
	ever_run = 1;

	timer_obj = setTimeout(update_timer,1000);
}

// --------- Keep the score. ---------
function update_score(section_num){
	var $scoreContainer		= $('#nav_score');
	// console.log($scoreContainer);
	var maxScore			= parseInt( $('#score_data').attr('data-maxScore') );
	var countQuestions		= parseInt( $('#score_data').attr('data-countQuestions') );
	var scorePerQuestion	= parseInt( maxScore / countQuestions );

	var newScore			= section_num * scorePerQuestion;

	$scoreContainer.html(newScore);
}


function update_wager_score(section_num, section){
	var $scoreContainer		= $('#nav_score');
	var is_correct = $(section + " input:checked").parent().hasClass('is_correct-Y');
    
    console.log(is_correct);
    
	if(is_correct){
		var maxScore			= parseInt( $('#score_data').attr('data-maxScore') );
		var countQuestions		= parseInt( $('#score_data').attr('data-countQuestions') );
		var scorePerQuestion	= parseInt( maxScore / countQuestions );
		var currentScore 		= parseInt($scoreContainer.html(),10);
		var newScore			= currentScore + scorePerQuestion;
		$("form#wager input[name='wager_total']").val(newScore);
		$scoreContainer.html(newScore);
	}
	$(section).addClass('scored');

}


function update_read_score(section_num, isGoingBack)
{
	var $scoreContainer		= $('#nav_score');
	$crtSection 			= $('#section_' + section_num);

	var maxScore 			= parseInt( $crtSection.find('.points_read').text() );
	var countQuestions		= parseInt( $('#act_nav').attr('data-activity_count') );
	var scorePerQuestion 	= parseInt( maxScore / countQuestions );
	var currentScore 		= parseInt( $scoreContainer.text() );
    
	if (isGoingBack == true) {
		// Decrease score.
		var newScore = currentScore - scorePerQuestion;
	}
	else {
		// Increase score.
		var newScore = currentScore + scorePerQuestion;
	}
	
	if (newScore < 0) {
		newScore = 0;
	}

	$scoreContainer.text(newScore);
}


function poll_start(){
	update_timer();
	show_section("section_0");
}

function showHideArrow(questionNumber)
{
    var $prevArrow          = $('.js-prev-arrow');
    var $nextArrow          = $('.js-next-arrow');
    
    if (questionNumber == 1)
    {
        $prevArrow.css({
            'visibility': 'hidden'
        });
    } else {
        $prevArrow.css({
            'visibility': 'visible'
        });
    }
}

function submitPoll() {
	$(POLL_OBJ.submitPollId).hide();
	
	POLL_OBJ.content_shuffle.addClass('submitted');
	POLL_OBJ.content_shuffle.click(false);		
	
    
    
	//highlightAnswers();
	setTimeout(function () {
		show_next_section();
	}, 2000); // how long do you want the delay to be? 
	//show_next_section(temp);
}

$(document).ready(function(){
	$(POLL_OBJ.navScore).html('0'); // First, reset the score.	
	
	$(POLL_OBJ.submitPollButton).on('click', function() {
		submitPoll();
	});
})
