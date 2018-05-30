<?php
/**
 * 使用方法：
 * php find.php -p "D:/www/weibo/miniblog_api_check/control/interface/internal"
 * mysql -hs3601i.mars.grid.sina.com.cn -P3601 -uuser_r -pT5Gbv3edC45f user
 * mysql -hs4665i.mars.grid.sina.com.cn -P4665 -ucooperation_r -pDAK*Adfad3k4lzx cooperation
 */
if ("WIN"===strtoupper(substr(PHP_OS, 0, 3))) {
  require_once("D:/www/dpa/configs/system.conf.php");
  $conf_file = "D:/www/config/httpd-vhost.conf";
  $base_dir = "D:/www/weibo/sqls";
}else {
  require_once("/tmp/chengfeng/dpa/configs/system.conf.php");
  $conf_file = "/usr/local/sinasrv2/etc/httpd-vhost.conf";
  $base_dir = "/tmp/chengfeng/weibo/sqls";
}
require_once("common/functions.php");
require_once("common/Files.cls.php");
require_once("common/global_func.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");

main($conf_file,$base_dir);

function main($conf_file,$base_dir)
{
  $l_arr = get_conf($conf_file,$base_dir); // 将配置文件中，按照虚拟主机分离出所有的数据库连接信息

  if (!empty($l_arr))
  {
    from_conf($l_arr,$base_dir);
  }
}

function proc_one($base_dir,$db_arr,$a_dbname,$a_server,$dbw="",$dbr="",$l_file="mysql_config.ini",$l_table="")
{
  $a_type = "R";  // 主从库类型
  // 修改数据库连接信息
  fill_db_server($db_arr);
  //print_r($_SERVER);
  $dbR = new DBR();
  //$dbW = new DBW();
  if($dbR->MysqlR->connectionR){
    /*$l_dbs = $dbR->SHOW_DATABASES();
    //print_r($l_dbs);
    if (!empty($l_dbs)) {
      foreach ($l_dbs as $l_db_a){
        $l_db_ = $l_db_a["Database"];
        $dbR->MysqlR->SetCurrentSchema($l_db_);  // 逐个数据库进行获取表结构
        $_arr = $dbR->getDBTbls();
        print_r($_arr);
        var_dump($dbR);
        break;
      }
    }
    exit;*/

    if (empty($l_table)) {
      // 如果没有指定表名称，则将所有的数据表遍历一遍
      $l_str = "";
      $_arr = $dbR->getDBTbls();
      if (!empty($_arr))
      foreach ($_arr as $l_tbl){
        $rlt = $dbR->SHOW_CREATE_TABLE($l_tbl["Name"]);
        //print_r($rlt);
        $l_sql = $rlt[0]["Create Table"];
        if (!empty($l_sql)) {
          if (false!==strpos($l_sql,"AUTO_INCREMENT=")) {
            $l_sql = preg_replace("/AUTO_INCREMENT=\d+/","",$l_sql);
          }
          $l_str .= $l_sql."\r\n\r\n";
        }
      }
      // echo $l_str;
      // 将此语句写入文本文件中
      $Files = new Files();
      $Files->overwriteContent($l_str,$base_dir."/".$a_server."/".$a_dbname."_sql_".$a_type.".txt");
    }else {
      $dbR->table_name = $l_table;
      $_arr = $dbR->getAlls();
      print_r($_arr);
    }
  }else {
    echo "DB connect error! server: $a_server  db: $a_dbname \n";
    //print_r($db_arr);
  }
}
//
function from_conf($arr,$base_dir,$need_arr=array("server"=>array(),"db"=>array(),"db_type"=>array())){
  $l_arr = array();
  if(!empty($arr)){
    // 需要将数组进一步分解成更细粒度的多维数组
    foreach ($arr as $l_server => $l_arr2){
      //if (!empty($need_arr["server"])) {}
      if ("t.sina.com.cn"==$l_server) {


      foreach ($l_arr2 as $l_dbname => $l_arr3){
        if (!empty($l_arr3)) {
          // 进行连接数据库的操作，并导出建表语句.
          //print_r($l_arr3);
          proc_one($base_dir,$l_arr3,$l_dbname,$l_server);
          usleep(2000);
        }else {
          echo date("Y-m-d H:i:s")." ".$l_server. " ".$l_dbname ." db info no R no W \n";
        }

      }
      }
    }
  }
}

function get_conf($conf_file){
  $l_rlt = array();
  $l_spe_begin = "<VirtualHost";
  $l_spe_end   = "</VirtualHost>";
  if (file_exists($conf_file)) {
    // 进行文件解析处理
    $l_cont = file($conf_file);

    $begin_key = 0;
    foreach($l_cont as $l_key=>$l_val){
      // 获取虚拟主机标记块，表示一个虚拟，然后转入下一行的解析
      if (false!==strpos($l_val,$l_spe_begin)) {
        $begin_key++;
        continue;
      }
      // 获取servername
      if (false!==stripos($l_val,"ServerName")) {
        $t_server = get_t_server($l_val);
        continue;
      }
      if (false!==stripos($l_val,$l_spe_end)) {
        unset($t_server);  // 销毁标记
        $begin_key = 0;    // 重新恢复表示一个块的结束
        continue;
      }

      // 分解每一项, 分离出想要的数据库连接信息
      if (""!=trim($l_val) && $begin_key>0 && isset($t_server)) {
        $l_detail = getDetail($l_val);

        if (!empty($l_detail)) {
          if(!empty($l_detail[4])) $l_rlt[$t_server][$l_detail[2]][str_replace("_","",$l_detail[4])][trim($l_detail[3])] = trim($l_detail[5]);
          else $l_rlt[$t_server][$l_detail[2]]["W"][trim($l_detail[3])] = trim($l_detail[5]);
        }
      }
    }
  }else {

  }

  return $l_rlt;
}

function getDetail($str){
  $l_rlt = array();

  // 快速定位需要的，不需要的就快速跳过
  if (!false==strpos($str, "SINASRV_DB")) {
    // 进行正则匹配
    if(preg_match("/(SINASRV_(DB\w*)_(HOST|PORT|USER|NAME|PASS)(_\w+)?)\s+[\"']?([^\"']+)[\"']?/", $str, $l_rlt)){
      //print_r($l_rlt);exit;
    }
  }else{

  }
  return $l_rlt;
}

function get_t_server($str){
  $type = str_ireplace("ServerName","",$str);
  return trim($type);
}

function fill_db_server($db_arr){
  $_SERVER["SRV_DB_HOST_W"] = $_SERVER["SINASRV_DB6_HOST"] = $db_arr["W"]["HOST"];
  $_SERVER["SRV_DB_PORT_W"] = $_SERVER["SINASRV_DB6_PORT"] = $db_arr["W"]["PORT"];
  $_SERVER["SRV_DB_USER_W"] = $_SERVER["SINASRV_DB6_USER"] = $db_arr["W"]["USER"];
  $_SERVER["SRV_DB_PASS_W"] = $_SERVER["SINASRV_DB6_PASS"] = $db_arr["W"]["PASS"];
  $_SERVER["SRV_DB_NAME_W"] = $_SERVER["SINASRV_DB6_NAME"] = $db_arr["W"]["NAME"];

  $_SERVER["SRV_DB_HOST_R"] = $_SERVER["SINASRV_DB6_HOST_R"] = $db_arr["R"]["HOST"];
  $_SERVER["SRV_DB_PORT_R"] = $_SERVER["SINASRV_DB6_PORT_R"] = $db_arr["R"]["PORT"];
  $_SERVER["SRV_DB_USER_R"] = $_SERVER["SINASRV_DB6_USER_R"] = $db_arr["R"]["USER"];
  $_SERVER["SRV_DB_PASS_R"] = $_SERVER["SINASRV_DB6_PASS_R"] = $db_arr["R"]["PASS"];
  $_SERVER["SRV_DB_NAME_R"] = $_SERVER["SINASRV_DB6_NAME_R"] = $db_arr["R"]["NAME"];
}


