<?php
/**
 *
a_div: \或%

D:/php5210/php D:/www/dpa/common/tools/unescape.php -f "unescape" -s "\u83b7\u53d6\u641c\u7d22\u7ed3\u679c\u5931\u8d25"
D:/php5210/php D:/www/dpa/common/tools/unescape.php -f "escape" -s "我的"


$a = "我的";
echo escape($a, "\u") . "\r\n"; // \u6211\u7684
 */

define("shuang__quot", '__@@_quot;@@__');
if ("WIN" === strtoupper(substr(PHP_OS, 0, 3)))
  require_once("D:/www/dpa/configs/system.conf.php");
else
  require_once("/home/chengfeng1/deve/runtime/configs/system.conf.php");
require_once("common/functions.php");

// 获取参数列表
require_once 'Console/Getopt.php';
$_options = Console_Getopt::getopt($argv, 's:d:f:', array());
$_o = array();
if (!PEAR::isError($_options)) {
  foreach ($_options[0] as $l_v){
    $_o[str_replace(shuang__quot, '"', $l_v[0])] = str_replace(shuang__quot, '"', $l_v[1]);
  }
}

$l_str = (!empty($_o["s"])) ? $_o["s"] : "我的";
$l_func = (!empty($_o["f"])) ? $_o["f"] : "unescape";
$l_div = (!empty($_o["d"])) ? $_o["d"] : "\u";

if ("WIN" === strtoupper(substr(PHP_OS, 0, 3))) {
  $l_str = iconv("GB2312","UTF-8//IGNORE", $l_str); // 命令行下通常是gbk编码，需要转
  echo iconv("UTF-8","GB2312//IGNORE", $l_func($l_str, $l_div)) . "\r\n";
}
else
  echo $l_func($l_str, $l_div) . "\r\n";
