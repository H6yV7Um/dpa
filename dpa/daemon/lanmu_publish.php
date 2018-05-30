<?php
/**
 * 本程序将一直运行着，直到有stop信号，这里的stop信号就是创建一个文件即可
 * windows启动 和 停止(创建文件即可)
          php D:/www/dpa/daemon/lanmu_publish.php
          echo '' > D:/tmp/stop_lanmupublish.txt 停止只需创建文件即可
 * linux下启动 和 停止
          php /data0/deve/projects/daemon/lanmu_publish.php 1>/dev/null 2>/dev/null &
          php /data0/deve/projects/daemon/lanmu_publish.php > /data1/logs/lanmu_publish_2016-03.txt &
          echo '' > /tmp/stop_lanmupublish.txt  停止命令

// 没有调用任何类的先测试一个月
 *
 */

// 控制进程数, 保证只有一个程序在运行
if ('WIN' !== strtoupper(substr(PHP_OS, 0, 3)) && getExecProcNum() > 1) exit;


// windows下无需输出日志
if ( 'WIN' === strtoupper(substr(PHP_OS, 0, 3)) ) {
  define("DEBUG2", true, true);
  define('STOP_FILE_PATH', "D:/tmp");

  require_once("D:/www/dpa/configs/system.conf.php");
  define("cmd_pre", 'D:/php5210/php ' . $GLOBALS['cfg']['PATH_RUNTIME'] . '/main.php', true);
} else {
  define("DEBUG2", true, true);
  define('STOP_FILE_PATH', "/tmp");

  // 按照ip进行细分
  $exec = "/sbin/ifconfig | grep 'inet addr' | awk '{ print $2 }' | awk -F ':' '{ print $2}' | head -1";
  $local_ip = exec($exec);
  if ('10.77.135.24' == trim($local_ip)) {
    require_once("/var/wd/cms/dpa/configs/system.conf.php");
  } else {
    // 不同的服务器可能配置不一样 TODO
    require_once("/home/sre/bz_11180/web_admin/dpa/configs/system.conf.php");
  }

  define("cmd_pre", 'php ' . $GLOBALS['cfg']['PATH_RUNTIME'] . '/main.php', true);
}

while (1) {
  main();
  sleep(2);
}

function main(){
  if (DEBUG2) {
    $old_t = microtime();
    list($ousec, $osec) = explode(" ", $old_t);
    echo "begin time: ".date("Y-m-d H:i:s",$osec) ." | microtime: ".($osec+$ousec)."\r\n";
  }
  proc_one();
  if (DEBUG2) {
    $end_t = microtime();
    list($usec, $sec) = explode(" ", $end_t);
    echo "end__ time: ".date("Y-m-d H:i:s",$sec) ." | microtime: ".($sec+$usec) .' spend: '.($sec+$usec-$osec-$ousec).'s'."\r\n"."\r\n";
  }
  // stop this program
  $l_stop_file = STOP_FILE_PATH .'/stop_lanmupublish.txt';
  if (file_exists($l_stop_file)){
    unlink($l_stop_file);
    exit;
  }
  if (date("Ym")>='201205') {
    // 设定4月，5月份以后改为调用class的方式，全部使用main.php的那一套，看看内存消耗情况，日志也用Files.cls.php
    //exit;
  }
}

function proc_one(){
  if (extension_loaded('memcached')) {
    require_once($GLOBALS['cfg']['PATH_RUNTIME'] . '/common/lib/MemCachedClient.php');
    $memcache = MemCachedClient::GetInstance('default');//
  } else {
    $memcache = new Memcache;
    $memcache->connect('localhost', 11211) or die ("Could not connect");
  }

  $l_key = '_lanmu_publish_';
  $get_result = $memcache->get($l_key);
  //print_r($get_result);

  if (is_array($get_result)) {
    $memcache->delete($l_key);  // 首先进行清空，然后进行后续处理

    foreach ($get_result as $p_id=>$a_val){
      foreach ($a_val as $t_id=>$l_val){
        foreach ($l_val as $id){
          $l_cmd = cmd_pre. ' -g "do=topublishdocs_edit&p_id='.$p_id.'&t_id='.$t_id.'&id='.$id.'"';
          //if (DEBUG2) echo date("Y-m-d H:i:s") . $l_cmd. "\r\n";
          $l_log = date("Y-m-d H:i:s"). " FILE:".__FILE__ ."\r\n"."CMD:". $l_cmd . "\r\n"."memory usage:".  memory_get_usage() .'; '. " memory usage peak(high):".  memory_get_peak_usage() . "\r\n\r\n";
          file_put_contents($GLOBALS['cfg']['LOG_PATH'] . "/lanmu_publish_".date("Y-m").".txt", $l_log, FILE_APPEND);
          exec($l_cmd);
        }
      }
    }
  }

  // 用于立即发布immediately
  $get_result = $memcache->get('_liji_publish_');
  if (is_array($get_result)) {
    $memcache->delete('_liji_publish_');  // 首先进行清空，然后进行后续处理

    $get_result = array_unique($get_result);  // 剔除重复的, 通常在种入的时候已经剔除，此处为了严格
    // 逐条命令进行执行
    foreach ($get_result as $l_cmd){
      $l_log = date("Y-m-d H:i:s"). " FILE:".__FILE__ ."\r\n"."CMD:". $l_cmd . "\r\n\r\n";
      file_put_contents($GLOBALS['cfg']['LOG_PATH'] . "/liji_publish_".date("Y-m").".txt", $l_log, FILE_APPEND);
      exec($l_cmd);
    }
  }
  return ;
}

// 获取当前进程数
function getExecProcNum($script = '') {
  if ('' == trim($script))
    $script = basename(__FILE__);
	$cmd = "/bin/ps axu|grep '$script' | grep -v grep";
	$result = array();
	exec($cmd, $result);
	$num = count($result);
	return $num;
}
