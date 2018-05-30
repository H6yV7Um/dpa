<?php
/**
 * 去掉PHP文件末尾多余的空白, 检测末尾是否?> 如果是，则需要去掉所有的空白，（并同时删除末尾的>? ）
 *
 * 使用方法：
   php P:/develope/www/dpa/eswine/common/tools/strip_end_whitespace.php -p D:/www/platform

 */
require_once( str_replace("\\","/",dirname(dirname(dirname(__FILE__))))."/configs/system.conf.php");
require_once("common/functions.php");
require_once("common/Files.cls.php");

// need trans
$_need_conv = array('php');
$_no_need_file = array(basename(__FILE__),'system.conf.php','UIBI_SIGN.conf.php','db.conf.php','MDB2_db.conf.php');

//
if (php_sapi_name()=='cli') {
  // 获取参数列表
  require_once 'Console/Getopt.php';
  $_options = Console_Getopt::getopt($argv, 'f:p:t:', array());
  $_o = array();
  if (!PEAR::isError($_options)) {
    foreach ($_options[0] as $l_v){
      $_o[$l_v[0]] = $l_v[1];
    }
  }
  $filename = (!empty($_o["f"])) ? $_o["f"] : "";
  $source_path = (!empty($_o["p"])) ? $_o["p"] : "";
  $target_path = (!empty($_o["t"])) ? $_o["t"] : $source_path;//通常都是覆盖
}else {
  $filename = isset($_GET["f"])?trim($_GET["f"]):"";
  $source_path = isset($_GET["p"])?trim($_GET["p"]):"";
  $target_path = isset($_GET["t"])?trim($_GET["t"]):$source_path;
}
$tar_charact="utf-8";

if (""!=$filename) {
  tOne($source_path,$filename,$target_path,$filename,$tar_charact);
}else {
  transPath($source_path,$target_path,true,$tar_charact);
}

function transPath($source_path,$target_path,$son=false,$tar_charact="utf-8"){
  // 如果是需要将某个目录下的所有文件转化一下，则需要遍历目录下文件
  $d = dir($source_path);
  if ($d) {
  while (false !== ($_file = $d->read())) {
    if ("."!=substr(ltrim($_file),0,1) && !in_array($_file,$GLOBALS['_no_need_file'])) {//  过滤掉 . .. .svn这三项
      // 单个文件的编码转化
      if(is_dir($source_path.DIRECTORY_SEPARATOR.$_file)){
        // 循环调用自身
        if($son) transPath($source_path.DIRECTORY_SEPARATOR.$_file,$target_path.DIRECTORY_SEPARATOR.$_file,$son,$tar_charact);
      }else {
        $s_file_name = $tar_file_name = $_file;
        tOne($source_path,$s_file_name,$target_path,$tar_file_name,$tar_charact);
      }
    }
  }
  $d->close();
  }
}

function tOne($s_path,$s_file,$tar_path,$tar_file,$tar_charact="utf-8"){
  // 只允许两种编码
  $charact_lower = strtolower($tar_charact);
  if ( "utf-8"!=$charact_lower && "gb2312"!=$charact_lower ) {
    echo "目前只对 gb2312, utf-8 互相转化";
    return null;
  }

  // 将指定的文件获取到内容
  if (file_exists($s_path."/".$s_file)) {
    $files = new Files();
    $ext = $files->getExt($s_file);
    if (!in_array(strtolower($ext),$GLOBALS['_need_conv'])) {
      return null; // 无需要转化的就直接返回
    }

    $l_cont = file_get_contents($s_path."/".$s_file);
    if ($l_cont == rtrim($l_cont)) {
      return ;
    }
    $cont = strip_end_whitespace_jieshufu($l_cont);  // 去掉末尾的空白，或者PHP结束符

    if ("utf-8"==$charact_lower) {
      // 判断是否为utf8编码的，如果是则不用转换，如果不是则需要转换
      if (!is_utf8_encode($cont)) {
        $cont = iconv("GBK","UTF-8//IGNORE",$cont);
        $cont = str_ireplace("charset=GB2312","charset=utf-8",$cont);
      }
    }else if ("gb2312"==$charact_lower) {
      // 判断是否为GB2312编码的，如果是则不用转换，如果不是则需要转换
      if (is_utf8_encode($cont)) {
        $cont = iconv("UTF-8","GBK",$cont);
        $cont = str_ireplace("charset=utf-8","charset=GB2312",$cont);
      }
    }else {
      echo "只支持 gb2312, utf-8 编码";
      return null;
    }

    // 最后保存为文件
    $files->overwriteContent($cont,$tar_path."/".$tar_file);
    echo $tar_path."/".$tar_file." 保存成功!"."\n";
  }else {
    echo "source file not exist!";
  }
}

function strip_end_whitespace_jieshufu($a_cont, $qudiao_jieshu=false){
  $l_cont = rtrim($a_cont);

  if ($qudiao_jieshu) {
    // 判断最后两个字符是否为 ? >
    if ("?>"==substr($l_cont,-2)) {
      $l_cont = substr($l_cont,0,-2);
    }
  }

  return $l_cont;
}
