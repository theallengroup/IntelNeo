<?php
global $std_views;
$std_views["drill_down_report"]['runit']=array (
  'help' => 'list_all',
  'title' => 'list_all',
  'auto_privilege' => '1',
  'type' => 'table',
  'side_actions' => 
  array (
    0 => 'all_run',
  ),
  'down_actions' => 
  array (
  ),
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'type' => 'label',
    ),
    'name' => 
    array (
      'name' => 'name',
      'type' => 'text',
    ),
    'description' => 
    array (
      'name' => 'description',
      'type' => 'textarea',
    ),
  ),
  'view_name' => 'runit',
);
?>
