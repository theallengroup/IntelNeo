<div class=system_menu align=left>
<? $this->shadow_start('menu'); ?>
<? echo $logo; ?>
<? foreach($template as $role_name=>$links): ?>
<ul class=default_menu_ul>
	<li class=default_menu_li> <b class='standard_title default_menu_title'><? echo $role_name; ?><br></b>
	<ul class=default_menu_ul>
		<? foreach($links as $link): ?>
			<li class=default_menu_li><? echo $link; ?>

		<? endforeach; ?>
	</ul>
</ul>
<? endforeach; ?>

<? $this->shadow_end('menu'); ?>
</div>
