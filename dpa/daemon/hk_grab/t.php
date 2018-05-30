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
$data_arr = getWind();
print_r($data_arr);
$dbR = new DBR();
$dbW = new DBW();
$p_id = 1;  // wind的项目id是1

// TABLENAME_PREF.
foreach ($data_arr as $l_k => $l_v){
  $l_arr["t_name_eng"] = $l_k;
  $l_arr["t_name_cn"] = $l_v;
  //print_r($l_arr);
  intable_def($dbW, $l_arr, TABLENAME_PREF."field_def", TABLENAME_PREF."table_def",$p_id);
}

// 往表定义表中插入数据
function intable_def(&$dbW, $a_arr, $f_def="field_def", $t_def="table_def",$p_id=0){
  $name_eng = $a_arr["t_name_eng"];   //
  $name_cn  = $a_arr["t_name_cn"];       // 暂时用英文的

  $dbW->table_name = $t_def;
  if($dbW->getExistorNot("name_eng='".$name_eng."' and p_id=$p_id")){
    echo " exist in db".NEW_LINE_CHAR;
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
    if ($dbW->insertOne($data_arr)) {
      $last_id = $dbW->LastID();
    }else {
      echo $dbW->getSQL();
      echo date("Y-m-d H:i:s")." "."insert error!".NEW_LINE_CHAR;
      print_r($data_arr);
    }
  }
  return $last_id;
}




function getWind(){
  $all_arr = array();
  $fei_arr = array();
  $yong_arr = array();
  $u_arr = array();
  $cn_arr = array();


  $a_arr = file("wind.txt");

  foreach ($a_arr as $line => $a_v){
    if (false!==strpos($a_v,"(")) {
      preg_match("/\(([A-Z0-9a-z_]+)/", $a_v, $matches);

      $l_v = $matches[1];
      if (!empty($l_v)) {

        $all_arr[] = $l_v;
        if (!in_array($l_v,$u_arr)) {
          $u_arr[] = $l_v;

          if (false===strpos($a_v,"废弃")) {
            // 去掉废弃的
            // 对该行进行中文名提取
            // 以$matches[0] 进行分割
            $l_tmp1= explode($matches[0],$a_v);
            $l_tmp2= explode("	",$l_tmp1[0]);
            if (count($l_tmp2)<2) {
              echo $a_v." count1".NEW_LINE_CHAR;
            }

            $cn_arr[$l_v] = str_replace("[内部]","",$l_tmp2[1]);
          }
        }

        /*
        if (false!==strpos($a_v,"废弃")) {
        if (!in_array($l_v,$fei_arr)) $fei_arr[] = $l_v;
        }else {
        if (!in_array($l_v,$yong_arr)) $yong_arr[] = $l_v;
        }
        */
      }else {
        echo $line." emptyline ----".NEW_LINE_CHAR;
      }
      //echo trim($a_v).NEW_LINE_CHAR;
    }
  }
  ksort($cn_arr);
  return $cn_arr;
}
