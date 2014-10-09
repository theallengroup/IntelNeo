var venues;
var wheel;
var Valor;
shuffle = function(o)
{
    for (var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
    return o;
};
String.prototype.hashCode = function()
{
    var hash = 5381;
    for (i = 0; i < this.length; i++)
    {
        char = this.charCodeAt(i);
        hash = ((hash << 5) + hash) + char;
        hash = hash & hash;
    }
    return hash;
    console.log(hash)
}
Number.prototype.mod = function(n)
{
    return ((this % n) + n) % n;
}
function Redir()
{
    $.each( venues, function( key, value ) 
    {
        if(value == Valor)
        {window.location ="./pregunta.php?id="+key+'&value='+Valor};
    });
    
}
function cargar_datos()
{
    wheel =
            {
                timerHandle: 0,
                timerDelay: 30,
                angleCurrent: 0,
                angleDelta: 0,
                size: 160,
                canvasContext: null,
                colors: ['#A4CC28',  '#016DC6','#02ACED','#FDB802',  '#FDD902', '#02427F', 
                         '#A4CC28',  '#016DC6','#02ACED','#FDB802',  '#FDD902', '#02427F'],
                segments: [],
                seg_colors: [],
                maxSpeed: Math.PI / 16,
                upTime: 1000,
                downTime: 5000,
                spinStart: 0,
                frames: 0,
                centerX: 160,
                centerY: 265,
                spin: function()
                {
                    if (wheel.timerHandle == 0)
                    {
                        wheel.spinStart = new Date().getTime();
                        wheel.maxSpeed = Math.PI / (16 + Math.random()); // Randomly vary how hard the spin is
                        wheel.frames = 0;
                        wheel.sound.play();
                        wheel.timerHandle = setInterval(wheel.onTimerTick, wheel.timerDelay);
                    }
                },
                onTimerTick: function()
                {
                    wheel.frames++;
                    wheel.draw();
                    var duration = (new Date().getTime() - wheel.spinStart);
                    var progress = 0;
                    var finished = false;
                    if (duration < wheel.upTime)
                    {
                        progress = duration / wheel.upTime;
                        wheel.angleDelta = wheel.maxSpeed * Math.sin(progress * Math.PI / 2);
                    }
                    else
                    {
                        progress = duration / wheel.downTime;
                        wheel.angleDelta = wheel.maxSpeed * Math.sin(progress * Math.PI / 2 + Math.PI / 2);
                        if (progress >= 1)
                        {
                            finished = true;
                            
                            Redir();
                            
                        }
                    }
                    wheel.angleCurrent += wheel.angleDelta;
                    while (wheel.angleCurrent >= Math.PI * 2)
                        wheel.angleCurrent -= Math.PI * 2;
                    if (finished)
                    {
                        clearInterval(wheel.timerHandle);
                        wheel.timerHandle = 0;
                        wheel.angleDelta = 0;
                    }
                },
                init: function(optionList)
                {
                    try
                    {
                        wheel.initWheel();
                        wheel.initAudio();
                        wheel.initCanvas();
                        wheel.draw();
                        $.extend(wheel, optionList);
                    } catch (exceptionData) {
                        alert('Wheel is not loaded ' + exceptionData);
                    }

                },
                initAudio: function() {
                    var sound = document.createElement('audio');
                    sound.setAttribute('src', './media/sounds/ruleta.mp3');
                    wheel.sound = sound;
                },
                initCanvas: function() {
                    var canvas = $('#wheel #canvas').get(0);

                    if ($.browser.msie) {
                        canvas = document.createElement('canvas');
                        $(canvas).attr('width', 300).attr('height', 400).attr('id', 'canvas').appendTo('.wheel');
                        canvas = G_vmlCanvasManager.initElement(canvas);
                    }

                    canvas.addEventListener("click", wheel.spin, false);
                    wheel.canvasContext = canvas.getContext("2d");
                },
                initWheel: function()
                {
                    shuffle(wheel.colors);
                },
                update: function()
                {
                    var r = 0;
                    wheel.angleCurrent = ((r + 0.5) / wheel.segments.length) * Math.PI * 2;
                    var segments = wheel.segments;
                    var len = segments.length;
                    var colors = wheel.colors;
                    var colorLen = colors.length;
                    var seg_color = new Array();
                    for (var i = 0; i < len; i++)
                        seg_color.push(colors[segments[i].hashCode().mod(colorLen)]);
                    wheel.seg_color = seg_color;
                    wheel.draw();
                },
                draw: function()
                {
                    wheel.clear();
                    wheel.drawWheel();
                    wheel.drawNeedle();
                },
                clear: function()
                {
                    var ctx = wheel.canvasContext;
                    ctx.clearRect(0, 0, 1000, 800);
                },
                drawNeedle: function()
                {
                    var ctx = wheel.canvasContext;
                    var centerX = wheel.centerX;
                    var centerY = wheel.centerY;
                    var size = wheel.size;
                    ctx.lineWidth = 3;
                    ctx.strokeStyle = '#959595';
                    ctx.fileStyle = '#959595';
                    ctx.beginPath();
                    ctx.arc(155,99,25,1*Math.PI,0*Math.PI,false);
                    ctx.moveTo(290 - size + 0, 100);
                    ctx.lineTo(290 - size + 50, 100 - 0);
                    ctx.lineTo(290 - size + 29, 100 + 40);
                    ctx.closePath();
                    ctx.fillStyle = '#959595';
                    ctx.closePath();
                    ctx.stroke();
                    ctx.fill();
                    var i = wheel.segments.length - Math.floor((wheel.angleCurrent / (Math.PI * 2)) * wheel.segments.length) - 2;
                    ctx.textAlign = "left";
                    ctx.textBaseline = "bottom";
                    ctx.font = "1.2em Intelbold";
                    Valor=(wheel.segments[i]);
                    $('#Texto').html(Valor);
                },
                drawSegment: function(key, lastAngle, angle)
                {
                    var ctx = wheel.canvasContext;
                    var centerX = wheel.centerX;
                    var centerY = wheel.centerY;
                    var size = wheel.size;

                    var segments = wheel.segments;
                    var len = wheel.segments.length;
                    var colors = wheel.seg_color;

                    var value = segments[key];
                    ctx.save();
                    ctx.beginPath();

                    // Start in the centre
                    ctx.moveTo(centerX, centerY);
                    ctx.arc(centerX, centerY, size, lastAngle, angle, false); // Draw a arc around the edge
                    ctx.lineTo(centerX, centerY); // Now draw a line back to the centre
                    // Clip anything that follows to this area
                    //ctx.clip(); // It would be best to clip, but we can double performance without it
                    ctx.closePath();

                    ctx.fillStyle = colors[key];
                    ctx.fill();
                    ctx.stroke();

                    // Now draw the text
                    ctx.save(); // The save ensures this works on Android devices
                    ctx.translate(centerX, centerY);
                    ctx.rotate((lastAngle + angle) / 2);
                    //Color de numero
                    ctx.fillStyle = '#FFFFFF';
                    ctx.fillText(value.substr(0, 20), size / 2 + 20, 0);
                    ctx.font = '3em Intelbold';
                    ctx.restore();
                },
                drawWheel: function() {
                    var ctx = wheel.canvasContext;
                    var angleCurrent = wheel.angleCurrent;
                    var lastAngle = angleCurrent;

                    var segments = wheel.segments;
                    var len = wheel.segments.length;
                    var colors = wheel.colors;
                    var colorsLen = wheel.colors.length;

                    var centerX = wheel.centerX;
                    var centerY = wheel.centerY;
                    var size = wheel.size;

                    var PI2 = Math.PI * 2;

                    ctx.lineWidth = 1;
                    //Estilos de la Interlinea 
                    ctx.strokeStyle = '#ffffff';
                    ctx.textBaseline = "middle";
                    ctx.textAlign = "center";
                    ctx.font = "3em Intelbold";

                    for (var i = 1; i <= len; i++) {
                        var angle = PI2 * (i / len) + angleCurrent;
                        wheel.drawSegment(i - 1, lastAngle, angle);
                        lastAngle = angle;
                    }
                    // Draw a center circle
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, 20, 0, PI2, false);
                    ctx.closePath();

                    ctx.fillStyle = '#FFFFFF';
                    //Estilo de semicirculo en posicion de abajo
                    ctx.strokeStyle = '#FFFFFF';
                    ctx.fill();
                    ctx.stroke();

                    // Draw outer circle
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, size, 0, PI2, false);
                    ctx.closePath();

                    ctx.lineWidth = 10;
                    //Estilos arco de arriba
                    ctx.strokeStyle = '#FFFFFF';
                    ctx.stroke();
                },
            };

};
function Datos()
{
    $.ajax
    ({
        url: "./codigo/ajaxdatosruleta.php",
        type: "POST", 
	data: 
        {
            Nulo: 'null'
	},
        success: function(datos)
        {
            venues = eval('('+datos+')');
            console.log(venues);
            cargar_datos();
            wheel.init();
            var segments = new Array();
            $.each(venues, function(key, value)
            {
                segments.push(value);
            });
            wheel.segments = segments;
            wheel.update();
            setTimeout(function()
            {
                window.scrollTo(0, 1);
            }, 0);
        }
    });
}
$(function() 
{
    Datos();

});