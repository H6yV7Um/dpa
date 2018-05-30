<?php
/**
 * 台股板块或指数列表抓取
 * http://210.5.28.134/cqs/query?encode=cn&type=mem&market=10&symbol=%23003
 *
 */
require_once("configs/system.conf.php");
require_once("common/func.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");
// 抓取和解析用
require_once("HTTP/Request.php");
require_once("simple_html_dom.php");
$_G_cookie_arr   = array();
$_G_timeout   = 30;

// 修改数据库连接信息
__gener_conf(INI_CONFIGS_PATH,"mysql_config.ini","trade_db_w","trade_db");

$_G_url_arr = array(
  "per"=>array("up"=>"http://210.67.12.97/fdc/publics/Top10GainersChange.ashx",
        "down"=>"http://210.67.12.97/fdc/publics/Top10LosersChange.ashx"),
  "vol"=>array("up"=>"http://210.67.12.97/fdc/publics/Top10TradeVolume.ashx"),
  "val"=>array("up"=>"http://210.67.12.97/fdc/publics/Top10TradeValue.ashx"),
);
// 三大法人
$_G_url_sdfr = array(
  "tse"=>array("buy"=>"http://210.67.12.97/fdc/publics/Top10TSEHotBuy.ashx",
         "sell"=>"http://210.67.12.97/fdc/publics/Top10TSEHotSell.ashx"),
  "otc"=>array("buy"=>"http://210.67.12.97/fdc/publics/Top10OTCHotBuy.ashx",
         "sell"=>"http://210.67.12.97/fdc/publics/Top10OTCHotSell.ashx"),
);

$dbR = new DBR();
$dbW = new DBW();
$table_name = TABLENAME_PREF."paihang_stock";
$table_sdfr = TABLENAME_PREF."paihang_sdfr";
$table_name_arr = array("paihang_stock"=>"排行资料","paihang_sdfr"=>"三大法人排行资料");

main($dbR, $dbW, $table_name, $_G_url_arr, $_G_timeout, $_G_cookie_arr);
main($dbR, $dbW, $table_sdfr, $_G_url_sdfr, $_G_timeout, $_G_cookie_arr); // 三大法人

function main(&$dbR, &$dbW, $table_name, $_URL_arr, $_G_timeout, $_G_cookie_arr=array()){
  foreach ($_URL_arr as $f_type=>$l_v_a){
    foreach ($l_v_a as $type=>$l_url){
      // 逐一抓取，然后入库
      echo date("Y-m-d H:i:s"). " " .$l_url."\r\n";
      $content = request_cont($l_url,$_G_timeout,$_G_cookie_arr);

      // 分析页面, 其实是xml
      $l_detail = getdetail($dbR, $dbW, $content,$table_name,$f_type,$type,$l_url);

      usleep(100);
    }
  }
}

function insertrecord(&$dbR, &$dbW, &$l_tick, $tick_tbl_name,$f_type,$type,$l_date_time){
  global $table_name_arr;
  // 获取字段个数
  $l_m_arr = get_simp_elem($l_tick,false);

  // 字段自动入库，自动创建字段, 先修改表结构
  if (TABLENAME_PREF==substr($tick_tbl_name,0,strlen(TABLENAME_PREF))) {
    $l_tkey = substr($tick_tbl_name,strlen(TABLENAME_PREF));
  }else {
    $l_tkey = $tick_tbl_name;
  }
  $l_r_n = autoCreateField($dbR, $dbW, $tick_tbl_name, $l_m_arr,$table_name_arr[$l_tkey]);

  // 修改表结构没有报错
  if (!$l_r_n) {
    // 可以插入数据了
    foreach ($l_m_arr as $l_k => $l_v){
      $data_arr[$l_k] = convCharacter($l_v,true);
    }

    $data_arr["symbol"] = $l_tick->id;
    $data_arr["rank"]   = $l_tick->rank;
    $data_arr["updatedate"] = $l_date_time["date"];
    $data_arr["updatetime"] = $l_date_time["time"];

    $data_arr["f_type"] = $f_type;
    $data_arr["type"] = $type;

    $dbW->table_name = $tick_tbl_name;
  //  inserone($dbW, $data_arr,"f_type='".$data_arr["f_type"]."' and type='".$data_arr["type"]."' and updatedate='".$data_arr["updatedate"]."' and symbol='".$data_arr["symbol"]."' and rank='".$data_arr["rank"]."'");
    updateRec($dbW, $data_arr,"f_type='".$data_arr["f_type"]."' and type='".$data_arr["type"]."' and updatedate='".$data_arr["updatedate"]."' and rank='".$data_arr["rank"]."'");
  }

  unset($l_m_arr);
  unset($data_arr);
}

function get_comp_info(&$dbR, &$dbW, &$l_xml,$tick_tbl_name, $f_type,$type,$a_url,$l_date_time){
  // 获取tick信息,
  foreach ($l_xml->find("Billboard",0)->find("Symbol") as $l_ticks) {
    $l_text = trim($l_ticks->innertext);
    if (!empty($l_text)) {
      insertrecord($dbR, $dbW, $l_ticks,$tick_tbl_name,$f_type,$type,$l_date_time);
      usleep(300);
    }else {
      echo date("Y-m-d H:i:s"). " " ." url $a_url record content empty! \r\n";
    }

    // 清下内存
    $l_ticks->clear();unset($l_ticks);
  }
}

// 获取详细信息
function getdetail(&$dbR,&$dbW,$content,$table_name,$f_type,$type,$a_url){

  if (false!==strpos($content, "<Symbol")) {
    // 对方提供的是utf8编码的
    $l_xml = str_get_html($content);

    // 获取更新日期和时间 2010/01/13
    if (false!==strpos($content, "time=")) {
      $l_time = $l_xml->find("Billboard", 0)->time;
    }else {
      $l_time = "";
    }

    $l_date_time = format_date($l_xml->find("Billboard", 0)->updatedate,$l_time);
    get_comp_info($dbR, $dbW,  $l_xml, $table_name,$f_type,$type,$a_url,$l_date_time);

    // 清下内存
    $l_xml->clear();unset($l_xml);
  }else {
    echo date("Y-m-d H:i:s"). " " ."url: $a_url record empty!"."\r\n";
  }
}

function format_date($a_str,$a_time){
  $l_str = "";
  $rlt_arr = array();

  if (false!==strpos($a_str,"/")) {
    $l_t = explode("/",$a_str);
    $l_y = $l_t[0];
    $l_m = $l_t[1]*1;
    $l_d = $l_t[2]*1;

    $l_str = $l_y."-".str_pad($l_m,2,0,STR_PAD_LEFT)."-".str_pad($l_d,2,0,STR_PAD_LEFT);
    // 上面是日期，而时分秒没有提供，只能用默认0了
    if (strlen($a_time)<5) {
      $a_time = "00:00:00";
    }else if (5==strlen($a_time)) {
      $a_time = $a_time.":00";
    }
    $rlt_arr = array("date"=>$l_str,"time"=>$a_time);
  }

  return $rlt_arr;
}

