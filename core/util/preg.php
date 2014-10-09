<?php

$txt='


<a href="algo.html"> bla</a>
<a href="algo1.html"> bla1</a>
<a href="algo2.html"> bla2</a>
<img src="mine.gif">

';

echo('<xmp>');
$p=preg_match_all('/<a href="([^"]+)">([^<]+)<\/a>/',$txt,$a);
print_r($a);
$p=preg_match_all('/<img src="([^"]+)"/',$txt,$a);
print_r($a);
echo('</xmp>');
