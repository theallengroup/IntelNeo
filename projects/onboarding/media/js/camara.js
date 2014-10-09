 //Nos aseguramos que estén definidas
            //algunas funciones básicas
            var oData;
            window.URL = window.URL || window.webkitURL;
            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || function() {
                alert('Su navegador no soporta esta opcion');
            };



            function GuardarFoto()
            {
                Imagenes();
                document.write("<img src='" + oData.cMiFoto + "'/>");
                if (datosVideo.StreamVideo)
                {
                    datosVideo.StreamVideo.stop();
                    window.URL.revokeObjectURL(datosVideo.url);
                }
                $.ajax
                ({
                    url: "AjaxPhotoCel.php",
                    type: "post",
                    data: {imagen: oData},
                    success: function(e)
                    {
                        console.log(e);
                    }
                });
            }
            function Imagenes()
            {
                var oCanvas = document.getElementById("foto");
                oData = {
                    cMiFoto: oCanvas.toDataURL("image/png"),
                    cNombreArchivo: "jorge.png"
                };
            }

            jQuery(document).ready(function() {
                //Este objeto guardará algunos datos sobre la cámara
                window.datosVideo = {
                    'StreamVideo': null,
                    'url': null
                };

                navigator.getUserMedia({'audio': false, 'video': true}, function(streamVideo) {
                    datosVideo.StreamVideo = streamVideo;
                    datosVideo.url = window.URL.createObjectURL(streamVideo);
                    jQuery('#camara').attr('src', datosVideo.url);
                }, function() {
                    alert('No fue posible obtener acceso a la cámara.');
                });

               
                jQuery('#botonFoto').on('click', function(e)
                {
                   
                    $('#contenedorFoto').append('<canvas id="foto"></canvas>');
                    //var contenedorFoto=jQuery('#contenedorFoto');
                    //contenedorFoto.append( "<p>Test</p>" );

                    //alert(contenedorFoto.p);
					var oCamara, oFoto, oContexto, w, h;
                    oCamara = jQuery('#camara');

                    oFoto = jQuery('#foto');
                    w = oCamara.width();
                    h = oCamara.height();
                    oFoto.attr({'width': w, 'height': h});
                    oContexto = oFoto[0].getContext('2d');
                    oContexto.drawImage(oCamara[0], 0, 0, w, h);
                    Imagenes();
					//$('#ValuePhoto').val(oData.cMiFoto);
					//document.getElementById("myForm").submit();
					$( "#contenedorCamara" ).empty();
                    $( "#MenuCamera" ).empty();
					//$('#MenuCamera').append('<div class="icon_retake"><input type="button" onclick="Retake()"  id="botonFotoRetake" class="b_photo_retake" value="Retake"><input type="button" id="botonFotosend" class="b_photo_send" value="Use" onclick="Use()"> </div>');
                   
                    $('#MenuCamera').append('<div class="icon_retake"><input type="button" onclick="Retake()"  id="botonFotoRetake" class="b_photo_retake" value="Retake"><form method=POST><input type=hidden name=mod value="usr2session" /><input type=hidden name=ac value="r_photo" /><textarea style="display:none;" class="textarea_" name=body>'+oData.cMiFoto+'</textarea><input type="submit" class="send2" value="Use"></div>');
					//document.write("<img src='" + oData.cMiFoto + "'/>");
                    /*<div class="icon_retake">
                        <input type="button" onclick="Retake()"  id="botonFotoRetake" class="b_photo_retake" value="Retake">
                        <form method=POST>
                        <input type=hidden name=mod value="usr2session" />
                        <input type=hidden name=ac value="r_photo" />
                        <textarea style="display:none;" class="textarea_" name=body>'+oData.cMiFoto+'</textarea>
                        <input type="submit" class="send2" value="Use">
                        
                    </div>*/

                    $('#Guardar_foto').html('<input id="" onclick="GuardarFoto()" type="button" value = "Guardar imagen"/>');
                });

              
                

            });
            function Retake()
                {
                
                  window.location.href='?mod=usr2session&ac=r_take_photo';
                  
                };
                

                