<?php
# autogenerated on:2009-05-18 17:13:27 
# by user:Administrator ID:1, try not to hand edit too much
$std_fields['dimension_field']=array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'label',
  ),
  'drill_down_report_id' => 
  array (
    'name' => 'drill_down_report_id',
    'type' => 'number',
  ),
  'name' => 
  array (
    'name' => 'name',
    'type' => 'list',
    'options' => array(),
    'events'=>array('change'=>'document.getElementById("label").value=this.value.replace(/_/g," ")[0].toUpperCase()+this.value.replace(/_/g," ").substring(1)')
  ),
  'label' => 
  array (
    'name' => 'label',
    'type' => 'text',
  ),
  'chart_type_id' => 
  array (
    'name' => 'chart_type_id',
    'type' => 'number',
  ),
);
?>
