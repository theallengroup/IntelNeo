<?=$this->get_shadow_start('round');?>
<h1 class='form_title standard_title'><?=$this->i18n('report_list')?></h1>
<? foreach($t as $r=>$link): ?>
<br><?=$link?>
<? endforeach; ?>
<br><br><?=$new_report_link?>
<?=$this->get_shadow_end('round');?>
