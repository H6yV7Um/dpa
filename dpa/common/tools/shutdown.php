<?php
/**
 * php shutdown.php -o restart -s 0
 *
D:/php/php D:/www/dpa/common/tools/shutdown.php -o off -s 60

 */
if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)) )require_once("D:/www/dpa/configs/system.conf.php");
else require_once("/data0/deve/runtime/configs/system.conf.php");
require_once("lang/".$GLOBALS['cfg']['LANG_DEFINE_FILE']);
require_once("common/lib/cArray.cls.php");

if ('cli'==php_sapi_name()) {
  $_o = cArray::get_opt($argv, 's:o:');
}else {
  $_o = $_GET;
}

$l_s = isset($GLOBALS['_o']["s"]) ? trim($GLOBALS['_o']["s"]) : 0;
$l_o = isset($GLOBALS['_o']["o"]) ? trim($GLOBALS['_o']["o"]) : "restart";

if (isset($_o["o"])) {
  require_once("common/lib/Shutdown_computer.cls.php");
  echo "\n".date("Y-m-d H:i:s");
  sleep($l_s);
  echo "\n".date("Y-m-d H:i:s")."\n";
  if ("off"==$_o["o"]) {
    Shutdown_computer::shutdown();
  }else if ("restart"==$_o["o"]) {
    Shutdown_computer::restart_computer();
  }
  exit;
}