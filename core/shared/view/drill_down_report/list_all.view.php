<?php
global $std_views;
$std_views["drill_down_report"]['list_all']=array (
  'current_action' => 'all',
  'help' => 'list_all',
  'title' => 'list_all',
  'type' => 'table',
  'side_actions' => 
  array (
    'all_edit',
    'all_delete',
    'all_export',
    'all_run',
  ),
  'down_actions' => 
  array (
    'all_delete_selected',
    'all_xls',
    'all_new',
  ),
  'fields' =>'all',
  'view_name' => 'list_all',
);
?>
