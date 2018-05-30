<?php
if (!defined("NEW_LINE_CHAR")) define('NEW_LINE_CHAR',"\r\n");
// 非windows环境下，可以使用 posix_getpid() 方法
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
  require_once("UnixDaemon.php");
  $pid_filePath = "/usr/home/chengfeng/daemon"; // 6.137 /data1/SINA/projects/deve/us_grab/daemon
  $pid_fileName = str_replace(array("\\","/",":"),"_",__FILE__).".txt";// pid_path 采用文件名的方式保存pid
  $_executor = new UnixDaemon($pid_filePath."/".$pid_fileName);
  $_executor->start();
}
// end save pid

main();

function main()
{
  $old_t = microtime();
  list($usec, $sec) = explode(" ", $old_t);
  echo         "begin time: ".date("Y-m-d H:i:s",$sec)." | microtime: ".$old_t.NEW_LINE_CHAR;
  while (true) {
    if (0==(time()+1-$sec)%10) {
      $end_t = microtime();
      list($usec, $sec) = explode(" ", $end_t);
      echo "end__ time: ".date("Y-m-d H:i:s",$sec)." | microtime: ".$end_t.NEW_LINE_CHAR;
      break;
    }
    sleep(1);
  }
}