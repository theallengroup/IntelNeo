<?php
include("ui2.php");
include("data/file_provider.php");
$ui2 = new ui2();
$dp = new file_provider();
$ui2->set_data_provider_object($dp);
$ui2->run();
?>
