<?php
/**
 * 从批量url（多个用逗号分隔）中，分离出每个url
 * 因为逗号可能是某个url的一部分，因此不能用 explode，而url也可能是 https，ftp，telnet等的混合
 * 因此使用了递归的方法进行，对特征采用正则表达式进行
 *
D:/php5210/php D:/www/dpa/common/tools/digui_substr.php
 */
$a = "http://maps.google.com/maps?q=31.19491,121.58599,http://maps.google.com/maps?q=31";

$l_arr = array();
geturls($l_arr, $a);
function geturls(&$l_arr, $a){
  if (preg_match("|,\s*https?://|i", $a, $matches)) {
    $pos = strpos($a, $matches[0]);
    $l_url = trim( substr($a, 0, $pos) );
    if(!empty($l_url)) $l_arr[] = $l_url;

    $l_lev_str = substr($a, $pos+1);
    geturls($l_arr, $l_lev_str);
  }else {
    $l_arr[] = trim($a);
  }
  return "";
}

print_r($l_arr);

