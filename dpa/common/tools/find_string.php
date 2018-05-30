<?php
/**
 * 使用方法：
 * php D:/www/dpa/common/tools/find_string.php -p "D:/www/sinafinance/trunk/biz" -c "browser" -r "/somestring/i" -f "a.php"
 * php D:/www/dpa/common/tools/find_string.php -p "D:/www/dpa/WEB-INF" -r "/\$response,\$form\)/i"
 * php D:/www/dpa/common/tools/find_string.php -p "D:/www/dpa" -r "/table_def|field_def/i"
 *
 // 1. 从定义的文件中分离字符串，然后遍历找到包含这些字符串的文件，并打印出来
 D:/php5210/php D:/www/dpa/common/tools/find_string.php -p "D:/www/wanda_svn/trunk/php" -x "D:/www/wanda_svn/trunk/php/webapi/models" -y "ErrorMsg.php"  -w "ErrorMsg.php"

 // 2. 从包含在所有文件中的特征字符串 ErrorMsg::(\w+)
 D:/php5210/php D:/www/dpa/common/tools/find_string.php -p "D:/www/wanda_svn/trunk/php" -r "/ErrorMsg::(\w+)/i" -x "D:/www/wanda_svn/trunk/php/webapi/models" -y "ErrorMsg.php"  -w "ErrorMsg.php"

 D:/php5210/php D:/www/dpa/common/tools/find_string.php -p "D:/www/wanda_svn/trunk/php/ffan/web" -r "/SetPrefer/i"

 */
require_once("D:/www/dpa/configs/system.conf.php");
require_once("common/functions.php");
require_once("common/Files.cls.php");

define("str_pad_num", 66);  // 44

// 获取参数列表
if (version_compare(PHP_VERSION, '4.3.0', '>=')) {
    $_o = getopt('p:c:r:f:m:x:y:w:t:');
} else {
    require_once 'Console/Getopt.php';
    $_options = Console_Getopt::getopt($argv, 'p:c:r:f:m:x:y:w:t:', array());
    $_o = array();
    if (!PEAR::isError($_options)) {
        foreach ($_options[0] as $l_v){
            $_o[$l_v[0]] = $l_v[1];
        }
    }
}
// 几个全局变量
$G_ALL_DATA   = array();
$G_biao_show   = array();
$G_allow_type  = array("php","txt","ini","conf","xml","shtml","html","htm","js","css",'json','m','h');
$G_no_need_conv = array("doc","zip","rar");  // need not find
$G_no_need_file = array();  // need not find file
$G_no_need_file = (!empty($_o["w"])) ? array($_o["w"]) : $G_no_need_file;
$G_no_need_dir = array();  // need not find dir

if (isset($_o['t']))
    $G_allow_type = explode(',', $_o['t']);

// "/TB_OBJECT_[0-9]+|MT_COL_TEXT|TB_CLASS_PROPERTY|TB_COM_COLLECTION|TB_OBJECT_CLASS/i";
if (isset($_o['x']) && isset($_o['y'])) {
    $a_const_file = get_const_str($_o["x"] . "/" . $_o["y"]);
    if ($a_const_str)
        $G_regex = "/" . $a_const_str . "/";
}
$G_regex = isset($_o["r"]) ? $_o["r"] : '';
if (!$G_regex) {
    exit('not give find_string!');
}
if (!isset($GLOBALS["G_search_string"])) $GLOBALS["G_search_string"] = array(); // 注册一下全局的G_search_string

$G_replace = (!empty($_o["m"])) ? $_o["m"] : "";

$G_COMMON_PATH = (!empty($_o["p"])) ? $_o["p"] : "D:/www/sinafinance/trunk/biz";

$l_common = (!empty($_o["c"])) ? $_o["c"] : "";
$l_common = empty($l_common) ? "" : "/" . $l_common;
$l_source_path = $G_COMMON_PATH.$l_common;

$l_filename = (key_exists("f",$_o)) ? $_o["f"] : "";

if (""!=$l_filename)
  tOne($l_source_path, $l_filename);
else
  transPath($l_source_path, true);

foreach ($G_ALL_DATA as $l_key => $l_val) {
  $l_pref = (1 + $l_key) . ". " . $l_val[0];
  if (0==$l_key)
    echo str_pad('序号       文件路径', str_pad_num) . " " . ' 搜索字符:出现次数' . NEW_LINE_CHAR;
  echo str_pad($l_pref,str_pad_num) . " " . $l_val[1] . NEW_LINE_CHAR;
}
arsort($G_biao_show);
print_r($G_biao_show);
print_r($GLOBALS['G_search_string']);
$diff1 = array_diff(array_keys($GLOBALS['G_search_string']), array_keys($G_biao_show));
$diff2 = array_diff(array_keys($G_biao_show), array_keys($GLOBALS['G_search_string']));
print_r($diff1);
print_r($diff2);
echo count($G_biao_show) . " 个搜索字符" . NEW_LINE_CHAR;

function transPath($source_path, $son=false){
  // 如果是需要将某个目录下的所有文件转化一下，则需要遍历目录下文件
  global $G_no_need_file;
  global $G_no_need_dir;

  $d = @dir($source_path);
  if ($d) {
      while (false !== ($_file = $d->read())) {
        if ("." != substr(ltrim($_file), 0, 1) ) {//  过滤掉 . .. .svn这三项
          $l_tmp_file = $source_path . "/" . $_file;
          if(is_dir($l_tmp_file)) {
            if (!in_array($l_tmp_file, $G_no_need_dir))
              if($son) transPath($l_tmp_file, $son);
          }
          else
            tOne($source_path, $_file);
        }
      }
      $d->close();
  }
}

function tOne($s_path, $s_file){
  global $G_allow_type;
  global $G_ALL_DATA;
  global $G_biao_show;
  global $G_regex;
  global $G_COMMON_PATH;
  global $G_no_need_file;
  global $G_no_need_dir;

  if (in_array($s_path, $G_no_need_dir) || in_array($s_file, $G_no_need_file))
    return ;

  // 将指定的文件获取到内容
  if (file_exists($s_path . "/" . $s_file)) {
    $files = new Files();
    $ext = $files->getExt($s_file);
    if (!in_array(strtolower($ext), $G_allow_type))
      return null; // 类型不符合就直接返回

    $cont = file_get_contents($s_path . "/" . $s_file);

    // 查找里面是否有需要的表名，如果有则记录；没有则返回
    // 采用正则表达式
    preg_match_all($G_regex, $cont, $matches);
    //preg_match_all("/insert |update /i",$cont,$matches);
    // 查找并替换掉： $response,$form)   '$response,$form,$get,$cookie)'
    /*if ($G_replace) {
      $cont = preg_replace($G_regex,$G_replace, $cont);
      file_put_contents($s_path."/".$s_file, $cont);
    }*/


    if (!empty($matches[0])) {
      $l_biao_show = array_count_values($matches[0]);

      $_biao = "";
      $i = 0;
      foreach ($l_biao_show as $tb => $num){
        // 统计每张表出现的总次数
        if ('ErrorMsg::FillResponseAndLog' == $tb)
          continue;

        if (key_exists($tb, $G_biao_show))
          $G_biao_show[$tb] += $num;
        else
          $G_biao_show[$tb] = $num;

        // 统计单张表出现的次数
        if ($i>0)
          $_biao .= "," . NEW_LINE_CHAR.str_pad("",(str_pad_num+1));
        $_biao .= $tb." : ".$num;

        $i++;
      }

      $G_ALL_DATA[] = array(
          str_replace($G_COMMON_PATH."/",
                      "",
                      str_replace('\\', "/", $s_path . "/" . $s_file)),
          $_biao);
    }
    return null;
  }else {
    echo "source file not exist!";
  }
  return null;
}

//
function get_regex($a_file="biz.txt"){
  $l_arr = file($a_file);
  $l_new = array();

  foreach ($l_arr as $l_val){
    if (false !== strpos($l_val,"|")) {
      $l_v = str_replace(array("|"," "),"",trim($l_val));
      if(!in_array($l_v,$l_new)) $l_new[] = $l_v;
    }
  }

  return join("\b|",$l_new);
}

// 找到所有定义的常量，然后跟被包含的字符串进行比较
function get_const_str($a_file="biz.txt"){
  // global $G_search_string;
  $l_arr = file($a_file);
  $l_const = array();
  $l_str   = array();

  if ($l_arr) {
      return '';
  }

  foreach ($l_arr as $l_val) {
    if (false !== strpos($l_val, " const ")) {
      if (preg_match('/\sconst\s+(\w+)\s+=/', $l_val, $matches)) {
        if(!in_array($matches[1], $l_const))
          $l_const[$matches[1]] = $matches[1];
        else echo " const double " . $matches[1] . "\r\n";
      }
    }

    if (false !== strpos($l_val, " self::")) {
      if (preg_match('/\sself::(\w+)\s+=>/', $l_val, $match)) {
        if(!in_array($match[1], $l_str))
          $l_str[$match[1]] = $match[1];
        else echo " string double " . $match[1] . "\r\n";
      }
    }
  }
  if (!$l_const) {
      return '';
  }

  $diff1 = array_diff($l_const, $l_str);
  $diff2 = array_diff($l_str, $l_const);
  if (!empty($diff1) || !empty($diff2)) {
    echo __LINE__ . " error \r\n";
    return '';
    exit;
  }
  $GLOBALS['G_search_string'] = $l_const;

  return join("\b|", $l_const);
}

// 找到所有被包含的字符串，然后跟所定义常量进行比较
function get_contain_str($a_file="biz.txt"){
  // global $G_search_string;
  $l_arr = file($a_file);
  $l_const = array();
  $l_str   = array();

  if ($l_arr) {
      return '';
  }

  foreach ($l_arr as $l_val) {
    if (false !== strpos($l_val, " const ")) {
      if (preg_match('/\sconst\s+(\w+)\s+=/', $l_val, $matches)) {
        if(!in_array($matches[1], $l_const))
          $l_const[$matches[1]] = $matches[1];
        else echo " const double " . $matches[1] . "\r\n";
      }
    }

    if (false !== strpos($l_val, " self::")) {
      if (preg_match('/\sself::(\w+)\s+=>/', $l_val, $match)) {
        if(!in_array($match[1], $l_str))
          $l_str[$match[1]] = $match[1];
        else echo " string double " . $match[1] . "\r\n";
      }
    }
  }


  $diff1 = array_diff($l_const, $l_str);
  $diff2 = array_diff($l_str, $l_const);
  if (!empty($diff1) || !empty($diff2)) {
    echo __LINE__ . " error \r\n"; exit;
  }

  $conver = array_map("AddErrorMsg", $l_const);
  $GLOBALS['G_search_string'] = array_flip($conver);

  return ;
}

function AddErrorMsg($n) {
   return "ErrorMsg::" . $n;
}

