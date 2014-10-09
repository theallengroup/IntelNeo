<?php
global $std_views;
$std_views["informe"]['list_all']=array (
  'current_action' => 'all',
  'help' => 'list_all',
  'title' => 'list_all',
  'type' => 'table',
  'side_actions' => 
  array (
    0 => 'all_edit',
    1 => 'all_run',
    2 => 'all_xml_export',
    3 => 'all_delete',
  ),
  'down_actions' => 
  array (
    0 => 'all_delete_selected',
    1 => 'all_xls',
    2 => 'all_new',
  ),
  'fields' =>'all', 
  'view_name' => 'list_all',
);
?>
