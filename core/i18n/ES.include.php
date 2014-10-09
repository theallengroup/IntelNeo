<?php
$i18n_std=array(
		'roles'=>array(
			'guest'=>'Invitado',
		),
		'menu'=>array(
			'logged_in_as'=>'Usuario:',
			),

		#Generic datatypes
		#yeah, right.
		'simple_search'=>array(
			//'_form_title'=>'Búsqueda Simple',
			'__search_term'=>'Término',
			'help___search_term'=>'Este término será buscado en todos los campos del mismo tipo. si el término es numérico, se buscarán solamente en los campos numéricos, si el término es alfanumérico se buscará en los campos alfanuméricos, etc.',
			'links'=>'Accesos Directos',
			'help_links'=>'',
			
		),
		'datatypes'=>array(
			'genders'=>array("M"=>'Masculino','F'=>'Femenino'),

			'gender'=>'Género',
			'help_gender'=>'El género tendrá efecto en algunos de los mensajes enviados al usuario.',

			'plural'=>'Plural',
			'help_plural'=>'La forma plurar se usa en la funcion de mostrar todos los registros (view:list_all ) ',

			'title'=>'Nombre de la Tabla',
			'help_title'=>'Indique la forma singular de los ítems de la tabla, este texto ser&aacute; visible en los formularios utilizados para editar, agregar y borrar',

			'new_title'=>'Agregar Elemento',
			'help_new_title'=>'Este texto aparecerá en los formularios utilizados para agregar',

			'edit_title'=>'Editar Elemento',
			'help_edit_title'=>'Este texto aparecerá en los formularios utilizados para editar',

			'view_title'=>'Ver Elemento',
			'help_view_title'=>'Este texto aparecerá en los formularios de solo lectura.',


			'help'=>'Ayuda',
			'help_help'=>'La ayuda guiará a sus usuarios a través de su aplicación, mientras más ayuda coloque, y más claro sea, menores contratiempos.',

			'fields'=>'Campos',
			'help_fields'=>'los Campos de la tabla, son ...',
		),
		'pagination'=>array(
			
			'page'=>'P&aacute;gina',
			'of'=>' de ',
			'total'=>'Total',
			'next'=>'Siguiente',
			'previous'=>'Anterior',
			'first'=>'Primera',
			'last'=>'&Uacute;ltima',
			'go'=>'Ir a la P&aacute;gina',
		),
		'download'=>'Descargar a Formato MS Excel:',
		'see_also'=>'Vea Tambi&eacute;n',
		'change_picture'=>'Cambiar Foto',		
		'uploaded_ok'=>'Foto Cambiada Exitosamente',
		'generated_on'=>'Fecha y Hora del Reporte:',
		'today'=>'Hoy',
		'months'=>array(
			'1'=>'Enero',
			'2'=>'Febrero',
			'3'=>'Marzo',
			'4'=>'Abril',
			'5'=>'Mayo',
			'6'=>'Junio',
			'7'=>'Julio',
			'8'=>'Agosto',
			'9'=>'Septiembre',
			'10'=>'Octubre',
			'11'=>'Noviembre',
			'12'=>'Diciembre',
		),
		'logout'=>'Salir',
		'confirm'=>array(
			'_form_title'=>'',
			'yes'=>'Si, estoy seguro.',
			'no'=>'No',
			'help_yes'=>'Si, estoy seguro.',
			'help_no'=>'No',
			'help_1'=>'Si, estoy seguro.',
			'help_2'=>'No',
					
			),
		'msg'=>array(
			'not_unique_field'=>' <div style="text-align:left" align=left>Error:Imposible Agregar.<br/> En la tabla de: <b>[#table]</b><br/> El valor: <b>"[#field_value]"</b> <br/> Ya existe en el campo: <b>[#field_name]</b>.</div>',
			'not_unique_field_update'=>' <div style="text-align:left" align=left>Error:Imposible Actualizar.<br/> En la tabla de: <b>[#table]</b><br/> El valor: <b>"[#field_value]"</b> <br/> Ya existe en el campo: <b>[#field_name]</b>.</div>',
			'form_inserted_ok'=>	'Agregado Exitosamente.<br/>[#b2l]',
			'insert_error'=>'Imposible Agregar.',

			'message'=>'Mensaje',
			'no_items'=>'No se encontraron:<em>[#name]</em> <br/><span style="text-align:right">[#ac]</span>',
			
			),
		'form' => array(
				'please_select'=>'Selecciona',
			),
		'unable_to_delete'=>'Imposible borrar: integridad referencial.',
		'load_csv'=>'Cargar Archivo CSV',
		'no_records_selected'=>'No se seleccion&oacute; ning&uacute;n Registro',
			'error'=>array(
				/// @todo 1131 add descriptive parameters to all error codes.
				//
				'invalid_sort_field'=>'[std076] Campo de Ordenamiento Inv&aacute;lido:[#sort_field]',
				'main_lang_file_missing'=>'Falta Archivo Principal de Idioma!',
				'cannot_load_base'=>'Imposible cargar m&oacute;dulo base: [#base_module]',
				'unable_to_continue'=>'Imposible Continuar.',
				'no_fields_error'=>'Error Grave: no hay Campos en el M&oacute;dulo.',
				'no_such_view'=>'No existe la Vista:<u>[#view]</u> en el M&oacute;dulo:[#mod]',
				'no_role'=>'ERROR: no existe el ROL. para la acci&oacute;n:[#ac]',
				'no_auth_ac'=>'El Usuario actual no posee los privilegios suficientes para realizar esta acci&oacute;n:<br/><br/><u>[#ac]</u><br/><br/> Por favor, contacte al administrador del sistema, si cree que necesita privilegios, para realizar esta acci&oacute;n',
				'no_auth_mod'=>'El Usuario actual no posee los privilegios suficientes ejecutar este m&oacute;dulo:[#program_name]<br/>Este error puede deberse a varias causas: <ul><li>el usuario intentó una operación no permitida para su perfil<li>La sesión estuvo inactiva durante mucho tiempo</ul><br/>para resolver este problema, intente: <a href="?logout=1">Ingresar de nuevo al sistema</a>',
				'unable_to_fetch_privileges'=>'Imposible obtener privilegios',
				'submitted_action_not_found'=>'No se encuentra la acci&oacute;n.',
				'no_view_handler'=>'No existe Manejador de Vista.',
				'no_views_file'=>'El archivo de Vistas no Existe',
				'no_views'=>'El modulo no posee Vistas.',
				'no_strings_file'=>"Error: El archivo de Idioma no existe: [#file]<br>[#local]<br>[#shared]",
				'strings_file_corrupt'=>"Error: El archivo de Idioma est&aacute; Corrupto: [#file]<br>[#local]<br>[#shared]",
				'no_rel_file'=>"Error: El archivo de Conecci&oacute;nes no existe: [#file]",
				'no_foreign_field_found'=>"Error: no hay campo foráneo de conneción en la tabla: [#mod]",
				'no_db_connection'=>'Imposible conectarse a la base de datos:[#db]',
				'delete'=>'Imposible Borrar, el registro no existe.',
				'no_such_action'=>'Error, no existe la Acci&oacute;n:[#ac] en el m&oacute;dulo:[#mod].',
				'no_such_record'=>'El registro no existe/fue borrado',
				'application_corrupt'=>'Error: es necesario re-generar, contacte al administrador.<br/>Campo desconocido:',/*TODO when does this happen?*/
				'application_corrupt2'=>'La aplicación usa campos que no existen en la base de datos, es necesario re-generar, contacte al administrador.<br/><br/>Campo desconocido:',
				'header'=>'Error de la Aplicación',
				'header_code'=>'Error de la Aplicación <br/>C&oacute;digo:',
				'no_such_module'=>'Error: el módulo no existe / no se encuentra el Archivo, por favor contacte al administrador.<br/><br/>Módulo:[#mod]',
				'no_fields'=>'Error, no hay cadenas en el formulario.',
				'nohelp4input'=>"Error, no hay texto de ayuda para el siguiente campo:<br/>Nombre:[#name]<br/>LLave:[#key]",
			),
		'list'=>array(
			'form_ed1'=>		'Edit2',
			'form_check_all'=>	'Todos',
			'form_all_xls'=>	'Exportar',
			'form_all_new'=>	'Agregar',
			'form_all_b2l'=>	'Volver a la lista de Resultados',
			'form_cancelled'=>	'Acción Cancelada por el Usuario.',
			'form_all_new2'=>	'Agregar',
			'form_all_image_upload'=>	'Subir Im&aacute;gen',
			'form_all_edit'=>	'Editar',
			'form_all_edit2'=>	'Actualizar',
			'form_all_delete'=>	'Borrar',
			'form_all_xml_export'=>	'Exportar XML',
			'form_all_load_from_excel'=>	'Importar',
			'form_all_delete2'=>	'Eliminar Permanentemente',
			'form_all_delete_selected'=>'Borrar selecci&oacute;n',
			'form_load_csv'=>'Cargar Archivo CSV',
			'form_std_ok'=>		'Aceptar',
			'form_update_ok'=>	'Actualizaci&oacute;n Exitosa',
			'form_no_update'=>	'No hubo Cambios.',
//			'form_login'=>		'Ingresar al Sistema',
//			'form_register'=>	'Registarse',
			
			'form_confirm'=>	'Está <font color=red>Seguro</font> de que desea eliminar el registro permanentemente?<div align=left><br/>[#_id]<br/>[#_name]</div>',
			'form_delete_ok'=>	'El registro fue Borrado.',

			'actions'=>'Acciones',

			'form_edit_all'=>'Editar',
			'help_form_edit_all'=>'Click para Editar este registro',
			'form_view:edit_readonly'=>'Ver',//?
			'form_read_only_view'=>'Ver',//?
			'form_read_only'=>'Solo Lectura',//?
			'help_form_view:edit_readonly'=>'Click para Ver este registro',
			'help_form_delete'=>'Click para Borrar permanentemente este registro (requiere confirmaci&oacute;n)',
#			'down_new'=>'Agregar',
#			'help_down_new'=>'Agregar un nuevo registro a esta tabla.',
			'form_view:new'=>'Agregar',
			'help_form_view:new'=>'Agregar un nuevo registro a esta tabla.',
			'form_xls'=>'Exportar',	
			'help_form_xls'=>'Generar Reporte tipo MS Excel 2000',	

			'form_fields_view'=>	'Opciones Avanzadas',

			'form_fields'=>		'Campos',
			'form_help_fields'=>	'Seleccione los campos que desea en esta vista, su selección será guardada para futuras visitas.',

			'form_fields_btn'=>	'Ver Campos',
			'form_help_fields_btn'=>'Haga click aquí para refrescar la vista, y ver solo los campos que ha seleccionado.',
			
			/*remove warnings*/
			'form_view'=>'',

			/*Search in ml()*/	
			'form_simple_search'=>'Búsqueda Simple',
			'form_advanced_search'=>'Búsqueda Avanzada',
			'form_search_ok'=>'Buscar',
			'form_add_item'=>'Agregar Ítem',

			'form_any_value'=>'Cualquiera',
			//JS array editor
			'form_clone'=>'Copiar',
			'form_append_item'=>'Agregar Ítem',
			'form_append_tree'=>'Agregar Árbol',
			'form_rename'=>'Renombrar',

			//delete selected
			'form_cannot_delete'=>'Imposible Eliminar Ítem:',
			'form_delete_ok'=>'Eliminado Ítem:',

			//ml.help
			'form_help'=>'Ayuda',
			'form_no_help_available'=>'No hay ayuda disponible para este tema.',
			'form_no_field_help_available'=>'No hay ayuda disponible para este campo.',
			//table editor component
			'form_add_row'=>'Agregar Fila',
			'form_delete_row'=>'[X]',
	
		),
		);
?>
