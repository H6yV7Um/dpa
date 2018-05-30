<?php
/**
 * 单个文件大小如果超过一定大小，可能会意外终止程序的运行。因此需要对文件的大小进行限制。
 *

php D:/www/dpa/common/tools/transcode/transgb2utf.php -p D:/www/pay/tenpay_openapi-PHP-SDK-1.1.3_gbk -t D:/www/pay/tenpay_openapi-PHP-SDK-1.1.3
php D:/www/dpa/common/tools/transcode/transgb2utf.php -p D:/www/pay/tenpay_appdemo-movieticket-php-1.0.2_gbk -t D:/www/pay/tenpay_appdemo-movieticket-php-1.0.2

php D:/www/dpa/common/tools/transcode/transgb2utf.php -p D:/www/pay/tenpay_B2C/php_utf8/classes -t D:/www/pay/tenpay_B2C/php_utf8/classes

 */
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED); // PHP5.3兼容问题

require_once(str_replace("\\", "/", dirname(dirname(dirname(dirname(__FILE__))))) . "/configs/system.conf.php");
require_once("common/functions.php");
require_once("common/Files.cls.php");

// need not trans
//$_no_need_conv = array("doc","zip");
$_need_conv = array('php','txt','sql');  // '*' 表示要转换所有的 'php'表示只处理php后缀的文件
$_no_need_file = array("transgb2utf.php","convCharacter.php","system.conf.php");


//
if (php_sapi_name()=='cli') {
  // 获取参数列表
 /* require_once 'Console/Getopt.php';
  $_options = Console_Getopt::getopt($argv, 'f:', array());
  $_o = array();
  if (!PEAR::isError($_options)) {
    foreach ($_options[0] as $l_v){
      $_o[$l_v[0]] = $l_v[1];
    }
  }*/
  $_o = cArray::get_opt($argv, 'f:p:t:c:');
}else {
  $_o = $_GET;
}
$common = "";
$filename = (!empty($_o["f"])) ? $_o["f"] : "";
$source_path = (!empty($_o["p"])) ? $_o["p"] : "D:/www/dpps".$common;
$target_path = (!empty($_o["t"])) ? $_o["t"] : "D:/www/dpa".$common;//通常都是覆盖


$tar_charact="utf-8";

if (""!=$filename) {
  tOne($source_path,$filename,$target_path,$filename,$tar_charact);
}else {
  //transPath($source_path,$target_path,true,$tar_charact);
}

function transPath($source_path,$target_path,$son=false,$tar_charact="utf-8"){
  global $_no_need_file;
  // 如果是需要将某个目录下的所有文件转化一下，则需要遍历目录下文件
  $d = @dir($source_path);
  if ($d) {
      while (false !== ($_file = $d->read())) {
        if ("."!=substr(ltrim($_file),0,1) && !in_array($_file,$_no_need_file)) {//  过滤掉 . .. .svn这三项
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
  //global $_no_need_conv;

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
    /*if (in_array(strtolower($ext),$_no_need_conv)) {
      return null; // 无需要转化的就直接返回
    }*/
    if (!in_array("*",$GLOBALS['_need_conv']) && !in_array(strtolower($ext),$GLOBALS['_need_conv'])) {
      return null; // 无需要转化的就直接返回
    }

    $cont = file_get_contents($s_path."/".$s_file);

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
    // 绝对地址换相对地址
    $cont = str_replace('dpa_html_gb2312','dpa_html',$cont);
    $cont = str_replace('dpps_html','dpa_html',$cont);
    $cont = str_replace('D:/www/dpa_gb2312','D:/www/dpa',$cont);
    $cont = str_replace('D:/www/dpps','D:/www/dpa',$cont);
    $cont = str_replace('/dpa_html','<!--{$RES_WEBPATH_PREF}-->dpa_html',$cont);

    // 最后保存为文件
    $files->overwriteContent($cont,$tar_path."/".$tar_file);
    echo $tar_path."/".$tar_file." 保存成功!"."\n";
  }else {
    echo "source file not exist!";
  }
}
