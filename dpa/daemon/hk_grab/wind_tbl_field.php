<?php
require_once("../../configs/system.conf.php");
require_once("mod/AutoTblFields.cls.php");
require_once("common/functions.php");
require_once("common/global_func.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");

/*
$dbR = new DBR();
$dbW = new DBW();
$f_def = TABLENAME_PREF."field_def_fcdb_utf_grab";//utf_grab gbk
$t_def = TABLENAME_PREF."table_def_fcdb_utf_grab";
// 先从数据库中获取到fcdb的表数据, 并按照
$dbR->table_name = $t_def;
$_arr = $dbR->getAlls();
$_t_a = array();

foreach ($_arr as $l_v){
$_t_a[] = strtoupper($l_v["name_eng"]);
}

sort($_t_a);

foreach ($_t_a as $l_v){
echo strtoupper($l_v).NEW_LINE_CHAR;
}

*/
$_cont = iconv("UTF-8","GBK//IGNORE",file_get_contents("wind_tbl_field.txt"));
$a_arr = explode("\r\n",$_cont);
$data_arr = getInfowind($a_arr);//getWind();
ksort($data_arr);

$dbR = new DBR();
$dbW = new DBW();
$p_id = 1;  // wind的项目id是1

//print_r($data_arr);exit;
//echo "总表数: ".count($data_arr).NEW_LINE_CHAR;
$t_def = TABLENAME_PREF."table_def";
$f_def = TABLENAME_PREF."field_def";

// TABLENAME_PREF.
foreach ($data_arr as $l_t => $l_v){

  foreach ($l_v as $l__v){
    $dbR->table_name = $t_def;
    $_tbl = $dbR -> getOne("where name_eng = '".$l_t."' and p_id=$p_id");

    if ($_tbl["id"]>0) {
      infield_def($dbW, $_tbl["id"], $l__v, $f_def, $t_def);
    }else {
      echo "table not fund ".NEW_LINE_CHAR;
    }
    usleep(200);
  }
}

// 往字段定义表中插入数据
function infield_def(&$dbW, $t_id, $a_arr, $f_def="field_def", $t_def="table_def"){
  $dbW->table_name = $f_def;

  $name_eng   = strtolower(trim($a_arr["name_eng"]));
  $name_cn   = trim($a_arr["name_cn"]);

  if($dbW->getExistorNot("t_id = $t_id  and name_eng='".$name_eng."'")){
    echo date("Y-m-d H:i:s")." t_id: $t_id  name_eng: $name_eng exist in field_def ! ".NEW_LINE_CHAR;
  } else {
    // 不存在则插入数据库中
    $data_arr = array(
      "creator"     => convCharacter($_SESSION["user"]["id"],true),
      "createdate"    => date("Y-m-d"),
      "createtime"    => date("H:i:s"),
      "source"    => "grab",
      "t_id"        => $t_id,
      "name_eng"     => $name_eng,
      "name_cn"     => convCharacter($name_cn,true),
      "is_null"      => $a_arr["Null"],
      //"key"        => $a_arr["Key"],
      //"extra"        => $a_arr["Extra"],
      "type"        => $a_arr["type"],
      "length"      => $a_arr["length"],
      //"attribute"      => $a_arr["attribute"],
      //"default"      => convCharacter($a_arr["Default"],true)
    );
    if (key_exists("status_", $a_arr)) {
      $data_arr["status_"]      = $a_arr["status_"];
    }
    if ($dbW->insertOne($data_arr)) {
      $last_id = $dbW->LastID();
    }else {
      echo $dbW->getSQL();
      echo date("Y-m-d H:i:s")." insert field_def err!".NEW_LINE_CHAR;
      print_r($data_arr);
    }
  }

  return $last_id;
}


function getInfowind($a_arr)
{
  if (count($a_arr)<=1) {
    return null;
  }

  $_rlt = array();
  // 逐项分解数据，变成数组
  foreach ($a_arr as $l_key=>$l_val){

    $G_regex = "/\((TB_OBJECT_[0-9]+|MT_COL_TEXT|TB_CLASS_PROPERTY|TB_COM_COLLECTION|TB_OBJECT_CLASS)/i";
    preg_match_all($G_regex,$l_val,$matches);

    // 获取表名，直到碰到下一个表，都应该作为此数组,同时获得交易类型
    if (!empty($matches[1])) {
      if (count($matches[1]) > 1) {
        echo date("Y-m-d H:i:s")." "." $l_key ".$l_val." matches too much !".NEW_LINE_CHAR;
      }
      $bread_date = getPinzhongDce($matches[1][0],$l_val);
      continue;
    }
    // 废弃的不需要
    if (false!==strpos($bread_date["str"],"废弃")) {
      continue;
    }
    // 分解每一项
    if (""!=trim($l_val) && isset($bread_date) ) {
      // 第一行必须是数字的,才认为是字段说明,需要进行正则匹配
      if (preg_match("/^\d+\s+[A-Za-z0-9_]+/",$l_val)) {

        $dce_detail = getDetailDce($l_val,$bread_date["table_name_eng"]);

        $_rlt[$bread_date["table_name_eng"]][$dce_detail["name_eng"]] = $dce_detail;
      }
    }
  }

  return $_rlt;
}

function getDetailDce($str,$t_name){
  $arr = array();
  $rlt = array();
  // 由于空格分隔的多样性，因此需要合并这些分隔数据
  $l_tm = explode("	",trim($str));
  //echo $l_tm[5].NEW_LINE_CHAR;//"---- f num:".count($l_tm)." ".
  //print_r($l_tm[5]);
  // 剔除空白
  foreach ($l_tm as $val){
    if (""!=trim($val)) {
      $arr[] = $val;
    }
  }
  // 只取前三
  if (count($arr)>3) {
    $rlt["name_eng"] = trim($arr[1]);
    $rlt["name_cn"]  = str_replace("[内部]","", trim($arr[2]));// $arr[2];

    $type_and_length = PMA_extract_type_length(trim($l_tm[5]));
    $rlt = array_merge($rlt, $type_and_length);

    // 废弃不用的字段需要标记出来
    if (false!==strpos($rlt["name_cn"],"[废弃]")) {
      $rlt["status_"] = "scrap";
    }

    if ("是"==trim($l_tm[7]))  $rlt["Null"]  = "YES";
    else $rlt["Null"]  = "NO";

    if (empty($rlt["type"])) {
      echo "l_tm empty".NEW_LINE_CHAR;
    }
    // 字段中如果有数字，则需要将数字同表名中的数字进行对照，必须一致，不一致需要输出错误信息
    if (preg_match("/_([0-9]+)$/",$t_name,$t_match) && preg_match("/_([0-9]+)$/",$rlt["name_eng"],$f_match)) {
      if ($t_match[1] != $f_match[1]) {
        echo $t_match[1] ." ". $f_match[1]. "t_mat_ch != f_mat_ch ".NEW_LINE_CHAR;
      }
    }
  }else {
    echo $str." count 小于 3".NEW_LINE_CHAR;
  }

  return $rlt;
}


//
function getPinzhongDce($str, $a_all){
  $arr = array("table_name_eng"=>$str,"str"=>$a_all);
  return $arr;
}
