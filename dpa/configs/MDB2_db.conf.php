<?php
/**
 * 数据库配置信息，涉及到主从库、分表
 * 主从库信息需要在apache中配置  ,以后也可以不用包含此文件,但php命令行模式下运行的时候需要此文件
 * 为了适合cli模式，单纯在apache中设置没有用
 *
 * 数据库连接信息常量分别为 mysql,redis,memcache
 * SRV_DB_DSN_W, SRV_REDIS_DSN_W, SRV_MEMCACHE_DSN_W
 * SRV_DB_DSN_R, SRV_REDIS_DSN_R, SRV_MEMCACHE_DSN_R
 *
 */
function __gener_conf($conf_path,$conf_file,$db_w="",$db_r="",$a_serv_k_v=array("SRV_DB_DSN_W","SRV_DB_DSN_R")){
  global $_SERVER;
  $configs = __fetch_config($conf_path,$conf_file);

  if (isset($_GET['debug']) && $_GET['debug']) {
      print_r($conf_path);print_r($conf_file);echo " - $db_w - ";echo " - $db_r - ";
      var_dump($configs);
      echo "!!!!!!!!!!!!!!!\r\n\r\n";
      //exit;
  }

  if (empty($configs)) {
    if (!isset($_SERVER[$a_serv_k_v[0]]))
    {
      exit(__FILE__ . " LINE:" . __LINE__ . " " . $db_w . " ". $db_r.  " not in _SERVER! ");
    }
  }else {
    if ((""!=$db_w && !array_key_exists($db_w,$configs)) || (""!=$db_r && !array_key_exists($db_r,$configs))) {
      // 需要输出信息，或者退出程序并报错。
      exit(__FILE__ . " LINE:" . __LINE__ . " " . $db_w . " ". $db_r.  " not in configs! ");
    }
    // write
    if(""!=$db_w) __server_db_dsn($_SERVER,$configs[$db_w], $a_serv_k_v[0]);
    // read
    if(""!=$db_r) __server_db_dsn($_SERVER,$configs[$db_r], $a_serv_k_v[1]);
  }
}

function __server_db_dsn(&$l_srv,$a_arr,$l_k="SRV_DB_DSN_W"){
  // write
  if(array_key_exists("dsn",$a_arr)){
    $l_srv[$l_k] = $a_arr["dsn"];
  }else if(array_key_exists("mysql_dsn",$a_arr)){
    // 兼容旧版
    $l_srv[$l_k] = $a_arr["mysql_dsn"];
  }else {
    $l_srv[$l_k] = $a_arr;
  }
}
__gener_conf($GLOBALS['cfg']['INI_CONFIGS_PATH'],$GLOBALS['cfg']['INI_DB_DSN_CONFIGS_FILE'],$GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_W'],$GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R']);
