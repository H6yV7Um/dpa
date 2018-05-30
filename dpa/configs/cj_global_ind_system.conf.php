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
  define("db_character","utf8",true);
  define("db_character_contype","utf-8",true);
  define("out_character","utf8",true);
  define("out_character_contype","utf-8",true);

} else {
  define("PATH_ROOT","/data0/htdocs/admin/dpa");
  define("PATH_RUNTIME","/data0/deve/runtime");
  define("PATH_PEAR","/usr/local/webserver/php/lib/php");
  define('LOG_PATH',"/data1/logs");
  define('RES_WEBPATH_PREF',"/");
  define('INI_CONFIGS_PATH',"/data0/deve/config_ini_files");
  define("IFWIN",false,true);
  define("db_character","utf8",true);
  define("db_character_contype","utf-8",true);
  define("out_character","utf8",true);
  define("out_character_contype","utf-8",true);

}
define('Business_Path',"WEB-INF/Business");
define('Validate_Path',"WEB-INF/validate");
define('Template_Path',"WEB-INF/template");
define('Tpl_Path',"WEB-INF/tpl");
define('DEBUG',false);
define('DEFAULT_ACTION',"mainpage",true);
define('DEFAULT_LOGIN_ACTION',"login",true);
define('TABLENAME_PREF',"dpps_");
define('TABLENAME_USER', TABLENAME_PREF . "user");
define('TABLENAME_LOGINLOG', TABLENAME_PREF . "loginlog");
define('NEW_LINE_CHAR',"\r\n");

// -------------- 路径初始化 ------------------- //
ini_set('include_path','.'.PATH_SEPARATOR.PATH_PEAR.PATH_SEPARATOR.PATH_RUNTIME);
