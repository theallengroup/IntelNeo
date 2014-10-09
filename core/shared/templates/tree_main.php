<?php $this->shadow_start('round'); ?>
<h1 class='form_title'><?= $tdata['title'] ?> </h1>
<?php foreach($tdata['links'] as $link):?>
<br /><?=$link ?>
<?php endforeach; ?>
<?php $this->shadow_end('round'); ?>
