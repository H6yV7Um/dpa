<?php
// windows机器上同服务器上的一些不同,但php命令行模式下运行的时候通常也不在windows下（极少）
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
  define("PATH_ROOT","D:/www/dpa");
  define('PATH_RUNTIME',"D:/www/dpa");
  define("PATH_PEAR","D:/www/pear");
  define('LOG_PATH',"D:/www");
  define('RES_WEBPATH_PREF',"/");
  define('INI_CONFIGS_PATH',"D:/www/config");
  define("IFWIN",true,true);
  define("db_character","latin1",true);
  define("db_character_contype","gb2312",true);
  define("out_character","utf8",true);
  define("out_character_contype","utf8",true);

} else {
  define("PATH_ROOT","/data2/SINA/projects/cron/rgt_");
  define("PATH_RUNTIME","/data0/deve/runtime");
  define("PATH_PEAR","/usr/local/share/pear");
  define('LOG_PATH',"/");
  define('RES_WEBPATH_PREF',"/");
  define('INI_CONFIGS_PATH',"/data2/SINA/runtime/config");
  define("IFWIN",false,true);
  define("db_character","latin1",true);
  define("db_character_contype","gb2312",true);
  define("out_character","gb2312",true);
  define("out_character_contype","gb2312",true);

}

define('DEBUG',false);
define('TABLENAME_PREF',"dpps_");
define('NEW_LINE_CHAR',"\r\n");

// -------------- 路径初始化 ------------------- //
ini_set('include_path','.'.PATH_SEPARATOR.PATH_PEAR.PATH_SEPARATOR.PATH_RUNTIME);
