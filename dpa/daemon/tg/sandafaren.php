<?php
/**
 * 公司资料
 * http://210.67.12.97/fdc/StockFA.aspx?symid=2330&pagecode=cor01
 * http://210.67.12.98/FA/GetTWStockFA.aspx?usp=CompanyBasic&symbol=2330
 *
 * http://210.67.12.97/fdc/StockFA.aspx?sm=1&pagecode=mkt01
 *
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

define("COMM_URL", "http://210.67.12.97/fdc/publics/3itrdsum.ashx?mkt=");

$table_name_arr = array(
  "sandafaren"=> array(
    "t_name_cn"=>"三大法人",
    "f_uni"=>array("mkt","up_date","iname")),
);
$mkt_arr = array("tse","otc");

// 获取参数列表
require_once 'Console/Getopt.php';
$_options = Console_Getopt::getopt($argv, 'm:t:', array());
$_o = array();
if (!PEAR::isError($_options)) {
  foreach ($_options[0] as $l_v){
    $_o[$l_v[0]] = $l_v[1];
  }
}

$mkt = (!empty($_o["m"])) ? $_o["m"] : "";
$t_arr = (!empty($_o["t"])) ? array($_o["t"]=>$table_name_arr[$_o["t"]]) : $table_name_arr;

$dbR = new DBR();
$dbW = new DBW();

if (!empty($mkt)) {
  procOnesymbol($dbR, $dbW, $t_arr, $mkt, $_G_timeout,$_G_cookie_arr);
}else {
  main($dbR, $dbW, $t_arr, $_G_timeout, $_G_cookie_arr);
}

function main(&$dbR, &$dbW, $table_name_arr, $_G_timeout,$_G_cookie_arr=array() ){
  // 获取所有的参数
  global $mkt_arr;

  // 逐一解析
  foreach ($mkt_arr as $l_m){
    procOnesymbol($dbR, $dbW, $table_name_arr, $l_m, $_G_timeout,$_G_cookie_arr);
  }
}

//
function procOnesymbol(&$dbR, &$dbW, $table_name_arr, $sym_or_secid, $_G_timeout,$_G_cookie_arr=array()){
  // 表名的循环
  if (!empty($table_name_arr)) {
    foreach ($table_name_arr as $tbl_name=>$tbl_desc){
      $l_f_a = array();
      $l_f_o_str = "";
      $t_name_cn = $tbl_desc["t_name_cn"];
      $uni_arr = array();
      if (key_exists("f_uni",$tbl_desc)) {
        $uni_arr = $tbl_desc["f_uni"];
      }

      if (key_exists("para",$tbl_desc)) {
        // ----- jian  info.php
      }else {
        $l_url = COMM_URL.$sym_or_secid;
        echo date("Y-m-d H:i:s"). " " .$l_url."\r\n";
        // table_name 要添加前缀
        proc_grab($dbR, $dbW, $uni_arr,$sym_or_secid, TABLENAME_PREF.$tbl_name,$l_url,$t_name_cn,array(),$_G_timeout,$_G_cookie_arr);
        usleep(300);
      }
    }
  }
}

function proc_grab($dbR, $dbW, $uni_arr,$a_id, $tbl_name,$l_url,$t_name_cn,$add_field,$_G_timeout,$_G_cookie_arr){
  // 对方提供的股票列表中包含有板块分类的数据
  $content = request_cont($l_url,$_G_timeout,$_G_cookie_arr);
  getdetail($dbR, $dbW, $content, $uni_arr,$a_id,$tbl_name,$l_url,$t_name_cn,$add_field);
}

function get_spe_elem(&$a_obj){
  // 循环出所有的字段
  $l_a = array();

  // 一定需要先转码，再替换
  $l_a[strtolower("IName")] = str_replace("　","",convCharacter(trim($a_obj->IName),true));
  $l_a[strtolower("v1")] = trim($a_obj->v1);
  $l_a[strtolower("v2")] = trim($a_obj->v2);
  $l_a[strtolower("v3")] = trim($a_obj->v3);

  return $l_a;
}

//
function insertrecord(&$dbR, &$dbW, $a_v_arr, $tick_tbl_name,$unique_arr,$a_id,$t_name_cn,$add_field){
  // 获取字段个数
  $l_m_arr = $a_v_arr;

  // 外部额外字段
  if (!empty($add_field)) {
    foreach ($add_field as $l_f=>$l_v){
      $l_m_arr[strtolower($l_f)] = $l_v;
    }
  }

  // 字段自动入库，自动创建字段, 先修改表结构
  $l_r_n = autoCreateField($dbR, $dbW, $tick_tbl_name, $l_m_arr,$t_name_cn);

  // 修改表结构没有报错
  if (!$l_r_n) {
    // 可以插入数据了
    foreach ($l_m_arr as $l_k => $l_v){
      $data_arr[$l_k] = convCharacter($l_v,true);
    }
    $data_arr["mkt"] = $a_id;
    $dbW->table_name = $tick_tbl_name;
    inserone($dbW, $data_arr,$unique_arr); // 唯一性条件可以先不用给出
  }

  unset($l_m_arr);
  unset($data_arr);
}

function get_comp_info(&$dbR, &$dbW, &$l_xml,$tick_tbl_name, $uni_arr,$a_id,$a_url,$t_name_cn,$add_field,$l_com){
  // 获取tick信息
  foreach ($l_xml->item as $l_ticks) {
    $l_text = trim($l_ticks->IName);
    $l_a = $l_com;
    if (!empty($l_text)) {
      // 一定需要先转码，再替换
      $l_a["iname"] = str_replace("　","",convCharacter($l_text,true));
      /*$v1 = convCharacter(trim($l_ticks->v1["description"]),true);
      $v2 = convCharacter(trim($l_ticks->v2["description"]),true);
      $v3 = convCharacter(trim($l_ticks->v3["description"]),true);
      */

      $l_a["v1"] = trim($l_ticks->v1);
      $l_a["v2"] = trim($l_ticks->v2);
      $l_a["v3"] = trim($l_ticks->v3);

      insertrecord($dbR, $dbW, $l_a,$tick_tbl_name,$uni_arr,$a_id,$t_name_cn,$add_field);
      usleep(300);

    }else {
      echo date("Y-m-d H:i:s"). " " ."a_id $a_id url $a_url record content empty! \r\n";
    }

    // 清下内存
    unset($l_ticks);
  }
}

// 获取详细信息
function getdetail(&$dbR,&$dbW,$content,$uni_arr,$a_id,$table_name,$a_url,$t_name_cn,$add_field){
  if (false!==strpos($content, "<tbl")) {
    // 替换掉不匹配的a标签
    if (false!==strpos($content, "<a") && false===strpos($content, "</a")) {
      $content = str_replace(array("<a","<A"),"",$content);
    }
    $l_com = array();

    // 对方提供的是utf8编码的
    $l_xml = new SimpleXMLElement($content);
    // 先获取共用的数据
    $l_com["updatetime"]= trim($l_xml->updateTime);
    $l_dt_a = exp_date_time($l_com["updatetime"]);

    $l_com["up_date"]  = $l_dt_a["date"];
    $l_com["up_time"]  = $l_dt_a["time"];
    $l_com["unit"]     = trim($l_xml->unit);
    $l_com["datadate"]   = trim($l_xml->datadate);

    get_comp_info($dbR, $dbW,  $l_xml, $table_name, $uni_arr,$a_id,$a_url,$t_name_cn,$add_field,$l_com);

    // 清下内存
    unset($l_xml);
  }else {
    echo date("Y-m-d H:i:s"). " " ."a_id: $a_id url: $a_url record empty!"."\r\n";
  }
}

function exp_date_time($a_str){
  $l_t = explode(" ",$a_str);
  return array("date"=>getDateTime(str_replace("/","",$l_t[0]),"date"),"time"=>trim($l_t[1]));
}
