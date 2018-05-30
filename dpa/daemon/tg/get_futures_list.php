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

//define("COMM_STOCK", "http://210.5.28.134/cqs/query?encode=cn&type=symbollist&market=10");
define("COMM_QIHUO", "http://210.5.28.134/cqs/query?encode=cn&type=symbollist&market=1&group=3");

$dbR = new DBR();
$dbW = new DBW();
$sector_def = TABLENAME_PREF."futures";

// 对方提供的股票列表中包含有板块分类的数据
$content = request_cont(COMM_QIHUO,$_G_timeout,$_G_cookie_arr);
// 分析页面, 其实是xml
$_a = getstru($content,$_G_timeout,$_G_cookie_arr);

// 然后插入数据库中，板块的放到 sector 表中；
echo " ---".count($_a)." ---".NEW_LINE_CHAR;
  $dbW->table_name = $sector_def;
  foreach ($_a as $l_val){
    $data_arr = array(
      "symbol"=>trim($l_val["id"]),
      "name_cn"=>iconv("UTF-8","GBK",$l_val["name"]),
      "type"=>trim($l_val["type"]),
    );
    inserone($dbW, $data_arr,"symbol='".$data_arr["symbol"]."'");
    usleep(200);
  }

//
function getstru($content, $timeout, $cookie_arr){

  // 对方提供的是utf8编码的, 采用simplexml解析比较快
  $l_xml = new SimpleXMLElement($content);
  $futures=array();
  $l_type1='';
  foreach ($l_xml->symbol as $l_k => $l_sym) {
    // 获取到id和name，id中有#的作为分类处理
    $l_id = (string)$l_sym->id;
    $l_na = (string)$l_sym->name;
    $l_type=substr($l_id,0,strlen($l_id)-2);
    if($l_type1!=$l_type)
    {
         $futures[]=array('id'=>$l_type.'&','name'=>substr($l_na,0,strlen($l_na)-2).'&','type'=>$l_type);
         $futures[]=array('id'=>$l_type.'@','name'=>substr($l_na,0,strlen($l_na)-2).'@','type'=>$l_type);
    }
    $l_type1=$l_type;
    $futures[]=array('id'=>$l_id,'name'=>$l_na,'type'=>$l_type);
  }
  // 清下内存
  unset($l_sym);
  unset($l_xml);

  //print_r($futures);
  return $futures;
}
