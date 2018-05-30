<?php
/**
 * 获取所有大盘的 行情数据.自动加字段
 * http://210.5.28.134/cqs/query?encode=cn&type=memtick&market=10&symbol=%23001
 */
require_once("configs/system.conf.php");
require_once("common/func.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");
// 抓取和解析用
require_once("HTTP/Request.php");
//require_once("simple_html_dom.php");
$_G_cookie_arr   = array();
$_G_timeout   = 30;

// 修改数据库连接信息
__gener_conf(INI_CONFIGS_PATH,"mysql_config.ini","trade_db_w","trade_db");

define("COMM_URL", "http://210.5.28.134/cqs/query?encode=cn&type=mem&market=10&symbol=%23");

$dbR = new DBR();
$dbW = new DBW();
$table_name = TABLENAME_PREF."futures_mem";
$table_hq = TABLENAME_PREF."futures_mem";
$table_name_arr = array("futures_mem"=>"期货行情分时历史记录","futures_hq"=>"期货行情");

// 由于需要将当前抓取的行情更新到大盘行情表中去, 所以需要将旧行情数据获取到，切总共只有64不超过100条，因此一次性取出
$dbR->table_name = $table_hq;
$old_hq_arr = $dbR->getAlls();
// 获取 symbol 的中文名称
$dbR->table_name = TABLENAME_PREF."futures";
$t_name_arr = $dbR->getAlls("","id,name_cn");
$hq_name_arr = get_hq_name_arr($t_name_arr);

main($dbR, $dbW, $table_name, $_G_timeout, $_G_cookie_arr);

//
function main(&$dbR, &$dbW, $tbl_name, $_G_timeout,$_G_cookie_arr=array()){
  global $table_name_arr;
  $t_name_cn = getTblNameCN($tbl_name,$table_name_arr);

  // 获取所有的 指数和分类
  $dbR->table_name = TABLENAME_PREF."futures";
  $sect_arr = $dbR->getAlls(" order by id");
  $uni_arr = array("symbol","trade_day","time");

  // 逐一解析
  foreach ($sect_arr as $l_sect){
    $l_url = COMM_URL.str_pad($l_sect["id"],3,0,STR_PAD_LEFT);
    //echo date("Y-m-d H:i:s")." ".$l_url. NEW_LINE_CHAR;

    // 对方提供的股票列表中包含有板块分类的数据
    $content = request_cont($l_url,$_G_timeout,$_G_cookie_arr);
    getdetail($dbR, $dbW, $content, $uni_arr,$l_sect["id"],$tbl_name,$l_url,$t_name_cn);
    usleep(300);
  }
}

// 获取详细信息
function getdetail(&$dbR,&$dbW,$content,$uni_arr,$a_id,$table_name,$a_url,$t_name_cn,$add_field=array()){
  // 对方提供的是utf8编码的, 采用simplexml解析比较快
  $l_xml = new SimpleXMLElement($content);
  $trade_day = getDateTime(strval($l_xml->symbol[0]->trade_day),"date");

  // 获取mem信息
  foreach ($l_xml->symbol[0]->mem as $l_mem) {
    if (!empty($l_mem)) {
      insertmem($dbR, $dbW, $l_mem,$table_name,$uni_arr,$a_id,$t_name_cn,$trade_day,$add_field);
      usleep(300);
    }
    // 清下内存
    unset($l_mem);
  }

  // 清下内存
  unset($l_xml);
}



function insertmem(&$dbR, &$dbW, &$l_mem, $mem_tbl_name,$unique_arr,$a_id,$table_name_cn,$trade_day,$add_field){
  global $table_hq;
  global $hq_name_arr;
  global $table_name_arr;
  $name_cn = $hq_name_arr[$a_id];

  // 字段自动入库，自动创建字段
  $l_m_arr = get_elem($l_mem);

  // 外部额外字段
  if (!empty($add_field)) {
    foreach ($add_field as $l_f=>$l_v){
      $l_m_arr[strtolower($l_f)] = $l_v;
    }
  }

  // 先修改表结构
  $l_r_n = autoCreateField($dbR, $dbW, $mem_tbl_name, $l_m_arr,$table_name_cn);
  // 行情表的也同时修改下
  //$l_r_q = autoCreateField($dbR, $dbW, $table_hq, array_merge($l_m_arr,array("name_cn"=>$name_cn)),getTblNameCN($table_hq,$table_name_arr));

  if (!$l_r_n ) {
    // 可以插入数据了
    $data_arr = $l_m_arr;

    // 防止空数据进入
    if (!key_exists("time",$data_arr)) {
      $data_arr["time"] = getTimeval("");
    }else {
      $data_arr["time"] = getTimeval($data_arr["time"]);  // 时间格式133100=>13:31:00
    }

    if ($data_arr["preclose"]>0 && $data_arr["pri"]>0) {
      $data_arr["chgpct"] =  round(($data_arr["pri"]-$data_arr["preclose"])*100/$data_arr["preclose"], 2);
    }else {
      // 设为空
      //$data_arr["chgpct"] = NULL;
    }

    $data_arr["symbol"] = $a_id;
    $data_arr["trade_day"] = $trade_day;

    /*
    注释掉写入分时表
    $dbW->table_name = $mem_tbl_name;
    inserone($dbW, $data_arr,$unique_arr);*/

    // 更新或插入到行情表中去, 唯一性条件就看symbol存在否 !!!!多一个中文名称!!
    $dbW->table_name = $table_hq;
    inser_update_hq($dbW, array_merge($data_arr,array("name_cn"=>$name_cn)),"symbol=".$data_arr["symbol"]);
  }else {
    echo date("Y-m-d H:i:s"). " autoCreateField error! "  .NEW_LINE_CHAR;
  }

  unset($l_m_arr);
  unset($data_arr);
}

function inser_update_hq(&$dbW, $data_arr,$a_exist_a){
  // 是否存在,拼装唯一性条件
  if($rlt = $dbW->getExistorNot($a_exist_a)){
    // 存在则更新, 不过也可以去掉对名称的不必要的频繁的更新 $data_arr["name_cn"]
    if ($dbW->updateOne($data_arr,$a_exist_a)) {
      echo date("Y-m-d H:i:s")." "."update succ!".NEW_LINE_CHAR;
    }else {
      echo $dbW->getSQL();
      echo date("Y-m-d H:i:s")." "."update error!".NEW_LINE_CHAR;
      //print_r($data_arr);
      return false;
    }
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

//
function getTimeval($a_str=""){
  $l_str = str_pad($a_str, 6, 0, STR_PAD_LEFT);
  return substr($l_str,0,2).":".substr($l_str,2,2).":".substr($l_str,4,2);
}

function get_hq_name_arr($a_arr){
  $arr = array();
  if (!empty($a_arr)) {
    foreach ($a_arr as $l_v){
      $arr[$l_v["id"]] = $l_v["name_cn"];
    }
  }
  return $arr;
}