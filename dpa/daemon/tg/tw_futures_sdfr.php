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
//define("COMM_QIHUO", "http://210.5.28.134/cqs/query?encode=cn&type=symbollist&market=1&group=3");
$futures_sdfr=array('value'=>"http://210.67.12.97/fdc/publics/TWIfutures.ashx?type=value",
                    'openinterest'=>"http://210.67.12.97/fdc/publics/TWIfutures.ashx?type=openinterest"
                    );
$dbR = new DBR();
$dbW = new DBW();
foreach($futures_sdfr as $k=>$v)
{
  //tw_futures_sdfr_value
$sector_def = TABLENAME_PREF."futures_sdfr_".$k;

$content = request_cont($v,$_G_timeout,$_G_cookie_arr);
// 分析页面, 其实是xml
$_a = getstru($content,$_G_timeout,$_G_cookie_arr,$k);
//continue;
echo " ---".count($_a)." ---".NEW_LINE_CHAR;
  $dbW->table_name = $sector_def;
  foreach ($_a as $l_val){
    /*$data_arr = array(
      "symbol"=>trim($l_val["id"]),
      "name_cn"=>iconv("UTF-8","GBK",$l_val["name"]),
      "type"=>trim($l_val["type"]),
    );*/
    inserone($dbW, $l_val);
    usleep(200);
  }
}
//
function getstru($content, $timeout, $cookie_arr,$type){

  // 对方提供的是utf8编码的, 采用simplexml解析比较快
  $l_xml = new SimpleXMLElement($content);
//  print_r($l_xml);
  $futures=array();
  $l_type1='';
  if($type=="value")
  $updatedate=$l_xml->ITRDValue->attributes();
  else
  $updatedate=$l_xml->ITRDOpenInterest->attributes();
  $updatedate= (string)$updatedate['updataTime'];
  if($type=="value")
  $array_ITR=$l_xml->ITRDValue->item;
  else
  $array_ITR=$l_xml->ITRDOpenInterest->item;
  foreach ($array_ITR as $l_k => $l_sym) {
    //print_r($l_sym);
    //exit;
    $l_id=(string)$l_sym['id'];
    $l_iname=(string)$l_sym->INAme;
    $l_v1_volume=(string)$l_sym->v1->Volume;
    $l_v1_contractvalue=(string)$l_sym->v1->ContractValue;
    $l_v2_volume=(string)$l_sym->v2->Volume;
                $l_v2_contractvalue=(string)$l_sym->v2->ContractValue;
    $l_v3_volume=(string)$l_sym->v3->Volume;
                $l_v3_contractvalue=(string)$l_sym->v3->ContractValue;
    $futures[]=array(//'id'=>$l_id,
        'fr_name'=>iconv("UTF-8","GBK",$l_iname),
        'updatedate'=>$updatedate,
        'v1_volume'=>$l_v1_volume,
        'v1_contractvalue'=>$l_v1_contractvalue,
        'v2_volume'=>$l_v2_volume,
        'v2_contractvalue'=>$l_v2_contractvalue,
        'v3_volume'=>$l_v3_volume,
                                'v3_contractvalue'=>$l_v3_contractvalue);
    /*$l_id = (string)$l_sym->id;
    $l_na = (string)$l_sym->name;
    $l_type=substr($l_id,0,strlen($l_id)-2);

    $l_type1=$l_type;
    $futures[]=array('id'=>$l_id,'name'=>$l_na,'type'=>$l_type);*/
  }
  print_r($futures);
  //exit;
  // 清下内存
  unset($l_sym);
  unset($l_xml);

  //print_r($futures);
  return $futures;
}
