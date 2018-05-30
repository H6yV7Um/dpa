<?php
/**
 * 将nginx或apache日志映射到数据库中去，简单实现
 * [以后完善为依据webserver的配置文件进行自动化获取日志格式，自动创建字段并自动检测日志文件存放路径和日志文件名称]
 *
 * 使用方法：

php D:/www/dpa/common/tools/log2db.php
php P:/develope/www/dpa/eswine/common/tools/log2db.php -p D:/www/cgi-bin -f

php /data0/deve/runtime/common/tools/log2db.php -p /data1/logs/2012/ >> /tmp/log2db.txt &

 *
 * 大体步骤是
 * 1. 读取指定日志文件，
 * 2. 逐行正则匹配并写入数据表中去，对于没有完成记录的，则记录到单独的另外的日志文件中去，起名 加 error即可
 *
 * 以后完善为按照月份或者按照天自动创建按照月/天的分表进行统计
 *
 * 数据表设计
 * remote_addr
 * remote_user
 * time_local
 * request
 * status
 * body_bytes_sent
 * http_referer
 * http_user_agent
 * http_x_forwarded_for
 * request_time
 *

CREATE TABLE IF NOT EXISTS `dpps_webserver_log` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `domain` varchar(50) NOT NULL COMMENT '域名',
  `diyu` varchar(50) DEFAULT NULL COMMENT '地域, 客户端所属地域',
  `remote_addr` varchar(15) NOT NULL COMMENT '客户端的ip地址',
  `remote_user` varchar(10) NOT NULL COMMENT '客户端用户名称',
  `time_local_zone` tinyint(4) NOT NULL COMMENT '访问时区',
  `time_local_date` date NOT NULL COMMENT '访问日期',
  `time_local_time` time NOT NULL COMMENT '访问时间',
  `request_method` varchar(8) NOT NULL COMMENT '请求的方法',
  `request_url` varchar(255) NOT NULL COMMENT '请求的url',
  `request_protocal` varchar(10) NOT NULL COMMENT '请求的http协议',
  `status` int(10) NOT NULL COMMENT '请求状态, 成功是200',
  `body_bytes_sent` int(11) NOT NULL COMMENT '文件主体内容大小',
  `http_referer` varchar(255) NOT NULL COMMENT '从哪个页面链接访问过来',
  `http_user_agent` varchar(255) NOT NULL COMMENT '客户端浏览器的相关信息',
  `http_x_forwarded_for` varchar(255) NOT NULL COMMENT '客户端的ip地址',
  `request_time` float(10,5) unsigned NOT NULL COMMENT '请求所耗时间',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  PRIMARY KEY  (`id`),
  KEY `idx_cdt` (`createdate`,`createtime`),
  KEY `remote_addr` (`remote_addr`),
  KEY `time_cdt` (`time_local_zone`,`time_local_date`,`time_local_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='web服务器日志, 例如nginx或apache日志';

 */

require_once( str_replace("\\","/",dirname(dirname(dirname(__FILE__))))."/configs/system.conf.php");
require_once("common/functions.php");
require_once("common/lib/cArray.cls.php");
require_once("common/Files.cls.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");

if ('cli'==php_sapi_name()) {
  $_o = cArray::get_opt($argv, 'f:p:b:');
}else {
  $_o = $_GET;
}

if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))){
  $l_path = "E:/software/green_program/SecureCRT/download/";
  $l_file = "shenghuo_20120704.log";
  $l_back = "D:/tmp/".date("Y");
}else {
  $l_path = "/data1/logs/2012";
  $l_file = "";
  $l_back = "/tmp/".date("Y");
}

$l_path = isset($GLOBALS['_o']["p"]) ? trim($GLOBALS['_o']["p"]) : $l_path;
$l_file = isset($GLOBALS['_o']["f"]) ? trim($GLOBALS['_o']["f"]) : $l_file;
$l_back = isset($GLOBALS['_o']["b"]) ? trim($GLOBALS['_o']["b"]) : $l_back;


main($l_path,$l_file,$l_back);

function main($l_path,$l_file,$l_back){
  // 获取抓取数据库的连接信息
  $l_name0_r = $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R'];
  $dbR = new DBR($l_name0_r);
  $l_err = $dbR->errorInfo();
  if ($l_err[1]>0){
    // 数据库连接失败后
    echo date("Y-m-d H:i:s") . " error request id: ". var_export($l_id,true).", table: $a_grab_tbl , error_msg: " . var_export($l_err,true). " FILE:".__FILE__. NEW_LINE_CHAR;
    return null;
  }
  $dbR->dbo = &DBO($l_name0_r);
  $dbR->table_name = TABLENAME_PREF."project";
  $p_arr_gongyong = $dbR->GetOne("where name_cn='共用数据'");
  $p_arr = $dbR->GetOne("where name_cn='服务器日志'");
  if (PEAR::isError($p_arr) || PEAR::isError($p_arr_gongyong)) {
    echo " error message： " .$p_arr->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
    return null;
  }

  $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr_gongyong);
  $dbR->dbo = &DBO('gongyong', $dsn);
  $dbR->SetCurrentSchema($p_arr_gongyong['db_name']);

  // 连接主库
  $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr);
  $dbW = new DBW($p_arr);
  $l_err = $dbW->errorInfo();
  if ($l_err[1]>0){
    // 数据库连接失败后
    echo date("Y-m-d H:i:s") . " error, ". var_export($l_err,true). " FILE:".__FILE__. NEW_LINE_CHAR;
    return null;
  }
  $dbW->dbo = &DBO('weblog', $dsn);
  $dbW->SetCurrentSchema($p_arr['db_name']);

  if (""==$l_file) {
    // 可能是批量处理
    transPath(&$dbR, &$dbW, $l_path , $l_back, true);
  }else {
    // 处理单个日志
    // 1. 读取本地日志文件
    $l_p_f = rtrim($l_path,"/ ") . "/" . $l_file;
    if (!file_exists($l_p_f)) {
      echo $l_p_f . "error, file not exist!" . "\r\n" ;
      return ;
    }
    tOne(&$dbR, &$dbW, $l_path ,$l_file, $l_back);
  }

}

function transPath(&$dbR, &$dbW, $source_path, $l_back, $son=false){
  // 如果是需要将某个目录下的所有文件转化一下，则需要遍历目录下文件
  $d = dir($source_path);
  if ($d) {
      while (false !== ($_file = $d->read())) {
        if ("."!=substr(ltrim($_file),0,1)) {//  过滤掉 . .. .svn这三项
          if(is_dir($source_path."/".$_file)){
            if($son) transPath(&$dbR, &$dbW, $source_path."/".$_file, $l_back."/".$_file, $son);
          }else {
            tOne(&$dbR, &$dbW, $source_path ,$_file, $l_back);
          }
        }
      }
      $d->close();
  }
}

function tOne(&$dbR, &$dbW, $a_path, $a_file, $l_back, $a_domain=''){
  $l_p_f = rtrim($a_path,"/ ") . "/" . $a_file;

  // 文件必须存在，文件必须是.log文件
  if (!file_exists($l_p_f) || false===strpos($a_file, ".log")) {
    return ;
  }

  if (""==$a_domain) {
    $l_domain = getDomainByFileName($a_file);
  }else {
    $l_domain = $a_domain;
  }

  // 如果文件很大可以采用读取定长字符进行拼装的方式，例如超过内存的2G文件等，当前没有这么大文件，只简单处理
  $l_lines = file($l_p_f);

  // 2. 逐行入库, 匹配不到的记录到另外的日志文件中，插入失败也写到另外的日志文件中
  foreach ($l_lines as $l_num => $l_li) {
    // 进行正则匹配，
    if (preg_match('/([\d.]*) - (\S+) \[([^\[\]]*)\] \"([^\"]*)\" (\d+) (\d+) \"([^\"]*)\" \"([^\"]*)\" (\S+)( \S+)?/',
      $l_li, $l_matches)) {
      // 请求时区、时间需要进行分解
      $l_zdt = getLogZoneDateTime($l_matches[3]);
      $l_mup = getLogMethUrlProto($l_matches[4]);

      if (empty($l_zdt) || empty($l_mup)) {
        echo " some error, ". __FILE__." ".__LINE__ . "\r\n" ;
        return ;
      }

      $data_arr = array(
        "createdate"  => date("Y-m-d"),
        "createtime"  => date("H:i:s"),
        "domain"    => $l_domain,
        "diyu"      => getDiyuByIp($dbR, $l_matches[1]),
        "remote_addr"  => $l_matches[1],
        "remote_user"  => $l_matches[2],
        "time_local_zone"  => $l_zdt["zone"],
        "time_local_date"  => $l_zdt["date"],
        "time_local_time"  => $l_zdt["time"],
        "request_method"  => $l_mup["method"],
        "request_url"    => $l_mup["url"],
        "request_protocal"  => $l_mup["proto"],
        "status"      => $l_matches[5],
        "body_bytes_sent"  => $l_matches[6],
        "http_referer"    => $l_matches[7],
        "http_user_agent"  => $l_matches[8],
        "http_x_forwarded_for"  => $l_matches[9],
      );
      if (isset($l_matches[10])) {
        $data_arr["request_time"] = str_replace(array("'",'"'), "", trim($l_matches[10]));
      }

      // 切换数据库
      $dbW->dbo = &DBO('weblog');
      $l_srv_db_dsn = $dbW->getDSN("array");
      if (!empty($l_srv_db_dsn["database"])) $dbW->SetCurrentSchema($l_srv_db_dsn["database"]);
      $dbW->table_name = TABLENAME_PREF . "webserver_log";
      $dbW->insertOne($data_arr);
      $l_err = $dbW->errorinfo();
      if ($l_err[1]>0){
        // 记录下错误的行，
        echo date("Y-m-d H:i:s") . " ERROR ". var_export($l_err,true). var_export($data_arr,true). " FILE:".__FILE__." ".__LINE__. NEW_LINE_CHAR;
        continue;
      }else{
        // 插入成功, 不处理
        //echo date("Y-m-d H:i:s") . " succ! LINE: " . (1+$l_num) . NEW_LINE_CHAR;
      }
      usleep(1000);
    }else {
      // 没有匹配到, 则将该行记录到日志文件中去。
      echo date("Y-m-d H:i:s") . "error, Cannot Match ". var_export($l_li,true). " FILE:".__FILE__." ".__LINE__. NEW_LINE_CHAR;
      continue;
    }
  }

  // 处理完以后就移动(或删除)该文件
  $files = new Files();
  $files->overwriteContent(file_get_contents($l_p_f), $l_back."/".$a_file);
  if (file_exists($l_back."/".$a_file)) unlink($l_p_f);// 复制成功就删除旧的
}

// 06/Jul/2012:00:00:31 +0800 有年月日和时区的字符串，分解为时区和年月日
function getLogZoneDateTime($a_str){
  $l_rlt = array();
  $l_tmp = explode(" ",$a_str);  // 得到两个部分，并分别进行处理
  $l_zone  = ($l_tmp[1] + 0)/100;  // 时区

  // 分解时间, 由于strptime在windows平台下没有实现, 因此使用正则
  if (preg_match('|([0-9]{2})/(\w+)/([0-9]{4}):([0-9]{2}):([0-9]{2}):([0-9]{2})|', $l_tmp[0], $l_matches)) {
    // 月份, 如果发现这里的月份不是数字的话, 则进行英文月份对照
    $l_month = getMonthBy3($l_matches[2]);
    if (empty($l_month)) {
      return $l_rlt;
    }
    $l_time = mktime($l_matches[4], $l_matches[5], $l_matches[6], $l_month, $l_matches[1], $l_matches[3]);

    $l_rlt["zone"] = $l_zone;
    $l_rlt["date"] = date("Y-m-d", $l_time);
    $l_rlt["time"] = date("H:i:s", $l_time);
  }

  return $l_rlt;
}

function getMonthBy3($a_str){
  $l_str = strtolower($a_str);

  $l_month = array(
    1  => 'Jan',
    2  => 'Feb',
    3  => 'Mar',
    4  => 'Apr',
    5  => 'May',
    6  => 'Jun',
    7  => 'Jul',
    8  => 'Aug',
    9  => 'Sep',
    10  => 'Oct',
    11  => 'Nov',
    12  => 'Dec',
  );
  //
  $l_trans = array_flip($l_month);
  $l_trans = array_change_key_case($l_trans, CASE_LOWER);
  if (array_key_exists($l_str, $l_trans)) {
    return $l_trans[$l_str];
  }else {
    echo "error, ".__FILE__." ".__LINE__ . "\r\n" ;
    return "";
  }
}

// GET /ta/fang/qiuzu HTTP/1.0 分解为请求方法，请求的url和请求协议
function getLogMethUrlProto($a_str){
  $l_rlt = array();

  $l_tmp = explode(" ",$a_str);  // 得到三个部分
  $l_rlt["method"] = $l_tmp[0];
  $l_rlt["url"] = $l_tmp[1];
  $l_rlt["proto"] = $l_tmp[2];

  return $l_rlt;
}

function getDiyuByIp(&$dbR, $a_ip){
  // 从数据库中获取最新ip对应的地域信息，么有的则为空
  $l_ip = ip2long($a_ip);

  $l_str = "";
  $dbR->dbo = &DBO('gongyong');
  $l_srv_db_dsn = $dbR->getDSN("array");
  if (!empty($l_srv_db_dsn["database"])) $dbR->SetCurrentSchema($l_srv_db_dsn["database"]);
  $dbR->table_name = "ip_data";
  $l_row = $dbR->GetOne("where ip_min<=$l_ip and ip_max>=$l_ip ", "country_city, description");
  if (PEAR::isError($l_row)){
    echo "error, --------" . $dbR->getSQL()." ". var_export($l_row,true). " FILE:".__FILE__." ".__LINE__. NEW_LINE_CHAR;
    return $l_str;
  }
  if (!empty($l_row)) {
    $l_str = $l_row["country_city"] . " " . $l_row["description"];
  }
  return $l_str;
}

function getDomainByFileName($a_str){
  // 文件名去掉后缀.log, 去掉日期得到的文件即是
  $a_str = preg_replace("/_?(logs)?(access)?_+\d+/", "", $a_str);
  return str_replace(".log", "", $a_str);
}
