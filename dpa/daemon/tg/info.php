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
$table_name_arr = array(
  //  基本资料
  "CompanyBasic"  => array(
              "t_name_cn"=>"基本资料",
              "f_uni"=>array("symbol"),
            ),
  "DRTHldStk"    => array(
              "t_name_cn"=>"内部人(或董监持股)",// 或董监持股
              "f_uni"=>array("symbol","x_holder_name","x_title_nm_c"),
            ),
  "Operation"    => array(
              "t_name_cn"=>"最近5年营运状态",
              "f_uni"=>array("symbol","x_year"),
            ),
  "SimpleEarningProfits"=> array(
              "t_name_cn"=>"获利能力",
              "f_uni"=>array("symbol","x_yq","x_ratio_code"),
            ),
  "SimpleOperation"=> array(
              "t_name_cn"=>"经营能力",
              "f_uni"=>array("symbol","x_yq","x_ratio_code"),
            ),
  "CompanyMeet"    => array(
              "t_name_cn"=>"股东会",
              "f_uni"=>array("symbol","x_meet_ymd","x_meet_time"),
            ),

  // 业务营收
  "CompanySales"    => array(
              "t_name_cn"=>"营收盈余",
              "f_uni"=>array("symbol","x_ym"),
            ),
  "ProductOprCur"    => array(
              "t_name_cn"=>"产品结构",
              "f_uni"=>array("symbol","x_ym","x_product_name"),
            ),
  "ProductOprAcc"    => array(
              "t_name_cn"=>"产品结构2",
              "f_uni"=>array("symbol","x_ym","x_product_name"),
            ),
  // 股本股权, 股东会 跟上面的重复
  "CompanyAccX"    => array(
              "t_name_cn"=>"股本形成",
              "f_uni"=>array("symbol","x_raise_b_ym"),
            ),

  // 董监持股,同内部人重复
  "DRTCMP"      => array(
              "t_name_cn"=>"董监持股",
              "f_uni"=>array("symbol","x_ym"),
            ),
  "StockTransfer"    => array(
              "t_name_cn"=>"持股转让",
              "f_uni"=>array("symbol","x_ymd","x_title","x_name","x_rpt_trn_stkno","x_rpt_stkno"),
            ),

  "CmpTransfer"    => array(
              "t_name_cn"=>"持股转让",
              "f_uni"=>array("symbol","x_ym"),
              "para"=>array(
                "pa2"=>array(
                          "f_name_cn"=>"期间",
                          "list"=>500,    // 历史资料
                ),
                "type"=>array(
                          "f_name_cn"=>"类型",
                          "list"=>3
                ),
              )
            ),
  "DRTcmpSetX"  => array(
              "t_name_cn"=>"质押设定",
              "f_uni"=>array("symbol","x_ymd","x_title","x_drt_holder","x_pledger","x_pledge"),
            ),
  // 投资动态
  "StockTransaction"  => array(
              "t_name_cn"=>"有价证券投资", // stock
              "f_uni"=>array("symbol","x_ymd","x_object"),
            ),
  "FundTransaction"  => array(
              "t_name_cn"=>"有价证券投资", // fund
              "f_uni"=>array("symbol","x_ymd","x_object"),
            ),
  "BondTransactionn"  => array(
              "t_name_cn"=>"有价证券投资", // bond
              "f_uni"=>array("symbol","x_ymd","x_object","x_type"),
            ),
  "BuyBack"  => array(
              "t_name_cn"=>"买回库藏股",
              "f_uni"=>array("symbol","x_dir_ymd","x_buy_limit"),
            ),
  "InvLong"  => array(
              "t_name_cn"=>"长期投资明细",
              "f_uni"=>array("symbol","x_yq","x_inv_nm"),
            ),
  "IvnCN"  => array(
              "t_name_cn"=>"转投资大陆",
              "f_uni"=>array("symbol","x_yq","x_can_corp_nm"),
            ),
  "LandTxnX"  => array(
              "t_name_cn"=>"土地资产异动",
              "f_uni"=>array("symbol","x_ymd" ,"x_trns_obj" ,"x_rel_type" ,"x_ivn_type" ,"x_land_type"),
            ),
  "InvSubStkX"  => array(
              "t_name_cn"=>"子公司活动", //
              "f_uni"=>array("symbol","x_ymd","x_rel_type","x_ivn_type","x_object","x_sub_company"),
            ),
  // 债务融资
  "InvLoan"  => array(
              "t_name_cn"=>"长短期借款", //
              "f_uni"=>array("symbol","x_yq","x_type","x_loaner","x_loan_desc","x_contract_date"),
            ),

  // 财务报表(累计)
  "SimpleBalSheetAcc"  => array(
              "t_name_cn"=>"简明财务报表",  //
              "f_uni"=>array("symbol","x_yq","x_acc_code"),
            ),
  "SimpleProfitLossAcc"  => array(
              "t_name_cn"=>"简明财务报表",//
              "f_uni"=>array("symbol","x_yq","x_acc_code"),
            ),
  "CashFlowStatement"  => array(
              "t_name_cn"=>"简明财务报表",//
              "f_uni"=>array("symbol","x_yq","x_acc_code"),
            ),
  "BalSheetAcc"  => array(
              "t_name_cn"=>"资产负债表",
              "f_uni"=>array("symbol","x_yq","x_acc_code"),
            ),
  "ProfitLossAcc"  => array(
              "t_name_cn"=>"损益表",
              "f_uni"=>array("symbol","x_yq","x_acc_code"),
            ),
  "CashFlowAcc"  => array(
              "t_name_cn"=>"现金流量表",
              "f_uni"=>array("symbol","x_yq","x_acc_code"),
            ),
  "CpaOpinion"  => array(
              "t_name_cn"=>"会计师意见",  //
              "f_uni"=>array("symbol","x_yq","x_office_name","x_check_type"),
            ),
  // 财务报表(单季)
  "ProfitLossSin"  => array(
              "t_name_cn"=>"损益表",
              "f_uni"=>array("symbol","x_yq","x_acc_code"),
            ),
  "ProfitLossSinQ"  => array(
              "t_name_cn"=>"简易损益表(近八季)",
              "f_uni"=>array("symbol","x_yq","x_acc_code"),
            ),
  "CashFlowSin"  => array(
              "t_name_cn"=>"现金流量表",
              "f_uni"=>array("symbol","x_yq","x_acc_code"),
            ),

  // 财务指标(累计)
  "SimpleAccRatQ"  => array(
              "t_name_cn"=>"简明指标",
              "f_uni"=>array("symbol","x_yq","x_ratio_code"),
            ),

  // 财务指标(单季)
  "SimpleSinRatQ"  => array(
              "t_name_cn"=>"简明指标",
              "f_uni"=>array("symbol","x_yq","x_ratio_code"),
            ),

  // 筹码面
  "StockDLRBuySell"  => array(
              "t_name_cn"=>"券商买卖超明细",  //
              "f_uni"=>array("symbol","x_ymd"),
              "para"=>array(
                "tdb"=>array(
                          "f_name_cn"=>"类型",
                          "list"=>"stock",
                ),
              )
            ),

  // ----------------------------------------------------------------

  //  基本资料
  "StockDivided"  => array(
              "t_name_cn"=>"股利",
              "f_uni"=>array("symbol","x_pay_year"),
              "para"=>array(
                "pa2"=>array(
                          "f_name_cn"=>"期间",
                          "list"=>"H",    // 历史资料
                ),
                "pa3"=>array(
                          "f_name_cn"=>"除权息",
                          "list"=>array(
                            "Cash",  // 现金股利
                            "Stock",// 股票股利
                      )
                ),
                "type"=>array(
                          "f_name_cn"=>"类型",
                          "list"=>4
                ),
              )
            ),

  // 业务营收

  // 股本股权, 股东会

  // 董监持股
  "DRTcmpPlg"  => array(
              "t_name_cn"=>"质押设定",
              "f_uni"=>array("symbol","x_ym"),
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

  // 投资动态

  // 债务融资

  // 财务报表(累计), 唯一性条件同 AccRatQ 不一样
  "AccRatQ__A"  => array(
              "t_name_cn"=>"营益分析表(近八季)",
              "f_uni"=>array("symbol","x_yq","x_acc_code"),
              "para"=>array(
                "pa2"=>array(
                          "f_name_cn"=>"财务指标类别",
                          "list"=>"A",  // 营益分析表
                ),
                "type"=>array(
                          "f_name_cn"=>"类型",
                          "list"=>2
                ),
              )
            ),
  // 财务指标(累计)
  "AccRatQ"  => array(
              "t_name_cn"=>"获利能力",
              "f_uni"=>array("symbol","x_yq","x_ratio_code"),
              "para"=>array(
                "pa2"=>array(
                          "f_name_cn"=>"财务指标类别",
                          "list"=>array(
                            "P",  // 获利能力
                            "O",  // 经营能力
                            "D",  // 偿债能力
                            "F",  // 财务结构
                      )
                ),
                "type"=>array(
                          "f_name_cn"=>"类型",
                          "list"=>2
                ),
              )
          ),
  // 财务报表(单季)

  // 财务指标(单季)
  "SinRatQ"  => array(
              "t_name_cn"=>"获利能力,经营能力,偿债能力等",
              "f_uni"=>array("symbol","x_yq","x_ratio_code"),
              "para"=>array(
                "pa2"=>array(
                          "f_name_cn"=>"财务指标类别",
                          "list"=>array(
                            "P",  // 获利能力
                            "O",  // 经营能力
                            "D",  // 偿债能力
                      )
                ),
                "type"=>array(
                          "f_name_cn"=>"类型",
                          "list"=>2

                ),
              )
            ),

  // 筹码面
  "CRDStock"  => array(
              "t_name_cn"=>"融资融券变动",
              "f_uni"=>array("symbol","x_ymd"),
              "para"=>array(
                "pa2"=>array(
                          "f_name_cn"=>"资料笔数",
                          "list"=>500
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
  "FRN3Stock"  => array(
              "t_name_cn"=>"法人持股",
              "f_uni"=>array("symbol","x_ymd"),
              "para"=>array(
                "pa2"=>array(
                          "f_name_cn"=>"资料笔数",
                          "list"=>500
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
  "DEPSTOCKW"  => array(
              "t_name_cn"=>"集保库存",
              "f_uni"=>array("symbol","x_ymw"),
              "para"=>array(
                "pa2"=>array(
                          "f_name_cn"=>"资料笔数",
                          "list"=>500
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
);

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

if (!empty($_o["s"])) {
  procOnesymbol($dbR, $dbW, $t_arr, $symbol, $_G_timeout,$_G_cookie_arr);// 当个个股
}else {
  main($dbR, $dbW, $t_arr, $_G_timeout, $_G_cookie_arr);
}

function main(&$dbR, &$dbW, $table_name_arr, $_G_timeout,$_G_cookie_arr=array() ){
  // 获取所有的 指数和分类
  $dbR->table_name = TABLENAME_PREF."CompanyBasic";
  $sect_arr = $dbR->getAlls("  order by symbol ","symbol");
  // 逐一解析
  foreach ($sect_arr as $l_sect){
    $sym_or_secid = $l_sect["symbol"];
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

function proc_grab(&$dbR, &$dbW, $uni_arr,$a_id, $tbl_name,$l_url,$t_name_cn,$add_field,$_G_timeout,$_G_cookie_arr){
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
  //$l_r_n = autoCreateField($dbR, $dbW, $tick_tbl_name, $l_m_arr,$t_name_cn);

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
      usleep(6000);
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
