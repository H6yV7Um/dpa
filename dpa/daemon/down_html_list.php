<?php
/**
 * 抓取网站到本地文件的方法
例如抓取 http://mirrors.163.com/ 到本地
【注】: 超过 400M的请过滤掉，搜本程序的 nexuiz-data-2.5.2-1.el6.noarch.rpm 或者用数组判断

 D:/php5210/php D:/www/dpa/daemon/down_html_list.php -u http://10.77.130.11/pub/centos/6/x86_64/ -h "D:/www/a/aa" > D:/a_cobbler.txt
 D:/php5210/php D:/www/dpa/daemon/down_html_list.php -h "D:/www/a/aa" > D:/a_cobbler.txt

 */
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED); // PHP5.3兼容问题

ini_set("display_errors","On");
ini_set('magic_quotes_runtime', 0);

ini_set('memory_limit', '2000M');

require_once( dirname(dirname(__FILE__)) . '/' . "configs/system.conf.php");
require_once("lang/".$GLOBALS['cfg']['LANG_DEFINE_FILE']);
require_once("common/functions.php");
require_once("common/grab_func.php");
require_once("common/lib/cArray.cls.php");
require_once("common/Files.cls.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");
// 抓取和解析用
require_once("HTTP/Request.php");
require_once("simple_html_dom.php");
$_G_cookie_arr   = array();
$_G_timeout   = 30000;

// 获取参数列表
require_once 'Console/Getopt.php';
$_options = Console_Getopt::getopt($argv, 'u:h:o:', array());
$_o = array();
if (!PEAR::isError($_options)) {
  foreach ($_options[0] as $l_v){
    $_o[$l_v[0]] = $l_v[1];
  }
}

if (!defined('NEW_LINE_CHAR')) define("NEW_LINE_CHAR", "\r\n");

define("GRAB_REQUEST_STATUS_DOING","doing");
define("GRAB_REQUEST_STATUS_DOING_STR","正在抓取中");
define("GRAB_REQUEST_STATUS_COMPLETE", "complete");
define("GRAB_REQUEST_STATUS_COMPLETE_STR", "该抓取任务处理完成");
if (!defined('DEBUG2')) define("DEBUG2", true);

//
$GLOBALS['_o']['COMMON_PATH_HOST'] = isset($_o['h']) ? $_o['h'] : "D:/www/a/aa";
$GLOBALS['_o']['COMMON_PATH_URL']  = isset($_o['u']) ? $_o['u'] : "http://localhost/a/aa";
$GLOBALS['_o']['OVER_WRITE']  = isset($_o['o']) ? $_o['o'] : 0;

// 修改数据库连接信息
__gener_conf($GLOBALS['cfg']['INI_CONFIGS_PATH'], "mysql_config.ini", "zhuaqu", "zhuaqu");

$level_num = 1;
$parent_id = 0;

$dbR = new DBR();
$dbW = new DBW();
$table_name = TABLENAME_PREF . "grab_request";

if (DEBUG2) {
  $old_t = microtime();
  list($ousec, $osec) = explode(" ", $old_t);
  echo "begin time: ".date("Y-m-d H:i:s",$osec) ." | microtime: ".($osec+$ousec).NEW_LINE_CHAR;
}

main($dbR, $dbW, $table_name, $level_num, $parent_id, $_G_timeout, $_G_cookie_arr);

if (DEBUG2) {
  $end_t = microtime();
  list($usec, $sec) = explode(" ", $end_t);
  echo "end__ time: ".date("Y-m-d H:i:s",$sec) ." | microtime: ".($sec+$usec) .' spend: '.($sec+$usec-$osec-$ousec).'s'.NEW_LINE_CHAR.NEW_LINE_CHAR;
}

//
function main(&$dbR, &$dbW, $table_name, $level_num, $parent_id, $_G_timeout, $_G_cookie_arr=array()) {
  if (file_exists("D:/stop_html_list.txt")) exit(" stop to grab!\r\n ");

  // 获取所有的某个级别的链接地址
  $dbR->table_name = $table_name;
  $l_arr = $dbR->getAlls(" where parent_id = $parent_id and levelnum='$level_num' and status_='in' ", "id, url");

  if (!empty($l_arr)) {
    $n_level_num = $level_num + 1;

    foreach ($l_arr as $l_v) {
      //set_status_by_id($dbR, $dbW, $table_name, $l_v["id"], array("status_" => GRAB_REQUEST_STATUS_DOING));

      $l_url = $l_v["url"];
      if (false !== strpos($l_url, 'nexuiz-data-2.5.2-1.el6.noarch.rpm') ||
          false !== strpos($l_url, 'root-doc-5.28.00h-3.el6.noarch.rpm') ||
          false !== strpos($l_url, 'wesnoth-data-1.10.5-1.el6.noarch.rpm')) {
        // 这两个文件太大，暂时不处理。 ini_set('memory_limit', '2000M'); 也没用
        echo date("Y-m-d H:i:s") . " $l_url " . " file too large \r\n";
        continue;
      }
      $l_h_u = array();
      $content = request_cont($l_h_u, $l_url, "", $_G_timeout, $_G_cookie_arr);

      $l_tmp = parse_url($l_url);
      $l_domain = getSimpleDomain($l_tmp["host"]);
      $l_class = getDomain($l_domain);

      inser2text($GLOBALS['_o']['COMMON_PATH_HOST'], $l_tmp['path'], $content);

      require_once( "daemon/grab/" . $l_class . "/list.php");
      $l_func = new $l_class;

      $l_all_a = array();
      if ('/' == substr(rtrim($l_tmp['path']), -1))
        $l_all_a = $l_func->getdetail($dbR, $dbW, $content, $l_url);

//print_r($content);
//print_r($l_all_a);
//exit;

      // 记录到数据表中去
      if (!empty($l_all_a)) {
        foreach ($l_all_a as $l_grab_url) {
          $data_arr = array(
            "levelnum"=>$n_level_num,
            "url"=>$l_grab_url,
            "createdate"=>date("Y-m-d"),
            "createtime"=>date("H:i:s"),
            "parent_id"=>$l_v["id"],
          );
          $dbW->table_name = $table_name;
          inserone($dbW, $data_arr, "url='$l_grab_url'");
          usleep(300);
          unset($l_grab_url);
        }
      }

      set_status_by_id($dbR, $dbW, $table_name, $l_v["id"], array("status_" => GRAB_REQUEST_STATUS_COMPLETE));

      usleep(1000);

      // 同时启动下一轮的抓取,level_num加一即可
      if ('/' == substr(rtrim($l_tmp['path']), -1))
        main($dbR, $dbW, $table_name, $n_level_num, $l_v["id"], $_G_timeout, $_G_cookie_arr);
    }
  } else {
    echo $dbR->getSQL() . " empty sql \r\n";
  }

  return ;
}

// 将整个内容存到本地或数据，暂时就存放成文件，可能涉及到转码问题
function inser2text($root_path, $l_path, $l_cot){
  echo date("Y-m-d H:i:s") . " inser2text " . $root_path . " " . $l_path . " proccess \r\n";
  $Files = new Files();
  $file_name = '';
  if ('/' == substr(rtrim($l_path), -1) ) {
    //$file_name = 'index.html';
    return ; // 如果url路径末尾带有/则不用写入
  }

  // 如果url路径末尾带有/则不用写入
  $q_file_name = rtrim($root_path, '/') . "/" . $l_path . $file_name;
  if ($l_cot) {
    if ($GLOBALS['_o']['OVER_WRITE'] || !file_exists($q_file_name))
      $Files->overwriteContent($l_cot, $q_file_name);
    //else echo $q_file_name . " file_exist \r\n";
  } else echo $l_path . " cot empty \r\n";
  return ;
}

function inserone(&$dbW, $data_arr, $a_exist_c=""){
  // 是否存在
  //$rlt = $dbW->getExistorNot($a_exist_c);

  if($rlt = $dbW->getExistorNot($a_exist_c)){
    echo date("Y-m-d H:i:s"). " exist! id " . $rlt["id"] . " " .$a_exist_c  .NEW_LINE_CHAR;
    if ($rlt["id"]>0) return $rlt["id"];
  } else {
    // 不存在则插入数据库中
    if ($dbW->insertOne($data_arr)) {
      return $dbW->LastID();
    }else {
      echo $dbW->getSQL();
      echo date("Y-m-d H:i:s")." "."insert error!".NEW_LINE_CHAR;
      //print_r($data_arr);
      return false;
    }
  }
  return false;
}

// 一个非常便利的方法
function set_status_by_id(&$dbR, &$dbW, $tbl, $id, $data_arr){
  // 最后将状态设置为doing
  $dbW->table_name = $tbl;
  //$data_arr = array("status_" => $a_status);
  $condition= "id=".$id;
  $dbW->updateOne($data_arr, $condition);
  $l_err = $dbW->errorInfo();
  if ($l_err[1]>0){
    // 数据库连接失败后
    echo date("Y-m-d H:i:s") ." FILE:".__FILE__. " LINE:".__LINE__. " error updateOne id: ". var_export($id,true).", table: $tbl , error_msg: " . var_export($l_err,true). " SQL:". $dbW->getSQL() . NEW_LINE_CHAR;
    return null;
  }
}

function getDomain($str) {
  if (preg_match("/^(\d+\.\d+\.\d+\.\d+)(:\d+)?/", $str, $matches)) $str = "URL_" . $str;
  return str_replace(".", "_", $str);
}