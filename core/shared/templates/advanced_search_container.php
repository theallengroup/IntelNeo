<?php 
//p2($t['hidden_fields']); 
?>
<?= $t['head'] ?>
<?= $t['form_content'] ?>
<?= $t['buttons'] ?>
<? foreach($t['hidden_fields'] as $h): ?>
<?=$h ?>

<? endforeach ;?>
<?= $t['foot'] ?>
