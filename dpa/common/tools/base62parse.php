<?php
require_once("D:/www/dpa/configs/system.conf.php");
require_once("common/functions.php");

require_once 'Console/Getopt.php';

require_once("D:/www/weibo/miniblog/tools/encode/base62Parse.php");

// 获取参数列表
$_options = Console_Getopt::getopt($argv, 'd:m:', array());
$_o = array();
if (!PEAR::isError($_options)) {
  foreach ($_options[0] as $l_v){
    $_o[$l_v[0]] = $l_v[1];
  }
}

$l_method = (!empty($_o["m"])) ? $_o["m"] : "decode";
$l_post_data = (!empty($_o["d"])) ? $_o["d"] : "";
$data_arr = explode(",", $l_post_data);

main($data_arr, $l_method);

function main($data_arr, $l_method){
  if(!empty($data_arr))
  foreach ($data_arr as $str){
    if(!empty($str)) echo proc_one($str,$l_method).NEW_LINE_CHAR;
  }
}

function proc_one($str,$l_method){
  //
  $obj = new base62Parse();
  if("decode"==$l_method) return $obj->decode($str);
  else if("encode"==$l_method) return $obj->encode($str);
  else return false;
}

