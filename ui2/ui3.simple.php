<?php
include("ui2.php");
include("data/simple_provider.php");
$ui2 = new ui2();
$dp = new simple_provider();
$ui2->set_data_provider_object($dp);
$ui2->run();
?>
