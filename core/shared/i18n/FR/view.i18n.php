<?php
	#cadenas EDIT.php

	$i18n['view']=array(
		'no_templates_found'=>'templates where not found',

		'cf_step1'=>'Config',
		'step2'=>'Mantenimiento de Cadenas<br/>Paso 2: Edici&oacute;n',
		'written_ok'=>'Se guardó exitosamente el archivo:',
		'write_error'=>'Imposible escribir en el archivo:',
		'copy_error'=>'Imposible hacer Copia de seguridad del archivo:',
		'file_select'=>array(
			'ok'=>'OK',
			'_form_title'=>'Mantenimiento de Cadenas<br/>Paso 1: seleccione el archivo de cadenas',
			'filename'=>'Archivo',
			'help_filename'=>'Seleccione el archivo de Cadenas a Editar, los archivos se encuentran en ./i18n/'.std_get_language()."/<nombre_archivo>.php",
		),

		'part2'=>array(
			'ok'=>'OK',
			'_form_title'=>'Seleccione los Campos',
			'fields'=>'Campos',
			'help_fields'=>'Seleccione los campos que aparecerán en la vista',
			'filename'=>'Archivo',
			'help_filename'=>"",
			'name'=>'Nombre',
			'title'=>'Título',
			'type'=>'Tipo',
			'template'=>'Plantilla',
			'actions'=>'Acciones',
			'side_actions'=>'Acciones Laterales',
			'down_actions'=>'Acciones inferiores',
			'filter'=>'Filtro',
			'restrict'=>'restricciones',

			'help_name'=>'El nombre de la vista no debe contener espacios.',
			'help_title'=>'Para agregar cadenas que no estén en la lista',
			'help_type'=>'Tipos implementados:table,record',
			'help_template'=>'La plantilla indica que archivo HTML debe usarse para mostrar esta vista, nota:(solo es valido en tipos: table), nota 2: los archivos están en la carpeta /templates/, para mas informacion, consulte la documentación',
			'help_actions'=>'Acciones (solo es valido en tipo: record)',
			'help_side_actions'=>'Acciones Laterales (solo es valido en tipo: table)',
			'help_down_actions'=>'Acciones inferiores (solo es valido en tipo: table)',
			'help_filter'=>'Filtro (es una funcion de PHP que permite alterar los datos, just antes de mostrarlos  p.e. bbcode, money, etc) (solo es valido en tipo:table)',
			'help_restrict'=>'Las restricciones: es una sentencia de SQL que permite filtrar los registros que aparecerán en esta vista, (p.e. solo los registros que pertenezcan a esta fecha, o a este usuario, etc) (solo es valido en tipo:table)',
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
