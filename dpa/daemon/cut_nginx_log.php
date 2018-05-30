<?php
// 用于切割每天的日志，并且将日志进行入库, 然后删除旧的日志

// $remote_addr - $remote_user [$time_local] "$request" $status $body_bytes_sent "$http_referer" "$http_user_agent" $http_x_forwarded_for 此日志格式对应如下的正则表达式
$l_reg = '|(\S+[^ ])? - (\S+)? \[(\d+/\w+/\d+:\d+:\d+:\d+ [+-]\d+)\] "([^"]+)" (\d+) (\d+) "([^"]+)" "([^"]+)" (.+)|';

//
$l_file = "D:/www/ni9ni/data1/logs/unknownlogs.log";
if (file_exists($l_file)) {
  $l_tmp = file($l_file);
  foreach ($l_tmp as $l_k=>$l_v){
    $l_v = trim($l_v);
    if ("" != $l_v) {
      //
      if (preg_match($l_reg,$l_v,$matches)) {
        echo ($l_k+1) . "\n";
        print_r($matches);
        echo $l_v . "\n";
        exit;
      }else {
        // 格式不匹配的，可以记录一下位置

      }
    }else {
      // 对于空行不做处理
    }
  }
}else {
  echo "file: $_file not exist!";
}


