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
$_G_cookie_arr   = array();
$_G_timeout   = 30;

// 修改数据库连接信息
__gener_conf(INI_CONFIGS_PATH,"mysql_config.ini","trade_db_w","trade_db");

define("COMM_STOCK", "http://210.5.28.134/cqs/query?encode=cn&type=mem&market=10&symbol=%23");

$dbR = new DBR();
$dbW = new DBW();
$sector_def = TABLENAME_PREF."sector";

for ($i=0;$i<150;$i++){
  // 逐一抓取，然后入库
  $l_e = str_pad($i,3,"0",STR_PAD_LEFT);
  $content = request_cont(COMM_STOCK.$l_e,$_G_timeout,$_G_cookie_arr);
  // 分析页面, 其实是xml
  $_a = getstru($content,$_G_timeout,$_G_cookie_arr);

  // 然后插入数据库中，板块的放到 sector 表中；
  //echo " ---".count($_a[0])." ---".NEW_LINE_CHAR;
  if (!empty($_a[0])) {
    $dbW->table_name = $sector_def;
    foreach ($_a[0] as $l_val){
      $data_arr = array(
        "id"=>trim($l_val["id"]),
        "name_cn"=>convCharacter($l_val["name"],true),
        "type"=>"IND"
      );
      // 需要判断是上市还是上柜
      if ("柜"==convCharacter(cn_substr($l_val["name"],2,"utf8",""),true)) {
        $data_arr["exchange_type"] = "otc";
      }else {
        $data_arr["exchange_type"] = "tse";
      }

      inserone($dbW, $data_arr,"id=".$data_arr["id"]);
      usleep(200);
    }
  }else {
    echo date("Y-m-d H:i:s"). " id: $i empty "  .NEW_LINE_CHAR;
  }
  usleep(100);
}

//
function getstru($content, $timeout, $cookie_arr){
  $l_arr = array();
  $l_lei_arr = array();

  // 对方提供的是utf8编码的, 采用simplexml解析比较快
  $l_xml = new SimpleXMLElement($content);

  foreach ($l_xml->symbol as $l_k => $l_sym) {
    // 获取到id和name，id中有#的作为分类处理
    $l_id = (string)$l_sym->id;
    $l_na = (string)$l_sym->name;

    // 对name进行分析，如果空，认为不存在
    if (""!=trim($l_na)) {

      if (false!==strpos($l_id,"#")) {
        $l_t = str_replace("#","",$l_id);
        $l_lei_arr[] = array("id"=>trim($l_t),"name"=>$l_na);
      }else {
        echo date("Y-m-d H:i:s"). " " .$l_id. " no# ".NEW_LINE_CHAR;
      }
    }else {
      //echo $l_id. " empty! ".NEW_LINE_CHAR;
    }
  }
  // 清下内存
  unset($l_sym);
  unset($l_xml);

  //print_r($l_lei_arr);
  //print_r($l_arr);

  return array($l_lei_arr);
}

