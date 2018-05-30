<?php
/**
 * 解析普通的excel数据，然后自动建表和字段，以及数据导入库。
 * 当前该方法只能解析 .xls .cvs。 而.xlsx 则需要phpexcel的高级版本，以后用到在做调试。用法：
 * php excel.php -p 'D:/www/tests/excel/xh1.xls' -t boruixianglong12wan
 *
 * 当前将一个文件导入一张数据表，在数据表中需要增加额外字段记录所属文件、所属sheet两个字段。
 *
 *
 */
if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)) )require_once("D:/www/dpa/configs/system.conf.php");
else require_once("/data0/deve/runtime/configs/system.conf.php");
require_once("lang/".$GLOBALS['cfg']['LANG_DEFINE_FILE']);
require_once("common/functions.php");
require_once('mod/DBR.cls.php');
require_once('mod/DBW.cls.php');
require_once 'Spreadsheet/Excel/reader.php';
require_once 'PinYin.class.php';

require_once 'Console/Getopt.php';
$_options = Console_Getopt::getopt($argv, 'p:t:', array());
$_o = array();
if (!PEAR::isError($_options)) {
  foreach ($_options[0] as $l_v){
    $_o[$l_v[0]] = $l_v[1];
  }
}
//$l_file = "D:/www/tests/excel/xh1.xlsx";  // 当前不支持此版本
$l_file = "D:/www/tests/excel/xh1.xls";
//$l_file = "D:/www/tests/excel/a.xls";

$l_file = !empty($_o["p"]) ? $_o["p"] : $l_file;
$a_tblname = !empty($_o["t"]) ? $_o["t"] : 'boruixianglong12wan';

// 连接数据库，准备自动见表以及入库数据 . 使用同一个数据库连接信息
$dbR = new DBR('dpa');
$l_err = $dbR->errorInfo();
if ($l_err[1]>0){
  // 数据库连接失败后
  exit(date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".");
}
$dbW = new DBW('dpa');

$pinyin = new PinYin('utf8');

proc_one_file($dbR, $dbW, $l_file, $a_tblname);

function proc_one_file(&$dbR, &$dbW, $l_file,$a_tblname='boruixianglong12wan'){
  if ( !file_exists($l_file) ) {
    echo 'file: '. "$l_file not exist! ".NEW_LINE_CHAR;
    return ;
  }

  $excel = new Spreadsheet_Excel_Reader();  //创建 Reader
  $excel->setOutputEncoding('UTF-8');      //设置文本输出编码
  $excel->read($l_file);            //读取Excel文件

  // print_r($excel->sheets);exit;

  $l_facatory = basename($l_file);      // 哪个文件，作为所属工厂

  // 各个sheet逐一处理
  foreach ($excel->sheets as $l_k => $l_sheet){
    $l_sheet_name = $excel->boundsheets[$l_k]['name'];  // 所属月份，如 1月申报
    //$l_sheet_name_py = $GLOBALS['pinyin']->getPY($l_sheet_name);
    //echo $l_sheet_name_py;exit;

    // 额外添加两个字段，确定每条记录所属的父级元素, 可能是多个并列的父级元素
    $l_other_ziduan = array('所属工厂'=>$l_facatory, '所属月份'=>$l_sheet_name);

    // 另外对于额外值还需要进行覆盖
    $l_other_ziduan_eng_key = array_flip($l_other_ziduan);
    array_walk($l_other_ziduan_eng_key,'getPy');
    $l_other_ziduan_eng_key = array_flip($l_other_ziduan_eng_key);

    //['numRows']为行数
    //['numCols']为列数
    //['cells']  为各行元素具体数据数组

    proc_one_sheet($dbR, $dbW, $l_sheet, $l_other_ziduan, $l_other_ziduan_eng_key, $a_tblname);

  }
}

function proc_one_sheet(&$dbR, &$dbW, $l_sheet, $l_other_ziduan, $l_other_ziduan_eng_key, $a_tblname){
  $dbR = new DBR('dpa');
  $dbW = new DBW('dpa');
  $dbR->SetCurrentSchema("test6");
  $dbW->SetCurrentSchema("test6");

  if ($l_sheet['numCols']>10) {
      // 逐条记录解析 l_i 对应的是行号, 通常从1开始
      foreach ($l_sheet['cells'] as $l_i=>$l_arr){

        // 第一行通常都是表头 纳税人姓名,身份证照类型......
        // 第一行作为表的字段信息
        if (1==$l_i) {
          // $l_other_ziduan 作为表头的默认增加条目, 需要合并到表头中去, 创建字段的时候需要用到
          // 数字索引的数组不能用array_merge ，因为会被重新从0开始索引，此处不能重新索引
          // $l_arr = $l_arr + array_keys($l_other_ziduan); ‘+’只能对一个进行追加，多个追加的结果只能有一个成功，其他未被追加进去
          cArray::array__unshift($l_arr, array_keys($l_other_ziduan),'after');

          // 作为数据表字段，其字段主要由汉字的拼音组成，而中文名作为字段的注释信息
          //$l_arr = array_filter($l_arr); // 此处无需去掉空值，因为空值并不会在$l_arr数组中
          $l_biaotou_eng = biaotou($dbR, $dbW, $l_arr, $l_other_ziduan, $a_tblname);
          if (empty($l_biaotou_eng)) {
            return ;
          }

          // $l_biaotou_eng_cn = array_combine($l_biaotou_eng,$l_arr);
        }else {
          // 获取表头，即后续各行对应的字段数组以后，就能拼装相应的数据进行逐条入库操作了


          // 有几项不能为空，这里的唯一性条件没有设置，本应该设置一个唯一性条件，暂时在数据表层面进行设置

          // $l_arr 是数字键名一维数组。其键名 从1开始 2，3，4......分别对应的是excel的A,B,C,D......
          // 具体在入库的时候，拼装入库数组的时候需要用到第一行的数字索引对应的字段英文名

          // 一个小限制, 第一条数据为空则不入库, 便于过滤不必要的数据???? 后面需要再完善之。
          $data_arr = array_flip($l_biaotou_eng);  // 变成英文键名，数字值数组
          array_walk($data_arr,'__call_back_ReplaceValue',$l_arr);//$data_arr每项的值被替换为相应数字键名l_arr的值

          $data_arr = array_merge($data_arr,$l_other_ziduan_eng_key);
          if (!array_key_exists("creator", $data_arr)) $data_arr["creator"] = isset($_SESSION["user"]["username"]) ? $_SESSION["user"]["username"] : 'admin';
               if (!array_key_exists("createdate", $data_arr)) $data_arr["createdate"] = date("Y-m-d");
               if (!array_key_exists("createtime", $data_arr)) $data_arr["createtime"] = date("H:i:s");

          $dbW->InsertIntoTbl($a_tblname,$data_arr);
          $l_err = $dbW->errorInfo();
          if ($l_err[1]>0){
            // 发生错误则输出错误信息，便于调试。
            echo var_export($l_err, true). " 发生错误,sql: ". $dbW->getSQL() . NEW_LINE_CHAR;
            // return null;
          }

          usleep(300);
        }
      }
  }
}
function __call_back_ReplaceValue(&$item1, $key, $prefix){
  $item1 = $prefix[$item1];
}
function biaotou(&$dbR, &$dbW, $l_arr, $l_other_ziduan, $a_tblname){

  // 1. 检查真实的表是否存在, 不存在要创建该表
  $l_t_real_all = $dbR->getDBTbls();
  $l_t_real_all = cArray::Index2KeyArr($l_t_real_all,array("key"=>"Name", "value"=>"Name"));
  if (!array_key_exists($a_tblname, $l_t_real_all)) {
    // 需要创建
    // 如果表不存在需要创建表,
    if(!empty($a_tblname)) {
      $dbW->create_table($a_tblname);
      // 发生错误的话需要返回
      $l_err = $dbW->errorInfo();
      if ($l_err[1]>0){
        echo var_export($l_err, true). " 发生错误,sql: ". $dbW->getSQL() . NEW_LINE_CHAR;
        return null;
      }
    } else {
      echo 'table name can not empty' . NEW_LINE_CHAR;
      return ;
    }
  }

  // 2. 检查该表是否存在于表定义表中，如果不存在则插入
  $dbR->table_name = TABLENAME_PREF."table_def";
  $l_t_all = $dbR->getAlls();
  $l_t_all_1 = cArray::Index2KeyArr($l_t_all,array("key"=>"id", "value"=>"name_eng"));
  if (!in_array($a_tblname, $l_t_all_1)) {
    // 则插入数据表中
    $data_arr = array(
      'creator'  =>'admin',
      'createdate'=>date("Y-m-d"),
      'createtime'=>date("H:i:s"),
      'p_id'    =>$l_t_all[0]['p_id'],
      'name_eng'  =>$a_tblname,
      'name_cn'  =>$a_tblname . '报表',
      //'description'=>$a_tblname,
    );
    $dbW->table_name = TABLENAME_PREF."table_def";
    $dbW->insertOne($data_arr);
    $l_err = $dbW->errorInfo();
    if ($l_err[1]>0){
      echo var_export($l_err, true). " 发生错误,sql: ". $dbW->getSQL() . NEW_LINE_CHAR;
      return null;
    }else {
      $l_new_t_id = $dbW->LastID();
    }
  }else {
    $l_new_t_id = array_search($a_tblname, $l_t_all_1);
  }

  // 3 对字段的处理

  // 3.1) 先获取旧数组表的字段
  $dbR->table_name = $a_tblname;
  $l_fields = $dbR->getTblFields($a_tblname);
  $old_struct = cArray::Index2KeyArr($l_fields, array('value'=>"Field"));
  if (empty($old_struct)) $old_struct = array("id");  // 设置一个默认的

  // 3.2) 要入库的所有字段
  $peizhi_ziduan = $l_arr;
  array_walk($peizhi_ziduan,'getPy');

  // 3.3) 多出的字段，就是新字段相对旧字段多出的字段
  $duoziduan = array_diff($peizhi_ziduan,$old_struct);

  // 逐一字段进行判断，如果字段不存在，则需要修改表结构，然后还要在字段定义表中增加字段信息记录
  if (!empty($duoziduan)) {
    $duoziduan_cn_arr = array_flip($duoziduan);
    array_walk($duoziduan_cn_arr,'__call_back_ReplaceValue',$l_arr); // 带上字段中文名称

    // 1. 先修改表结构，修改成功后
    $dbW->table_name = $a_tblname;
    $dbW->alter_table($duoziduan_cn_arr);
    $l_err = $dbW->errorInfo();
    if ($l_err[1]>0){
      echo var_export($l_err, true). " 发生错误,sql: ". $dbW->getSQL() . NEW_LINE_CHAR;
      return null;
    }

    // 2. 再加入到字段定义表中去
    $l_data_arr = array("creator"=>isset($_SESSION["user"]["username"])?$_SESSION["user"]["username"]:"admin");
    DbHelper::fill_field($dbR,$dbW,$l_data_arr,$a_tblname,TABLENAME_PREF."field_def",TABLENAME_PREF."table_def");

    // $l_err = $dbW->errorInfo(); 如果有错则输出错误，没错则继续

    // 字段创建好以后， 对应的数据入库不在本函数完成
  }

  // 返回字段的英文名数组
  return $peizhi_ziduan;
}

function getPy(&$item1, $key){
  $item1 = preg_replace('/\W/',"_", $GLOBALS['pinyin']->getPY($item1));
}

/*
// 此方法可用，解析 .xlsx 文件
$l_file = "D:/www/tests/excel/xh1.xlsx";
require 'PHPExcel.php';
require 'PHPExcel/Reader/Excel2007.php';
require 'PHPExcel/Reader/Excel5.php';

// require 'PHPExcel/IOFactory.php';
// $PHPExcel = PHPExcel_IOFactory::load($l_file);

$PHPExcel = new PHPExcel();

$PHPReader = new PHPExcel_Reader_Excel2007();
$PHPExcel = $PHPReader->load($l_file);

$sheet = $PHPExcel->getActiveSheet();
print_r($sheet);exit;
$allCol=PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
$allRow=$sheet->getHighestRow();
for ($col=0; $col<$allCol;$col++) {
    for ($row=0; $row<$allRow;$row++) {
        echo $sheet->getCellByColumnAndRow($col, $row)->getValue() . NEW_LINE_CHAR;
    }
}
*/


/* 能解析 .xls
$filename = "D:/www/tests/excel/a.xls";
$sheet1 = 'Sheet1';
$sheet2 = "sheet2";
$excel_app = new COM("Excel.application") or Die ("Did not connect");
print "Application name: {$excel_app->Application->value}\n" ;
print "Loaded version: {$excel_app->Application->version}\n";
$Workbook = $excel_app->Workbooks->Open("$filename") or Die("Did not open $filename $Workbook");
$Worksheet = $Workbook->Worksheets($sheet1);
$Worksheet->activate;
$excel_cell = $Worksheet->Range("B2");
$excel_cell->activate;
$excel_result = $excel_cell->value;
print "$excel_result\n";

$Worksheet = $Workbook->Worksheets($sheet2);
$Worksheet->activate;
$excel_cell = $Worksheet->Range("B2");
$excel_cell->activate;
$excel_result = $excel_cell->value;
print "$excel_result\n";
#To close all instances of excel:
$Workbook->Close;
unset($Worksheet);
unset($Workbook);
$excel_app->Workbooks->Close();
$excel_app->Quit();
unset($excel_app);
*/

/*
也能解析 .xls
$conn = new COM("ADODB.Connection", NULL, CP_UTF8) or die("Cannot start ADO");
$connstr="Driver={Microsoft Excel Driver (*.xls)};DBQ=".realpath($l_file);
$conn->Open($connstr);

$rs = $conn->Execute("SELECT * FROM [Sheet1$]");    // 记录集

$num_columns = $rs->Fields->Count();
echo $num_columns . "\n";

for ($i=0; $i < $num_columns; $i++)
{
    $fld[$i] = $rs->Fields($i);
}

$rowcount = 0;
while (!$rs->EOF)
{
    for ($i=0; $i < $num_columns; $i++)
    {
        echo $fld[$i]->value . "\t";
    }
    echo "\n";
    $rowcount++;            // rowcount 自增
    $rs->MoveNext();
}

$rs->Close();
$conn->Close();

$rs->Release();
$conn->Release();

$rs = null;
$conn = null; */



