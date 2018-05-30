<?php
/**
 * 1. 根据数据库结构，自动生成business文件、tpl文件、并修改mvc.conf.php
 * 2. 根据模板文件(template下)，并自动生成上述文件
 */
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
  define("SOURCE_PATH_PRE","D:/www/dpa/WEB-INF");
} else {
  define("SOURCE_PATH_PRE","/home/auto/projects/images_bitauto/pic");
}
require_once(SOURCE_PATH_PRE."/mvc.conf.php");

main_template();

function main_template(){
  // 获取相册列表
  $files1 = scandir(SOURCE_PATH_PRE."/template");

  foreach ($files1 as $html_name){
    if ("."!=$html_name && ".."!=$html_name && ""!=$html_name
    && "header.html"!=$html_name && "footer.html"!=$html_name && ".svn"!=$html_name
    && "add.html"!=$html_name && "list.html"!=$html_name
    && "css"!=$html_name && "images"!=$html_name && "js"!=$html_name) {//  过滤掉目录
      // 只取文件名
      $para = substr($html_name,0,strpos($html_name,"."));

      if (false!==strpos($html_name,"_list")) {
        $_like = "document_list";
      }else if (false!==strpos($html_name,"_del")) {
        $_like = "project_del";
      }else if (false!==strpos($html_name,"_edit")) {
        $_like = "document_edit";
      }else if (false!==strpos($html_name,"_add")) {
        $_like = "document_add";
      }else {
        $_like = "mainpage";
      }
      // 在 business 中创建 action文件  和 验证文件 (如果不存在)
      buildBusiness(SOURCE_PATH_PRE."/Business", $para, $_like,"Action");
      //buildBusiness(SOURCE_PATH_PRE."/validate", $para, $_like,"Validate");

      // 在 tpl 中创建 tpl 文件(如果不存在)
      buildTPL(SOURCE_PATH_PRE."/tpl", $para, "mainpage.php");

      // 在 mod 中创建  文件(如果不存在)
      //buildMod(str_replace("WEB-INF","mod",SOURCE_PATH_PRE), $para, "Document","R");
      //buildMod(str_replace("WEB-INF","mod",SOURCE_PATH_PRE), $para, "Document","W");

      // 修改文件 mvc.conf.php, mvc.conf.php 以后不需要进行在末尾追加处理。
      //modifyMVCconf(SOURCE_PATH_PRE, $para, "mainpage");
      //exit;
    }
  }
}

function main_db(){
  // 从数据库获得表，依据表创建相应的操作, 以后考虑此方式

}

function buildMod($path,$para,$default,$exff = "R"){
  $val = str_replace(array("_add","_list","_del","_edit","_res"),"",$para);
  if ("footer"!=$val && "header"!=$val && "login"!=$val) {
    $U_para = strtoupper((substr($val,0,1))).(substr($val,1));
    $U_default= strtoupper((substr($default,0,1))).(substr($default,1));// 保证首字母是大写
    $L_default= strtolower((substr($default,0,1))).(substr($default,1));// 保证首字母是小写
    // 建立 文件，并修改文件内容
    $content  = file_get_contents($path."/".$default."$exff.cls.php");
    $content  = str_replace("lib_".$L_default,"lib_".$val,$content);// 先替换lib 表名
    $content  = str_replace($U_default,$U_para,$content);  //
    $filename = $U_para."$exff.cls.php";

    if (!file_exists($path."/".$filename)) {
      writeContent( $content, $path."/".$filename );
      echo date("Y-m-d H:i:s")." ".$path."/".$filename."   succ! "."\r\n";
    }
  }
}

function modifyMVCconf($path,$para,$default){
  $filename = "mvc.conf.php";
  $n_para = strtoupper((substr($para,0,1))).(substr($para,1));
  $_default = "
  '$para'=>array(
        'validate'      =>  true,
        'forwards'      =>  array('sysError'=> array('name'=>'sysError','path'=>'sysError.html','redirct'=>true))
    ),";

  // 如果没有配置，则需要自动添加
  if (!key_exists($para,$GLOBALS['ACTION_CONFIGS'])) {
    $o_content = file_get_contents($path."/".$filename);

    $posit = '$GLOBALS["ACTION_CONFIGS"] = array(';
    $new_cont  = str_replace($posit,$posit.$_default,$o_content);

    writeContent( $new_cont, $path."/".$filename );
    echo date("Y-m-d H:i:s")." ".$path."/".$filename."   succ! "."\r\n";
  }else {
    echo date("Y-m-d H:i:s")." ".$para." key exist! "."\r\n";
  }
}

function buildTPL($path,$para,$defaultcontent){
  $content = file_get_contents($path."/".$defaultcontent);
  $filename = $para.".php";

  if (!file_exists($path."/".$filename)) {
    writeContent( $content, $path."/".$filename );
    echo date("Y-m-d H:i:s")." ".$path."/".$filename."   succ! "."\r\n";
  }
}

function buildBusiness($path,$para,$default,$exff="Action"){
  $n_para = strtoupper((substr($para,0,1))).(substr($para,1));
  $default= strtoupper((substr($default,0,1))).(substr($default,1));// 保证首字母是大写
  // 建立 validate 文件，并修改文件内容
  $content = file_get_contents($path."/".$default."$exff.cls.php");
  $content = str_replace($default.$exff,$n_para.$exff,$content);//
  $filename = $n_para."$exff.cls.php";

  if (!file_exists($path."/".$filename)) {
    writeContent( $content, $path."/".$filename );
    echo date("Y-m-d H:i:s")." ".$path."/".$filename."   succ! "."\r\n";
  }
}

//建立目地文件夹
function createdir($dir='')
{
  if (!is_dir($dir)){
    // 该参数本身不是目录 或者目录不存在的时候
    $temp = explode('/',$dir);
    $cur_dir = '';
    for($i=0;$i<count($temp);$i++)
    {
      $cur_dir .= $temp[$i].'/';
      if (!is_dir($cur_dir))
      {
        @mkdir($cur_dir,0775);
      }
    }
  }
}

function writeContent( $content, $filePath, $mode='w' ){
    createdir(dirname($filePath));
    if ( !file_exists($filePath) || is_writable($filePath) ) {

      if (!$handle = @fopen($filePath, $mode)) {
        return "can't open file $filePath";
      }

      if (!fwrite($handle, $content)) {
        return "cann't write into file $filePath";
      }

      fclose($handle);

      return '';

    } else {
      return "file $filePath isn't writable";
    }
}

// \.elements\((['"])?(\w+)['"]?\) .elements[$1$2$1]
// (\w+)\.cgi\? main.php?do=$1&
