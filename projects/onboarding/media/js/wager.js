var id;
function getUrlVars()
{
	var vars = [], hash;
	var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');

	for(var i = 0; i < hashes.length; i++)
	{
		hash = hashes[i].split('='); vars.push(hash[0]); vars[hash[0]] = hash[1];
	}
	return vars;
}
function Perdio(e)
{
	var event = e || window.event;
	var $btn = $(event.target);
    $btn.css({"background-color": "#FF0000", "border": "5px solid #FFF"});
    setTimeout(function(){
		window.location='../onboarding/wager_answers.php?id=4&value=200';
	},1500);
}
function  gano(Value)
{
    $("#gano").css({"background-color": "#00FF00", "border": "5px solid #FFF"});
    setTimeout(function(){
                window.location='./?mod=usr2session&ac=r_score_read';
            },1500);
}
function BuscarPregunta()
{
    var url_id=getUrlVars()['activity_id'];
console.log(url_id);
    $.ajax
    ({
     type: "POST",
     url: "codigo/AjaxWagerQuestion.php",
     data:
  {
   id_url_: url_id
  },
     success: function(e)
     {
        e = e.replace("[[", "");
        e = e.replace("]]", "");
        e = e.replace("\"", "");
        e = e.replace("\"", "");
        e = e.replace("\"", "");
        e = e.replace("\"", "");
        e=e.split(',');
        id=e[0];
        $('#question').html(e[1]);}
    });
}


function Preguntar(value)
{
    window.location += "&id="+id+'&value='+value;
}
$(function()
{
    BuscarPregunta();
});