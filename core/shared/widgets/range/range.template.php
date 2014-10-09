<?php 
//p2($t->fields);
foreach($t->fields as $w): 
?>
	<br/><?=$w->get_label() ?>
	<?=$w->user_interface() ?>

<? endforeach; ?>

