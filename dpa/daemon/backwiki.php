<?php
$date = date("Y_m_d");
$comm_path = "/data1/backup/wiki.sina.com.cn/";
$comm_dir = date("Ymd");

main($date, $comm_path, $comm_dir);

function main($date, $comm_path, $comm_dir){
  // mysql back
  mysql_back($date, $comm_path, $comm_dir);

  // 接着备份wiki的配置文件, file back, 本机才进行文件备份
  $ini_array = parse_ini_file("/etc/sysconfig/network-scripts/ifcfg-eth0");
  if ("10.210.74.89" == trim($ini_array["IPADDR"])) {
    file_back($date, $comm_path, $comm_dir);
  }
}

function mysql_back($date, $comm_path, $comm_dir){
  // 建一个目录, 并进入该目录
  $cmd = " cd $comm_path; mkdir $comm_dir; cd $comm_dir; ";

  // 备份mysql数据
  $cmd .= "mysqldump -h10.210.141.102 -P3600 -uapi_wiki -p123qwe api_wiki > ".$date.".api_wiki.sql";
  exec($cmd);  // 执行系统命令
}

function file_back($date, $comm_path, $comm_dir){
  $cmd = " cd $comm_path; mkdir $comm_dir; cd $comm_dir; ";
  $cmd .= "cp /data1/www/htdocs/wiki.sina.com.cn/LocalSettings.php ".$date.".LocalSettings.php";
  exec($cmd);  // 执行系统命令
}


