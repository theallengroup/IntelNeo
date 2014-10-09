<?php
function time_elapsed_string($datetime, $full = false) {
	$now = new DateTime();
	$ago = new DateTime($datetime);
	$diff = $now->diff($ago);

	#$diff->w = floor($diff->d / 7);
	#$diff->d -= $diff->w * 7;

	$string = array(
		'y' => 'year',
		'm' => 'month',
		#'w' => 'week',
		'd' => 'day',
		'h' => 'hour',
		'i' => 'minute',
		's' => 'second',
	);
	foreach ($string as $k => &$v) {
		if ($diff->$k) {
			$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
		} else {
			unset($string[$k]);
		}
	}

	if (!$full) $string = array_slice($string, 0, 1);
	return $string ? implode(', ', $string) . ' ago' : 'just now';
}
date_default_timezone_set('America/Los_Angeles');
echo(time_elapsed_string('2014-08-26 01:37:06'));

?>
