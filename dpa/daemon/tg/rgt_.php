<?php
/**
 *
$_url_arr = array(
  "http://product.horise.com/eval/data/ShortStock.mvc?date=2010-04-21&callback=GetDayStock&top=10",
  "http://hq.horise.com/data/sina_cooperate.aspx?callback=GetTenOrgStock&top=10",
  "http://product.horise.com/eval/data/OrgStockSina.mvc?callback=GetOrgStock&top=10",
);
 */
require_once("configs/system.conf.php");
require_once("common/func.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");
// 抓取和解析用
require_once("HTTP/Request.php");
require_once("JSON.php");

$_G_cookie_arr   = array();
$_G_timeout   = 30;

// 修改数据库连接信息
__gener_conf(INI_CONFIGS_PATH,"mysql_config.ini","market_db_w","market_db_w");

$table_name_arr = array(
  // 每日强势股
  "ShortStock"    => array(
              "t_name_cn"=>"每日强势股",
              "COMM_URL"=>"http://product.horise.com/eval/data/ShortStock.mvc?callback=_",
              "f_uni"=>array("currdate","code"),
              "para"=>array(
                "top"=>array(
                          "f_name_cn"=>"条数",
                          "list"=>20
                ),
                "date"=>array(
                          "f_name_cn"=>"日期",
                          "list"=>date("Y-m-d")
                )
              )
            ),
  // 十大机构推荐金股
  "TenOrgStock"  => array(
              "t_name_cn"=>"十大机构推荐金股",
              "COMM_URL"=>"http://hq.horise.com/data/sina_cooperate.aspx?callback=_",
              "f_uni"=>array("currdate","code"),
              "para"=>array(
                "top"=>array(
                          "f_name_cn"=>"条数",
                          "list"=>20
                )
              )
            ),
  // 机构强势股
  "OrgStock"  => array(
              "t_name_cn"=>"机构强势股",
              "COMM_URL"=>"http://product.horise.com/eval/data/OrgStockSina.mvc?callback=_",
              "f_uni"=>array("currdate","code"),
              "para"=>array(
                "top"=>array(
                          "f_name_cn"=>"资料笔数",
                          "list"=>20
                )
              )
            ),
);

// 获取参数列表
require_once 'Console/Getopt.php';
$_options = Console_Getopt::getopt($argv, 'd:t:', array());
$_o = array();
if (!PEAR::isError($_options)) {
  foreach ($_options[0] as $l_v){
    $_o[$l_v[0]] = $l_v[1];
  }
}

$a_date = (!empty($_o["d"])) ? $_o["d"] : "";
$t_arr = (!empty($_o["t"])) ? array($_o["t"]=>$table_name_arr[$_o["t"]]) : $table_name_arr;

$dbR = new DBR();
$dbW = new DBW();

if (!empty($_o["d"])) {
  procOnesymbol($dbR, $dbW, $t_arr, $a_date, $_G_timeout,$_G_cookie_arr);// 当个个股
}else {
  main($dbR, $dbW, $t_arr, $_G_timeout, $_G_cookie_arr);
}

function main(&$dbR, &$dbW, $table_name_arr, $_G_timeout,$_G_cookie_arr=array() ){

  $sect_arr = array(date("Y-m-d"));
  // 逐一解析
  foreach ($sect_arr as $l_date){
    $sym_or_secid = $l_date;
    procOnesymbol($dbR, $dbW, $table_name_arr, $sym_or_secid, $_G_timeout,$_G_cookie_arr);
  }
}

//
function procOnesymbol(&$dbR, &$dbW, $table_name_arr, $sym_or_secid, $_G_timeout,$_G_cookie_arr=array()){
  // 表名的循环
  if (!empty($table_name_arr) && !empty($sym_or_secid)) {
    foreach ($table_name_arr as $tbl_name=>$tbl_desc){
      $l_f_a = array();
      $l_f_o_str = "";
      $t_name_cn = $tbl_desc["t_name_cn"];
      $uni_arr = array();
      if (key_exists("f_uni",$tbl_desc)) {
        $uni_arr = $tbl_desc["f_uni"];
      }

      if (key_exists("para",$tbl_desc)) {
        // 需要循环出所有的参数，同时记录多个值的参数
        foreach ($tbl_desc["para"] as $l_fid=>$l_pa){
          if (is_array($l_pa["list"])) {
            $l_f_a[$l_fid] = $l_pa["list"];
          }else {
            $l_f_o_str .= $l_fid."=".$l_pa["list"]."&";
          }
        }
        $l_comm_url = $tbl_desc["COMM_URL"]."&".$l_f_o_str;

        // 目前只支持一轮循环,多个参数循环的以后再优化
        if (!empty($l_f_a)) {
          if (1==count($l_f_a)) {
            foreach (current($l_f_a) as $l_v){
              $uni_arr_n = $uni_arr;  // 防止累积
              $l_f_i = key($l_f_a);
              $l_url = $l_comm_url.$l_f_i."=".$l_v;
              $uni_arr_n[] = $l_f_i;
              echo date("Y-m-d H:i:s"). " " .$l_url."\r\n";
              proc_grab($dbR, $dbW, $uni_arr_n,$sym_or_secid, TABLENAME_PREF.$tbl_name,$l_url,$t_name_cn,array($l_f_i=>$l_v),$_G_timeout,$_G_cookie_arr);

              usleep(300);
            }
          }else {
            echo date("Y-m-d H:i:s"). " " ."l_f_a count>1"."\r\n";
          }
        }else {
          $l_url = $l_comm_url;
          echo date("Y-m-d H:i:s"). " " .$l_url."\r\n";
          proc_grab($dbR, $dbW, $uni_arr,$sym_or_secid, TABLENAME_PREF.$tbl_name,$l_url,$t_name_cn,array(),$_G_timeout,$_G_cookie_arr);
          usleep(300);
        }
      }else {
        $l_url = $tbl_desc["COMM_URL"];
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
  $l_h_u = array();  // 地址调用，必须声明一个
  $content = request_cont($l_h_u,$l_url,$_G_timeout,$_G_cookie_arr);
  unset($l_h_u);
  getdetail($dbR, $dbW, $content, $uni_arr,$a_id,$tbl_name,$l_url,$t_name_cn,$add_field);
}

//
function insertrecord(&$dbR, &$dbW, &$l_tick, $tick_tbl_name,$unique_arr,$a_id,$t_name_cn,$add_field){
  // 获取字段个数
  $l_m_arr = get_json_elem($l_tick,false);

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
      // 需要对日期时间进行特殊处理
      $l_v = conv2datetime($l_v);
      $data_arr[$l_k] = convCharacter($l_v,true);
    }
    $data_arr["tdate"] = $a_id;
    $dbW->table_name = $tick_tbl_name;
    inserone($dbW, $data_arr,$unique_arr); // 唯一性条件可以先不用给出
  }

  unset($l_m_arr);
  unset($data_arr);
}

function get_comp_info(&$dbR, &$dbW, &$l_xml,$tick_tbl_name, $uni_arr,$a_id,$a_url,$t_name_cn,$add_field){
  // 获取tick信息
  foreach ($l_xml as $l_ticks){
    if (!empty($l_ticks)) {
      insertrecord($dbR, $dbW, $l_ticks,$tick_tbl_name,$uni_arr,$a_id,$t_name_cn,$add_field);
      usleep(60000);
    }else {
      echo date("Y-m-d H:i:s"). " " ."a_id $a_id url $a_url record content empty! \r\n";
    }

    unset($l_ticks);
  }
}

// 获取详细信息
function getdetail(&$dbR,&$dbW,$content,$uni_arr,$a_id,$table_name,$a_url,$t_name_cn,$add_field){
  $content = trim($content);
  $stp = "_(";
  if ($stp==substr($content,0,strlen($stp))) {
    $content = substr($content,strlen($stp));
    $content = substr($content,0,-2);
    //echo $content.NEW_LINE_CHAR;
    // 对方提供的是utf8编码的
    if (function_exists('json_decode')) {
      $l_xml = json_decode($content);
    }else {
    $json = new Services_JSON();
    $l_xml = $json->decode($content);
    }
    if (!empty($l_xml)) {
      get_comp_info($dbR, $dbW,  $l_xml, $table_name, $uni_arr,$a_id,$a_url,$t_name_cn,$add_field);
    }
    unset($l_xml);
  }else {
    echo date("Y-m-d H:i:s"). " " ." url: $a_url record empty!"."\r\n";
  }
}


function get_json_elem(&$a_obj, $a_num=true){
  // 循环出所有的字段
  $l_a = array();

  foreach ($a_obj as $l_f=>$l_val){
    if($a_num) {
      // 科学计数法都转为数字了 number_format(strval($l_v)*1.0,2,".","");
      $l_val *= 1.0;
    }
    $l_a[strtolower($l_f)] = $l_val;
  }

  return $l_a;
}

//
function conv2datetime($str){
  if (false!==strpos($str,"/Date(")) {
    if (preg_match("/Date\((\d{10})/i",$str,$match)) {
      // 只获取前10个用于时间戳，毫秒省略掉
      $l_ts = $match[1];
      $l_dt = date("Y-m-d H:i:s", $l_ts);
      $str = $l_dt;// 返回格式化好了的日期和时间
    }
  }

  return $str;
}
