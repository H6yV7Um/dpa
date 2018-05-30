<?php
/**
 * 兼容PHP4的代码
 *
 */
require_once('common/Files.cls.php');

class cFile
{
  // 如果重新创建的系统，则需要修改mysql数据库连接信息初始值
  function modifyMysqlConfigIniAndLANGConfigFileWhenCreateSYSTEM($data_arr){
    if ("SYSTEM"==strtoupper($data_arr["type"])) {
      $files = new Files();
      // 修改 INI_DB_DSN_CONFIGS_FILE (mysql_config.ini)
      $l_file = $GLOBALS['cfg']['INI_CONFIGS_PATH'] . "/" . $GLOBALS['cfg']['INI_DB_DSN_CONFIGS_FILE'];
      if (file_exists($l_file)) {
        $l_cont = file_get_contents($l_file);
        // 解析ini配置文件，需要增加或替换
        $l_configs = __fetch_config($GLOBALS['cfg']['INI_CONFIGS_PATH'],$GLOBALS['cfg']['INI_DB_DSN_CONFIGS_FILE']);

        if (!array_key_exists($data_arr["db_name"],$l_configs)) {
          // 不存在则增加
          $l_cont .= NEW_LINE_CHAR."[".$data_arr["db_name"]."]".NEW_LINE_CHAR;
          $l_cont .= 'dsn = "'. cString::getMysqlDsnStr($data_arr).'"'.NEW_LINE_CHAR;
        }else {
          // 存在则需要进行整块替换. 是否要进行替换，有待考量，暂时不进行替换
        }
        $files->overwriteContent($l_cont,$l_file);
      }

      // 修改 LANG_DEFINE_FILE (chinese.utf8.lang.php)
      $l_file = $GLOBALS['cfg']['PATH_RUNTIME'] . "/configs/system.conf.php";
      if (file_exists($l_file)) {
        $l_cont = file_get_contents($l_file);
        if (false!==strpos($l_cont,"SYSTEM_DB_DSN_NAME_")) {
          $l_pattern = '/define\(["\'](SYSTEM_DB_DSN_NAME_\w)["\'](\s+)?,(\s+)?["\']\w+["\']/i';
          $l_replacement = 'define("${1}","'.$data_arr["db_name"].'"';
          $l_cont = preg_replace($l_pattern,$l_replacement,$l_cont) ;
          $files->overwriteContent($l_cont,$l_file);
        }
      }
    }
  }
}