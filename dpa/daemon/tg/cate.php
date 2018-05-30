<?php
/**
 * 获取行业分类, 产业与概念中心
 * http://210.67.12.98/FA/getCategoryTSE.aspx
 */
if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
  require_once("D:/www/dpa/daemon/tg/configs/system.conf.php");
}else {
  require_once("/data0/deve/runtime/configs/tg_system.conf.php");
}
require_once("common/functions.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");
// 抓取和解析用
require_once("HTTP/Request.php");
require_once("simple_html_dom.php");
$_G_cookie_arr   = array();
$_G_timeout   = 30;


function insert_project (&$dbW, $form){
  $data_arr = array(
    "cn_name" => $form["_PF_cn_name"],
    "type"     => key_exists("pt",$form)?strtoupper($form["pt"]):"PUB",
    "db_host" => key_exists("_PF_db_host",$form)?$form["_PF_db_host"]:$_SERVER["SRV_DB_HOST_R"],
    "db_name" => key_exists("_PF_db_name",$form)?$form["_PF_db_name"]:$_SERVER["SRV_DB_NAME_R"],//
    "db_port" => key_exists("_PF_db_name",$form)?$form["_PF_db_port"]:$_SERVER["SRV_DB_PORT_R"],
    "db_user" => key_exists("_PF_db_name",$form)?$form["_PF_db_user"]:$_SERVER["SRV_DB_USER_R"],
    "db_pwd"  => key_exists("_PF_db_name",$form)?$form["_PF_db_pwd"] :$_SERVER["SRV_DB_PASS_R"],
  );
  if(key_exists("_PF_db_timeout",$form))$data_arr["db_timeout"]=$form["_PF_db_timeout"];
  if(key_exists("_PF_description",$form))$data_arr["description"]=$form["_PF_description"];
  if(key_exists("_PF_host_id",$form))$data_arr["host_id"]=$form["_PF_host_id"];

  $dbW->table_name = $form["TABLENAME_PREF"]."project";
  $dbW->insertOne($data_arr);
}

// 修改数据库连接信息
//__gener_conf(INI_CONFIGS_PATH,"mysql_config.ini","trade_db_w","trade_db");
$dbR = new DBR();
        $dbR->table_name = TABLENAME_PREF."project";
        print_r($dbR->getAlls());exit;

        $p_arr = $dbR->getOne(" where p_id = ".$request["p_id"]);
        // print_r($p_arr); // 模板信息需要从另一个库中获取信息
        $dbR = new DBR($p_arr);
        $dbR->table_name = TABLENAME_PREF."template";
        $t_arr = $dbR->getOne(" where t_id = ".$request["t_id"]);
        $p_arr["tbl_name"] = $t_arr["t_name"];  // 表 sp_t2
        //

$_URL_arr = array(
  // 一级分类
  1 => array(
    "tse"=>"http://210.67.12.98/FA/getCategoryTSE.aspx",
    "otc"=>"http://210.67.12.98/FA/getCategoryOTC.aspx",
  ),
  // 二级分类,后面需要跟上一级分类的代码
  2 => array(
    "tse"=>"http://210.67.12.98/FA/getCategorySubTSE.aspx?code=",
    "otc"=>"http://210.67.12.98/FA/getCategorySubOTC.aspx?code=",
  ),
  // 概念、集团亚分类
  3 => array(
    "concept"=>"http://210.67.12.98/FA/getCategorySubConcept.aspx",
    "group"  =>"http://210.67.12.98/FA/getCategorySubGroup.aspx",
  ),
);

$dbR = new DBR();
$dbW = new DBW();
$table_name = TABLENAME_PREF."category";
$table_name_arr = array("category"=>"分类表");

main($dbR, $dbW, $table_name, $_URL_arr, $_G_timeout, $_G_cookie_arr);

//
function main(&$dbR, &$dbW, $sector_def, $_URL_arr, $_G_timeout,$_G_cookie_arr=array()){

  foreach ($_URL_arr as $level_num=>$l_val){
    // 二级分类需要一级分类的列表
    if (1==$level_num || 3==$level_num) {
      foreach ($l_val as $l_market=>$l_url){
        $content = request_cont($l_url,$_G_timeout,$_G_cookie_arr);
        $l_detail = getdetail($dbR, $dbW, $content,$sector_def,$level_num,$l_market,$l_url);
        usleep(300);
      }
    }else if (2==$level_num) {
      foreach ($l_val as $l_market=>$l_url){
        // 再多一重获取一级分类列表的循环
        // 获取所有的 指数和分类
        $dbR->table_name = $sector_def;
        $sect_arr = $dbR->getAlls(" where levelnum=1 and market='$l_market'  order by id");

        // 逐一解析
        foreach ($sect_arr as $l_sect){
          echo date("Y-m-d H:i:s"). " " .$n_url."\r\n";
          $n_url = $l_url.$l_sect["sys_indu_code"];// 需要多加一个id

          $content = request_cont($n_url,$_G_timeout,$_G_cookie_arr);
          $l_detail = getdetail($dbR, $dbW, $content,$sector_def,$level_num,$l_market,$n_url);
          usleep(300);
        }
      }
    }
  }
}

function insertrecord(&$dbR, &$dbW, &$l_tick, $tick_tbl_name,$level_num, $l_market){
  global $table_name_arr;
  // 获取字段个数
  $l_m_arr = get_simp_elem($l_tick,false);

  // 字段自动入库，自动创建字段, 先修改表结构
  $l_r_n = autoCreateField($dbR, $dbW, $tick_tbl_name, $l_m_arr,getTblNameCN($tick_tbl_name,$table_name_arr));

  // 修改表结构没有报错
  if (!$l_r_n) {
    // 可以插入数据了
    foreach ($l_m_arr as $l_k => $l_v){
      $data_arr[$l_k] = convCharacter($l_v,true);
    }

    $data_arr["industry_id"] = substr(trim($data_arr["sys_indu_code"]),0,2);
    $data_arr["idx_code"] = str_replace("#","",$data_arr["idx_code"]);
    $data_arr["levelnum"]   = $level_num;
    $data_arr["market"]   = $l_market;

    $dbW->table_name = $tick_tbl_name;
    inserone($dbW, $data_arr,"levelnum='".$data_arr["levelnum"]."' and market='".$data_arr["market"]."' and sys_indu_code='".$data_arr["sys_indu_code"]."'"); // 唯一性条件可以先不用给出
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
      echo date("Y-m-d H:i:s"). " " ."levelnum: $level_num market: $l_market url: $a_url record content empty! \r\n";
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
    echo date("Y-m-d H:i:s"). " " ."levelnum: $level_num market: $l_market url: $a_url record empty!"."\r\n";
  }
}

