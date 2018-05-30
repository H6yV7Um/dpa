<?php
/**
 * http://210.5.28.134/cqs/query?encode=cn&type=symbollist&market=10
 * 台股列表抓取
 * yahoo 列表: http://biz.cn.yahoo.com/special/twcode/index.html
 *
 * '0', '1', '2', '3' 1:股票，2：指数, 3:其他
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

define("COMM_STOCK", "http://210.5.28.134/cqs/query?encode=cn&type=symbollist&market=10");
//define("COMM_QIHUO", "http://210.5.28.134/cqs/query?encode=cn&type=symbollist&market=01&commodity=WMT");

$dbR = new DBR();
$dbW = new DBW();
$sector_def = TABLENAME_PREF."sector";
$zq_def = TABLENAME_PREF."zq_list";

// 对方提供的股票列表中包含有板块分类的数据
$content = request_cont(COMM_STOCK,$_G_timeout,$_G_cookie_arr);
// 分析页面, 其实是xml
$_a = getstru($content,$_G_timeout,$_G_cookie_arr);

// 然后插入数据库中，板块的放到 sector 表中；
echo " ---".count($_a[0])." ---".NEW_LINE_CHAR;
if (!empty($_a[0])) {
  $dbW->table_name = $sector_def;
  foreach ($_a[0] as $l_val){
    $data_arr = array(
      "id"=>trim($l_val["id"]),
      "name_cn"=>iconv("UTF-8","GBK",$l_val["name"]),
    );
    inserone($dbW, $data_arr,"id=".$data_arr["id"]);
    usleep(200);
  }
}
echo " ---".count($_a[1])." ---".NEW_LINE_CHAR;
// 其他在证券表
if (!empty($_a[1])) {
  $dbW->table_name = $zq_def;
  foreach ($_a[1] as $l_val){
    $data_arr = array(
      "exchange_id"=>1,        // 交易市场就是台股
      "symbol"=>trim($l_val["id"]),  //
      "name_cn_s"=>iconv("UTF-8","GBK",$l_val["name"]),
    );
    inserone($dbW, $data_arr,"exchange_id=".$data_arr["exchange_id"] ." and symbol='".$data_arr["symbol"]."'");

    usleep(200);
  }
}
echo " ---".count($_a[2])." ---".NEW_LINE_CHAR;
if (!empty($_a[2])) {
  $dbW->table_name = $zq_def;
  foreach ($_a[2] as $l_val){
    $data_arr = array(
      "exchange_id"=>1,        // 交易市场就是台股
      "symbol"=>trim($l_val["id"]),  //
      "name_cn_s"=>iconv("UTF-8","GBK",$l_val["name"]),
      "zqtype"=>"OTH"
    );
    inserone($dbW, $data_arr,"exchange_id=".$data_arr["exchange_id"] ." and symbol='".$data_arr["symbol"]."'");
    usleep(200);
  }
}

//
function getstru($content, $timeout, $cookie_arr){
  $l_arr = array();
  $l_lei_arr = array();
  $l_char_arr = array();
  $l_other_arr = array();

  // 对方提供的是utf8编码的, 采用simplexml解析比较快
  $l_xml = new SimpleXMLElement($content);

  foreach ($l_xml->symbol as $l_k => $l_sym) {
    // 获取到id和name，id中有#的作为分类处理
    $l_id = (string)$l_sym->id;
    $l_na = (string)$l_sym->name;

    // 对url进行分类,有#,有字母的，纯数字的
    $l_other_arr[] = array("id"=>$l_id,"name"=>$l_na);
    if (false!==strpos($l_id,"#")) {
      $l_t = str_replace("#","",$l_id);
      $l_lei_arr[] = array("id"=>trim($l_t),"name"=>$l_na);
    }else {
      if (preg_match("/[a-z]/i",$l_id)) {
        $l_char_arr[] = array("id"=>trim($l_id),"name"=>$l_na);
      }else {
        $l_arr[] = array("id"=>trim($l_id),"name"=>$l_na);
      }
    }
  }
  // 清下内存
  unset($l_sym);
  unset($l_xml);

  //print_r($l_lei_arr);
  //print_r($l_arr);

  return array($l_lei_arr,$l_arr,$l_char_arr);
}
