<?php
// D:/php5210/php D:/www/dpa/daemon/tools/memcache_view.php

function get_opt($argv,$a_para_short='h:P:u:p:d:t:w:a:o:n:i:',$a_para_long=array()){
  require_once 'Console/Getopt.php';
  $_options = Console_Getopt::getopt($argv, $a_para_short, $a_para_long);
  $_o = array();
  if (!PEAR::isError($_options)) {
    foreach ($_options[0] as $l_v){
      $_o[$l_v[0]] = $l_v[1];
    }
  }

  return $_o;
}
$_o = get_opt($argv, 'k:p:d:');  // 获取参数列表

main($_o);

function main($_arr){
  echo date("Y-m-d H:i:s") . " begin: \r\n";
  $memcache = new Memcache;
  $memcache->connect('localhost', isset($_arr['p'])?$_arr['p']:11211) or die ("Could not connect");

  $l_key = isset($_arr['k'])?$_arr['k']:'_lanmu_publish_';
  $get_result = $memcache->get($l_key);
  echo "memcache key: $l_key, value: \r\n";
  if (empty($get_result)) {
    var_dump($get_result);
  }else
  print_r($get_result);

  if (isset($_arr['d'])&& 1==isset($_arr['d'])) {
    $memcache->delete($l_key);
  }
  return ;
}
