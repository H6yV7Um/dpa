<?php
/**
 * 线上覆盖所有文件的 overfile.php , 使用方法：
 * php overfile.php -p /data0/deve/runtime
 * php overfile.php -p /data0/htdocs/admin/dpa
 * php /data0/deve/runtime/common/tools/overfile.php -p /data0/htdocs/admin/dpa/WEB-INF/Business -f Topublishdocs_editAction.cls.php

   php P:/develope/www/dpa/work/common/tools/overfile.php -p D:/www/cgi-bin
   php overfile.php -p D:/dpa/doc
 */
set_magic_quotes_runtime(0);
ini_set('memory_limit', -1);

require_once( str_replace("\\","/",dirname(dirname(dirname(__FILE__))))."/configs/system.conf.php");
require_once("common/functions.php");
require_once("common/lib/cArray.cls.php");
require_once("common/Files.cls.php");

// need trans
$_need_conv = array('*');  // '*' 表示要转换所有的 'php'表示只处理php后缀的文件
$_no_need_file = array(basename(__FILE__),'system.conf.php','UIBI_SIGN.conf.php','db.conf.php','MDB2_db.conf.php','overfile.php','chinese.utf8.lang.php');

//
if ('cli'==php_sapi_name()) {
  $_o = cArray::get_opt($argv, 'f:p:t:c:');
}else {
  $_o = $_GET;
}
$filename = (!empty($_o["f"])) ? $_o["f"] : "";
$source_path = (!empty($_o["p"])) ? $_o["p"] : "";
$target_path = (!empty($_o["t"])) ? $_o["t"] : $source_path;//通常都是覆盖

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
          if(is_dir($source_path.'/'.$_file)){
            // 循环调用自身
            if($son) transPath($source_path.'/'.$_file,$target_path.'/'.$_file,$son,$tar_charact);
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
    if (!in_array("*",$GLOBALS['_need_conv']) && !in_array(strtolower($ext),$GLOBALS['_need_conv'])) {
      return null; // 无需要转化的就直接返回
    }

    $cont = isset($GLOBALS['_o']['c'])? isset($GLOBALS['_o']['c']) : md5(time());//"hello world!";  防止文件过大导致内存泄露而终止file_get_contents($s_path."/".$s_file) // 随意字符

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
    $l_path_file_ = str_replace("\\","/",$tar_path."/".$tar_file);
    $files->overwriteContent($cont, $l_path_file_);
    echo $l_path_file_." save succ!"."\n";
  }else {
    echo "source file not exist!";
  }
}
