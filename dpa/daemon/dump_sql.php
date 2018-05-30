<?php
/**
 * mysqldump命令被禁止使用的时候可以用这个方法
 *
 * 使用方法：
 *   cd /home/chengfeng/cf_tmp/dpa/daemon/;php dump_sql.php -h 192.168.113.165 -P 3306 -u dbppc_r -p dbppc_r4534ab4c66fe403  -d ppc -t call_log > a.txt & 或
 *   php dump_sql.php
 *
 *   php dump_sql.php -h 192.168.112.1   -P 3320 -u off_ppc_sys_w -p saj1203KJDAS > 112_1.txt &
 *    php dump_sql.php -h 192.168.64.4    -P 3306 -u dbdev   -p ganjidev > 64_4.txt &
 *   php dump_sql.php -h 192.168.64.154  -P 3306 -u dbdev   -p ganjidev > 64_154.txt &
 *   php dump_sql.php -h 192.168.113.165 -P 3306 -u dbppc_r -p dbppc_r4534ab4c66fe403 > 113_165.txt &
 *   php dump_sql.php -i 1 -h 192.168.113.230 -P 3306 -u dbppc_r -p dbppc_r4534ab4c66fe403 > 113_230.txt
 *   php dump_sql.php -i 1 -h 192.168.113.159 -P 3306 -u dbppc_w -p dbppc_wc5c54bf4ec39cc7 > 113_159.txt
 *   php dump_sql.php -i 1 -h 192.168.64.120  -P 3306 -u stat -p ievohh7zai3aeTiikae6 -d ganji_stat > 64_120.txt
 *   php dump_sql.php -h 192.168.58.150 -P 3306 -u off_dbmob_r -p Readoygj123ok# > 58_150.txt
 *   php dump_sql.php -h 192.168.58.149 -P 3306 -u off_dbmob -p wapmovgj123ok# -d mobile_stat> 58_149.txt
 *   mysql://off_dbmob_r:Readoygj123ok#@192.168.58.150:3306/mobile_stat
 * ganji_stat=>mysql://stat:ievohh7zai3aeTiikae6@192.168.64.120:3306/ganji_stat
 *
 * mysqldump -h 192.168.58.149 -P 3306 -u off_dbmob -p wapmovgj123ok#  mobile_stat -w" 1 limit 10" > mobile_stat.sql
 *

 * @abstract 默认全部会被转换为utf8编码
 * @author chengfeng@ganji.com
 * @since  2011-07-01
 *
 *
 * error :
 * USING BTREE
 * CONSTRAINT `FK_employee_contract` FOREIGN KEY (`EmployeeId`) REFERENCES `employee_info` (`Id`)

php ~/cf_tmp/dpa/daemon/dump_sql.php -h 10.77.135.214 -P 3316 -u api_manager -p api_manager -d api_manager -t api

php ~/cf_tmp/dpa/daemon/dump_sql.php -h 10.77.135.214 -P 3316 -u api_manager -p api_manager -d api_manager -t api > a.txt &


 */
error_reporting(E_ALL & ~ (E_STRICT | E_NOTICE | E_DEPRECATED)); // PHP5.3兼容问题, PHP5.4严格性
ini_set('date.timezone','Asia/Shanghai');  // 最好设置一下时区，或php.ini设置

ini_set("display_errors", 1);

define("DEFAULT_LIMIT_NUM", 300);
ini_set('memory_limit', -1);

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
  $l_comm = "D:/www/dpa";
}else {
  $l_comm = "/home/chengfeng/cf_tmp/dpa";
}
$l_comm = dirname(__DIR__);

require_once($l_comm."/configs/system.conf.php");
require_once("common/functions.php");
require_once("common/lib/cString.cls.php");
require_once("common/Files.cls.php");
require_once("common/global_func.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");
$G_base_dir = $l_comm."/daemon";      // sql文件存放路径


function get_opt($argv){
  $_o = getopt('h:P:u:p:d:t:w:a:o:n:i:');

  // 用于指定某个数据库甚至是表
  $_arr = array();
  if (!empty($_o["h"])) $_arr["host"]     = $_o["h"];    // 指定的主机
  if (!empty($_o["P"])) $_arr["port"]     = $_o["P"];    // 指定的端口
  if (!empty($_o["u"])) $_arr["user"]     = $_o["u"];    // 指定的用户名
  if (!empty($_o["p"])) $_arr["pass"]     = $_o["p"];    // 指定的密码
  if (!empty($_o["d"])) $_arr["db_name"]     = $_o["d"];    // 指定的数据库名
  if (!empty($_o["t"])) $_arr["table_name"]  = $_o["t"];    // 指定的数据表名
  if (!empty($_o["w"])) $_arr["with_data"]  = $_o["w"];    // 导出的数据是否需要数据, 默认不需要数据，只需要表结构
  if (!empty($_o["o"])) $_arr["data_only"]  = $_o["o"];    // 是否只要数据不要表结构，默认为false，即数据和表结构都需要
  if (($_o["n"]+0)>=1)  $_arr["limit_num"]  = $_o["n"]+0;  // 导出数据的时候默认获取多少条数
  if (!empty($_o["i"])) $_arr["insert2db"]  = $_o["i"];    // 是否insert到数据库中去
  return $_arr;
}
$_o = get_opt($argv);  // 获取参数列表

main($_o);

function main($_arr){
  echo "\r\n". date("Y-m-d H:i:s") . " begin: \r\n";
  $l_hosts = gethostport($_arr);
  //print_r($l_comm);print_r($_arr);exit;

  // 逐个连接，并获取数据表结构及部分数据
  foreach ($l_hosts as $l_host=>$l_v){
    if ("localhost"!==$l_host){
    foreach ($l_v as $l_port=>$dsn){
      if (array_key_exists("insert2db",$_arr)) {
        __gener_conf($GLOBALS['cfg']['INI_CONFIGS_PATH'],$GLOBALS['cfg']['INI_DB_DSN_CONFIGS_FILE'],"dpa","dpa");  // 主要是为写库准备的
        $dbW = new DBW();
        if (! $dbW->isconnectionW){
          // 没连上就跳过此条。
          continue;
        }
      }else {
        $dbW = "";
      }

      $dbR = new DBR("mysql://".$dsn["db_user"].":".$dsn["db_pass"]."@".$dsn["db_host"].":".$dsn["db_port"]."/".$dsn["db_name"]);
      if (! $dbR->isconnectionR){
        // 没连上就跳过此条。
        continue;
      }
      if(!empty($_arr) && array_key_exists("db_name", $_arr)) $l_dbs[0] = $_arr["db_name"];
      else $l_dbs = getAllDB($dbR);

      if (!empty($l_dbs)){
        foreach ($l_dbs as $l_db){
          $dbR->SetCurrentSchema($l_db);
          // 写库中如果没有此数据库，需要创建此数据库
          if (array_key_exists("insert2db",$_arr)) {
            $dbW->create_db($l_db,"utf8");
            $dbW->SetCurrentSchema($l_db);
          }
          proc_one_db($dbR,$dbW,$_arr);
          usleep(200);
        }
      }else {
        echo  date("Y-m-d H:i:s") . " FILE: ".__FILE__." ". " FUNCTION: ".__FUNCTION__." Line: ". __LINE__."\n";
      }

      $dbR="";unset($dbR);
      $dbW="";unset($dbW);
    }
    }
  }

  echo "\r\n". date("Y-m-d H:i:s") . " end__: \r\n";
}

function proc_one_db(&$dbR, &$dbW, $a_arr)
{
  global $G_base_dir;
  //$dbR = new DBR();
  $_arr = $dbR->getDBTbls();
  if(!empty($a_arr) && array_key_exists("table_name", $a_arr)) $_arr = array( array("Name"=>$a_arr["table_name"]));

  $l_arr = getCreateTbl($dbR, $dbW, $_arr, $a_arr);

  $l_str = $l_arr[0];

  $l_num = getCountNum($dbR, $l_arr[1]);
  echo "\r\nDB_NAME ". $dbR->GetCurrentSchema() . ": \r\n";print_r($l_num);

  $l_dsn = $dbR->getDSN("array");
  /*
    [phptype] => mysql
    [dbsyntax] => mysql
    [username] => root
    [password] => db_pass
    [protocol] => tcp
    [hostspec] => localhost
    [port] => 3306
    [socket] =>
    [database] => ct
    [mode] =>
  */

  // 将此语句写入文本文件中
  $Files = new Files();
  $Files->overwriteContent( add_create_db_str($l_str, $l_dsn) ,$G_base_dir."/".$l_dsn["hostspec"]."/".$l_dsn["hostspec"]."_".$l_dsn["port"]."_".$l_dsn["database"]."_sql.txt");

  unset($l_str);  // 节省内存
  unset($l_arr);
}


function getCreateTbl(&$dbR, &$dbW, $_arr, $a_opt){
  $l_with_data = array_key_exists("with_data", $a_opt)? $a_opt["with_data"] : false;
  $l_data_only = array_key_exists("data_only", $a_opt)? $a_opt["data_only"] : false;

  $l_str = "";
  $l_ins = "";
  $l_tab = "";
  if (!empty($_arr)){
    foreach ($_arr as $l_tbl){
      $l_table_name = trim($l_tbl["Name"]);
      if (""!=$l_table_name){
        //
        $dbR->table_name = $l_table_name;
        $l_tmp = $dbR->SHOW_CREATE_TABLE($l_table_name);
        $l_err = $dbR->errorInfo();
        if ($l_err[1]>0){
          // sql有错误，后面的就不用执行了。
          echo "\r\n".  date("Y-m-d H:i:s") . " FILE: ".__FILE__." ". " FUNCTION: ".__FUNCTION__." Line: ". __LINE__."\n" . " _arr:" . var_export($_arr, TRUE);
          continue;
        }

        // 还有另外一种情况,就是 没有Table,而是View
        if (array_key_exists("Create Table", $l_tmp[0])){
          // 建表语句中的字符编码不能这样转码，必须在查询的时候要事先进行 setcharactor????以后完善
          // 通过建表语句重新设置数据库的字符编码, 找到字符编码设置
          if (preg_match("/DEFAULT CHARSET=(\w+)/i",$l_tmp[0]["Create Table"],$l_match)) {
            $dbR->setCharset($l_match[1]);
            //$dbW->setCharset($l_match[1]);  // 入库保持一致性
          }

          $rlt = $dbR->SHOW_CREATE_TABLE($l_table_name);
          if (is_array($rlt) && !empty($rlt[0])){
            $l_tab[] = $rlt[0]["Table"];
            // 每张表的字符编码设置可能不一样甚至同一张表的注释都不一样的编码，最终都要转化成utf8
            $l_sql = fmtCreatetblChar($rlt[0]["Create Table"], "utf8");
            if (!empty($l_sql)) {
              // 获取每个批次的数据条数，例如导出多少条等
              $l_num = ($a_opt["limit_num"]>0) ? $a_opt["limit_num"] : DEFAULT_LIMIT_NUM;

              if (array_key_exists("insert2db",$a_opt)){
                $dbW->exec($l_sql); // 建表语句
                $l_err = $dbW->errorInfo();
                if (1105 == $l_err[1]) {
                  // Too long comment for table
                  $l_t1 = explode("COMMENT=",$l_sql);
                  $l_sql = $l_t1[0];
                  $dbW->exec($l_sql); // 重新执行
                  $l_err = $dbW->errorInfo();
                }
                if ($l_err[1]>0){
                  // sql有错误，后面的就不用执行了。
                  echo "\r\n". date("Y-m-d H:i:s") . " FILE: ".__FILE__." ". " FUNCTION: ".__FUNCTION__." Line: ". __LINE__."\n" . " err:". var_export($l_err, true). " sql:".var_export($dbW->getSQL(), true);
                }

                // 获取数据并插入数据
                getThenInsertData($dbR, $dbW, $rlt[0]["Table"], $l_num);
              }
              $l_str .= $l_sql.";".NEW_LINE_CHAR;

              // 同时生成导出有限条数据
              if ($l_with_data) {
                $l_daochu = daochu($dbR, $rlt[0]["Table"], $l_num);
                //print_r($l_daochu);
                $l_str .= $l_daochu. NEW_LINE_CHAR;
                $l_ins .= $l_daochu. NEW_LINE_CHAR;
              }
            }
          }else {
            echo "\r\n". date("Y-m-d H:i:s") . " " .__FILE__ . " ". __LINE__ ."\r\n";
            print_r($rlt);
          }
        }
      }else {

      }
    }
  }

  if($l_data_only) return array($l_ins,$l_tab);
  else return array($l_str,$l_tab);
}


/**
 *
 * 对建表语句逐项进行分解，对每个字段的中文注释可能存在字符编码的乱码现象
 * @param 建表语句  $a_str
 * @param string 字符编码，暂时只能转化成utf8的编码
 *
 */
function fmtCreatetblChar($a_str, $tar_charact="utf8"){
  // 空的话直接返回空
  if (is_array($a_str) || empty($a_str)){
    return "";
  }

  $l_exp = "";
  if (false !== strpos($a_str, "\r\n")){
    $l_exp = "\r\n";
  }else if (false !== strpos($a_str, "\n")){
    // linux sql中的换行实际上是这个
    $l_exp = "\n";
  }else if (false !== strpos($a_str, "\r")){
    $l_exp = "\r";
  }else {
    echo date("Y-m-d H:i:s") . " FILE: ".__FILE__." ". " FUNCTION: ".__FUNCTION__." Line: ". __LINE__."\n" . " err:". var_export($a_str, true);
    return "";  // 没有分隔的话就直接退出
  }
  $l_tmp = explode($l_exp, $a_str);

  // 进行转码并重新拼接sql
  $l_arr = array();
  foreach ($l_tmp as $l_str){
    if (!is_utf8_encode($l_str)) $l_str = iconv("GBK","UTF-8//IGNORE",$l_str);
    $l_arr[] = $l_str;
  }
  $a_str = implode($l_exp, $l_arr);

  // 字符串替换一下
  return sql_2_want($a_str);
}

function sql_2_want($l_sql){
  // 建表语句添加上IF NOT EXISTS
  if (false!==stripos($l_sql,"CREATE TABLE")) {
    $l_sql = preg_replace('/^CREATE TABLE/i', 'CREATE TABLE IF NOT EXISTS', $l_sql);
  }
  // 去掉自增的数字, 不过要是dump的时候则不应去掉
  if (false!==stripos($l_sql,"AUTO_INCREMENT=")) {
    $l_sql = preg_replace("/AUTO_INCREMENT=\d+/","",$l_sql);
  }
  // 替换字符编码设置
  if (false!==stripos($l_sql,"DEFAULT CHARSET=")) {
    $l_sql = preg_replace("/DEFAULT CHARSET=\w+/i","DEFAULT CHARSET=utf8",$l_sql);
  }
  // 替换InnoDB
  //if (false!==stripos($l_sql,"ENGINE=InnoDB")) $l_sql = str_replace("ENGINE=InnoDB","ENGINE=MyISAM",$l_sql);

  //$l_sql = str_replace("DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", "", $l_sql);

  // 后置的 USING BTREE 本应该提到索引字段前面，不然会报错。此处为了从简，先去掉也能执行。
  if (false!==stripos($l_sql,"USING BTREE")) {
    $l_sql = preg_replace("/USING BTREE/","",$l_sql);
  }
  return $l_sql;
}

//
function getThenInsertData(&$dbR, &$dbW, $l_tbl, $a_num=DEFAULT_LIMIT_NUM){
  // 获取自增长的字段，没有自增长的字段则退出并记录日志。
  $l_dsn = $dbW->getDSN("array");
  $l_dbr = new DBR($l_dsn);
  $l_auto_field = get_PRI_field($l_dbr, $l_tbl);

  $l_dbr->table_name = $l_tbl;
  $l_field_info = cArray::Index2KeyArr($l_dbr->getTblFields(),array("key"=>"Field", "value"=>array(1)));

  //
  if (""!==$l_auto_field){
    // 在现有的数据库中获取到最大的自增id，
    $max_id = $l_dbr->getOne("","max(`".$l_auto_field."`) as max_id");
    $max_id = $max_id["max_id"] + 0;
    if ($max_id<=0){
      $max_id = 0;
    }

    // 去数据源处获取到数据，
    $l_order = " order by `".$l_auto_field."`  ";
    $dbR->table_name = $l_tbl;
    $l_arr = $dbR->getAlls(" where `".$l_auto_field."` > $max_id " .$l_order." limit ". $a_num);//最多获取设定的数值

    // 然后逐一insert到目标数据库，最好是用replace into，不用判断是否存在此记录。
    pinzhuangsql($dbR,$dbW, $l_arr, $l_field_info,$l_tbl, true);
  }else {
    echo "\r\n". date("Y-m-d H:i:s") . " dsn : ". var_export($l_dsn, true).", table: $l_tbl  no_auto_increate\r\n";
  }

  $l_dbr = "";unset($l_dbr);
}

// 获取主键字段名称
function get_PRI_field(&$l_dbr, $l_tbl){
  $l_f = "";
  // 优先使用自增字段，其次看其他pri
  $l_dbr->table_name = $l_tbl;
  $a_fields = $l_dbr->getTblFields ();

  if (! empty ( $a_fields )) {
    foreach ( $a_fields as $l_arr ) {
      if ("auto_increment" == trim ( $l_arr ["Extra"] )) {
        $l_f = trim ( $l_arr ["Field"] );
        break;
      }
    }
  }
  // 没找到则看看全体pri
  if (""==$l_f){
    $l_index = $l_dbr->getTblIndex2();  // 该表结构，主要是获取主键字段
    $l_field = $l_index[2]["PRIMARY"][1]["Column_name"];
    if (!empty($l_field)){
      $l_f = $l_field;
    }
  }

  // 还么有找到就找数据类型为int或bigint的数据.
  if (""==$l_f && ! empty ( $a_fields )){
    foreach ( $a_fields as $l_arr ) {
      $l_type = trim ( $l_arr ["Type"] );
      if (preg_match('/bigint/i',$l_type) || preg_match('/^int/i',$l_type)) {
        $l_f = trim ( $l_arr ["Field"] );
        break;
      }
    }
  }

  // 还么有找到就找数据类型 datetime 的数据.
  if (""==$l_f && ! empty ( $a_fields )){
    foreach ( $a_fields as $l_arr ) {
      $l_type = trim ( $l_arr ["Type"] );
      if (preg_match('/datetime/i',$l_type)) {
        $l_f = trim ( $l_arr ["Field"] );
        break;
      }
    }
  }

  // 还没有找到就人为先指定一个好了。依据dsn进行指定。


  // 还没找到则应该去找数据为整型没有重复。???? 以后完善之，现在就只能返回空了.
  return $l_f;
}


// 导出一定数量的数据，并且以sql的形式输出。
function daochu(&$dbR, $l_tbl, $a_num=DEFAULT_LIMIT_NUM ){
  //
  $dbR->table_name = $l_tbl;
  $l_field_info = cArray::Index2KeyArr($dbR->getTblFields(),array("key"=>"Field", "value"=>array(1)));

  $l_order = "";
  $l_field = get_PRI_field($dbR, $l_tbl);
  if (!empty($l_field)) {
    //echo $l_field.NEW_LINE_CHAR;
    $l_order = " order by `".$l_field."`";
  }

  //
  $l_arr = $dbR->getAlls($l_order." limit ". $a_num);//最多获取设定的数值
  //echo $dbR->getSQL() . NEW_LINE_CHAR;

  $l_rlt = "";
  // 拼装字段字符串，
  if (!empty($l_arr)) {
    $l_rlt = pinzhuangsql($dbR,$dbW=0, $l_arr, $l_field_info, $l_tbl);
  }

  return $l_rlt;
}

function pinzhuangsql(&$dbR,&$dbW, $l_arr, $l_field_info,$l_tbl="", $if_insert=false){
  $l_rlt = "";

  // 拼装字段字符串，
  if (!empty($l_arr)) {
    // 需要进行拼装insert sql INSERT INTO `dpps_project` (`id`, `cn_name`, `type`) VALUES(1, '通用发布系统', 'SYSTEM');
    foreach ($l_arr as $l_data){
      $l_fls = "";
      $l_vls = "";
      foreach ($l_data as $l_k => $l_v){
        // 需要依据字段类型进行排查，对于数据为空的直接过滤掉
        if (""==$l_v) {
          if ("NO" == $l_field_info[$l_k]["Null"] && ""==trim($l_field_info[$l_k]["Default"])) {
            // 如果数值为空
            if (false!==strpos($l_field_info[$l_k]["Type"],"decimal") || false!==strpos($l_field_info[$l_k]["Type"],"int")) {
              $l_fls .= "`".$l_k."`,";
              $l_vls .= 0 . ",";
            }else {
              $l_fls .= "`".$l_k."`,";
              $l_vls .= "'".$l_v."',";  // 本来就是空的
            }
          }
        }else {
          if (!is_utf8_encode($l_v)) {
            // 重新去获取一次.
            //echo iconv("ISO-8859-1","UTF-8//IGNORE",trim($l_v)). NEW_LINE_CHAR;
            $l_v = iconv("GBK","UTF-8//IGNORE",trim($l_v));  // 进行转码
          }
          $l_fls .= cString_SQL::FormatField($l_k) . ",";
          $l_vls .= cString_SQL::FormatValue($l_v, 'string') . ",";
        }
      }
      $sql = "INSERT INTO `".$l_tbl."` (". rtrim($l_fls,",") .") VALUES(". rtrim($l_vls,","). ");".NEW_LINE_CHAR;
      if ($if_insert) {
        $dbW->table_name = $l_tbl;
        $dbW->exec($sql);
        $l_err = ($dbW->errorInfo());
        if ($l_err[1]>0){
          echo date("Y-m-d H:i:s") . " FILE: ".__FILE__." ". " FUNCTION: ".__FUNCTION__." Line: ". __LINE__."\n" . " err:". var_export($l_err, true). " sql:".var_export($dbW->getSQL(), true);
        }
        usleep(200);
      }
      $l_rlt .= $sql;
    }
  }
  return $l_rlt;
}

//
function getCountNum(&$dbR, $a_tbl){
  $l_rlt = array();

  if (empty($a_tbl)) {
    return $l_rlt;
  }

  foreach ($a_tbl as $l_tbl){
    if (""!=$l_tbl) {
      $dbR->table_name = $l_tbl;
      $l_rlt[$l_tbl] = $dbR->getCountNum();
    }
  }
  return $l_rlt;
}

//
function getAllDB(&$dbR){
  $l_no_need_db = array("information_schema", "mysql", "test");
  //$dbR = new DBR();
  $l_rlt = array();
  $l_tmp = $dbR->SHOW_DATABASES();
  foreach ($l_tmp as $l_arr){
    $l_d_n = trim($l_arr["Database"]);
    // 过滤掉一些默认的表
    if (!in_array($l_d_n, $l_no_need_db)) $l_rlt[] = $l_d_n;
  }

  return $l_rlt;
}

function gethostport($a_arr){
  $l_rlt = array();

  if (!empty($a_arr) && array_key_exists("host", $a_arr) && array_key_exists("user", $a_arr)){
    $l_rlt[$a_arr["host"]][$a_arr["port"]] = array("db_host"=>$a_arr["host"],"db_port"=>$a_arr["port"],"db_user"=>$a_arr["user"],"db_pass"=>$a_arr["pass"],"db_name"=>$a_arr["db_name"],"table_name"=>$a_arr["table_name"]);
  }else {

    $configs = __fetch_config($GLOBALS['cfg']['INI_CONFIGS_PATH'],$GLOBALS['cfg']['INI_DB_DSN_CONFIGS_FILE']);

    // 本应该进行主机和端口的排重，有时间再做????
    // print_r($configs);

    if(!empty($configs)){
      foreach ($configs as $l_n=>$l_v){
        $l_host = "";
        if(array_key_exists("dsn",$l_v)){
          $b = parse_url($l_v["dsn"]);
          $l_host = $b["host"];
          $l_port = $b["port"];
          $l_user = $b["user"];
          $l_pass = $b["pass"];
        }else if(array_key_exists("mysql_dsn",$l_v)){
          $b = parse_url($l_v["mysql_dsn"]);
          $l_host = $b["host"];
          $l_port = $b["port"];
          $l_user = $b["user"];
          $l_pass = $b["pass"];
        }else {
          $b = $l_v;

          if(array_key_exists("db_host",$l_v)){
            $l_host = $b["db_host"];
            $l_port = $b["db_port"];
            $l_user = $b["db_user"];
            $l_pass = $b["db_pass"];
          }else if(array_key_exists("mysql_host",$l_v)){
            $l_host = $b["mysql_host"];
            $l_port = $b["mysql_port"];
            $l_user = $b["mysql_user"];
            $l_pass = $b["mysql_pass"];
          }else {
            echo "---------err!";print_r($l_v);
          }
        }
        //
        if (""!=$l_host){
          $l_rlt[$l_host][$l_port] = array("db_host"=>$l_host,"db_port"=>$l_port,"db_user"=>$l_user,"db_pass"=>$l_pass);
        }
      }

      // 主机和端口生成的数组进行逐个连接
    }else {
      echo "empty config!";
    }
  }

  return $l_rlt;
}

// 同一台机器，需要带上host和port以示区分
function add_create_db_str($a_str, $a_dsn, $with_host_port=TRUE){
  // 添加上建表语句， 数据库名称添加了 主机和port的
  if (!empty($a_dsn) && array_key_exists("database", $a_dsn)){
    // 拼装数据库名称类似： ppc_192_168_12_1_3306, 三部分连起来, 并且要去掉一些特殊符号
    if($with_host_port) $l_db_name = "h_". str_replace(array(".",":","/") , "_", $a_dsn["hostspec"]) . "_". $a_dsn["port"]."_".$a_dsn["database"];
    else $l_db_name = $a_dsn["database"];

    $l_str  = '';//"DROP DATABASE IF EXISTS `" .$l_db_name. "`;\r\n";
    $l_str .= "CREATE DATABASE IF NOT EXISTS `" .$l_db_name. "`;\r\n";
    $l_str .= "USE `" .$l_db_name. "`;\r\n\r\n";
    $a_str = $l_str.$a_str;
  }

  return $a_str;
}


