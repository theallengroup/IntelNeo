<div >
<?php
//
foreach($t as $field){ ?>
	<div  >
	<span class=' standard_text form_flabel ' style='font-size:18pt'><?=$field['label']?></span>
	<br />
	<?=$field['input']?>
	</div>
<? } ?>
</div>
