<?php
# autogenerated on:2009-05-15 15:38:07 
# by user:Administrator ID:1, try not to hand edit too much
$std_views['campo_de_agrupamiento']=array (
  'edit_custom' => 
  array (
    'title' => 'edit_table_title',
    'type' => 'record',
    'actions' => 
    array (
      0 => 'all_b2l',
      1 => 'all_edit2',
      2 => 'all_delete',
    ),
    'fields' => 
    array (
      'id' => 
      array (
        'name' => 'id',
        'type' => 'label',
      ),
      'nombre' => 
      array (
        'name' => 'nombre',
      ),
    ),
  ),
  'list_custom' => 
  array (
    'help' => 'list_all',
    'title' => 'table_plural',
    'type' => 'table',
    'side_actions' => 
    array (
      0 => 'all_edit',
      1 => 'all_delete',
    ),
    'down_actions' => 
    array (
      0 => 'all_delete_selected',
      1 => 'all_xls',
      2 => 'all_new',
    ),
    'fields' => 
    array (
      'id' => 
      array (
        'name' => 'id',
        'type' => 'label',
      ),
      'nombre' => 
      array (
        'name' => 'nombre',
      ),
    ),
  ),
);
?>