var venues;
var wheel;
var Valor;
shuffle = function(o) {
    for (var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
    return o;
};

String.prototype.hashCode = function() {
    var hash = 5381;
    for (i = 0; i < this.length; i++) {
        char = this.charCodeAt(i);
        hash = ((hash << 5) + hash) + char;
        hash = hash & hash;
    }
    return hash;
    console.log(hash)
}

Number.prototype.mod = function(n) {
    return ((this % n) + n) % n;
}

function Redir() {
    $.each( venues, function( key, value )  {
        if(value == Valor){
            window.location += '&value=' + Valor
        };
    });
}

function cargar_datos() {
    wheel = {
        timerHandle: 0,
        timerDelay: 30,
        angleCurrent: 0,
        angleDelta: 0,
        canvas: null,
        canvasContext: null,
        radius: 0,
        centerX: 0,
        centerY: 0,
        colors: ['#A4CC28',  '#016DC6','#02ACED','#FDB802',  '#FDD902', '#02427F',
                 '#A4CC28',  '#016DC6','#02ACED','#FDB802',  '#FDD902', '#02427F'],
        segments: [],
        seg_colors: [],
        maxSpeed: Math.PI / 16,
        upTime: 1000,
        downTime: 3500,
        spinStart: 0,
        frames: 0,
        spin: function() {
            if (wheel.timerHandle == 0) {
                wheel.spinStart = new Date().getTime();
                wheel.maxSpeed = Math.PI / (16 + Math.random()); // Randomly vary how hard the spin is
                wheel.frames = 0;
                wheel.sound.currentTime = (wheel.sound.duration - (wheel.downTime / 1000));
                wheel.sound.play();
                wheel.timerHandle = setInterval(wheel.onTimerTick, wheel.timerDelay);
            }
        },
        onTimerTick: function() {
            wheel.frames++;
            wheel.draw();
            var duration = (new Date().getTime() - wheel.spinStart);
            var progress = 0;
            var finished = false;
            if (duration < wheel.upTime) {
                progress = duration / wheel.upTime;
                wheel.angleDelta = wheel.maxSpeed * Math.sin(progress * Math.PI / 2);
            }
            else {
                progress = duration / wheel.downTime;
                wheel.angleDelta = wheel.maxSpeed * Math.sin(progress * Math.PI / 2 + Math.PI / 2);
                if (progress >= 1) {
                    finished = true;
                    wheel.sound.pause();
                    Redir();
                }
            }
            wheel.angleCurrent += wheel.angleDelta;
            while (wheel.angleCurrent >= Math.PI * 2) {
                wheel.angleCurrent -= Math.PI * 2;
            }
            if (finished) {
                clearInterval(wheel.timerHandle);
                wheel.timerHandle = 0;
                wheel.angleDelta = 0;
            }
        },
        init: function(optionList) {
            try {
                wheel.initWheel();
                wheel.initAudio();
                wheel.initCanvas();
                wheel.draw();
                $.extend(wheel, optionList);
            }
            catch (exceptionData) {
                alert('Wheel is not loaded ' + exceptionData);
            }
        },
        initAudio: function() {
            var sound = document.createElement('audio');
            console.log(sound);
            sound.setAttribute('src', 'http://bramp.net/javascript/wheel.mp3');
            wheel.sound = sound;
        },
        initCanvas: function() {
            var canvas = $('#wheel #canvas').get(0);

            if ($.browser.msie) {
                canvas = document.createElement('canvas');
                $(canvas).attr('width', 300).attr('height', 350).attr('id', 'canvas').appendTo('.wheel');
                canvas = G_vmlCanvasManager.initElement(canvas);
            }
           /* console.log(canvas);
            $('#wheel').css({'height': '76%'})
            $('.top').css({'height': '24%'})
            $(canvas).attr('width', 440).attr('height', 400);*/

            var docH = $('.content').innerHeight();
            var topH = $('.top').innerHeight();
            var infoH = $('.spin-info').innerHeight();

            var w = $('#wheel');
            var c = $(canvas);
            w.css('height', docH - topH - (4 * infoH));

            c.css('height', w.innerHeight());
            c.css('width', w.innerWidth());

            c.attr('height', c.innerHeight());
            c.attr('width', c.innerWidth());

            canvas.addEventListener("click", wheel.spin, false);
            canvas.addEventListener("touchmove", wheel.spin, false);

            wheel.canvas = canvas;
            wheel.canvasContext = canvas.getContext("2d");

            var size = Math.min(canvas.height, canvas.width);
            wheel.radius = size / 2 - (size * .02) - 10;
            wheel.centerX = canvas.width / 2;
            wheel.centerY = canvas.height / 2 + (size * .02) + 5;
        },
        initWheel: function() {
            shuffle(wheel.colors);
        },
        update: function() {
            wheel.angleCurrent = (1.48 * Math.PI) - (((2 * Math.PI) / wheel.segments.length) / 2);
//            wheel.angleCurrent = ((r + 0.5) / wheel.segments.length) * Math.PI * 2;
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
        draw: function() {
            wheel.clear();
            wheel.drawWheel();
            wheel.drawNeedle();
        },
        clear: function() {
            var ctx = wheel.canvasContext;
            ctx.clearRect(0, 0, 1000, 800);
        },
        drawNeedle: function() {
            var ctx = wheel.canvasContext;
            var centerX = wheel.centerX;
            var centerY = wheel.centerY;
            var radius = wheel.radius;

            ctx.lineWidth = 3;
            ctx.strokeStyle = '#959595';
            ctx.fileStyle = '#959595';

            var length = radius * .12;
            var start = length * .2;
            size = Math.min(canvas.height, canvas.width);

            ctx.beginPath();
            ctx.arc(centerX - start, 1.5*length, length, 1*Math.PI, 0*Math.PI, false);

            ctx.moveTo(centerX - length - (.15*length), 1.5*length + 1);
            ctx.lineTo(centerX + length - (.2*length), 1.5*length + 1);
            ctx.lineTo(centerX, 1.5*length + (.2*radius));
            ctx.closePath();

            ctx.fillStyle = '#959595';
            ctx.stroke();
            ctx.fill();

            ctx.textAlign = "left";
            ctx.textBaseline = "bottom";
            ctx.font = "1.2em Intelbold";

            var i = 0;
            var len = wheel.segments.length;
            if( len > 0 ) {
                var needle = 1.48 * Math.PI;
                var offset = wheel.angleCurrent;
                // console.log('needle is: ' + needle);
                // console.log('offset is: ' + offset);

                // TODO: can this loop be converted to an equation?
                var segmentAngle = Math.PI * 2 / len;
                for (var j = 0; j < len; j++) {
                    var startAngle = (j * segmentAngle) + offset;
                    if(startAngle > Math.PI * 2) {
                        startAngle -= Math.PI * 2;
                    }
                    var endAngle = startAngle + segmentAngle;
                    if(endAngle > (Math.PI * 2)) {
                        endAngle -= Math.PI * 2;
                    }
                    // console.log('between ' + startAngle + ' and ' + endAngle + ' for ' + wheel.segments[j]);
                    if( needle >= startAngle && needle < endAngle ) {
                        // console.log("Found match!");
                        i = j;
                        break;
                    }
                }
            }
            // console.log('i: ' + i);
            // console.log('segment: ' + wheel.segments[i]);
            // console.log("\n");
            Valor = (wheel.segments[i]);
            $('#Texto').html(Valor);
        },
        drawSegment: function(key, lastAngle, angle) {
            var ctx = wheel.canvasContext;
            var centerX = wheel.centerX;
            var centerY = wheel.centerY;
            var radius = wheel.radius;

            var segments = wheel.segments;
            var len = wheel.segments.length;
            var colors = wheel.seg_color;

            var value = segments[key];
            ctx.save();

            ctx.beginPath();
            // Start in the centre
            ctx.moveTo(centerX, centerY);
            ctx.arc(centerX, centerY, radius, lastAngle, angle, false); // Draw a arc around the edge
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
            ctx.font = "1.2em Intelbold";
            ctx.fillText(value.substr(0, 20), radius / 2 + 10, 0);

            //console.log(lastAngle + " to " + angle + " with " + value.substr(0,20));

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
            var radius = wheel.radius;

            var PI2 = Math.PI * 2;

            ctx.lineWidth = 1;
            //Estilos de la Interlinea
            ctx.strokeStyle = '#ffffff';
            ctx.textBaseline = "middle";
            ctx.textAlign = "center";
            ctx.font = "1.2em Intelbold";

            for (var i = 1; i <= len; i++) {
                var angle = PI2 * (i / len) + angleCurrent;
                wheel.drawSegment(i - 1, lastAngle, angle);
                lastAngle = angle;
            }

            // Draw a center circle
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius * .1, 0, PI2, false);
            ctx.closePath();

            ctx.fillStyle = '#FFFFFF';
            //Estilo de semicirculo en posicion de abajo
            ctx.strokeStyle = '#FFFFFF';
            ctx.fill();
            ctx.stroke();

            // Draw outer circle
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius, 0, PI2, false);
            ctx.closePath();

            ctx.lineWidth = 5;
            //Estilos arco de arriba
            ctx.strokeStyle = '#FFFFFF';
            ctx.stroke();
        },
    };
};

function getUrlVars() {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('='); vars.push(hash[0]); vars[hash[0]] = hash[1];
    }
    return vars;
}

function Datos() {
    $.ajax ({
        url: "./codigo/ajaxdatosruleta.php",
        type: "POST",
        data:  {
            id: getUrlVars()['activity_id']
        },
        success: function(datos) {
            venues = eval('('+datos+')');
            cargar_datos();
            wheel.init();
            $.event.trigger({
                type: "wheelLoaded"
            });
            var segments = new Array();
            $.each(venues, function(key, value) {
                segments.push(value);
            });
            wheel.segments = segments;
            wheel.update();

            if (typeof Valor !== 'undefined') {
                $('#Texto').html(Valor);
            }

            setTimeout(function() {
                window.scrollTo(0, 1);
            }, 0);
        }
    });
}

$(function() {
    Datos();
});