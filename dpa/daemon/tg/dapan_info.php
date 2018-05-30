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
require_once("simple_html_dom.php");
$_G_cookie_arr   = array();
$_G_timeout   = 30;

// 修改数据库连接信息
__gener_conf(INI_CONFIGS_PATH,"mysql_config.ini","trade_db_w","trade_db");

define("COMM_URL", "http://210.67.12.98/FA/GetTWStockFA.aspx?usp=");

// 大盘分析_XML
$table_name_arr = array(
  //  大盘动向
  "CRDMarket"  => array(
              "t_name_cn"=>"信用交易",
              "f_uni"=>array("symbol","x_ymd"),
              "para"=>array(
                "pa2"=>array(
                          "f_name_cn"=>"资料笔数",
                          "list"=>200
                    ),
                "tdb"=>array(
                          "f_name_cn"=>"证券类型",
                          "list"=>"stock"
                    ),
                "type"=>array(
                          "f_name_cn"=>"类型",
                          "list"=>3
                    ),
              )
            ),
  "CrdInduMarket"  => array(
              "t_name_cn"=>"资金流向",
              "f_uni"=>array("symbol","x_ymd","x_industry_name"),
              "para"=>array(
                "tdb"=>array(
                          "f_name_cn"=>"证券类型",
                          "list"=>"stock"
                    ),
              )
            ),
  // 法人特区
  "FRN3Market"  => array(
              "t_name_cn"=>"法人动向",
              "f_uni"=>array("symbol","x_ymd"),
              "para"=>array(
                "tdb"=>array(
                          "f_name_cn"=>"证券类型",
                          "list"=>"stock"
                    ),
              )
            ),
  "ForeignerBuySell"  => array(
              "t_name_cn"=>"外资买卖超",
              "f_uni"=>array("symbol","x_ymd","x_list_code"),
              "para"=>array(
                "tdb"=>array(
                          "f_name_cn"=>"证券类型",
                          "list"=>"stock"
                    ),
              )
            ),
  "DLRBuySell"  => array(
              "t_name_cn"=>"自营商买卖超",
              "f_uni"=>array("symbol","x_ymd","x_list_code"),
              "para"=>array(
                "tdb"=>array(
                          "f_name_cn"=>"证券类型",
                          "list"=>"stock"
                    ),
              )
            ),
  "ITHBuySell"  => array(
              "t_name_cn"=>"投信买卖超",
              "f_uni"=>array("symbol","x_ymd","x_list_code"),
              "para"=>array(
                "tdb"=>array(
                          "f_name_cn"=>"证券类型",
                          "list"=>"stock"
                    ),
              )
            ),
  "FRN3BuySell"  => array(
              "t_name_cn"=>"法人汇总",
              "f_uni"=>array("symbol","x_ymd","x_list_code"),
              "para"=>array(
                "tdb"=>array(
                          "f_name_cn"=>"证券类型",
                          "list"=>"stock"
                    ),
              )
            ),
  "DLRDealerMarket"  => array(
              "t_name_cn"=>"自营商进出",
              "f_uni"=>array("symbol","x_ymd","x_dlr_nm_c_short"),
              "para"=>array(
                "tdb"=>array(
                          "f_name_cn"=>"证券类型",
                          "list"=>"stock"
                    ),
              )
            ),
  // 融资融券
  "CRDchange"  => array(
              "t_name_cn"=>"资券增减表",
              "f_uni"=>array("symbol","x_ymd"),
              "para"=>array(
                "tdb"=>array(
                          "f_name_cn"=>"证券类型",
                          "list"=>"stock"
                    ),
              )
            ),
  "Industry"  => array(
              "t_name_cn"=>"",
              "f_uni"=>array("symbol","x_code"),
              "para"=>array(
                "tdb"=>array(
                          "f_name_cn"=>"证券类型",
                          "list"=>"misc"
                    ),
              )
            ),

  "TopStockRevenue"  => array(
              "t_name_cn"=>"营收余额",
              "f_uni"=>array("symbol","x_ym","x_list_code"),
              "para"=>array(
                "pa2"=>array(
                          "f_name_cn"=>"资料笔数",
                          "list"=>500
                ),
                "type"=>array(
                          "f_name_cn"=>"类型",
                          "list"=>3
                ),
              )
            ),
  "CrdInduBuySell"  => array(
              "t_name_cn"=>"融资融券余额",
              "f_uni"=>array("symbol","x_ymd","x_industry_name"),
              "para"=>array(
                "tdb"=>array(
                          "f_name_cn"=>"证券类型",
                          "list"=>"stock"
                    ),
              )
            ),
  // 排行榜
  "TopEpsQ"  => array(
              "t_name_cn"=>"季报EPS排行",
              "f_uni"=>array("symbol","x_yq","x_list_code"),
              "para"=>array(
                "pa2"=>array(
                          "f_name_cn"=>"资料笔数",
                          "list"=>200
                ),
                "type"=>array(
                          "f_name_cn"=>"类型",
                          "list"=>3
                ),
              )
            ),
  "TopEpsY"  => array(
              "t_name_cn"=>"年报EPS排行",
              "f_uni"=>array("symbol","x_yq","x_list_code"),
              "para"=>array(
                "pa2"=>array(
                          "f_name_cn"=>"资料笔数",
                          "list"=>200
                ),
                "type"=>array(
                          "f_name_cn"=>"类型",
                          "list"=>3
                ),
              )
            ),
);
$dapan_symbol_arr = array("TWNTSE","TWNOTC");

// 获取参数列表
require_once 'Console/Getopt.php';
$_options = Console_Getopt::getopt($argv, 's:t:', array());
$_o = array();
if (!PEAR::isError($_options)) {
  foreach ($_options[0] as $l_v){
    $_o[$l_v[0]] = $l_v[1];
  }
}

$symbol = (!empty($_o["s"])) ? $_o["s"] : "";
$t_arr = (!empty($_o["t"])) ? array($_o["t"]=>$table_name_arr[$_o["t"]]) : $table_name_arr;

$dbR = new DBR();
$dbW = new DBW();

if ((!empty($_o["s"]))) {
  procOnesymbol($dbR, $dbW, $t_arr, $symbol, $_G_timeout,$_G_cookie_arr);
}else {
  // 大盘
  foreach ($dapan_symbol_arr as $sym_or_secid){
    procOnesymbol($dbR, $dbW, $t_arr, $sym_or_secid, $_G_timeout,$_G_cookie_arr);
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
        $l_comm_url = COMM_URL.cutTableN($tbl_name)."&".$l_f_o_str;

        // 目前只支持一轮循环,多个参数循环的以后再优化
        if (!empty($l_f_a)) {
          if (1==count($l_f_a)) {
            foreach (current($l_f_a) as $l_v){
              $uni_arr_n = $uni_arr;  // 防止累积
              $l_f_i = key($l_f_a);
              $l_url = $l_comm_url.$l_f_i."=".$l_v."&symbol=".$sym_or_secid;
              $uni_arr_n[] = $l_f_i;
              echo date("Y-m-d H:i:s"). " " .$l_url."\r\n";
              proc_grab($dbR, $dbW, $uni_arr_n,$sym_or_secid, TABLENAME_PREF.$tbl_name,$l_url,$t_name_cn,array($l_f_i=>$l_v),$_G_timeout,$_G_cookie_arr);

              usleep(300);
            }
          }else {
            echo date("Y-m-d H:i:s"). " " ."l_f_a count>1"."\r\n";
          }
        }else {
          $l_url = $l_comm_url."&symbol=".$sym_or_secid;
          echo date("Y-m-d H:i:s"). " " .$l_url."\r\n";
          proc_grab($dbR, $dbW, $uni_arr,$sym_or_secid, TABLENAME_PREF.$tbl_name,$l_url,$t_name_cn,array(),$_G_timeout,$_G_cookie_arr);
          usleep(300);
        }
      }else {
        $l_url = COMM_URL.cutTableN($tbl_name)."&symbol=".$sym_or_secid;
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

//
function insertrecord(&$dbR, &$dbW, &$l_tick, $tick_tbl_name,$unique_arr,$a_id,$t_name_cn,$add_field){
  // 获取字段个数
  $l_m_arr = get_simp_elem($l_tick,false);

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
    $data_arr["symbol"] = $a_id;
    $dbW->table_name = $tick_tbl_name;
    inserone($dbW, $data_arr,$unique_arr); // 唯一性条件可以先不用给出
  }

  unset($l_m_arr);
  unset($data_arr);
}

function get_comp_info(&$dbR, &$dbW, &$l_xml,$tick_tbl_name, $uni_arr,$a_id,$a_url,$t_name_cn,$add_field){
  // 获取tick信息
  foreach ($l_xml->find("FA",0)->find("Record") as $l_ticks) {
    $l_text = trim($l_ticks->innertext);
    if (!empty($l_text)) {
      insertrecord($dbR, $dbW, $l_ticks,$tick_tbl_name,$uni_arr,$a_id,$t_name_cn,$add_field);
      usleep(60000);
    }else {
      echo date("Y-m-d H:i:s"). " " ."a_id $a_id url $a_url record content empty! \r\n";
    }

    // 清下内存
    $l_ticks->clear();unset($l_ticks);
  }
}

// 获取详细信息
function getdetail(&$dbR,&$dbW,$content,$uni_arr,$a_id,$table_name,$a_url,$t_name_cn,$add_field){
  if (false!==strpos($content, "<Record")) {
    // 替换掉不匹配的a标签
    if (false!==strpos($content, "<a") && false===strpos($content, "</a")) {
      $content = str_replace(array("<a","<A"),"",$content);
    }

    // 对方提供的是utf8编码的
    $l_xml = str_get_html($content);
    get_comp_info($dbR, $dbW,  $l_xml, $table_name, $uni_arr,$a_id,$a_url,$t_name_cn,$add_field);

    // 清下内存
    $l_xml->clear();unset($l_xml);
  }else {
    echo date("Y-m-d H:i:s"). " " ."a_id: $a_id url: $a_url record empty!"."\r\n";
  }
}

function cutTableN($a_str,$sep="__"){
  if (false!==strpos($a_str,$sep)) {
    $l_t = explode($sep,$a_str);
    return $l_t[0];
  }else {
    return $a_str;
  }
}
