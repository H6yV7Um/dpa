<?php
//error_reporting(E_ALL);

$utime_file =  date("Y_m_d_H", floor(utime()));

$__FILEPT = "getunreadnum/".basename(__FILE__)."_".$_SERVER["SERVER_ADDR"]."_".$utime_file.".txt";
if ( 'WIN' !== strtoupper(substr(PHP_OS, 0, 3)) ) {
  $__FILEPT = $_SERVER["SINASRV_APPLOGS_DIR"].$__FILEPT;
}
$__rand = mt_rand(1000000000, 9999999999);

writeContent( logfmt( array( $__rand, str_pad("",10, " "), utime(), str_pad(__LINE__,3," ",STR_PAD_LEFT), $_SERVER["SERVER_ADDR"]) ), $__FILEPT );



writeContent( logfmt( array( $__rand, str_pad("",10, " "), utime(), str_pad(__LINE__,3," ",STR_PAD_LEFT), $_SERVER["SERVER_ADDR"]) ), $__FILEPT );

function logfmt($arr){
  $str = "";
  foreach ($arr as $k=>$v){
    if(2==$k) $v=str_pad($v,16, " ");
    $str .= $v." @@ ";
  }
  return rtrim($str, " @")."\r\n";
}

function utime() {
  // microtime() = current UNIX timestamp with microseconds
  $time  = explode( ' ', microtime());
  $usec  = (double)$time[0];
  $sec  = (double)$time[1];
  return $sec + $usec;
}

function writeContent( $content, $filePath, $overwrite=true, $mode='ab' ){
  if ( !file_exists($filePath) || $overwrite ) { // || is_writable($filePath)
    createdir(dirname($filePath)); // 创建目录
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

//建立目地文件夹
function createdir($dir=''){
  if (!is_dir($dir)){
    // 该参数本身不是目录 或者目录不存在的时候
    $temp = explode('/',$dir);
    $cur_dir = '';
    for($i=0;$i<count($temp);$i++){
      $cur_dir .= $temp[$i].'/';
      if (!@is_dir($cur_dir)){
        @mkdir($cur_dir,0775);
      }
    }
  }
}