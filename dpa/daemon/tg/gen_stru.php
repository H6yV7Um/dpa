<?php
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
  "DLRBuySell__S"  => array(
              "t_name_cn"=>"券商买卖超明细",  //
              "f_uni"=>array("symbol","x_ymd","x_title","x_name"),
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
/**
 * 获取行业分类, 产业与概念中心
 * http://210.67.12.98/FA/getCategoryTSE.aspx
 */
require_once("./system.conf.php");
require_once("./func.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");

// 修改数据库连接信息
__gener_conf(INI_CONFIGS_PATH,"mysql_config.ini","trade_db_w","trade_db_w");


$dbR = new DBR();
$dbW = new DBW();
$p_id = 0;  // wind的项目id是1

$a_data_arr = $a_data_ar2 = array("source"=>"db","creator"=>2);  // 能在外部增加字段的
fill_table($dbR,$dbW,$a_data_arr,"all",TABLENAME_PREF."field_def",TABLENAME_PREF."table_def",$p_id);
fill_field($dbR,$dbW,$a_data_ar2,"all",TABLENAME_PREF."field_def",TABLENAME_PREF."table_def");




// 自动填充 table_def 表
function fill_table(&$dbR, &$dbW, $data_arr, $tbl_name="all", $f_def="field_def", $t_def="table_def", $p_id=0){
  if (""!=$tbl_name) {
    // 先获取所有的表
    $all_table = $dbR->getDBTbls();

    if ("all"==$tbl_name) {
      // 循环插入
      if (!empty($all_table)){
        foreach ($all_table as $l_table){
          if ($f_def != $l_table["Name"] && $t_def != $l_table["Name"] && TABLENAME_PREF . "project" != $l_table["Name"] && "dpool_check_db"!=$l_table["Name"])
          {
            // 只入库 tw_
            if (false!==strpos($l_table["Name"],"tw_")) {
              ins2table_def($dbW, $data_arr, $l_table["Name"], $f_def, $t_def, $p_id);
            }
          }
        }
      }
    }else {
      // 循环遍历
      if (!empty($all_table)){
        foreach ($all_table as $l_table){
          // 只插入特定的表
          if ($tbl_name == $l_table["Name"]) ins2table_def($dbW,$data_arr, $l_table["Name"], $f_def, $t_def, $p_id);
        }
      }
    }
  }
}

function getTblNameCN22($tbl_name,$table_name_arr){
  if (!empty($table_name_arr)) {
    if (TABLENAME_PREF==substr($tbl_name,0,strlen(TABLENAME_PREF))) {
      $l_tkey = substr($tbl_name,strlen(TABLENAME_PREF));
    }else {
      //$l_tkey = $tbl_name;
    }
    if (array_key_exists($l_tkey,$table_name_arr)) {
      $t_name_cn = $table_name_arr[$l_tkey]["t_name_cn"];
    }else {
      $t_name_cn = $tbl_name;
    }
  }else {
    $t_name_cn = $tbl_name;
  }
  return $t_name_cn;
}

// 往表定义表中插入数据
function ins2table_def(&$dbW, $a_data_arr, $a_tablename, $f_def="field_def", $t_def="table_def",$p_id=0){
  global $table_name_arr;
  $name_eng = $a_tablename;
  $name_cn = getTblNameCN22($name_eng,$table_name_arr);

  $dbW->table_name = $t_def;
  if($dbW->getExistorNot("name_eng='".$name_eng."'")){
    //continue;
  } else {
    // 不存在则插入数据库中
    $data_arr = array(
      "p_id"        => $p_id,
      "field_def_table"=> $f_def,
      "creator"     => convCharacter($_SESSION["user"]["id"],true),
      "createdate"    => date("Y-m-d"),
      "createtime"    => date("H:i:s"),
      "name_eng"     => trim($name_eng),
      "name_cn"     => convCharacter($name_cn,true)
    );
    $data_arr = array_merge($data_arr,$a_data_arr);  // 外面给出的数据可修改里面的参数
    if ($dbW->insertOne($data_arr)) {
      $last_id = $dbW->LastID();
    }else {
      echo $dbW->getSQL();
      echo "insert error!";
      print_r($data_arr);
    }
  }
  usleep(300);
}

// 自动填充 field_def 表
function fill_field(&$dbR, &$dbW, $data_arr, $want_tbl="all", $f_def="field_def", $t_def="table_def"){
  // 先获取表
  if (""!=$want_tbl) {
    if ("all"==$want_tbl) {
      // 自动完成所有表的导入，包括自身也需要导入
      $dbR->table_name = $t_def;
      $_tbls = $dbR -> getAlls();
      // 需要for 循环

      foreach ($_tbls as $_tbl){
        if ($_tbl["id"]>0) {
          ins2field_def($dbR,$dbW,$data_arr,$_tbl["id"],$f_def,$t_def);
        }
      }
    }else {
      // 具体的表名
      $dbR->table_name = $t_def;
      $_tbl = $dbR -> getOne("where name_eng = '$want_tbl'");

      if ($_tbl["id"]>0) {
        ins2field_def($dbR,$dbW,$data_arr,$_tbl["id"],$f_def,$t_def);
      }
    }
  }
}

// 往字段定义表中插入数据
function ins2field_def(&$dbR, &$dbW, $a_data_arr, $t_id=17, $f_def="field_def", $t_def="table_def"){
  // 在 table_def 中的 id
  $dbR->table_name = $t_def;
  $_tbl_name = $dbR->getOne("where id = $t_id");
  $all_field = $dbR->getTblFields($_tbl_name["name_eng"]);

  $dbW->table_name = $f_def;
  // 循环插入
  if (!empty($all_field))
  foreach ($all_field as $l_arr){
    $name_eng   = strtolower($l_arr["Field"]);   // 很特殊的key Tables_in_auto
    $name_cn   = $name_eng;     // 暂时用英文的
    $_type_all   = explode_type_length_attribute($l_arr);

    if($dbW->getExistorNot("t_id = $t_id  and name_eng='".$name_eng."'")){
      echo "t_id = $t_id  and name_eng='".$name_eng."' exist!".NEW_LINE_CHAR;
      continue;
    } else {
      // 不存在则插入数据库中
      $data_arr = array(
        "creator"     => convCharacter($_SESSION["user"]["id"],true),
        "createdate"    => date("Y-m-d"),
        "createtime"    => date("H:i:s"),
        "t_id"        => $t_id,
        "name_eng"     => trim($name_eng),
        "name_cn"     => convCharacter($name_cn,true),
        "is_null"      => $l_arr["Null"],
        "key"        => $l_arr["Key"],
        "extra"        => $l_arr["Extra"],
        "type"        => $_type_all["type"],
        "length"      => $_type_all["length"],
        "attribute"      => $_type_all["attribute"],
        "default"      => convCharacter($l_arr["Default"],true)
      );
      $data_arr = array_merge($data_arr,$a_data_arr);  // 外面给出的数据可修改里面的参数
      if ($dbW->insertOne($data_arr)) {
        $last_id = $dbW->LastID();
      }else {print_r($data_arr);
        echo $dbW->getSQL();exit;
        echo " insert error!";

      }
    }
    usleep(300);
  }
}


//define("PMA_MYSQL_INT_VERSION",51000);
function explode_type_length_attribute(&$row){

  $type             = $row['Type'];
  $type_and_length  = PMA_extract_type_length($row['Type']);

  // reformat mysql query output - staybyte
  // loic1: set or enum types: slashes single quotes inside options
  if (preg_match('@^(set|enum)\((.+)\)$@i', $type, $tmp)) {
    $tmp[2]      = substr(preg_replace('@([^,])\'\'@', '\\1\\\'', ',' . $tmp[2]), 1);
    $type         = $tmp[1] . '(' . str_replace(',', ', ', $tmp[2]) . ')';
        $type         = htmlspecialchars($type);  // for the case ENUM('&#8211;','&ldquo;')
    $binary       = 0;
    $unsigned     = 0;
    $zerofill     = 0;
    $timestamp    = 0;
  } else {
    // strip the "BINARY" attribute, except if we find "BINARY(" because
    // this would be a BINARY or VARBINARY field type
    if (!preg_match('@BINARY[\(]@i', $type)) {
      $type         = preg_replace('@BINARY@i', '', $type);
    }
    $type         = preg_replace('@ZEROFILL@i', '', $type);
    $type         = preg_replace('@UNSIGNED@i', '', $type);
    if (empty($type)) {
      $type     = ' ';
    }

    if (!preg_match('@BINARY[\(]@i', $row['Type'])) {
      $binary           = stristr($row['Type'], 'blob') || stristr($row['Type'], 'binary');
    } else {
      $binary           = false;
    }

    $unsigned     = stristr($row['Type'], 'unsigned');
    $zerofill     = stristr($row['Type'], 'zerofill');
    $timestamp    = ("timestamp"==$row['Type'])?true:false;
  }

  $attribute     = ' ';
  if ($binary) {
    $attribute = 'BINARY';
  }
  if ($unsigned) {
    $attribute = 'UNSIGNED';
  }
  if ($zerofill) {
    $attribute = 'UNSIGNED ZEROFILL';
  }
  // MySQL 4.1.2+ TIMESTAMP options
  // (if on_update_current_timestamp is set, then it's TRUE)
  if ($timestamp) {
    $attribute = 'ON UPDATE CURRENT_TIMESTAMP';
  }

  if (""==trim($row['Default'])) {
    if ($row['Null'] == 'YES') {
      $row['Default'] = 'NULL';
    }
  }

  if ($type_and_length['type'] == 'bit') {
    $row['Default'] = PMA_printable_bit_value($row['Default'], $type_and_length['length']);
  }


  return array_merge($type_and_length,array("attribute"=>$attribute));
}



/**
 * Converts a bit value to printable format;
 * in MySQL a BIT field can be from 1 to 64 bits so we need this
 * function because in PHP, decbin() supports only 32 bits
 *
 * @uses    ceil()
 * @uses    decbin()
 * @uses    ord()
 * @uses    substr()
 * @uses    sprintf()
 * @param   numeric $value coming from a BIT field
 * @param   integer $length
 * @return  string  the printable value
 */
function PMA_printable_bit_value($value, $length) {
  $printable = '';
  for ($i = 0; $i < ceil($length / 8); $i++) {
    $printable .= sprintf('%08d', decbin(ord(substr($value, $i, 1))));
  }
  $printable = substr($printable, -$length);
  return $printable;
}
/**
 * Extracts the true field type and length from a field type spec
 *
 * @uses    strpos()
 * @uses    chop()
 * @uses    substr()
 * @param   string $fieldspec
 * @return  array associative array containing the type and length
 */
function PMA_extract_type_length($fieldspec) {
  $first_bracket_pos = strpos($fieldspec, '(');
  if ($first_bracket_pos) {
    $length = chop(substr($fieldspec, $first_bracket_pos + 1, (strpos($fieldspec, ')') - $first_bracket_pos - 1)));
    $type = chop(substr($fieldspec, 0, $first_bracket_pos));
  } else {
    $type = $fieldspec;
    $length = '';
  }
  return array(
    'type' => $type,
    'length' => $length
  );
}
