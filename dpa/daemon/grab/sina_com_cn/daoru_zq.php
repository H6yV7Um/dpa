<?php
/**
 * 全部股票，参考如下地址：
 * http://money.finance.sina.com.cn/api/list.php/BasicStockSrv.getList?type=A&ex=sh
 * http://money.finance.sina.com.cn/api/list.php/BasicStockSrv.getList?type=A&ex=sz
 *
 *
 *
 */
ini_set('memory_limit', '200M');

if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)) )
{
  require_once("D:/www/dpa/configs/system.conf.php");
  $l_dsn  = "dpa3308";
  $py_data_path = "D:/www/pear/py.dat";
}
else
{
  require_once("/data0/deve/runtime/configs/system.conf.php");
  $l_dsn  = "dpa";
  $py_data_path = "/usr/local/webserver/php/lib/php/py.dat";
}
require_once("lang/".$GLOBALS['cfg']['LANG_DEFINE_FILE']);
require_once("common/functions.php");
require_once("common/global_func.php");
require_once("common/grab_func.php");
require_once("common/Files.cls.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");
require_once("PinYin.class.php");
require_once("HTTP/Request.php");
require_once("simple_html_dom.php");
require_once("JSON.php");

if ('cli'==php_sapi_name()) {
  $_o = cArray::get_opt($argv, 'i:d:t:');
}else {
  $_o = $_GET;
}

define("DEBUG2",true,true);
if (DEBUG2) {
    $old_t = microtime();
    list($ousec, $osec) = explode(" ", $old_t);
    echo "begin time: ".date("Y-m-d H:i:s",$osec) ." | microtime: ".($osec+$ousec).NEW_LINE_CHAR;
}


$G_tuishi_symbol = array(
  "sh600991",
  "sh600591",
  "sh600607",
  "sh600631",
  "sh600842",
  "sh600849",
);
//fill_stock_cj();
//grab_stock_list_zhengjianhui();
//grab_stock_list_zhengjianhui(array('name'=>"大盘指数"),'指数表');


// 从数据库中获取所有的股票代码，然后逐一抓取相关的总股本等信息，记住，只从股票列表中获取
get_jsvar();
function get_jsvar($a_tar_tbl='股票表'){
  // 从数据库中获取到所有的节点, 然后逐一处理
  $dbR = new DBR();
  $l_err = $dbR->errorInfo();
  if ($l_err[1]>0){
    echo date("Y-m-d H:i:s") . " 出错了, error信息： " . $l_err[2]. NEW_LINE_CHAR;
    return $l_options;
  }
  $dbR->table_name = TABLENAME_PREF."project";
  $p_arr_cj = $dbR->GetOne("where name_cn='财经频道'");
  // 获取表信息
  $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr_cj);
  $dbR->dbo = &DBO('caijingpingdao_r', $dsn);
  $dbW = new DBW($p_arr_cj);
  $dbR->table_name = TABLENAME_PREF."table_def";
  $t_arr_zhishu   = $dbR->GetOne("where name_cn='指数表'");
  $t_arr_stock_list = $dbR->GetOne("where name_cn='$a_tar_tbl'");
  //$table_name = $t_arr_stock_list["name_eng"];
  $tbl_stock_list = $t_arr_stock_list["name_eng"];
  $dbR->table_name = $tbl_stock_list;

  // 每1000条一处理
  $page_size = 1000;
  if ($page_size<=0) $page_size=1;  // 确保除数大于0
  $l_where = "where status_='use' and jsvar is NULL ";
  $l_total = $dbR->getCountNum($l_where);
  echo $l_total . NEW_LINE_CHAR;

  $l_page_num = ceil($l_total/$page_size);
  // 获取到总数以后进行分页
  $l_max_id = 0;
  for ($i=0;$i<=$l_page_num;$i++){
    // 节省内存的一种方式
    $l_data = $dbR->getAlls($l_where ." and id>".$l_max_id . " order by id asc limit $page_size", "id,symbol");
    echo $dbR->getSQL() . NEW_LINE_CHAR;
    // 修改最大id
    $l_last = count($l_data)-1;
    if(isset($l_data[$l_last]["id"])) $l_max_id = $l_data[$l_last]["id"] ;

    // 逐项抓取，然后入库
    proc_jsvar_one($p_arr_cj,$tbl_stock_list,$l_data);
  }
  //
  if ('WIN' !== strtoupper(substr(PHP_OS, 0, 3)) ) {
    $l_url = "http://finance.sina.com.cn/realstock/company/hotstock_daily_a.js";
    echo NEW_LINE_CHAR . date("Y-m-d H:i:s"). " " .$l_url.NEW_LINE_CHAR;
    $l_h_u = array();
    $l_cot = request_cont($l_h_u,$l_url);
    if (!is_utf8_encode($l_cot)) $l_cot = iconv("GBK","UTF-8//IGNORE",$l_cot);

    if (""!=trim($l_cot)){
      $l_cot = preg_replace("|/\*.*((\r)?(\n)*(.)*)*\*/|","",$l_cot);
      $file = new Files();
      $file->overwriteContent($l_cot,"/data0/htdocs/cj/cn/stock/hotstock_daily_a.js");
    }
  }
}

function proc_jsvar_one($p_arr_cj,$tbl_stock_list,$a_data){
  $dbW = new DBW($p_arr_cj);
  $dbW->table_name = $tbl_stock_list;

  //
  if (is_array($a_data) && !empty($a_data)) {
    foreach ($a_data as $l_data){
      $l_url = "http://finance.sina.com.cn/realstock/company/".$l_data["symbol"]."/jsvar.js";

      //
      echo NEW_LINE_CHAR . date("Y-m-d H:i:s"). " " .$l_url.NEW_LINE_CHAR;
      $l_h_u = array();
      $l_cot = request_cont($l_h_u,$l_url,"",60);

      if(false===$l_cot) {
        // 网络不通的时候
        echo "please check the network! LINE: ". __LINE__ ." symbol:".$l_data['symbol'] . NEW_LINE_CHAR;
        //exit;
      }else if (""!= trim($l_cot)) {
        if (!is_utf8_encode($l_cot)) $l_cot = iconv("GBK","UTF-8//IGNORE",$l_cot);
        // 只去掉注释/* */ 使用正则
        $l_cot = preg_replace("|/\*.*((\r)?(\n)*(.)*)*\*/|","",$l_cot);
        // 将数据更新到数据表中去
        $data_arr = array("jsvar"=>$l_cot);
        // 是否停牌股票
        if (false!==strpos($l_cot, "var stock_state = 3;")) {
          $data_arr["status_"] = 'stop';
        }
        $condition = ' `id` = '.$l_data['id'];
        $dbW->updateOne($data_arr,$condition);
        $l_err = $dbW->errorInfo();
        if ($l_err[1]>0){
          echo date("Y-m-d H:i:s"). " ".$dbW->getSQL() ." update_error! symbol:".$l_data['symbol'] . var_export($l_err, true) . NEW_LINE_CHAR;
        }else {
          echo date("Y-m-d H:i:s"). " "." update__succ! symbol:".$l_data['symbol'] . NEW_LINE_CHAR;
        }
      }else {
        echo "error! symbol:".$l_data['symbol']." LINE:". __LINE__ . NEW_LINE_CHAR;
        return ;
      }
      //exit;
      sleep(2);
    }
  }
}

/*$l_2ji = array(
  "概念板块" => array(
    'name'=>"概念板块",
    'suoshu_ziduan'=>array(
      'gn_fenlei1',
      'gn_fenlei2'
    )
  ),
  "地域板块" => array(
    'name'=>"地域板块",
    'suoshu_ziduan'=>array(
      'diyu_fenlei1',
      'diyu_fenlei2'
    )
  ),
  "分类" => array(
    // 需要剔除所有指数
    'name'=>"分类",
    'suoshu_ziduan'=>array(
      'fenlei_suoshu_fenlei1',
      'fenlei_suoshu_fenlei2'
    )
  ),
  "指数成分" => array(
    'name'=>"指数成分",
    'suoshu_ziduan'=>array(
      'zhishu_fenlei1',
      'zhishu_fenlei2'
    )
  ),

);
foreach ($l_2ji as $l__2) {
grab_stock_list_zhengjianhui($l__2);
}*/


// 依据行情中心导航，抓取股票列表数据，证监会行业分类的所有个股
function grab_stock_list_zhengjianhui($a_2ji_name=array('name'=>'证监会行业','suoshu_ziduan'=>array('zhengjianhui_suoshu_fenlei1','zhengjianhui_suoshu_fenlei2')), $a_tar_tbl='股票表'){
  // 从数据库中获取到所有的节点, 然后逐一处理
  $dbR = new DBR();
  $l_err = $dbR->errorInfo();
  if ($l_err[1]>0){
    echo date("Y-m-d H:i:s") . " 出错了, error信息： " . $l_err[2]. NEW_LINE_CHAR;
    return $l_options;
  }
  $dbR->table_name = TABLENAME_PREF."project";
  $p_arr_cj = $dbR->GetOne("where name_cn='财经频道'");
  // 获取表信息
  $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr_cj);
  $dbR->dbo = &DBO('caijingpingdao_r', $dsn);
  $dbW = new DBW($p_arr_cj);
  $dbR->table_name = TABLENAME_PREF."table_def";
  $t_arr_gongyong = $dbR->GetOne("where name_cn='行情中心导航'");
  $t_arr_zhishu   = $dbR->GetOne("where name_cn='指数表'");
  $t_arr_stock_list = $dbR->GetOne("where name_cn='$a_tar_tbl'");
  $table_name = $t_arr_gongyong["name_eng"];
  $tbl_stock_list = $t_arr_stock_list["name_eng"];
  $dbR->table_name = $table_name;
  $l_2ji = $dbR->GetOne("where name_cn='".$a_2ji_name['name']."' and jibie=2 ");

  //
  if (!empty($l_2ji)) {
    // 继续获取其子节点
    $dbR->table_name = $table_name;
    $l_3ji = $dbR->getAlls("where parent_id=".$l_2ji["id"] . " order by id ASC ");

    if (!empty($l_3ji)) {
      // 从此处开始处理
      foreach ($l_3ji as $l_vals){
        $dbR->table_name = $table_name;
        $l_4ji = $dbR->getAlls("where parent_id=".$l_vals["id"] . " order by id ASC ");

        if (!empty($l_4ji)) {
          foreach ($l_4ji as $l__v){
            // 没有第五级别的，不过为了安全起见，还是检查一下是否有5级的
            //$l_5ji = $dbR->getAlls("where parent_id=".$l__v["id"] . " order by id ASC ");
            //print_r($l_5ji); // 发现确实没有第五级别的, 输出全部为空数组

            // 逐项进行拼装url，完成获取总数，以及抓取股票列表的任务
            $l_suoshu_ = array();
            if (isset($a_2ji_name['suoshu_ziduan'][0])) $l_suoshu_[$a_2ji_name['suoshu_ziduan'][0]] = $l_vals["name_cn"];
            if (isset($a_2ji_name['suoshu_ziduan'][1])) $l_suoshu_[$a_2ji_name['suoshu_ziduan'][1]] = $l__v["name_cn"];
            $l_sym_arr = ProconeNod($dbW, $tbl_stock_list, $l__v, $l_suoshu_);
            if (null===$l_sym_arr) {
              return ;
            }
          }
        }else {
          //if ("所有指数"!=$l_vals['name_cn']) continue;
          // 当没有下级分类的时候，通常本大类就是一个可点击的节点
          $l_suoshu_ = array();  // 此处做多只需要一级即可

          if ("所有指数"==$l_vals['name_cn']) {
            // 所有指数应该入库指数表
            $l_tmp_tbl = $t_arr_zhishu["name_eng"];
          }else {
            if (isset($a_2ji_name['suoshu_ziduan'][0])) $l_suoshu_[$a_2ji_name['suoshu_ziduan'][0]] = $l_vals["name_cn"];
            $l_tmp_tbl = $tbl_stock_list;
          }
          $l_sym_arr = ProconeNod($dbW, $l_tmp_tbl, $l_vals, $l_suoshu_);
          if (null===$l_sym_arr) {
            return ;
          }
        }
      }
    }else {
      // 当没有下级分类的时候，通常本大类就是一个可点击的节点
      $l_sym_arr = ProconeNod($dbW, $tbl_stock_list, $l_2ji);
      if (null===$l_sym_arr) {
        return ;
      }
    }
  }
}

function ProconeNod(&$dbW, $tbl_stock_list, $l_vals, $l_suoshu_=array()){
  $l_rlt = GetoneNod($l_vals,$l_suoshu_);
  if (null===$l_rlt) {
    return null;
  }
  if (is_array($l_rlt)) {
    if (empty($l_rlt)) {
      // 可能该分类下没有相关股票，此情况确实存在. 在前面以及输出过信息了
      echo __LINE__ ." this nod is empty, name_cn: " . $l__v['name_cn']. " nav_node:" . $l__v["nav_node"]. NEW_LINE_CHAR;
    }else {
      // 都将代码进行入库处理
      foreach ($l_rlt as $l__code_msg) {
        $L_insert = insert2stocklist($dbW, $tbl_stock_list, $l__code_msg,$l_suoshu_);
        usleep(100);
      }
    }
  }else {
    echo " -- error! " . __LINE__ . NEW_LINE_CHAR;
    return ;
  }
  return ;
}

function insert2stocklist(&$dbW, $table_name, $a_data,$l_suoshu_=array()){
  // 第1级别的就是行情中心字样，第二项需要检查是否为数组，数组则进行下级循环
  $dbW->table_name = $table_name;
  /*$data_arr = array(
    "symbol"  =>$a_data['symbol'],
    "code"     =>$a_data['code'],
    "code_int"  =>$a_data["code_int"],
    "name_cn"  =>$a_data['name_cn'],
  );*/
  $data_arr = $a_data;

  // 先判断数据库中是否存在这些数据，如果存在则进行更新，如果不存在则需要insert
  $l_exist_c = cString_SQL::getUniExist($data_arr, array('symbol'));
  $l_exi_one = $dbW->getExistorNot($l_exist_c);
  if (PEAR::isError($l_exi_one)) {
    echo " error message： " .$l_exi_one->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
    return ;
  }
  if ( is_array($l_exi_one) && !empty($l_exi_one) ) {
    echo date("Y-m-d H:i:s"). " symbol:".$data_arr["symbol"]." exist! " . NEW_LINE_CHAR;
    $son_parent_id = $l_exi_one["id"];

    // 是否更新取决于 $l_suoshu_ 是否为空, 额外的字段更新的时候采用追加添加的方式进行,多条采用逗号分隔
    if (is_array($l_suoshu_) && !empty($l_suoshu_)) {
      $data_arr = array();
      foreach ($l_suoshu_ as $l_ziduan=>$l_1v){
        if (array_key_exists($l_ziduan,$l_exi_one)) {

          if (empty($l_exi_one[$l_ziduan])) {
            // 如果原来数据为空,直接追加
            $data_arr[$l_ziduan] = $l_1v;
          }else {
            // 如果原来数据不为空，需要进行是否存在的判断
            $l_tmp = explode(",", $l_exi_one[$l_ziduan]);
            if (!in_array($l_1v,$l_tmp)) {
              $l_tmp[] = $l_1v;  // 添加进来
              $data_arr[$l_ziduan] = implode(",", $l_tmp);
            }
          }
        }
      }
      if (!empty($data_arr)) {
        $condition = ' `id` = '.$l_exi_one['id'];
        $dbW->updateOne($data_arr,$condition);
        $l_err = $dbW->errorInfo();
        if ($l_err[1]>0){
          echo date("Y-m-d H:i:s"). " ".$dbW->getSQL() ." update_error! symbol:".$a_data['symbol'] . var_export($l_err, true) . NEW_LINE_CHAR;
        }else {
          echo date("Y-m-d H:i:s"). " ".$dbW->getSQL() ." update__succ! symbol:".$a_data['symbol'] . NEW_LINE_CHAR;
        }
      }
    }
    // 不进行任何处理 else {}
  } else {
    $dbW->insertOne($data_arr);
    $l_err = $dbW->errorInfo();
    if ($l_err[1]>0){
      echo date("Y-m-d H:i:s"). " ".$dbW->getSQL() ." insert to article_list error!" . var_export($l_err, true) . NEW_LINE_CHAR;
      return null;
    }else {
      echo date("Y-m-d H:i:s"). " insert symbol:".$data_arr["symbol"]." succ! " . NEW_LINE_CHAR;
      $son_parent_id = $dbW->LastID();
    }
  }
  return $son_parent_id;
}



function GetoneNod($l__v,$zhengjianhui_fenlei=array()){
  if (!is_array($zhengjianhui_fenlei)) $zhengjianhui_fenlei=array();  // 必须为数组类型
  $l_count_pre = "http://vip.stock.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodeStockCount?node=";
  $l_node_pre  = "http://vip.stock.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodeData?_s_r_a=init";  // &page=1&num=40&sort=symbol&asc=1&node=hangye_ZM
  if (!function_exists('json_decode')) {
  $json = new Services_JSON();
  }

  $l_node = trim($l__v["nav_node"]);
  if (""!=$l_node) {
    // 进行拼装url, 先获取总数, 然后再请求股票列表，数目不匹配的将会输出错误信息
    $l_url = $l_count_pre.$l__v["nav_node"];  // 获取总数
    echo NEW_LINE_CHAR . date("Y-m-d H:i:s"). " " .$l_url.NEW_LINE_CHAR;
    $l_h_u = array();
    $l_cot = request_cont($l_h_u,$l_url);
    if (!is_utf8_encode($l_cot)) $l_cot = iconv("GBK","UTF-8//IGNORE",$l_cot);

    // 分离出其中的数字，可能是 : null 或 (new String("22"))
    if ("null"== trim($l_cot)) {
      echo __LINE__ ." this nod is empty " . var_export($l__v,true). NEW_LINE_CHAR;
      $l_total = 0;
    }else if(false!==strpos($l_cot, 'new String')) {
      $l_cot = str_replace(array("new String","(",")",'"',"'",";"),"",$l_cot);
      $l_total = trim($l_cot) + 0;  // 获取到总数了
    }else if(false===$l_cot) {
      // 网络不通的时候
      echo "please check the network! LINE: ". __LINE__ . var_export($l__v,true)." ". NEW_LINE_CHAR;
      exit;
    }else {
      echo "error!". __LINE__ . var_export($l__v,true). NEW_LINE_CHAR;
      return ;
    }
    echo "node: $l_node , total num : " .$l_total.NEW_LINE_CHAR;

    // 得到总数以后，进行分页抓取
    $l_page_size = 2500;
    $l_page_num = ceil($l_total/$l_page_size);
    $l_symbol = array();
    for ($i=1;$i<=$l_page_num;$i++){
      $l_url = rtrim($l_node_pre,'&')."&page=".$i."&num=".$l_page_size."&sort=symbol&asc=1&node=".$l_node;
      echo date("Y-m-d H:i:s"). " " .$l_url.NEW_LINE_CHAR;
      $l_h_u = array();
      $l_cot = request_cont($l_h_u,$l_url);
      sleep(1);

      if (!is_utf8_encode($l_cot)) $l_cot = iconv("GBK","UTF-8//IGNORE",$l_cot);
      if (function_exists('json_decode')) {
        $l_content = json_decode($l_cot);
      }else {
        $l_content = $json->decode($l_cot);
      }

      if (!empty($l_content)) {
      foreach ($l_content as $l_code){
        if (is_array($l_code) && isset($l_code['symbol'])) {
          $l__info = array(
            "symbol"=>$l_code['symbol'],
            "code"   =>$l_code['code'],
            "code_int"=> ($l_code['code'] + 0),
            "name_cn"=>$l_code['name'],
          );
        } else if (is_object($l_code)){
          $l__info = array(
            "symbol"=>$l_code->symbol,
            "code"   =>$l_code->code,
            "code_int"=> ($l_code->code + 0),
            "name_cn"=>$l_code->name,
          );
        }else {
          echo "something_UNKOWN ". __LINE__ . var_export($l_code,true). NEW_LINE_CHAR;
          continue ;
        }
        $l__info = array_merge($zhengjianhui_fenlei, $l__info);  // 证监会行业分类信息也纳入

        if(!array_key_exists($l__info['symbol'], $l_symbol)) {
          $l_symbol[$l__info['symbol']] = $l__info;
        }else {
          echo "re-display, symbol:" .$l__info['symbol'] . NEW_LINE_CHAR;
        }
      }
      }
    }
    // 进行总数校验
    if ( count($l_symbol) != $l_total) {
      echo "error! num_not_match : count:" .count($l_symbol). " total:" . $l_total ." LINE: " . __LINE__ . NEW_LINE_CHAR."l__v:". var_export($l__v,true). NEW_LINE_CHAR;
      //return ;  " l_symbol:".var_export($l_symbol,true) .
    }
    // 如果本行业本来为空，也可能空数组
    return $l_symbol;
  }else {
    echo " error!!! " .__LINE__. var_export($l__v,true). NEW_LINE_CHAR ;
    return ;
  }
}


function fill_stock_cj(){
  // 获取数据
  $l_url = "http://vip.stock.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodes";
  $timeout = 60;
  $l_h_u = array();
  $l_cot = request_cont($l_h_u,$l_url,"",$timeout);
  if (!is_utf8_encode($l_cot)) $l_cot = iconv("GBK","UTF-8//IGNORE",$l_cot);
  //echo $l_cot . NEW_LINE_CHAR;
  if (function_exists('json_decode')) {
    $l_content = json_decode($l_cot);
  }else {
  $json = new Services_JSON();
  $l_content = $json->decode($l_cot);//unescape($l_content, "\\")
  }
  if (!is_array($l_content)) {
    return ;
  }
  $dbR = new DBR();
  $l_err = $dbR->errorInfo();
  if ($l_err[1]>0){
    // 数据库连接失败后
    echo date("Y-m-d H:i:s") . " 出错了, error信息： " . $l_err[2]. NEW_LINE_CHAR;
    return $l_options;
  }
  $dbR->table_name = TABLENAME_PREF."project";
  $p_arr_cj = $dbR->GetOne("where name_cn='财经频道'");

  // 进行入库处理
  $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr_cj);
  $dbR->dbo = &DBO('caijingpingdao_r', $dsn);
  $dbR->table_name = TABLENAME_PREF."table_def";
  $t_arr_gongyong = $dbR->GetOne("where name_cn='行情中心导航'");

  $dbW = new DBW($p_arr_cj);
  $table_name = $t_arr_gongyong["name_eng"];

  // 逐条进行入库, 不同的层级各项的意思会不一样
  if (!empty($l_content)) {
    $l_p_id = insert2db($dbW, $l_content, $table_name, 0, 0);
    if (null===$l_p_id) {
      return ;  // 出错就结束程序运行
    }

    if (is_array($l_content[1])) {
      // 接着处理其子节点, 主要是一些大的标题
      foreach ($l_content[1] as $l_1ji){
        $l_1p_id = insert2db($dbW, $l_1ji,$table_name,$l_p_id,1);

        // 继续处理此级别下的子节点, 二级导航了
        if (is_array($l_1ji[1])) {
          foreach ($l_1ji[1] as $l_2ji){
            $l_2p_id = insert2db($dbW, $l_2ji,$table_name,$l_1p_id,2);

            if (is_array($l_2ji[1])) {
              foreach ($l_2ji[1] as $l_3ji){
                $l_3p_id = insert2db($dbW, $l_3ji,$table_name,$l_2p_id,3);

                if (is_array($l_3ji[1])) {
                  foreach ($l_3ji[1] as $l_4ji){
                    $l_4p_id = insert2db($dbW, $l_4ji,$table_name,$l_3p_id,4);

                    if (is_array($l_4ji[1])) {
                      foreach ($l_4ji[1] as $l_5ji){
                        $l_5p_id = insert2db($dbW, $l_5ji,$table_name,$l_4p_id,5);

                        if (is_array($l_5ji[1])) {
                          echo "@@@@@@@@@@@@@@@@@@@@@@@@@".NEW_LINE_CHAR;
                          return ;
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
    echo "complete!".NEW_LINE_CHAR;
  }else {
    echo "empty!".NEW_LINE_CHAR;
  }

  return ;

}

function insert2db(&$dbW, $l_1ji, $table_name, $parent_id, $a_jibie){
      // 第1级别的就是行情中心字样，第二项需要检查是否为数组，数组则进行下级循环
      $dbW->table_name = $table_name;
      $data_arr = array(
        "parent_id" => $parent_id,
        "jibie"   => $a_jibie,
        "name_cn"   => $l_1ji[0],
      );
      if (array_key_exists(2,$l_1ji)) {
        $data_arr["nav_node"] = $l_1ji[2];
      }
      if (array_key_exists(3,$l_1ji)) {
        $data_arr["name_eng"] = $l_1ji[3];
      }
      if (array_key_exists(4,$l_1ji)) {
        $data_arr["other_linktype_node"] = $l_1ji[4];
      }
      if (!is_array($l_1ji[1])) {
        $data_arr["son_"] = $l_1ji[1];
      }
      // 先判断数据库中是否存在这些数据，如果存在则进行更新，如果不存在则需要insert
      $l_exist_c = cString_SQL::getUniExist($data_arr, array('parent_id','name_cn'));
      $l_exi_one = $dbW->getExistorNot($l_exist_c);
      if (PEAR::isError($l_exi_one)) {
        echo " error message： " .$l_exi_one->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
        return ;
      }
      if ( is_array($l_exi_one) && !empty($l_exi_one) ) {
        echo date("Y-m-d H:i:s"). " parent_id:".$data_arr["parent_id"] ." and name_cn:" . $data_arr["name_cn"] . " exist! " . NEW_LINE_CHAR;
        $son_parent_id = $l_exi_one["id"];
      } else {
        $dbW->insertOne($data_arr);
        $l_err = $dbW->errorInfo();
        if ($l_err[1]>0){
          echo date("Y-m-d H:i:s"). " ".$dbW->getSQL() ." insert to article_list error!" . var_export($l_err, true) . NEW_LINE_CHAR;
          return null;
        }else {
          $son_parent_id = $dbW->LastID();
        }
      }

      return $son_parent_id;
}

if (DEBUG2) {
    $end_t = microtime();
    list($usec, $sec) = explode(" ", $end_t);
    echo "end__ time: ".date("Y-m-d H:i:s",$sec) ." | microtime: ".($sec+$usec) .' spend: '.($sec+$usec-$osec-$ousec).'s'.NEW_LINE_CHAR.NEW_LINE_CHAR;
}
