<?php 
$fs = $t->get_fs();
foreach($fs['options'] as $op_value=>$op_text): 
?>
	<input type=checkbox name="<?=$t->get_name() ?>" value="<?=$op_value ?>" />
	<label for="<?=$t->get_name() ?>"><?=$op_text ?></label> <br />
<? endforeach; ?>

