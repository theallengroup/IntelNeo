<?php
$result = array( 0, 17, 0, 0, 33, 0, 0, 0, 0, 50);
$total = array_reduce( $result, "sumCalc", 0);
function sumCalc( $a, $b){
return $a + $b;
}
print_r($total);s
?>
