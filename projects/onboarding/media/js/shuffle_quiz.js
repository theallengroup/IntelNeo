var SHUFFLE_OBJ = {};

SHUFFLE_OBJ.navScore         	= '#nav_score';

SHUFFLE_OBJ.submitShuffleId 	= '#id_shuffle_submit';
SHUFFLE_OBJ.submitShuffleButton = '#id_shuffle_submit a';

SHUFFLE_OBJ.shuffleQuizCountCorrectAnswers = [];

function shuffle_clicked(obj, question_id, op_number, op_id) {
	if(!$(obj).parents('.content_shuffle').hasClass('submitted')) { // check if question response
		SHUFFLE_OBJ.answer_row 		 = $('#' + current_section + ' .a_shuffle').eq(op_number - 1);
		SHUFFLE_OBJ.answer_row_input = $(obj);
		
		SHUFFLE_OBJ.content_shuffle  = SHUFFLE_OBJ.answer_row_input.parents('.content_shuffle');
		
		SHUFFLE_OBJ.is_single_answer = SHUFFLE_OBJ.content_shuffle.hasClass('single_answer') ? true : false;
			
		if(SHUFFLE_OBJ.is_single_answer) {
			ballList = SHUFFLE_OBJ.content_shuffle.find('.select_sh').get();	
		} else {
			ballList = SHUFFLE_OBJ.content_shuffle.find('.shuffle_ob .select_sh').get();
		}
		
		moveAnimate(ballList, SHUFFLE_OBJ.answer_row.get(), SHUFFLE_OBJ.is_single_answer);
		
		return true; // make sure we select the radio button input
	} else {
		return
	}
}

function highlightAnswers() {
	
	var answer_row = SHUFFLE_OBJ.answer_row,
		$input     = SHUFFLE_OBJ.answer_row_input,	
		$inputs    = $input.parents('.content_shuffle').find('.shuffle_input');		

	if($input.data('isshuffle') == '1') {
		var cls = ($input.data('iscorrect') == 'option[Y]') ? 'correct-answer' : 'wrong-answer';

		answer_row.addClass(cls);
		answer_row.find('label').css({'color': 'white'});
		
		// Count correct answers for shuffle quiz.
		var crtSection 			= $input.parents('.content_shuffle').attr('id');
		var maxScore 			= parseInt( $('#score_data').attr('data-maxScore') );
		var countQuestions 		= parseInt( $('#score_data').attr('data-countQuestions') );
		var scorePerQuestion 	= parseInt( maxScore / countQuestions );
		
		var correctAnswer = true;

		$inputs.each(function() {
			if($(this).is(':checked') == true && $(this).data('iscorrect') == 'option[N]') {
				correctAnswer = false;
			}
		});	
		if (correctAnswer == true) {
			SHUFFLE_OBJ.shuffleQuizCountCorrectAnswers[crtSection] = scorePerQuestion;
		}
		else {
			SHUFFLE_OBJ.shuffleQuizCountCorrectAnswers[crtSection] = 0;
		}
		
		var newScore = 0;

		for (i in SHUFFLE_OBJ.shuffleQuizCountCorrectAnswers) {
			newScore = newScore + SHUFFLE_OBJ.shuffleQuizCountCorrectAnswers[i];
		}
		$(SHUFFLE_OBJ.navScore).html(newScore);
		
		//highlight correct answer
		var newParent  = $(answer_row.get());
		
		var correctRow = newParent.parent().find('input[data-iscorrect="option[Y]"]').parent();//.parent();
		correctRow.addClass('correct-answer');
		correctRow.find('label').css({'color':'white','font-weight':'bold'});
		
		//for multiple answers questions highlite all wrong answers
		if(SHUFFLE_OBJ.is_single_answer == false && correctAnswer == false) {
			var incorrectRow = newParent.parent().find('input[data-iscorrect="option[N]"]').parent();//.parent();
			incorrectRow.addClass('wrong-answer');
			incorrectRow.find('label').css({'color':'white'});
		}
	} else {
		answer_row.css({'background-image': 'url("./media/img/wood_q.jpg")', 'color': 'white'});
		answer_row.find('label').css({'color': 'white'});
	}

}

function shuffle_start() {
	setTimeout(function(){show_section("section_1");}, 1500); //let it load.
}

function moveAnimate(elements, newParent, is_single_answer) {
	element 				= $(elements).first(); //Allow passing in either a JQuery object or selector
	newParent				= $(newParent); //Allow passing in either a JQuery object or selector
	var oldOffset 			= element.offset();
	var temp 				= element.clone();

	if(is_single_answer == false) {
		if(newParent.find('.select_sh.remove').length == 1) {	
			newParent.find('input[type="checkbox"]').attr('checked', true);
			return;
		}	
		if($(elements).length == 0) {
			newParent.find('input[type="checkbox"]').attr('checked', false);
			newParent.parent().find('input[type="checkbox"]').attr('disabled', true);
			return;
		}
		element.addClass('remove');
		element.html('<img src="./media/img/remove_shuffle.png" onClick="removeAnimate(this); ">');
	}
	
	element.appendTo(newParent);

	element.css({
		opacity: '0',
		'z-index':'-9999'
	});

	var newOffset = element.offset();
	
	temp.appendTo('body')
		.css('position', 'absolute')
		.css('left', oldOffset.left)
		.css('top', oldOffset.top)
		.css('zIndex', 3000).animate(
			{
				'top': newOffset.top, 
				'left': newOffset.left
			},
			100, 
			'easeInOutQuad', 
			function() { // Animation callback.
				if(is_single_answer == false) {
					var shuffleOb = SHUFFLE_OBJ.content_shuffle.find('.shuffle_ob');
					if($(elements).length <= 1) {
						setTimeout(function () {
							shuffleOb.html('<div class="messageBox"><p>tap the - to remove puck</p></div>');
						}, 300);
						$(SHUFFLE_OBJ.submitShuffleId).show();
					}
				} else {
					$(SHUFFLE_OBJ.submitShuffleId).show();
				}
							
				element.css('zIndex', 3100).animate(
					{opacity: 1},
					100, 
					'easeInOutQuad', 
					function() {}
				);
				setTimeout(function () {
					temp.remove();					
				}, 500);
				
			}
		);	
}

function removeAnimate(element) {
	ballList = SHUFFLE_OBJ.content_shuffle.find('.shuffle_ob').get();
	
	element = $(element).parent();
	
	if($(ballList).find('.select_sh').length == 0) {
		$('#id_shuffle_submit').hide(); 
	}
	
	element.parent().find('input[type="checkbox"]').attr('checked', false);
	element.parent().parent().find('input[type="checkbox"]').attr('disabled', false);
	
	element.removeClass('remove').removeAttr('style');
	element.html('<img src="./media/img/select_shuffle.png">');
	
	if($(ballList).find('.messageBox').length > 0) {		
		$(ballList).find('.messageBox').remove();
	}	
	
	element.appendTo(ballList);
}

function submitShuffle() {
	$(SHUFFLE_OBJ.submitShuffleId).hide();
	
	SHUFFLE_OBJ.content_shuffle.addClass('submitted');
	SHUFFLE_OBJ.content_shuffle.click(false);	
	SHUFFLE_OBJ.content_shuffle.find('.select_sh img').removeAttr('onClick').attr('src', './media/img/select_shuffle.png');
	SHUFFLE_OBJ.content_shuffle.find('.shuffle_ob .messageBox').remove();
	
	highlightAnswers();
	// temporary commented for testing
	setTimeout(function () {
		show_next_section();
	}, 2000); // how long do you want the delay to be? 
	//show_next_section(temp);
}

$(document).ready(function(){
	$(SHUFFLE_OBJ.navScore).html('0'); // First, reset the score.	
	
	$(SHUFFLE_OBJ.submitShuffleButton).on('click', function() {
		submitShuffle();
	});
})
