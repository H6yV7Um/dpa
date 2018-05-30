<?php
/**
 * 查找某个目录(-p)下所有的文件中含有某个字符串(-s)的文件，并将字符串替换成指定(-r)的字符串。
 *
 * 使用方法: [windows下特殊符号需要用双引号. 例如第二条就需要双引号]

php replace_str.php -p "D:/www/dpa/WEB-INF" -f Document_addAction.cls.php -s 'TPL_WENDANG_STR'
php replace_str.php -p "D:/www/net_cn_ftp/htdocs/it" -s "<!--#include file=" -r "<!--#include virtual="
php replace_str.php -p "D:/www/net_cn_ftp/htdocs" -s "http://t.ni9ni.com/it" -r "http://www.ni9ni.com/it"
php replace_str.php -p "D:/www/net_cn_ftp/htdocs" -s "http://img3.ni9ni.com/" -r "http://www.ni9ni.com/img3/"
php replace_str.php -p "D:/www/net_cn_ftp/htdocs" -s "include virtual=\"/f" -r "include virtual=\"/ssi/f"
php replace_str.php -p "D:/www/net_cn_ftp/htdocs" -s "include virtual=\"/h" -r "include virtual=\"/ssi/h"

php replace_str.php -p "D:/www/net_cn_ftp/htdocs/xiaohua/20120805" -s "include file=\"/ssi" -r "include virtual=\"/ssi"

 */
require_once("D:/www/dpa/configs/system.conf.php");
require_once("common/functions.php");
require_once("common/Files.cls.php");

// need not trans
$_no_need_conv = array("doc","zip");
$_only_type     = array("php");
$_no_need_file = array("transgb2utf.php","convCharacter.php","system.conf.php");

//
if (php_sapi_name()=='cli') {
  // 获取参数列表
  require_once 'Console/Getopt.php';
  $_options = Console_Getopt::getopt($argv, 'p:f:s:r:', array());
  $_o = array();
  if (!PEAR::isError($_options)) {
    foreach ($_options[0] as $l_v){
      $_o[$l_v[0]] = $l_v[1];
    }
  }
  $l_path = (!empty($_o["p"])) ? $_o["p"] : "D:/www/dpa/WEB-INF";
  $l_file = (!empty($_o["f"])) ? $_o["f"] : "";
  $l_str  = (!empty($_o["s"])) ? $_o["s"] : "TPL_WENDANG_STR";
  $l_repl = (!empty($_o["r"])) ? $_o["r"] : '$GLOBALS[\'language\'][\''.$l_str.'\']';
}else {
  exit();
}
$tar_charact="utf-8";

if (""!=$l_file) {
  tOne($l_path,$l_file,$l_path,$l_file,$l_str,$l_repl,$tar_charact);
}else {
  transPath($l_path,$l_path,$l_str,$l_repl,true,$tar_charact);
}

function transPath($source_path,$target_path,$l_str,$l_repl,$son=false,$tar_charact="utf-8"){
  global $_no_need_file;
  // 如果是需要将某个目录下的所有文件转化一下，则需要遍历目录下文件
  $d = dir($source_path);
  if ($d) {
  while (false !== ($_file = $d->read())) {
    // echo $_file."\n";
    if ("."!=substr(ltrim($_file),0,1) && !in_array($_file,$_no_need_file)) {//  过滤掉 . .. .svn这三项
      // 单个文件的编码转化
      if(is_dir($source_path.'/'.$_file)){
        // 循环调用自身
        if($son) transPath($source_path.'/'.$_file,$target_path.'/'.$_file,$l_str,$l_repl,$son,$tar_charact);
      }else {
        $s_file_name = $tar_file_name = $_file;
        tOne($source_path,$s_file_name,$target_path,$tar_file_name,$l_str,$l_repl,$tar_charact);
      }
    }
  }
  $d->close();
  }
}

function tOne($s_path,$s_file,$tar_path,$tar_file,$a_str,$a_repl,$tar_charact="utf-8"){
  global $_no_need_conv;

  // 只允许两种编码
  $charact_lower = strtolower($tar_charact);
  if ( "utf-8"!=$charact_lower && "gb2312"!=$charact_lower ) {
    echo "目前只对 gb2312, utf-8 互相转化";
    return null;
  }
  echo $s_path."/".$s_file."\r\n";
  // 将指定的文件获取到内容
  if (file_exists($s_path."/".$s_file)) {

    $files = new Files();
    $ext = $files->getExt($s_file);
    if (in_array(strtolower($ext),$_no_need_conv)) {
      return null; // 无需要转化的就直接返回
    }

    $cont = file_get_contents($s_path."/".$s_file);

    // 如果被查找的字符串有引号则无需替换
    if (false !== strpos($cont,"'$a_str'")) {
      echo $s_path . "/" . $s_file . " it has replace\n";
      return null;
    }
    if (false === strpos($cont, $a_str)) {
      //echo "\n";  // 并不包含需要的字符串
      return null;
    }

    if ("utf-8"==$charact_lower) {
      // 判断是否为utf8编码的，如果是则不用转换，如果不是则需要转换
      if (is_utf8_encode($cont)) {

      }else {
        // 是先替换字符，还是先转码，需要依据当前文档的编码而定
        // 保证当前文档编码跟需要替换的编码一致，如果是英文的可以不考虑这个问题
        // 先进行转码，转码为当前文本同样的字符编码
        $cont = iconv("GBK","UTF-8//IGNORE",$cont);
        $cont = str_ireplace("charset=GB2312","charset=utf-8",$cont);
      }
    }else if ("gb2312"==$charact_lower) {
      // 判断是否为GB2312编码的，如果是则不用转换，如果不是则需要转换
      if (!is_utf8_encode($cont)) {

      }else {
        $cont = iconv("UTF-8","GBK",$cont);
        $cont = str_ireplace("charset=utf-8","charset=GB2312",$cont);
      }
    }else {
      echo "只支持 gb2312, utf-8 编码";
      return null;
    }
    // 进行替换
    $cont = str_replace($a_str,$a_repl,$cont);

    // 最后保存为文件
    $files->overwriteContent($cont,$tar_path."/".$tar_file);
    echo $tar_path."/".$tar_file." save succ!"."\n";
  }else {
    echo "source file not exist!";
  }
}
