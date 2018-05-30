<?php
// l:单个文件的行数; f:文件路径和文件名 b:单行长度byte; n:新文件名前缀; p:存放路径
$_o = get_opt($argv, 'l:b:f:n:p:');

echo date("Y-m-d H:i:s") . "  " . $l_url . "\r\n";$l_begin_time = utime();
main($_o);
echo " cost:". ( utime() - $l_begin_time) . "\r\n";

function main($_o) {
  $byte  = 4096;//(isset($_o["b"]) && $_o["b"]>0) ? ($_o["b"]+0) : 4096;
  //$line  = (isset($_o["l"]) && $_o["l"]>0) ? ($_o["l"]+0) : 20;
  $file  = (isset($_o["f"]) && !empty($_o["f"])) ? $_o["p"] : "1.txt";
  $name  = (isset($_o["n"]) && !empty($_o["n"])) ? $_o["n"] : "__aa__.txt";
  $path  = (isset($_o["p"]) && !empty($_o["p"])) ? rtrim($_o["p"]," /") : ".";

  createdir($path);

  if (!file_exists($file)) {
    echo $file . " not exist! ";
    return ;
  }

  unlink($path."/".$name);

  $handle = @fopen($file, "r");
  if ($handle) {
    while (!feof($handle)) {
      // 单个文件的长度, 需要在适当的时候进行清零
      $buffer = fgets($handle, $byte);
      if (false !== strpos($buffer, "2")) file_put_contents($path."/".$name, $buffer, FILE_APPEND);
      var_dump($buffer);echo   "\r\n";
    }
    fclose($handle);
  }

  return null;
}

function get_opt($a_argv, $a_para_short='b:e:p:',$a_para_long=array()){
  // 获取参数列表
  require_once 'Console/Getopt.php';
  $_options = Console_Getopt::getopt($a_argv, $a_para_short, $a_para_long);
  $_o = array();
  if (!PEAR::isError($_options)) {
    foreach ($_options[0] as $l_v){
      $_o[$l_v[0]] = $l_v[1];
    }
  }
  return $_o;
}

function utime() {
  // microtime() = current UNIX timestamp with microseconds
  $time  = explode( ' ', microtime());
  $usec  = (double)$time[0];
  $sec  = (double)$time[1];
  return $sec + $usec;
}

//建立目地文件夹
function createdir($dir='')
{
  if (!is_dir($dir)){
    mkdir($dir,0775,true);
  }
}
