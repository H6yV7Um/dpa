<?php
$date = date("Y_m_d");
$comm_path = "/data1/sql/";
$comm_dir = date("Ymd");

mysql_back($date, $comm_path, $comm_dir);

function mysql_back($date, $comm_path, $comm_dir){
    // 建一个目录, 并进入该目录
    $dir = rtrim($comm_path, ' /') . '/' . $comm_dir;
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $cmd_pre = "cd $dir;";

    // 备份mysql数据
    $cmd = "mysqldump -h139.196.176.221 -P3306 -uroot -p10y9c2U5 --default-character-set=utf8 dpa > ".$date."_dpa.sql";
    exec($cmd_pre . $cmd);  // 执行系统命令
    sleep(10);

    $cmd = "mysqldump -h139.196.176.221 -P3306 -uroot -p10y9c2U5 --default-character-set=utf8 wangzhan > ".$date."_wangzhan.sql";
    exec($cmd_pre . $cmd);
    sleep(10);

    $cmd = "mysqldump -h139.196.176.221 -P3306 -uroot -p10y9c2U5 --default-character-set=utf8 user > ".$date."_user.sql";
    exec($cmd_pre . $cmd);
}

