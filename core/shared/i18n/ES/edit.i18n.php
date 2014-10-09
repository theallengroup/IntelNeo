<?php
	#cadenas EDIT.php

	$i18n['edit']=array(
		
		'msg_field_exists'=>'Error: el campo ya existe',

		'add_field'=>array(
			'_form_title'=>'Agregar Campo',
			'module'=>'M&oacute;dulo',
			'help_module'=>'Una tabla de su aplicaaci&oacute;n',
			'field_name'=>'Campo (Espa&ntilde;ol)',
			'help_field_name'=>'Campo que ver&aacute; el usuario',
			'behavior'=>'Comportamiento',
			'help_behavior'=>'(solo para usuarios avanzados)',
			'field_sqlname'=>'Campo (SQL)',
			'help_field_sqlname'=>'debe ser un nombre de SQl v&aacute;lido',
			'field_help'=>'Ayuda',
			'help_field_help'=>'LA informacion de Campo',
			'field_type'=>'Tipo',
			'help_field_type'=>'Tipo',
			'field_length'=>'Longitud',
			'help_field_length'=>'Entero Positivo',
			'field_options'=>'Opciones (separadas por comas, sólo se requiere si (TYPE=LIST))',
			'help_field_options'=>'',
			'field_default'=>'Valor por Defecto',
			'help_field_default'=>'',
		),

		'drop_field'=>array(
			'_form_title'=>'Eliminar Campo',
			'field'=>'Campo',
			'help_field'=>' ',
		),

		'cf_step1'=>'Configuraci&oacute;n',
		'step2'=>'Mantenimiento de Cadenas<br/>Paso 2: Edici&oacute;n',
		'written_ok'=>'Se guardó exitosamente el archivo:',
		'write_error'=>'Imposible escribir en el archivo:',
		'copy_error'=>'Imposible hacer Copia de seguridad del archivo:',
		'file_select'=>array(
			'ok'=>'OK',
			'_form_title'=>'Mantenimiento de Cadenas<br/>Paso 1: seleccione el archivo de cadenas',
			'filename'=>'Archivo',
			'help_filename'=>'Seleccione el archivo de Cadenas a Editar, los archivos se encuentran en ./i18n/'.std_get_language()."/[nombre_archivo].php",
		),

		'view_step2'=>'Mantenimiento de Vistas<br/>Paso 2: Edici&oacute;n',
		'view_select'=>array(
			'ok'=>'OK',
			'_form_title'=>'Mantenimiento de Vistas<br/>Paso 1: seleccione la tabla',
			'filename'=>'Archivo',
			'help_filename'=>"",
		),
	);
?>
