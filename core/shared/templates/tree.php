<?php 
$this->shadow_start('round'); 
//p2($tdata) 
?>

<?= $tdata['navigation_link'] ?>
<?= $tdata['record_info'] ?>
<?php foreach($tdata['children'] as $child):?>
<br /><?=$child['name'] ?>
	<?php foreach($child['items'] as $item):?>
	<br /><?=$item ?>
	<?php endforeach; ?>
<?php endforeach; ?>
<?php $this->shadow_end('round'); ?>
