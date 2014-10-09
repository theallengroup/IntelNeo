<?php
	#the PATH array, determines what to do with each input field @ i18n/lang/whatever.php

$i18n_datatype["i18n"]=array(

	'i18n/*/table_help'=>array(
		'description'=>	$i18n_std["datatypes"]['help'],
		'help'=>	$i18n_std["datatypes"]['help_help'],
		'type'=>'textarea',
	),
	'i18n/*/table_gender'=>array(
		'description'=>	$i18n_std["datatypes"]['gender'],
		'help'=>	$i18n_std["datatypes"]['help_gender'],
		'type'=>'list',
		'options'=>array(
			"M"=>$i18n_std["datatypes"]['genders']["M"],
			"F"=>$i18n_std["datatypes"]['genders']["F"]
		)),
	'i18n/*/table_title'=>array(
		'description'=>	$i18n_std["datatypes"]['title'],
		'help'=>	$i18n_std["datatypes"]['help_title'],
	),
	'i18n/*/new_table_title'=>array(
		'description'=>	$i18n_std["datatypes"]['new_title'],
		'help'=>	$i18n_std["datatypes"]['help_new_title'],
	),
	'i18n/*/edit_table_title'=>array(
		'description'=>	$i18n_std["datatypes"]['edit_title'],
		'help'=>	$i18n_std["datatypes"]['help_edit_title'],
	),
	'i18n/*/view_table_title'=>array(
		'description'=>	$i18n_std["datatypes"]['view_title'],
		'help'=>	$i18n_std["datatypes"]['help_view_title'],
	),

	'i18n/*/table_plural'=>array(
		'description'=>	$i18n_std["datatypes"]['plural'],
		'help'=>	$i18n_std["datatypes"]['help_plural'],
		),
	'i18n/*/fields/help_*'=>array(
		'description'=>	$i18n_std["datatypes"]['help'],
		'help'=>	$i18n_std["datatypes"]['help_help'],
		'type'=>"textarea",
		),
	'i18n/*/fields$'=>array(
		'value'=>"<h2>".	$i18n_std["datatypes"]['fields']."</h2>",
		'description'=>	$i18n_std["datatypes"]['help_fields'],
		'help'=>	$i18n_std["datatypes"]['help_fields'],

		),
	);

?>
