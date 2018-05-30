#bin/bash /usr/local/webserver/php/bin/php
<?php
/**
 * 获取参数列表
 *
 * -p 路径，默认为当前目录 ./
 * -t 类型，file 文件；folder 目录 ； all 文件和目录。默认为目录
 * -m 只删除一个还是多个,默认多个
 * -n 文件或目录的名称 -- 需要全匹配，以后支持正则表达式
 *

php D:/www/dpa/common/tools/rm_svn.php -p D:/www/wanda_git/ffan/xadmin/

 */

require_once 'Console/Getopt.php';
$_options = Console_Getopt::getopt($argv, 'p:t:m:n:', array());
$_o = array();
if (!PEAR::isError($_options)) {
  foreach ($_options[0] as $l_v){
    $_o[$l_v[0]] = $l_v[1];
  }
}

main($_o);

function main($_o){
  //$name = (!empty($_o["n"])) ? $_o["n"] : ".svn";    // 目前都是写死了的，只删除.svn
  $type = (!empty($_o["t"])) ? $_o["t"] : "folder";
  $path = (!empty($_o["p"])) ? $_o["p"] : "./";
  $mult = (!empty($_o["m"])) ? $_o["m"] : true;

  if ( 'WIN' === strtoupper(substr(PHP_OS, 0, 3)) ) {
    transPath($path, ".svn");
  } else {
    $l_a = 1;  // 执行结果
    if ($mult) {
      while (""!==trim($l_a)) {
        proc_one($l_a, $path);
      }
    } else {
      proc_one($l_a, $path);
    }
  }
}

//
function transPath($source_path,$name=".svn"){
  // 如果是需要将某个目录下的所有文件转化一下，则需要遍历目录下文件
  $d = dir($source_path);
  if ($d) {
  while (false !== ($_file = $d->read())) {
    if ($_file != "." && $_file != "..") {//  只删除指定名称的文件或目录
      // 检查是目录还是文件
      if(is_dir($source_path.DIRECTORY_SEPARATOR.$_file)){
        // 遍历子目录
        transPath($source_path.DIRECTORY_SEPARATOR.$_file, $name);
        // 删除整个目录
        if($name == $_file)removeDir($source_path.DIRECTORY_SEPARATOR.$_file);
      }else {
        // 删除指定文件名
        if($name == $_file) tOne($source_path,$_file);
      }
    }
  }
  $d->close();
  }
}

// removeDir是比remove_directory更简洁的方法，推荐使用此方法
function removeDir($dirName) {
  $result = false;
  if( !is_dir($dirName) ) {
    trigger_error("目录名称错误", E_USER_ERROR);
  }
  $handle = opendir($dirName);
  while(($file = readdir($handle)) !== false) {
    if($file != '.' && $file != '..') {
      $dir = $dirName . DIRECTORY_SEPARATOR . $file;
      is_dir($dir) ? removeDir($dir) : unlink($dir);
    }
  }
  closedir($handle);
  $result = rmdir($dirName) ? true : false;
  return $result;
}
/*
function remove_directory($source_path) {
  $d = dir($source_path);
  if ($d) {
  while (false !== ($_file = $d->read())) {
    if ($_file != "." && $_file != "..") {
      // 检查是目录还是文件
      if(is_dir($source_path.DIRECTORY_SEPARATOR.$_file)){
        // 删除整个目录
        remove_directory($source_path.DIRECTORY_SEPARATOR.$_file);
      }else {
        tOne($source_path,$_file);
      }
    }
  }
  $d->close();
  }
  if(rmdir($source_path)){
    //echo date("Y-m-d H:i:s")." dir removing succ  $source_path \n";
  }else{
    echo date("Y-m-d H:i:s")." dir removing error $source_path \n";
  }

  return null;
}*/

function tOne($s_path,$s_file){
  // 删除单个文件
  $l_file_ = $s_path.DIRECTORY_SEPARATOR.$s_file;
  if (file_exists($l_file_)) {
    if(unlink($l_file_)){
      echo date("Y-m-d H:i:s")." file removing succ  $l_file_ \n";
    } else {
      echo date("Y-m-d H:i:s")." file removing error $l_file_ \n";
      exit;
    }
  }else {
        echo date("Y-m-d H:i:s")." source file not exist! $l_file_ \n";
  }
  return null;
}

function proc_one(&$l_a, $path){
  $l_comd = "find $path -name  '.svn'";
  echo "\r\n".$l_comd."\r\n";
  echo "being: \r\n";
  $l_a = exec($l_comd);

  $l_comd = "rm -rf ".$l_a;
  echo "\r\n".$l_comd."\r\n";
  $l_e2 = exec($l_comd);
  echo "exec: "."\r\n";
  //print_r($l_e2);
  echo "\r\n end "."\r\n";
}

