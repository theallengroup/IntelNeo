
function TextoTotal() {
    for(var n = 1; n <= question_count; n++) {
        var section = 'section_' + n;
        var question_id = $('#' + section).attr('data-questionId')

        var $container = $('#' + section + ' #letras_container')
        var answer = answers[question_id].split('');
        var cantidad = answer.length;
        for(var j = 0; j <cantidad; j++) {
            var $input = $('<input type="text" id="section_' + section + '_respuesta' + (j+1) + '" class="ClassRespuesta"/>');
            $container.append($input);
            if( answer[j] == ' ' ) {
                $input.attr('type', 'hidden').val(' ');
                $container.append($('<br/>'));
            }
            $container.append("\n");
        }

        var letters = tiles[question_id];
        for(var k = 0; k < letters.length; k++ ) {
            var l = letters[k][0];
            var tf = letters[k][1] ? 'true' : 'false';
            var $input = $('<input type="button" class="' + tf + '" value="' + l + '" onclick="values_button(this.value);" />');
            $('#' + section + ' #teclas_container').append($input).append("\n");
        }
    }
}

function eliminar() {
    $('#' + current_section + ' .false').hide();
}

function borrar() {
    $('#' + current_section + ' .ClassRespuesta').val('');
    var question_id = $('#' + current_section).attr('data-questionId');
    $('input[name="option[' + question_id + ']"]').val('');
}

function siguiente() {
    // clear entry
    borrar();

    // force show next section even though we didn't answer
    var s = get_next_section();
    if( s == 999 ) {
        document.forms[0].submit();
    }
    else {
        show_section("section_" + s);
    }
}

function validar(text) {
    if(cantidad==(i-1)) {
        enviar();
    }
}

function get_blank_fields() {
	var $blankInputs = $('#' + current_section + ' input.ClassRespuesta[type="text"]').filter(function() {
		return this.value == "";
	});

	return $blankInputs;
}

function current_answer() {
    $s = '#' + current_section + ' input.ClassRespuesta'
    return $($s + ',' + $s + ':hidden').map(function(){return $(this).val();}).get().join('').trim();
}

function values_button(value) {
    blank = get_blank_fields();

    if( blank.size() > 0 ) {
        $input = blank.first();
        $input.val(value);

        var question_id = $('#' + current_section).attr('data-questionId');

        if( blank.size() == 1 ) {
            $answer = $('input[name="option[' + question_id + ']"]');
            $answer.val(current_answer());
            // just filled last one
            $("#spin_btn").css("background-color", "#7FCEF3");
        }
    }
}

function repetir() {
    history.back(1);
}

function enviar() {
    var text = '#respuesta'+i;
    var answer=$(text).val();
    var res=$('#is_correct').val();
    var id_user=$('#id_user').val();
    res;
    if(answer.localeCompare(res.toUpperCase())) {
        valor=$('#id_value').val();
        id=$('#id_user').val();
        $.ajax({
            url: "./codigo/ajax_rank.php",
            type: "POST",
            data: {
                Value :  valor,
                id : id
            },
            success: function(datos) {
                $('#log').html(datos);
                window.location='./?mod=usr2session&ac=r_score_read';
            }
        });
    }
    else {
        var redir=getUrlVars()['activity_id'];
        window.location='./?mod=usr2session&ac=r_run_activity&activity_id='+redir+'&status=READY';
    }
}

/**
* Compute and show earned spin quiz points.
*/
function update_spin_score(section_num)
{
	// Get maximum number of earned points from "value" url param and find
	// how many points will be earned for each correct answer.
	var urlVars 			= getUrlVars();
	var maxScore 			= parseInt(urlVars.value, 10);
	var countQuestions		= parseInt( $('#act_nav').attr('data-activity_count') );
	var scorePerQuestion	= parseInt( maxScore / countQuestions );

	// For currently active question, find if given answer is correct.
	var $crtSection			= $('#section_' + section_num);
	var questionId			= $crtSection.attr('data-questionid');

	var enteredAnswerVal 	= $crtSection.find('input[name="option['+questionId+']"]').val();
	var correctAnswerVal 	= (typeof answers[questionId] !== 'undefined') ? answers[questionId].toUpperCase() : '';
	var isCrtAnswerCorrect 	= (enteredAnswerVal == correctAnswerVal) ? true : false;

	if (isCrtAnswerCorrect) {
		document.spinQuizCountCorrectAnswers['section_'+section_num] = scorePerQuestion;
	}
	else {
		document.spinQuizCountCorrectAnswers['section_'+section_num] = 0;
	}

	// Calculate how many points have been earned until now.
	var newScore = 0;
	for (i in document.spinQuizCountCorrectAnswers) {
		newScore = newScore + document.spinQuizCountCorrectAnswers[i];
	}

	// Show current score.
	$('#nav_score').html(newScore);
}


function getUrlVars() {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('='); vars.push(hash[0]); vars[hash[0]] = hash[1];
    }
    return vars;
}

$(document).ready(function() {
    TextoTotal();

	// Set initial points.
	$('#nav_score > p').html('0');
	document.spinQuizCountCorrectAnswers = [];
});
