<?php
/**
 * 功能描述：
 *   导入统计局网站提供的内地行政区域代码等信息, 总共 3511条记录
 *
 * 省级：
    (31)  SELECT * FROM `region_sheng` WHERE `code_city` =0 AND `code_quxian` =0 -- 含4个直辖市
 * 市级:
    (344) SELECT * FROM `region_sheng` WHERE `code_city` !=0 AND `code_quxian` =0 -- 含4*2个直辖市的'市辖区','县'
    (336) SELECT * FROM `region_sheng` WHERE `code_city` !=0 AND `code_quxian` =0 AND name_cn not in ('市辖区','县');
    (340) + 4 个直辖市 （北京、天津、上海、重庆）
 * 区县:
    (3136)SELECT * FROM `region_sheng` WHERE `code_city` !=0 AND `code_quxian` !=0
    (2855)SELECT * FROM `region_sheng` WHERE `code_city` !=0 AND `code_quxian` !=0 AND name_cn not in ('市辖区','县');
    (281) SELECT * FROM `region_sheng` WHERE `code_city` !=0 AND `code_quxian` !=0 AND name_cn in ('市辖区','县');


php D:/www/dpa/common/tools/region_daoru.php

php /data0/deve/runtime/common/tools/region_daoru.php

 *
 */
ini_set('memory_limit', '200M');

if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)) )
{
  require_once("D:/www/dpa/configs/system.conf.php");

  $l_arr = array(
    'ganji'   => array("D:/www/daily/2012/20120331/region_ganji.htm",  'name_eng_ganji'),
    '58'   => array("D:/www/daily/2012/20120331/region_58.htm",     'name_eng_58'),
    'zhantai'=> array("D:/www/daily/2012/20120331/region_zhantai.htm",  'name_eng_zhantai'),
  );
  $l_file_sheng = "D:/www/daily/2012/20120331/region.txt";
  $l_file_guoji = "D:/www/daily/2012/20120331/guojiquhao.htm";
  $l_dsn  = "dpa3308";
  $py_data_path = "D:/www/pear/py.dat";
}
else
{
  require_once("/data0/deve/runtime/configs/system.conf.php");
  $l_arr = array(
    'ganji'   => array("/home/www/daily/2012/20120331/region_ganji.htm",  'name_eng_ganji'),
    '58'   => array("/home/www/daily/2012/20120331/region_58.htm",     'name_eng_58'),
    'zhantai'=> array("/home/www/daily/2012/20120331/region_zhantai.htm",  'name_eng_zhantai'),
  );
  $l_file_sheng = "/home/www/daily/2012/20120331/region.txt";
  $l_file_guoji = "/home/www/daily/2012/20120331/guojiquhao.htm";
  $l_dsn  = "dpa";
  $py_data_path = "/usr/local/webserver/php/lib/php/py.dat";
}
require_once("common/functions.php");
require_once("common/global_func.php");
require_once("common/grab_func.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");
require_once("PinYin.class.php");
require_once("HTTP/Request.php");
require_once("simple_html_dom.php");

// 执行的顺序最好别颠倒，在保证前面有数据的前提下，才执行后续的动作
//guojiquhao();
//cn_xingzheng_sheng();
//geturlinfo($l_arr,'ganji');
//geturlinfo($l_arr,'58');
//geturlinfo($l_arr,'zhantai');

// 生活频道 批量添加以城市为根栏目的 票务栏目页、栏目页、房屋栏目页、兼职栏目页
//add_lanmu_shenghuo();
//add_lanmu_shenghuo(array("lanmu_biao_name_cn"=>"房屋栏目页","lanmu_name_cn"=>"房屋"));
//add_lanmu_shenghuo(array("lanmu_biao_name_cn"=>"票务栏目页","lanmu_name_cn"=>"票务"));
add_lanmu_shenghuo(array("lanmu_biao_name_cn"=>"兼职栏目页","lanmu_name_cn"=>"兼职"));
function add_lanmu_shenghuo($a_arr=array("lanmu_biao_name_cn"=>"栏目页","lanmu_name_cn"=>"交友聚会"))
{
  // 直接从栏目页字段算法获取相应数据，只不过是自动轮训的进行 所属城市和 栏目名称的双重轮询
  $l_name0_r = $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R'];
  $dbR = new DBR($l_name0_r);
  $l_err = $dbR->errorInfo();
  if ($l_err[1]>0){
    // 数据库连接失败后
    echo date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
    return null;
  }
  $dbR->table_name = TABLENAME_PREF."project";
  $p_arr_shenghuo = $dbR->GetOne("where name_cn='生活频道'");

  // 生活频道的城市列表
  $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr_shenghuo);
  $dbR->dbo = &DBO('shenghuo', $dsn);
  $dbR->table_name = TABLENAME_PREF."table_def";
  $t_arr_lanmu = $dbR->GetOne("where name_cn='".$a_arr["lanmu_biao_name_cn"]."'");
  $t_arr_city  = $dbR->GetOne("where name_cn='城市列表'");
  $t_arr_lmpz  = $dbR->GetOne("where name_cn='栏目配置'");

  // 获取栏目页的字段算法 特别需要提取出来 "所属城市"和"栏目名称" 两个字段进行循环的
  $dbR->table_name = TABLENAME_PREF. "field_def";
  $l_lanmu_ziduan = $dbR->getAlls("where t_id=".$t_arr_lanmu["id"]." and status_='use' ");

  /*// 各个字段进行字段算法
  require_once("common/lib/Parse_Arithmetic.php");
  // 模拟一些数据
  $actionMap = $actionError = new stdClass();
  $arr = $request = $response = $form = $get = $cookie = array();
  $a_p_self_ids = array(
    1=>array("ziduan"=>"p_id"),
    2=>array("ziduan"=>"t_id"),
  );
  $request['p_id'] = $p_arr_shenghuo["id"];
  $request['t_id'] = $t_arr_lanmu["id"];
  $p_self_info = DbHelper::getProTblFldArr($dbR, $request, $a_p_self_ids);
  $arr = array_merge($arr, $p_self_info);
  if(!array_key_exists("f_info",$arr)) return null;
  $arr["dbR"] = $dbR;
  $arr["table_name"] = $table_name;
  $arr["TBL_def"] = TABLENAME_PREF."table_def";
  $arr["FLD_def"] = TABLENAME_PREF."field_def";
  // 执行算法
  Parse_Arithmetic::parse_for_list_form($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

  // 返回的数据中 $arr["f_info"]["s_shu_chengshi"]["length"] , ["aups_f097"]["length"]
  // 或者:$response["arithmetic"]["s_shu_chengshi"]["pa_val"],["aups_f097"]["pa_val"]
  if (!empty($response["arithmetic"]["s_shu_chengshi"]["pa_val"]) &&
    !empty($response["arithmetic"]["aups_f097"]["pa_val"])) {
    foreach ($response["arithmetic"]["s_shu_chengshi"]["pa_val"] as $l_city_eng){
      foreach ($response["arithmetic"]["aups_f097"]["pa_val"] as $l_lanmu_cn_eng=>$l_lanmu_cn){
        //
      }
    }
  }*/

  // 当前直接copy一下算法，然后进行拼装，分别去城市列表中和栏目配置中获取数据
  $dbR->table_name = $t_arr_city["name_eng"];
  $l_city_arr = $dbR->getAlls("where status_='use' ",'name_eng,name_cn');
  //print_r($l_city_arr);

  $dbR->table_name = $t_arr_lmpz["name_eng"];
  $l_lmpz_arr = $dbR->getAlls("where aups_f078='".$a_arr["lanmu_name_cn"]."' or aups_f070='".$a_arr["lanmu_name_cn"]."' and status_='use' ",'aups_f070');
  //print_r($l_lmpz_arr);

  $dbW = new DBW($p_arr_shenghuo);
  if (!empty($l_city_arr) && !empty($l_lmpz_arr)) {
    foreach ($l_city_arr as $l_k => $l_ct){
      foreach ($l_lmpz_arr as $l_pz){
        // 判断是否存在，不存在直接 insert
        $l_vals = array();
        $l_vals["s_shu_chengshi"]   = $l_ct["name_eng"];
        $l_vals["aups_f097"]     = $l_pz['aups_f070'];
        $l_exist_c = cString_SQL::getUniExist($l_vals, array('s_shu_chengshi','aups_f097'));
        $dbW->table_name = $t_arr_lanmu["name_eng"];
        $l_exi_one = $dbW->getExistorNot($l_exist_c);
        if (PEAR::isError($l_exi_one)) {
          echo " error message： " .$l_exi_one->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
          return ;
        }

        // 不存在则插入
        if (empty($l_exi_one)) {
          $l_vals["creator "]   = "admin";
          $l_vals["createdate "]   = date("Y-m-d");
          $l_vals["createtime "]   = date("H:i:s");
          $dbW->insertOne($l_vals);
          $l_err = $dbW->errorInfo();
          if ($l_err[1]>0){
            echo date("Y-m-d H:i:s"). " ".$dbW->getSQL() ." insert to error!" . var_export($l_err, true) . NEW_LINE_CHAR;
            return null;
          }else {
            $son_parent_id = $dbW->LastID();
            echo date("Y-m-d H:i:s"). " insert succ! id:".$son_parent_id. NEW_LINE_CHAR;
          }
        }
        usleep(1000);
        /*$request["所属城市"] = $form["所属城市"] = $l_ct["name_eng"];
        $request["栏目名称"] = $form["栏目名称"] = $l_pz['aups_f070'];
        // 拼装城市和栏目名称数据进行提交
        $l_doPath = "document_add";
            $if_is_open_page = 1;  // 跳过身份认证进入后续操作
            $l_ret = MoNiDO($l_doPath, $l_ptd_info_arr,$request,$response,$form,$get,$cookie, $if_is_open_page);
            print_r($l_ret);exit;*/
      }
    }
  }

}

// 用于导入城市代码到生活库
//fill_region_2_shenghuo();
function fill_region_2_shenghuo(){
  $l_name0_r = $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R'];
  $dbR = new DBR($l_name0_r);
  $l_err = $dbR->errorInfo();
  if ($l_err[1]>0){
    // 数据库连接失败后
    echo date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
    return null;
  }
  $dbR->table_name = TABLENAME_PREF."project";
  $p_arr_gongyong = $dbR->GetOne("where name_cn='共用数据'");
  $p_arr_shenghuo = $dbR->GetOne("where name_cn='生活频道'");

  // 从共用数据库中获取到数据
  $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr_gongyong);
  $dbR->dbo = &DBO('gongyong', $dsn);
  $table_name = "region_sheng";
  $dbR->table_name = $table_name;
  $l_shengfen = $dbR->getAlls("where code_quxian=0 and status_='use' and name_cn not in ('市辖区', '县') ");
  //$L_11 = cArray::Index2KeyArr($l_shengfen,array("value"=>"name_cn"));
  //print_r($L_11);

  // 循环一下，重新处理
  $l_zhixiashi = array(11,12,31,50);  // 北京,天津,上海,重庆 四个直辖市的code_sheng代码
  $for_json = array();
  foreach ($l_shengfen as $vals){
    if (0==$vals['code_city']) {
      // 省级名称不应该留下, 而直辖市应该留下
      if (!in_array($vals['code_sheng'],$l_zhixiashi)) {
        continue ;
      }
    }else {
      if (false!==strpos($vals['name_cn'],'直辖')) {
        continue ;
      }
    }
    $for_json[] = $vals;
  }
  //$L_12 = cArray::Index2KeyArr($for_json,array("value"=>"name_cn"));
  //print_r($L_12);$L_13 = (array_diff($L_11,$L_12));print_r($L_13);echo count($L_13);exit;

  // 进行入库处理
  $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr_shenghuo);
  $dbR->dbo = &DBO('shenghuo', $dsn);
  $dbR->table_name = TABLENAME_PREF."table_def";
  $t_arr_gongyong = $dbR->GetOne("where name_cn='城市列表'");

  $dbW = new DBW($p_arr_shenghuo);
  $dbW->table_name = $t_arr_gongyong["name_eng"];

  // 逐条进行入库
  if (!empty($for_json)) {
    foreach ($for_json as $l_vals){

        // 存在则更新,
        $data_arr = array(
          "s_shu_xingqiu_id" => $l_vals["s_shu_xingqiu_id"],
          "s_shu_area_id" => $l_vals["s_shu_area_id"],
          "name_eng" => $l_vals["name_eng"],
          "name_cn" => $l_vals["name_cn"],
          "pingyin_shouzimu" => $l_vals["pingyin_shouzimu"],
          "code_sheng" => $l_vals["code_sheng"],
          "code_city" => $l_vals["code_city"],
          "code_quxian" => $l_vals["code_quxian"],
          "name_eng_58"     => $l_vals["name_eng_58"],
          "name_eng_ganji"   => $l_vals["name_eng_ganji"],
          "name_eng_zhantai"   => $l_vals["name_eng_zhantai"],
        );
        // 先判断数据库中是否存在这些数据，如果存在则进行更新，如果不存在则需要insert
        $l_exist_c = cString_SQL::getUniExist($l_vals, array('s_shu_xingqiu_id','s_shu_area_id','code_sheng','code_city','code_quxian'));
        $l_exi_one = $dbW->getExistorNot($l_exist_c);
        if (PEAR::isError($l_exi_one)) {
          echo " error message： " .$l_exi_one->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
          return ;
        }
        if (is_array($l_exi_one) && !empty($l_exi_one)) {
          $l_data_arr = array(
            "mender" => $l_vals["mender"],
            "menddate" => date("Y-m-d"),
            "mendtime" => date("H:i:s"),
          );
          $condition = ' `id` = '.$l_exi_one['id'];
          $dbW->updateOne(array_merge($data_arr,$l_data_arr),$condition);
          $l_err = $dbW->errorInfo();
               if ($l_err[1]>0){
            echo date("Y-m-d H:i:s"). " ".$dbW->getSQL() ." update data error!" . var_export($l_err, true) . NEW_LINE_CHAR;
          }
        }else {
          // 不存在则插入
          $l_data_arr = array(
            "creator" =>  $l_vals["creator"],
            "createdate" => date("Y-m-d"),
            "createtime" => date("H:i:s"),
          );
          $dbW->insertOne(array_merge($data_arr,$l_data_arr));
          $l_err = $dbW->errorInfo();
               if ($l_err[1]>0){
            echo date("Y-m-d H:i:s"). " ".$dbW->getSQL() ." insert data error!" . var_export($l_err, true) . NEW_LINE_CHAR;
          }
        }

    }

    echo "complete!".NEW_LINE_CHAR;
  }else {
    echo "empty!".NEW_LINE_CHAR;
  }

  return ;

}

// 用于更新数据库中 name_eng 字段
// fill_name_eng();
function fill_name_eng(){
  //
  $dbR = new DBR($GLOBALS['l_dsn']);
  $dbW = new DBW($GLOBALS['l_dsn']);
  $dbR->SetCurrentSchema("common_db");
  $dbW->SetCurrentSchema("common_db");
  $table_name = "region_sheng";
  $dbR->table_name = $table_name;
  $dbW->table_name = $table_name;
  $pinyin = new PinYin("UTF-8",$GLOBALS['py_data_path']);

  // 先取出全部记录，然后逐条进行name_eng的判断
  $l__row = $dbR->getAlls('', "id,name_eng,name_eng_58,pingyin_shouzimu,name_cn");

  if (!empty($l__row)) {

    foreach ($l__row as $l_vals){
      $condition = ' `id` = '.$l_vals['id'];
      $data_arr = array();
      // 先用58同城的拼音填充name_eng, 58没有，则采用中文拼音全称
      if (empty($l_vals['name_eng'])) {
        $l_name_eng_58 = trim($l_vals['name_eng_58']);
        if (!empty($l_name_eng_58)) {
          $data_arr = array(
            "name_eng"=>$l_name_eng_58,
            "pingyin_shouzimu"=>substr($l_name_eng_58,0,1),
          );
          $dbW->updateOne($data_arr,$condition);
          $l_err = $dbW->errorInfo();
          if ($l_err[1]>0){
            echo date("Y-m-d H:i:s") . "failed:" . $dbW->getSQL() .NEW_LINE_CHAR. $l_err[2]. ".".NEW_LINE_CHAR;
          }
        }else {
          // 直接用拼音
          $l_py = trim( $pinyin->getPY( trim($l_vals['name_cn']) ) );
          $data_arr = array(
            "name_eng"=>$l_py,
            "pingyin_shouzimu"=>substr($l_py,0,1),
          );
          $dbW->updateOne($data_arr,$condition);
          $l_err = $dbW->errorInfo();
          if ($l_err[1]>0){
            echo date("Y-m-d H:i:s") . "failed:" . $dbW->getSQL() .NEW_LINE_CHAR. $l_err[2]. ".".NEW_LINE_CHAR;
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

function geturlinfo($a_arr,$a_one='ganji'){
  $l_uri     = $a_arr[$a_one][0];
  $l_zd_name   = $a_arr[$a_one][1];
  if (empty($l_uri)) {
    return ;
  }

  $dbR = new DBR($GLOBALS['l_dsn']);
  $dbW = new DBW($GLOBALS['l_dsn']);
  $dbR->SetCurrentSchema("common_db");
  $dbW->SetCurrentSchema("common_db");
  $table_name = "region_sheng";
  $dbR->table_name = $table_name;
  $dbW->table_name = $table_name;

  $l_cont = file_get_contents($l_uri);
  $l_xml = str_get_html($l_cont);

  $l_r = array();
  $l_exist = $l_mul = array();
  // 所有的dd标签中的a标签中的中文名和链接的url输出为数组
  foreach($l_xml->find("a[href]") as $l_a){
    $l_href = $l_a->href;
    $l_domain = str_replace(array("http://","https://"),"",$l_href);
    $l_name = trim($l_a->innertext);
    // 清下内存
    $l_a->clear();unset($l_a);

    // 逐条检查是否存在于数据库中, 不存在于数据库中的则输出提示信息
    if ("吉林"==$l_name) $l_name = "吉林市";
    $l__row = $dbR->getAlls('where `code_quxian`=0 and name_cn like "'.$l_name.'%"');

    if (!empty($l__row)) {
      if (count($l__row)>1) {
        $l__row[] = $l_name . " - ". $l_href;
        $l_mul[] = $l__row;
      }else {
        // 此处需要更新 name_eng 和 pingyin_shouzimu 两个字段
        $l_name_eng = substr($l_domain,0,strpos($l_domain,"."));
        $l_shouzm = substr($l_name_eng,0,1);
        $data_arr = array(
          $l_zd_name=>$l_name_eng,
          //'pingyin_shouzimu'=>$l_shouzm,
        );
        $condition = ' `id` = '.$l__row[0]['id'];
        $dbW->updateOne($data_arr,$condition);
        //$l_exist[] = $l__row;
      }
    }else {
      // echo "not in db!".NEW_LINE_CHAR;
      //continue;
      $l_r[] = $l_name . " - ". $l_href;
    }
  }
  // 清下内存
  $l_xml->clear();unset($l_xml);

  //print_r($l_exist);
  print_r($l_mul);
  print_r($l_r);
  return $l_r;
}

function cn_xingzheng_sheng(){
  //
  $dbR = new DBR($GLOBALS['l_dsn']);
  $dbW = new DBW($GLOBALS['l_dsn']);
  $dbR->SetCurrentSchema("common_db");
  $dbW->SetCurrentSchema("common_db");
  $table_name = "region_sheng";
  $dbR->table_name = $table_name;
  $dbW->table_name = $table_name;

  if (file_exists($GLOBALS['l_file_sheng'])) {
    $l_cont = file($GLOBALS['l_file_sheng']);
  }else {
    $l_cont = "";
  }
  if (!empty($l_cont)) {
    foreach ($l_cont as $l_line){
      // 逐行处理
      $l_one = str_replace('&nbsp;', ' ', $l_line);
      $l_one = trim($l_one);
      // 将字符串截取成两段
      $l_code = substr($l_one, 0, strpos($l_one, " "));
      $l_cont = substr($l_one, strpos($l_one, " ")+1);

      //
      if (is_numeric($l_code)) {
        // 插入到数据库中去
        $data_arr = array(
          "xingzheng_quhua_daima"=>$l_code,
          "code_sheng"=>substr($l_code,0,2),
          "code_city"=>substr($l_code,2,2),
          "code_quxian"=>substr($l_code,4,2),
          // "name_eng"=>'bj',
          "name_cn"=>trim($l_cont),
          "s_shu_xingqiu_id"=>1,  //
          "s_shu_area_id"=>1,    // 所属国家或地区
          //"pingyin_shouzimu"=>"b",// 拼音首字母
        );
        $dbW->insertOne($data_arr);
        $l_err = $dbW->errorInfo();
        if ($l_err[1]>0){
          //
          echo date("Y-m-d H:i:s") . "failed:" . $dbW->getSQL() .NEW_LINE_CHAR. $l_err[2]. ".".NEW_LINE_CHAR;
          return null;
        }
        usleep(10);
      }else {
        echo "some thing error!" . NEW_LINE_CHAR;
      }

    }
  }
  echo "complete!".NEW_LINE_CHAR;
}

function guojiquhao(){
  //
  $dbR = new DBR($GLOBALS['l_dsn']);
  $dbW = new DBW($GLOBALS['l_dsn']);
  $dbR->SetCurrentSchema("common_db");
  $dbW->SetCurrentSchema("common_db");
  $table_name = "region_area";
  $dbR->table_name = $table_name;
  $dbW->table_name = $table_name;
  $pinyin = new PinYin("UTF-8",$GLOBALS['py_data_path']);

  if (file_exists($GLOBALS['l_file_guoji'])) {
    $l_cont = file_get_contents($GLOBALS['l_file_guoji']);
  }else {
    $l_cont = "";
    return ;
  }
  $l_xml = str_get_html($l_cont);

  // 所有的dd标签中的a标签中的中文名和链接的url输出为数组
  foreach($l_xml->find("table") as $l_tbl){
    $l_name = trim($l_tbl->find("a",0)->plaintext);
    $l_code = $l_tbl->find("a",1)->plaintext;
    $l_id = $l_code+0;
    $l_py = $pinyin->getPY($l_name);

    // 清下内存
    $l_tbl->clear();unset($l_tbl);

    // 逐条检查是否存在于数据库中, 不存在于数据库中的则输出提示信息
    if ($l_id>0) {
      // 插入到数据库中去
      $data_arr = array(
        "guojiquhao_int"=> $l_id,
        "guojiquhao"=>$l_code,
        "name_cn"=>$l_name,
        "pingyin_shouzimu"=>substr($l_py,0,1),
        "jibie"=>1,        // 默认为1
        "s_shu_xingqiu_id"=>1,  // 所属地球
      );
      $dbW->insertOne($data_arr);
      $l_err = $dbW->errorInfo();
      if ($l_err[1]>0){
        echo date("Y-m-d H:i:s") . "failed:" . $dbW->getSQL() .NEW_LINE_CHAR. $l_err[2]. ".".NEW_LINE_CHAR.NEW_LINE_CHAR;
        //return null;
      }
      usleep(10);
    }else {
      echo "some thing error!" . NEW_LINE_CHAR;
    }
  }
  // 清下内存
  $l_xml->clear();unset($l_xml);

  echo "complete!".NEW_LINE_CHAR;
  return ;
}

//Update_name_eng_zhantai2benzhan();
// 将本站之前抓取的文章的所属城市中的站台name_eng全部更新为本站自己的name_eng，以后统一使用本站自己的
function Update_name_eng_zhantai2benzhan(){
  $dbR = new DBR();
  $l_err = $dbR->errorInfo();
  if ($l_err[1]>0){
    // 数据库连接失败后
    echo date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
    return $l_options;
  }
  $dbR->table_name = TABLENAME_PREF."project";
  $p_arr_gongyong = $dbR->GetOne("where name_cn='共用数据'");
  $p_arr_shenghuo = $dbR->GetOne("where name_cn='生活频道'");
  $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr_gongyong);$dbR->dbo = &DBO('gongyong', $dsn);
  //$dbR = new DBR($p_arr_gongyong);
  $dbR->table_name = "region_sheng";
  $l_city = $dbR->getAlls("where status_='use' and name_eng_zhantai != '' ", "id,name_eng,name_cn,name_eng_zhantai");
  $l_common_static = cArray::Index2KeyArr($l_city,array("key"=>"name_eng_zhantai","value"=>array()));

  // 生活频道
  $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr_shenghuo);$dbR->dbo = &DBO('shenghuo', $dsn);
  //$dbR = null;
  //$dbR = new DBR($p_arr_shenghuo);
  $dbR->table_name = "aups_t002";
  $l_s_shucity = $dbR->getAlls("where s_shu_chengshi != '' ", "id,s_shu_chengshi");

  $dbW = new DBW($p_arr_shenghuo);
  $dbW->table_name = "aups_t002";
  // 逐条进行入库
  if (!empty($l_s_shucity)) {
    foreach ($l_s_shucity as $l_vals){
      if (array_key_exists($l_vals['s_shu_chengshi'],$l_common_static)) {
        $l_gongyong_name_eng = $l_common_static[$l_vals['s_shu_chengshi']]['name_eng'];
        if ($l_gongyong_name_eng != $l_vals['s_shu_chengshi']) {
          // 需要进行更新该字段
          $data_arr = array(
            's_shu_chengshi' => $l_gongyong_name_eng
          );
          $condition = ' `id` = '.$l_vals['id'];
          $dbW->updateOne($data_arr,$condition);
          $l_err = $dbW->errorInfo();
               if ($l_err[1]>0){
            echo date("Y-m-d H:i:s"). " err--".$dbW->getSQL() ." insert to article_list error!" . var_export($l_err, true) . NEW_LINE_CHAR;
          }else {
            echo date("Y-m-d H:i:s"). " update succ! new:".$l_gongyong_name_eng." old:".$l_vals['s_shu_chengshi'] . NEW_LINE_CHAR;
          }
        }else {
          echo date("Y-m-d H:i:s"). " they are equ :" .$l_gongyong_name_eng . NEW_LINE_CHAR;
        }
      }else {
        echo date("Y-m-d H:i:s"). "err! id:" .$l_vals['id']." - ".$l_vals['s_shu_chengshi'].NEW_LINE_CHAR;
      }
    }

    echo "complete!".NEW_LINE_CHAR;
  }else {
    echo "empty l_s_shucity!".NEW_LINE_CHAR;
  }

  return ;
}
