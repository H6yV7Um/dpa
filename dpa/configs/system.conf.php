<?php
// windows机器上同服务器上的一些不同,但php命令行模式下运行的时候通常也不在windows下（极少）
// define\(["'](\w+)["'],["'](\w+)["']([^\)]+)?\) => \$GLOBALS['cfg']['$1'] = "$2"
// define\(["'](\w+)["'],(\w+)([^\)]+)?\) => \$GLOBALS['cfg']['$1'] = $2
// define\(["'](\w+)["'],["']([^"]+)["']([^\)]+)?\) => \$GLOBALS['cfg']['$1'] = "$2"
define('SESSION_EXPIRE_TIME', 0); // session的超时时间
define('SESSIONID', 'SESSIONID');

$GLOBALS['cfg']['WEB_DOMAIN'] = 'hiexhibition.com';
$GLOBALS['cfg']['SMS_CODE_KEY'] = '_SMS_CODE_KEY'; // 短语验证码key

$GLOBALS['cfg']['__LIMIT__'] = '__LIMIT__';   // 条数限制
$GLOBALS['cfg']['__OFFSET__'] = '__OFFSET__'; // 起始位置

$GLOBALS['cfg']['DEVELOPE_ENV'] = '';
if ('cli' == php_sapi_name() && isset($_o) && $_o["e"]) $GLOBALS['cfg']['DEVELOPE_ENV'] = $_o["e"];

// memcached 服务器地址及端口, 支持多组memcache
$GLOBALS['g_memcached_servers'] = array(
    'default' => array(array('127.0.0.1', 11211),
        array('127.0.0.1', 11211),
    ),
    'session' => array(array('127.0.0.1', 11211),
        array('127.0.0.1', 11211),
    ),
);
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    if(!isset($GLOBALS['cfg']['PATH_ROOT'])) $GLOBALS['cfg']['PATH_ROOT'] = str_replace("\\","/",dirname(dirname(__FILE__)));//"D:/www/dpa";
    $GLOBALS['cfg']['PATH_RUNTIME'] = str_replace("\\","/",dirname(dirname(__FILE__)));    // PATH_RUNTIME用于后台程序的，PATH_ROOT前台程序
    $GLOBALS['cfg']['PATH_PHP_LIBS'] = "D:/www";
    $GLOBALS['cfg']['PATH_PEAR'] = $GLOBALS['cfg']['PATH_PHP_LIBS'] . "/pear";
    $GLOBALS['cfg']['LOG_PATH']  = "D:/www";
    $GLOBALS['cfg']['RES_WEBPATH_PREF'] = "/";
    $GLOBALS['cfg']['INI_CONFIGS_PATH'] = "D:/www/config";
    $GLOBALS['cfg']['IFMDB2'] = true;
    $GLOBALS['cfg']['IFMYSQLI'] = false;
    $GLOBALS['cfg']['MEMCACHE_SESSION'] = true;
    $GLOBALS['cfg']['db_character'] = "utf8";
    $GLOBALS['cfg']['db_character_contype'] = "utf-8";
    $GLOBALS['cfg']['out_character'] = "utf8";
    $GLOBALS['cfg']['out_character_contype'] = "utf-8";
    $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_W'] = "dpa";
    $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R'] = "dpa";
    $GLOBALS['cfg']['WEB_PROXY_FILE'] = "D:/www/config/proxy.ini";
    $GLOBALS['cfg']['IMG_UPLOAD_PATH'] = "D:/www/dpa/img3/upload";
    $GLOBALS['cfg']['IMG_URL_PRE'] = $GLOBALS['cfg']['RES_WEBPATH_PREF'] . "dpa/img3/upload";

} else if ('CYGWIN' === strtoupper(PHP_OS)) {
    if(!isset($GLOBALS['cfg']['PATH_ROOT'])) $GLOBALS['cfg']['PATH_ROOT'] = "/cygdrive/d/www/dpa";
    $GLOBALS['cfg']['PATH_RUNTIME'] = "/cygdrive/d/www/dpa";
    $GLOBALS['cfg']['PATH_PHP_LIBS'] = "/cygdrive/d/www";
    $GLOBALS['cfg']['PATH_PEAR'] = $GLOBALS['cfg']['PATH_PHP_LIBS'] . "/pear";
    $GLOBALS['cfg']['LOG_PATH']  = "/data1/logs";
    $GLOBALS['cfg']['RES_WEBPATH_PREF'] = "/";
    $GLOBALS['cfg']['INI_CONFIGS_PATH'] = "/cygdrive/d/www/config";
    $GLOBALS['cfg']['IFMDB2'] = false;
    $GLOBALS['cfg']['IFMYSQLI'] = false;
    $GLOBALS['cfg']['MEMCACHE_SESSION'] = true;
    $GLOBALS['cfg']['db_character'] = "utf8";
    $GLOBALS['cfg']['db_character_contype'] = "utf-8";
    $GLOBALS['cfg']['out_character'] = "utf8";
    $GLOBALS['cfg']['out_character_contype'] = "utf-8";

} else if ('DARWIN' === strtoupper(PHP_OS)) {
    // 苹果 Mac 得到的是Darwin

    if(!isset($GLOBALS['cfg']['PATH_ROOT'])) $GLOBALS['cfg']['PATH_ROOT'] = "/Users/cf/svn_dev/dpa";
    $GLOBALS['cfg']['PATH_RUNTIME'] = str_replace("\\","/",dirname(dirname(__FILE__)));//"/data0/deve/runtime";
    $GLOBALS['cfg']['PATH_PHP_LIBS'] = "/usr/local/opt/php56/lib/php/libs";
    $GLOBALS['cfg']['PATH_PEAR'] = "/usr/local/opt/php56/lib/php/";
    $GLOBALS['cfg']['INI_CONFIGS_PATH'] = file_exists('/Users/cf/Documents/deve/config_ini_files') ? '/Users/cf/Documents/deve/config_ini_files' : __DIR__;
    // 如果是 https会有“已阻止载入混合活动内容”的风险
    if (isset($_SERVER['SERVER_PORT']) && 443 == $_SERVER['SERVER_PORT']) $GLOBALS['cfg']['RES_WEBPATH_PREF'] = 'https://img3.' .$GLOBALS['cfg']['WEB_DOMAIN']. '/'; else
        $GLOBALS['cfg']['RES_WEBPATH_PREF'] = 'http://img3.' .$GLOBALS['cfg']['WEB_DOMAIN']. '/';
    $GLOBALS['cfg']['LOG_PATH']  = "/Users/cf/logs_all/logs_dpa";
    $GLOBALS['cfg']['IFMDB2'] = true;
    $GLOBALS['cfg']['IFMYSQLI'] = false;
    $GLOBALS['cfg']['MEMCACHE_SESSION'] = true;
    $GLOBALS['cfg']['db_character'] = "utf8";
    $GLOBALS['cfg']['db_character_contype'] = "utf-8";
    $GLOBALS['cfg']['out_character'] = "utf8";
    $GLOBALS['cfg']['out_character_contype'] = "utf-8";
    $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_W'] = "dpa";
    $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R'] = "dpa3307_r";
    $GLOBALS['cfg']['IMG_UPLOAD_PATH'] = "/data0/htdocs/img3/upload"; // 以后放到
    $GLOBALS['cfg']['IMG_URL_PRE'] = $GLOBALS['cfg']['RES_WEBPATH_PREF'] . "upload";

} else {
    if(!isset($GLOBALS['cfg']['PATH_ROOT'])) $GLOBALS['cfg']['PATH_ROOT'] = "/data0/htdocs/admin/dpa";
    $GLOBALS['cfg']['PATH_RUNTIME'] = str_replace("\\","/",dirname(dirname(__FILE__)));//"/data0/deve/runtime";
    $GLOBALS['cfg']['PATH_PHP_LIBS'] = "/usr/local/webserver/php/lib/php/libs";
    $GLOBALS['cfg']['PATH_PEAR'] = "/usr/local/webserver/php/lib/php";
    $GLOBALS['cfg']['INI_CONFIGS_PATH'] = file_exists('/data0/deve/config_ini_files') ? '/data0/deve/config_ini_files' : __DIR__;
    // 如果是 https会有“已阻止载入混合活动内容”的风险
    if (isset($_SERVER['SERVER_PORT']) && 443 == $_SERVER['SERVER_PORT']) $GLOBALS['cfg']['RES_WEBPATH_PREF'] = 'https://img3.' .$GLOBALS['cfg']['WEB_DOMAIN']. '/'; else
        $GLOBALS['cfg']['RES_WEBPATH_PREF'] = 'http://img3.' .$GLOBALS['cfg']['WEB_DOMAIN']. '/';
    $GLOBALS['cfg']['LOG_PATH']  = "/data1/logs";
    $GLOBALS['cfg']['IFMDB2'] = true;
    $GLOBALS['cfg']['IFMYSQLI'] = false;
    $GLOBALS['cfg']['MEMCACHE_SESSION'] = true;
    $GLOBALS['cfg']['db_character'] = "utf8";
    $GLOBALS['cfg']['db_character_contype'] = "utf-8";
    $GLOBALS['cfg']['out_character'] = "utf8";
    $GLOBALS['cfg']['out_character_contype'] = "utf-8";
    $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_W'] = "dpa";
    $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R'] = "dpa3307_r";
    $GLOBALS['cfg']['IMG_UPLOAD_PATH'] = "/data0/htdocs/img3/upload"; // 以后放到
    $GLOBALS['cfg']['IMG_URL_PRE'] = $GLOBALS['cfg']['RES_WEBPATH_PREF'] . "upload";

    // 按照ip进行细分
    $exec = "/sbin/ifconfig | grep 'inet addr' | awk '{ print $2 }' | awk -F ':' '{ print $2}' | head -1";
    $local_ip = exec($exec);
    // WEB_ROOT用于项目内部
    if ('10.77.135.24' == trim($local_ip)) {
        // cms.wanhui.cn上的内网后台管理
        $GLOBALS['cfg']['PATH_PEAR'] = "/var/wd/cms/pear";
        $GLOBALS['cfg']['INI_CONFIGS_PATH'] = "/var/wd/cms/config_ini_files";
        $GLOBALS['cfg']['RES_WEBPATH_PREF'] = "http://cms.wanhui.cn/";
        $GLOBALS['cfg']['IMG_UPLOAD_PATH'] = "/var/wd/cms/upload/userfiles/cms_upload";
        $GLOBALS['cfg']['IMG_URL_PRE'] = $GLOBALS['cfg']['RES_WEBPATH_PREF'] . "upload/userfiles/cms_upload";
        $GLOBALS['cfg']['LOG_PATH']  = "/var/wd/cms/logs";

        // memcached 服务器地址及端口, 支持多组memcache
        $GLOBALS['g_memcached_servers'] = array(
            'default' => array(array('127.0.0.1', 11220),
                array('127.0.0.1', 11221),
            ),
            'session' => array(array('127.0.0.1', 11220),
                array('127.0.0.1', 11221),
            ),
        );
    }
}
// 以下常量将全部使用全局变量的方式，便于灵活修改全部放在变量cfg中
$GLOBALS['cfg']['IMG_TTF_FILE'] = $GLOBALS['cfg']['PATH_PEAR'] . '/jpgraph/fonts/DejaVuSans.ttf';
$GLOBALS['cfg']['Business_Path'] = "WEB-INF/Business";
$GLOBALS['cfg']['Validate_Path'] = "WEB-INF/validate";
$GLOBALS['cfg']['Template_Path'] = "WEB-INF/template";
$GLOBALS['cfg']['Tpl_Path']     = "WEB-INF/tpl";
$GLOBALS['cfg']['INI_DB_DSN_CONFIGS_FILE'] = "mysql_config.ini";
$GLOBALS['cfg']['INI_REDIS_DSN_CONFIGS_FILE'] = "redis_config.ini";
$GLOBALS['cfg']['INI_MEMCACHE_DSN_CONFIGS_FILE'] = "memcache_config.ini";
$GLOBALS['cfg']['LANG_DEFINE_FILE'] = "chinese.utf8.lang.php";
$GLOBALS['cfg']['DEBUG'] = false;
$GLOBALS['cfg']['DEFAULT_ACTION'] = "mainpage";
$GLOBALS['cfg']['DEFAULT_LOGIN_ACTION'] = "login";
$GLOBALS['cfg']['UPLOADIMG_PRE'] = "uploadimg_";
$GLOBALS['cfg']['RADIO_UPLOADIMG_CHANGE'] = "radio_change_";
$GLOBALS['cfg']['MAX_UPLOAD_IMG_SIZE'] = 8*1024*1024; // 最大文件大小 8M
// 数据库相关
$GLOBALS['cfg']['DB_DEFALUT_TYPE']     = 'aups_p';
$GLOBALS['cfg']['DB_TB_DEFALUT_TYPE']     = 'aups_t';
$GLOBALS['cfg']['DB_FIELD_DEFALUT_TYPE']   = 'aups_f';
// log4php配置文件路径
$GLOBALS['cfg']['LOG_CONF_FILE'] = $GLOBALS['cfg']['PATH_RUNTIME'] . '/configs/log4php.properties';

// 表相关
define('TABLENAME_PREF',"dpps_");
$GLOBALS['cfg']['TABLENAME_USER']      = "user";
$GLOBALS['cfg']['TABLENAME_LOGINLOG'] = "loginlog";
define('NEW_LINE_CHAR',"\r\n");


// -------------- 路径初始化 ------------------- //
ini_set('include_path','.'.PATH_SEPARATOR.$GLOBALS['cfg']['PATH_PEAR'].PATH_SEPARATOR.$GLOBALS['cfg']['PATH_RUNTIME'].PATH_SEPARATOR.$GLOBALS['cfg']['PATH_ROOT'].PATH_SEPARATOR.$GLOBALS['cfg']['LOG_PATH']);
