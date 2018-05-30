<?php
/**
 * 获取行业分类, 产业与概念中心
 * http://210.67.12.98/FA/getCategoryTSE.aspx
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

$_URL_arr = array(
  "tse"=>"http://210.67.12.98/FA/getCategorySubSymbolTSE.aspx?code=",
  "otc"=>"http://210.67.12.98/FA/getCategorySubSymbolOTC.aspx?code=",
  "concept"=>"http://210.67.12.98/FA/getCategorySubSymbolConcept.aspx?code=",
  "group"  =>"http://210.67.12.98/FA/getCategorySubSymbolGroup.aspx?code=",
);

$dbR = new DBR();
$dbW = new DBW();
$table_name = TABLENAME_PREF."categorysymbol";
$table_name_arr = array("categorysymbol"=>"分类相关个股表");

main($dbR, $dbW, $table_name, $_URL_arr, $_G_timeout, $_G_cookie_arr);

//
function main(&$dbR, &$dbW, $sector_def, $_URL_arr, $_G_timeout,$_G_cookie_arr=array()){
  foreach ($_URL_arr as $l_market=>$l_url){
    // 再多一重获取一级分类列表的循环
    // 获取所有的 指数和分类
    $dbR->table_name = TABLENAME_PREF."category";  // 获取亚分类列表
    $sect_arr = $dbR->getAlls(" where levelnum>1 and market='$l_market'  order by id");

    // 逐一解析
    foreach ($sect_arr as $l_sect){
      $sys_indu_code = $l_sect["sys_indu_code"];
      $n_url = $l_url.$sys_indu_code;// 需要多加一个id

      echo date("Y-m-d H:i:s"). " ". $n_url."\r\n";
      $content = request_cont($n_url,$_G_timeout,$_G_cookie_arr);
      $l_detail = getdetail($dbR, $dbW, $content,$sector_def,$sys_indu_code,$l_market,$n_url);
      usleep(300);
    }
  }
}

function insertrecord(&$dbR, &$dbW, &$l_tick, $tick_tbl_name,$level_num, $l_market){
  global $table_name_arr;
  // 获取字段个数
  $l_m_arr = get_simp_elem($l_tick,false);

  // 字段自动入库，自动创建字段, 先修改表结构
  $l_r_n = autoCreateField($dbR, $dbW, $tick_tbl_name, $l_m_arr,$table_name_arr);

  // 修改表结构没有报错
  if (!$l_r_n) {
    // 可以插入数据了
    foreach ($l_m_arr as $l_k => $l_v){
      $data_arr[$l_k] = convCharacter($l_v,true);
    }

    $data_arr["sys_indu_code"]   = $level_num;
    $data_arr["market_e"]   = $l_market;

    $dbW->table_name = $tick_tbl_name;
    inserone($dbW, $data_arr,"market_e='".$data_arr["market_e"]."' and list_code='".$data_arr["list_code"]."' and sys_indu_code='".$data_arr["sys_indu_code"]."'"); // 唯一性条件可以先不用给出
  }

  unset($l_m_arr);
  unset($data_arr);
}

function get_comp_info(&$dbR, &$dbW, &$l_xml,$tick_tbl_name,$level_num, $l_market,$a_url){
  // 获取信息,
  foreach ($l_xml->find("FA",0)->find("Record") as $l_ticks) {
    $l_text = trim($l_ticks->innertext);
    if (!empty($l_text)) {
      insertrecord($dbR, $dbW, $l_ticks,$tick_tbl_name,$level_num, $l_market);
      usleep(300);
    }else {
      echo date("Y-m-d H:i:s"). " " ."sys_indu_code: $level_num market: $l_market url: $a_url record content empty! \r\n";
    }

    // 清下内存
    $l_ticks->clear();unset($l_ticks);
  }
}

// 获取详细信息
function getdetail(&$dbR,&$dbW,$content,$table_name,$level_num,$l_market,$a_url){
  if (false!==strpos($content, "<Record")) {
    // 替换掉不匹配的a标签
    if (false!==strpos($content, "<a") && false===strpos($content, "</a")) {
      $content = str_replace(array("<a","<A"),"",$content);
    }

    // 对方提供的是utf8编码的
    $l_xml = str_get_html($content);
    get_comp_info($dbR, $dbW,  $l_xml, $table_name, $level_num, $l_market,$a_url);

    // 清下内存
    $l_xml->clear();unset($l_xml);
  }else {
    echo date("Y-m-d H:i:s"). " " ."sys_indu_code: $level_num market: $l_market url: $a_url record empty!"."\r\n";
  }
}

