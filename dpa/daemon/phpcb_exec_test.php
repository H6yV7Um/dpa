<?php
// php D:/www/dpa/daemon/phpcb_exec_test.php 

error_reporting(E_ALL);
$cmd = "E:/develope/php/phpcb-code-beautiful/phpcb1.0.1/phpCB D:/www/dpa_strip_whitespace/main.php";

echo '<pre>';

//$last_line = system($cmd, $retval);
exec($cmd, $last_line, $retval); // 所有的输出行组成的数组
//$last_line = passthru($cmd, $retval);

echo '
</pre>
<hr />Last line of the output: ' . var_export($last_line, true) . '
<hr />Return value: ' . var_export($retval, true);


echo "\r\n\r\n" . $cmd . "\r\n";
var_dump($last_line);
var_dump($retval);

