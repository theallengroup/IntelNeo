<?php
	p2($view);
	include_once(STD_LOCATION.'include/std_hierarchy.php');
	$h = new std_hierarchy();
	$result = $h->get_view_contents($view);

?>
