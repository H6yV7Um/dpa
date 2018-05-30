<?php
if (!defined("NEW_LINE_CHAR")) define('NEW_LINE_CHAR',"\r\n");
define("DEBUG",true,true);
// 循环执行
//while(1){
  if (DEBUG) {
    $old_t = microtime();
    list($usec, $sec) = explode(" ", $old_t);
    echo "begin time: ".date("Y-m-d H:i:s",$sec) ." | microtime: ".$old_t.NEW_LINE_CHAR;
  }
  main();
  //sleep(6);
  if (DEBUG) {
    $end_t = microtime();
    list($usec, $sec) = explode(" ", $end_t);
    echo "end__ time: ".date("Y-m-d H:i:s",$sec) ." | microtime: ".$end_t.NEW_LINE_CHAR.NEW_LINE_CHAR.NEW_LINE_CHAR;
  }
//}

function main(){
  if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
    //$exec = "php-win daemon.php >> a.txt ";  // windows下后台运行

    for($i=0;$i<50;$i++){
      $b = exec("php-win daemon.php >> win".$i.".txt ");
    }
  }else {

    /*// 开启子进程测试，能保证同时进行，即并发
    require_once("proc_mgr.ex.php");

    for($i=0;$i<1000;$i++){
      $dispatch_map[] = array('main_0', $i);
    }
    $pm = new proc_mgr($dispatch_map, false);
    $pm->start_proc();
    */

    // 另一种后台运行的方式，前后会有一定时间的延后
    for($i=0;$i<1000;$i++){
      $b = exec("php daemon.php >> unix_".$i.".txt &");
    }
  }
}

function main_0($para)
{
  $old_t = microtime();
  list($usec, $sec) = explode(" ", $old_t);
  $str =          "begin time: ".date("Y-m-d H:i:s",$sec)." | microtime: ".$old_t.NEW_LINE_CHAR;
  while (true) {
    sleep(1);
    if (0==(time()+1-$sec)%10) {
      $end_t = microtime();
      list($usec, $sec) = explode(" ", $end_t);
      $str .= "end__ time: ".date("Y-m-d H:i:s",$sec)." | microtime: ".$end_t.NEW_LINE_CHAR.NEW_LINE_CHAR;
      break;
    }
  }
  $rlt = $para.NEW_LINE_CHAR.$str; // 加上序号
  echo $rlt;
  return $rlt;
}
