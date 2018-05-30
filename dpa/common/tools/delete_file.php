<?php
/**
 * 1. 删除或重命名某个目录（包括子目录）下的某些扩展名文件
 * 使用方法： -p 路径 -f 文件名 -e扩展名 -n不需要的扩展名 -r是否重命名
 *   php delete_file.php -p "D:/www/dpa/daemon/ganji/task/logs/sql/192.168.113.165" -e "txt" -r 1
 *
 * 2. 删除某个目录下的不需要的文件
 *   用法：
 * D:/php/php D:/www/dpa/common/tools/delete_file.php -p "D:/www/dpa/WEB-INF/Business" -w "D:/www/dpa/doc/business_need_file.txt"
 * 说明:
 * -w 需要保留的文件列表所在文件 -w "D:/www/dpa/doc/business_need_file.txt"
 * -p 目录 D:/www/dpa/WEB-INF/Business 下面删除一些不需要的文件
 *
 *
 * 也可以存放到文件中 del.bat
D:/php/php D:/www/dpa/common/tools/delete_file.php -p "D:/www/dpa/WEB-INF/Business" -w "D:/www/dpa/doc/business_need_file.txt"
D:/php/php D:/www/dpa/common/tools/delete_file.php -p "D:/www/dpa/WEB-INF/template" -w "D:/www/dpa/doc/template_need_file.txt"
D:/php/php D:/www/dpa/common/tools/delete_file.php -p "D:/www/dpa/WEB-INF/tpl" -w "D:/www/dpa/doc/tpl_need_file.txt"
D:/php/php D:/www/dpa/common/tools/delete_file.php -p "D:/www/dpa/WEB-INF/validate" -w "D:/www/dpa/doc/validate_need_file.txt"
 * 进行批量删除
 *
 */
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
  $l_comm = "D:/www/dpa";
}else {
  $l_comm = "/home/chengfeng/cf_tmp/dpa";
}
require_once($l_comm."/configs/system.conf.php");
require_once("common/functions.php");
require_once("common/lib/cArray.cls.php");
require_once("common/Files.cls.php");

$_o = cArray::get_opt($argv, 'p:f:e:r:n:w:');

if (isset($GLOBALS['_o']["w"])) {
  $_need_file_path = trim($GLOBALS['_o']["w"]); // 需要保留的文件
  $_need_file = file($_need_file_path);
  $_need_file = array_map("trim",$_need_file);  // 每项需要trim一下
}
// need not trans
$_no_need_conv   = isset($GLOBALS['_o']["n"])? explode(",", $GLOBALS['_o']["n"]):array("doc","zip");
$_need_conv   = isset($GLOBALS['_o']["e"])? explode(",", $GLOBALS['_o']["e"]):array("txt");

main();

function main(){

  $source_path = isset($GLOBALS['_o']["p"])?trim($GLOBALS['_o']["p"]): "D:/www/dpa/daemon/ganji/task/logs/sql/192.168.113.165";

  $filename = isset($GLOBALS['_o']["f"])?trim($GLOBALS['_o']["f"]):"";

  if (""!=$filename) {
    tOne($source_path,$filename);
  }else {
    transPath($source_path,true);
  }
}
function transPath($source_path,$son=false){
  // 如果是需要将某个目录下的所有文件转化一下，则需要遍历目录下文件
  $d = @dir($source_path);
  if ($d) {
      while (false !== ($_file = $d->read())) {
        if ("."!=substr(ltrim($_file),0,1)) {//  过滤掉 . .. .svn这三项
          //echo $_file.NEW_LINE_CHAR;
          // 单个文件的编码转化
          if(is_dir($source_path.DIRECTORY_SEPARATOR.$_file)){
            // 循环调用自身
            if($son) transPath($source_path.DIRECTORY_SEPARATOR.$_file,$son);
          }else {
            tOne($source_path,$_file);
          }
        }
      }
      $d->close();
  }
}

function tOne($s_path,$s_file){
  // 将指定的文件获取到内容
  if (file_exists($s_path."/".$s_file)) {

    if (isset($GLOBALS["_need_file"]) && is_array($GLOBALS["_need_file"])) {
      if ( !in_array($s_file,$GLOBALS["_need_file"]) ) {
        unlink($s_path."/".$s_file);
      }
    }else {

    $files = new Files();
    $ext = $files->getExt($s_file);

    if (in_array(strtolower($ext),$GLOBALS["_no_need_conv"])) {
      return null; // 无需要转化的就直接返回
    }


    if (in_array(strtolower($ext),$GLOBALS["_need_conv"])) {
      if (! $GLOBALS['_o']["r"]){
        unlink($s_path."/".$s_file);
      }else {
        $n_file = str_replace(".sql", "_sql.txt", $s_file);
        echo $n_file."\r\n";
        rename($s_path."/".$s_file, $s_path."/".$n_file);
      }
    }
    }
  }else {
    echo "source file not exist!\n";
  }
}
