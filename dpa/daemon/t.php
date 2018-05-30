<?php
/**
 * 数据库连接测试所需的最少文件
 *    主要用于测试 MySQL server has gone away 错误的原因.
 *
 * 原因找到：
more /data0/mysql/3306/my.cnf|grep timeout 的配置文件中找到
interactive_timeout = 120
wait_timeout = 120
----- 均是120秒（2分钟）没有操作就会过期，从而。测试如下： sleep (125)

解决办法是：
1. 在120秒内不停地进行 mysql_ping($link);
-- 实验证明可行，但是需要在程序中很多地方进行估算时间，比较费事，但是一个可行的解决办法，第二种

2. a)在120秒以后再重新尝试着连接 mysql_ping($link); 是否能成功呢？ (windows下默认为8小时 28800 , 命令行下执行 mysql> show variables like '%timeout'; 就能看到了 )
-- 实验证明此函数并不能重连成功
   b)如果不能成功是否可以再次执行dbo->connect()一下呢？
-- 实验证明 ,
   c)如果不能成功是否可以再次执行_doconnect(user,pass)一下呢？
-- 实验证明 , 此方法也不行
   d) 销毁 $GLOBALS['mdb2_conns'] ，然后进行重新连接是否可行？
-- 实验证明, 此方法也不行

基于以上原因，有理由认为不需要 new dbR()这样的方式，而采用其他替代方案。
1. 直接使用 $dbo = & MDB2::factory($dsn, $option);

--------------------
采用
$dbR->dbo = &DBO('grab'); 的切换方式最可行。
以后每次调用dbr->getone等的时候之前执行上述语句


 */
if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)) )require_once("D:/www/dpa/configs/system.conf.php");
else require_once("/data0/deve/runtime/configs/system.conf.php");
require_once("common/functions.php");
require_once("mod/DBR.cls.php");


$old_t = microtime();
list($ousec, $osec) = explode(" ", $old_t);
$dbR = new DBR();
$l_name0 = key($GLOBALS['mdb2_conns']);
echo $l_name0 . NEW_LINE_CHAR;
$l_err = $dbR->errorInfo();
if ($l_err[1]>0){
  // 数据库连接失败后
  echo date("Y-m-d H:i:s",$osec) . " error_msg: " . var_export($l_err,true). " LINE:".__LINE__. NEW_LINE_CHAR;
}else {
  echo date("Y-m-d H:i:s",$osec) . " succ! ". NEW_LINE_CHAR;
}

// 获取数据
$dbR->table_name = TABLENAME_PREF."project";
$p_arr = $dbR->GetOne("where 1 limit 1");
$l_err = $dbR->errorInfo();
if ($l_err[1]>0){
  echo date("Y-m-d H:i:s") . " error_msg: " . var_export($l_err,true). " FILE:".__FILE__. " LINE:". __LINE__ . NEW_LINE_CHAR;
}else {
  $l_dsn = $dbR->getDSN("array");
  echo date("Y-m-d H:i:s") . " succ! ". var_export($p_arr,true)." LINE:". __LINE__ . NEW_LINE_CHAR;
}

// 停顿125秒
$i = 65;
while ($i>0) {
  $i--;
  sleep(1);
  // 期间不断地进行 mysql_ping($l_link); 是否就能避免 MySQL server has gone away 的问题呢？

  /*
  此方法会增加内存使用
  $dbR2 = new DBR();
  $l_err = $dbR2->errorInfo();
  if ($l_err[1]>0){
    // 数据库连接失败后
    echo date("Y-m-d H:i:s") . " dbr2 failed: " . var_export($l_err,true). " FILE:".__FILE__. " LINE:". __LINE__ . NEW_LINE_CHAR;
  }else {
    $dbR2->table_name = TABLENAME_PREF."project";
    $p_arr2 = $dbR2->GetOne("where 1 limit 1");
    $l_rlt = mysql_ping($dbR2->dbo->connection);
  }*/

  $dbR->dbo = &DBO($l_name0);
  $l_rlt = mysql_ping($dbR->dbo->connection);

  echo date("Y-m-d H:i:s") . " i:".$i. " ". $dbR->dbo->connection . NEW_LINE_CHAR; // var_dump($l_rlt);
}

// 停顿125秒以后重新执行上面的查询就会失败
$end_t = microtime();
list($usec, $sec) = explode(" ", $end_t);
$dbR->table_name = TABLENAME_PREF."project";
$p_arr = $dbR->GetOne("where 1 limit 1");
$l_err = $dbR->errorInfo();
if ($l_err[1]>0){
  echo date("Y-m-d H:i:s",$sec) .' after '.($sec+$usec-$osec-$ousec).'s'. " error_msg: " . var_export($l_err,true). " FILE:".__FILE__. " LINE:". __LINE__ . NEW_LINE_CHAR;

  $dbR->table_name = TABLENAME_PREF."project";
  $p_arr = $dbR->GetOne("where 1 limit 1");
  $l_err = $dbR->errorInfo();
  if ($l_err[1]>0){
    echo date("Y-m-d H:i:s") . " reconnect_fail "." error_msg: " . var_export($l_err,true). " FILE:".__FILE__. " LINE:". __LINE__ . NEW_LINE_CHAR;
  }else {
    echo date("Y-m-d H:i:s") . " reconnect_succ! ". var_export($p_arr,true)." LINE:". __LINE__ . NEW_LINE_CHAR;
  }
}else {
  echo date("Y-m-d H:i:s",$sec) .' after '.($sec+$usec-$osec-$ousec).'s'. " succ! ". var_export($p_arr,true)." LINE:". __LINE__ . NEW_LINE_CHAR;
}
