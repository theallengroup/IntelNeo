<?php
$i18n_std=array(
		'roles'=>array(
			'guest'=>'Guest',
		),
		'menu'=>array(
			'logged_in_as'=>'User:',
			),

		#Generic datatypes
		#yeah, right.
		'simple_search'=>array(
			//'_form_title'=>'Búsqueda Simple',
			'__search_term'=>'Term',
			'help___search_term'=>'This term will be searched in all the fields',
			'links'=>'Shortcuts',
			'help_links'=>'',
			
		),
		'datatypes'=>array(
			'genders'=>array("M"=>'Male','F'=>'Female'),

			'gender'=>'Gender',
			'help_gender'=>'Gender will have effect in some of the messages sent to the user.',

			'plural'=>'Plural',
			'help_plural'=>'The plural form is used in the table view view:list_all ',

			'title'=>'Table Name',
			'help_title'=>'Please give the singu;ar form of the table items. this text will be visible in the forms used to edit , add and delete.',

			'new_title'=>'Add Item',
			'help_new_title'=>'This text will be visible in the Add Item forms',

			'edit_title'=>'Edit Item',
			'help_edit_title'=>'This text willl be visible in the Edit Item forms.',

			'view_title'=>'View Item',
			'help_view_title'=>'This will appear in the read-only forms.',


			'help'=>'Help',
			'help_help'=>'Help will guide your users throughout your application, the more help you rovide, and the clrarer it is , the less problems you\'ll get.',

			'fields'=>'Fields',
			'help_fields'=>'Table Fields ',
		),
		'pagination'=>array(
			
			'page'=>'Page',
			'of'=>' of ',
			'total'=>'Total',
			'next'=>'Next',
			'previous'=>'Previous',
			'first'=>'First',
			'last'=>'Last',
			'go'=>'Go to Page ',
		),
		'download'=>'Download in MS Excel Format:',
		'uploaded_ok'=>'Image Upload Successful',
		'change_picture'=>'Change Picture',
		'see_also'=>'See Also',
		'generated_on'=>'Report Date and Time:',
		'today'=>'Today',
		'months'=>array(
			'1'=>'January',
			'2'=>'February',
			'3'=>'March',
			'4'=>'April',
			'5'=>'May',
			'6'=>'June',
			'7'=>'July',
			'8'=>'Agugust',
			'9'=>'September',
			'10'=>'October',
			'11'=>'November',
			'12'=>'December',
		),
		'logout'=>'Logout',
		'confirm'=>array(
			'_form_title'=>'',
			'yes'=>'Yes, I am sure.',
			'no'=>'No',
			'help_yes'=>'yes, I am sure',
			'help_no'=>'No',
			'help_1'=>'yes, I am sure',
			'help_2'=>'No',
					
			),
		'msg'=>array(
			'not_unique_field'=>' <div style="text-align:left" align=left>Error:Unable to add.<br/> In table: <b>[#table]</b><br/> The value: <b>"[#field_value]"</b> <br/> Already exists in the field: <b>[#field_name]</b>.</div>',
			'not_unique_field_update'=>' <div style="text-align:left" align=left>Error:Unable to update.<br/> In table: <b>[#table]</b><br/> The value: <b>"[#field_value]"</b> <br/> Already exists in the field: <b>[#field_name]</b>.</div>',
			'form_inserted_ok'=>	'Added Successfully.<br/>[#b2l]',
			'insert_error'=>'Unable to Add.',

			'message'=>'Message',
			'no_items'=>'Cannot find: <em>[#name]</em>  <br/><span style="text-align:right">[#ac]</span>',
			
			),
		'form' => array(
				'please_select'=>'Please Select',
			),
		'unable_to_delete'=>'Unable to delete due to Referentiasl Integrity',
		'load_csv'=>'Load CSV File',
		'no_records_selected'=>'No records selected.',
			'error'=>array(
				/// @todo 1131 add descriptive parameters to all error codes.
				//
				'invalid_sort_field'=>'[std076] Invalid Sort Field:[#sort_field]',
				'main_lang_file_missing'=>'Main Language File Missing!',
				'cannot_load_base'=>'Unable to load Base Module: [base_module]',
				'unable_to_continue'=>'Unable to Continue.',
				'no_fields_error'=>'Critical Error: no fields in module',
				'no_such_view'=>'No Such View:<u>[#view]</u> in module:[#mod]',
				'no_role'=>'Error: no such role for action:[#ac]',
				'no_auth_ac'=>'The Current user has not enough Privilege to do this.<br/> This error might be due to several causes.<ul><il>The user attempted an invalid operation for its profile<li>The session has been inactive for too long</ul> in order to solve this problem, try <a href="?logout=1">login back</a>',
				'no_auth_mod'=>'The current User has not enough privileges  to execute This Module:[#program_name]<br/>This error may have several causes: <ul><li>The user attempted an invalid operation for its profile<li>The session has been inactive for too long</ul><br/>in order to solve this problem, try: <a href="?logout=1">login back</a>',
				'unable_to_fetch_privileges'=>'Unable to fetch Privileges',
				'submitted_action_not_found'=>'Action not found.',
				'no_view_handler'=>'No Such view handler',
				'no_views_file'=>'View file does not exist.',
				'no_views'=>'The module has no views.',
				'no_strings_file'=>"Error: the language file does not exist: [#file]",
				'strings_file_corrupt'=>"Error: Language File is Corrupt!: [#file]<br>[#local]<br>[#shared]",
				'no_rel_file'=>"Error: the relationships file does not exists: [#file]",
				'no_foreign_field_found'=>"Error: no foreign connection field on the table: [#mod]",
				'no_db_connection'=>'Unable to connect to the database:[#db]',
				'delete'=>'Unable to delete, the record does not exists.',
				'no_such_action'=>'Error, no such action:[#ac] in the module:[#mod].',
				'no_such_record'=>'The record is missing/was deleted',
				'application_corrupt'=>'Error: must re-generate, contact the administratror<br> uknown field:',
				'application_corrupt2'=>'The application uses fields that do not exist in the database, re-generation is necesary, please contact the admininistrator.<br/><br/>Uknown Field:',
				'header'=>'Application Error',
				'header_code'=>'Application Error <br/>Code:',
				'no_such_module'=>'Error: the module does not exist/ fiel not found, please contact the administrator<br/><br/>Module:[#mod]',
				'no_fields'=>'Error: no strings in form',
				'nohelp4input'=>"Error: no help text in the field::<br/>Name:[#name]<br/>key:[#key]",
			),
		'list'=>array(
			'form_ed1'=>		'Edit2',//wtf???
			'form_check_all'=>	'All',
			'form_all_xls'=>	'Export',
			'form_all_new'=>	'Add',
			'form_all_b2l'=>	'Back to results',
			'form_cancelled'=>	'Cancelled by User',
			'form_all_new2'=>	'Add',
			'form_all_image_upload'=>	'Upload Image',
			'form_all_edit'=>	'Edit',
			'form_all_edit2'=>	'Update',
			'form_all_delete'=>	'Delete',
			'form_all_xml_export'=>	'Export XML',
			'form_all_load_from_excel'=>	'Import',
			'form_all_delete2'=>	'Delete for ever',
			'form_all_delete_selected'=>'Delete Selected',
			'form_load_csv'=>'Load CSV File',
			'form_std_ok'=>		'OK',
			'form_update_ok'=>	'Update Successfull',
			'form_no_update'=>	'No Changes were made.',
//			'form_login'=>		'Login',// to the System
//			'form_register'=>	'Register',
			
			'form_confirm'=>	'Are you <font color=red>Sure</font> You want to the delete the record for ever?',
			'form_delete_ok'=>	'Record was Deleted',

			'actions'=>'Actions',

			'form_edit_all'=>'Edit',//again????
			'help_form_edit_all'=>'Click to edit this Record',
			'form_view:edit_readonly'=>'View',
			'form_read_only_view'=>'View',//?
			'form_read_only'=>'Read Only',//?
			'help_form_view:edit_readonly'=>'Click to View this Record',
			'help_form_delete'=>'Click to delete this record permanently (requires confirmation)',
#			'down_new'=>'Add',
#			'help_down_new'=>'Add a new record to this table',
			'form_view:new'=>'Add',
			'help_form_view:new'=>'Add a new record to this table',
			'form_xls'=>'Export',	
			'help_form_xls'=>'Generate an Excel Report',	

			'form_fields_view'=>	'Advanced',

			'form_fields'=>		'Fields',
			'form_help_fields'=>	'Select the fields you wish on this view to appear, your selection
			will be stored for duture visits.',

			'form_fields_btn'=>	'View Fields',
			'form_help_fields_btn'=>'Click Here to update the View, and see just the fields you&amp;ve just selected',
			
			/*remove warnings*/
			'form_view'=>'',

			/*Search in ml()*/	
			'form_simple_search'=>'Simple Search',
			'form_advanced_search'=>'Advanced Search',
			'form_search_ok'=>'Search',
			'form_add_item'=>'Add Item',

			'form_any_value'=>'Any',
			//JS array editor
			'form_clone'=>'Copy',
			'form_append_item'=>'Add Item',
			'form_append_tree'=>'Add Tree',
			'form_rename'=>'Rename',

			//delete selected
			'form_cannot_delete'=>'Unable to delete Item:',
			'form_delete_ok'=>'Deleted Item:',

			//ml.help
			'form_help'=>'Help',//wtf again?
			'form_no_help_available'=>'No help available on this topic',
			'form_no_field_help_available'=>'no help available on the field',
			//table editor component
			'form_add_row'=>'Add Row',
			'form_delete_row'=>'[X]',
	
		),
		);
?>
