<? $this->shadow_start('menu'); ?>
<? echo $logo; ?>
<script>
	function ocultar(rolename){
		var hide = document.getElementById('0');
		hide.style.display='none';
	}
</script>
<? foreach($template as $role_name=>$links): ?>
<ul class=default_menu_ul>
	<li class=default_menu_li> 
		<b class='standard_title default_menu_title'>
			<? echo $role_name; ?><br>
		</b>
	<ul class=default_menu_ul>
		<? 	$count=1;
			foreach($links as $link): ?>
			<li class=default_menu_li>
				<? echo $link; ?></li>
				
		<? 
			$count = $count + 1; 
			endforeach; ?>
	</ul>
</ul>
<? endforeach; ?>

<? $this->shadow_end('menu'); ?>


