<?php
class foo {

  function __set($name,$val) {
    print("Hello, you tried to put $val in $name");
  }

  function __get($name) {
    print("Hey you asked for $name");
  }
}

$x = new foo();
$x->bar = 3;
print($x->winky_winky);
?>
