<?php
error_reporting(E_ALL & ~ (E_STRICT | E_NOTICE | E_DEPRECATED)); // PHP5.3兼容问题, PHP5.4严格性
ini_set('date.timezone','Asia/Shanghai');  // 最好设置一下时区，或php.ini设置

ini_set("display_errors", 1);
ini_set('magic_quotes_runtime', 0);  // 或 set_magic_quotes_runtime(0);

$l_time  = explode(' ', microtime());
$GLOBALS['cfg']['g_time_start'] = (double)$l_time[0] + (double)$l_time[1];  // 用于记录程序的运行时间

$GLOBALS['cfg']['WEB_DOMAIN'] = 'hiexhibition.com';

// 命令行模式下，全局变量 $_POST,$_GET,$_COOKIE,$_FILES,$_SERVER 都存在，不过前四个均为空数组
if ('cli' == php_sapi_name()) {
    // 命令行调用方法：
    // php main.php -d "do=project_list&pt=PUB"

    // 模拟cookie， 进行身份认证。进行后台管理相关的事项的时候，需要携带cookie，公开网页不需要
    if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
        require_once("common/lib/cArray.cls.php");
        $l_file = 'D:/www/config/admin@' .$GLOBALS['cfg']['WEB_DOMAIN']. '.txt';
    } else if ('DARWIN' === strtoupper(PHP_OS)) {
        require_once("/Users/cf/svn_dev/dpa/common/lib/cArray.cls.php");
        $l_file = '/Users/cf/Documents/deve/Cookies/admin@' .$GLOBALS['cfg']['WEB_DOMAIN']. '.txt';  // 需要cookie进行认证，特别是cli模式下
    } else {
        require_once("/data0/deve/runtime/common/lib/cArray.cls.php");
        $l_file = '/data0/deve/Cookies/admin@' .$GLOBALS['cfg']['WEB_DOMAIN']. '.txt';  // 需要cookie进行认证，特别是cli模式下
    }
    if(file_exists($l_file)) $_COOKIE = cArray::parse_cookiefile($l_file);


    if (!isset($_SERVER["HTTP_HOST"])) $_SERVER["HTTP_HOST"] = $GLOBALS['cfg']['WEB_DOMAIN'];

    // CLI模式运行的时候必须先设置一个用户名, 通过
    //$_SESSION["user"] = array("username"=>"robot","password"=>"admin");
    // 通过这些信息，去数据表中获取到robot的完整信息。可以借用main.php的整个流程。
    // 运行到后面user数组信息就会被修改了。

    // 几乎不需要$_POST数组，完全是get方式
    //$_POST =  array("username"=>"robot", "password"=>"admin");

    // 同时将一些全局变量全部注册一遍，保证cli和web的一致性。
    // 获取参数列表
    require_once 'Console/Getopt.php';
    $_options = Console_Getopt::getopt($argv, 'u:g:d:f:j:e:', array());
    $_o = array();
    if (!PEAR::isError($_options)) {
        foreach ($_options[0] as $l_v){
            $_o[$l_v[0]] = $l_v[1];
        }
    }

    if (!empty($_o["d"])) {
        parse_str($_o["d"], $_POST);  // 注册到POST中去
        if (!array_key_exists("REQUEST_METHOD", $_SERVER)) $_SERVER["REQUEST_METHOD"] = "POST";
    }
    if (!empty($_o["g"])) {
        parse_str($_o["g"], $_GET);  // 注册到GET中去, parse_str将键和值进行了urldecode。还进行了转义
        if (!array_key_exists("REQUEST_METHOD", $_SERVER)) $_SERVER["REQUEST_METHOD"] = "GET";
    }
    // 后面的会覆盖前面的，同 php.ini variables_order = "EGPCS" 保持一致
    $_REQUEST = array_merge($_GET,$_POST,$_COOKIE);

    define('HOST_NAME', ''); // cookie中使用的hostname
} else {
    define('HOST_NAME', substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'], '.')));
}

$GLOBALS['cfg']['PATH_ROOT'] = str_replace(array("\\","//"), "/", dirname(__FILE__));
if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) require_once("configs/system.conf.php");
else require_once("/data0/deve/runtime/configs/system.conf.php");
// 立即进行do参数的判断和指定
if (key_exists("do", $_REQUEST)) $doPath = urldecode(trim($_REQUEST['do']));
if (empty($doPath)){$doPath = $GLOBALS['cfg']['DEFAULT_ACTION'];}

require_once($GLOBALS['cfg']['PATH_RUNTIME'] . "/lang/" . $GLOBALS['cfg']['LANG_DEFINE_FILE']);
require_once($GLOBALS['cfg']['PATH_RUNTIME'] . "/common/functions.php");
require_once($GLOBALS['cfg']['PATH_RUNTIME'] . "/common/Log.php");
require_once($GLOBALS['cfg']['PATH_RUNTIME'] . "/common/lib/Cookie.php");
if ($GLOBALS['cfg']['MEMCACHE_SESSION']) require_once($GLOBALS['cfg']['PATH_RUNTIME'] . "/common/lib/Session.php");
require_once($GLOBALS['cfg']['PATH_ROOT'] . '/WEB-INF/mvc.conf.php');
require_once($GLOBALS['cfg']['PATH_RUNTIME'] . '/mvc/ActionConfig.cls.php');
require_once($GLOBALS['cfg']['PATH_RUNTIME'] . '/mvc/ActionServer.cls.php');

// 检查ip地址, 只允许内网ip访问
/*if ('cli' != php_sapi_name()) {
  $client_ip = trim(getip());
  if (!$client_ip || !in_array(substr($client_ip, 0, 3), array('10.', '127'))) {
    die ("非法请求，来自于：" . $client_ip);
  }
}*/

// 数据复原, 保持同cli模式一致了，因为cli模式模拟的GPC数据其实是没有额外转义的
// 而且只需要对值进行复原，经过分析键名可以不用复原，不会存在sql注入漏洞问题。大部分作为字段会用``
if (function_exists('get_magic_quotes_gpc') && -1 == version_compare(PHP_VERSION, '5.2.99') && get_magic_quotes_gpc()) {
    $_GET     = digui_deep($_GET, 'stripslashes');
    $_POST    = digui_deep($_POST, 'stripslashes');
    $_COOKIE  = digui_deep($_COOKIE, 'stripslashes');
    $_REQUEST = digui_deep($_REQUEST, 'stripslashes');
}

// 初始化session
require_once($GLOBALS['cfg']['PATH_RUNTIME'] . "/common/InitCookieAndSession.php");

// ------------------- 应用过程 begin --------------------- //
// init environment and congfiguration
if ($GLOBALS['cfg']['DEFAULT_LOGIN_ACTION']===$GLOBALS['cfg']['DEFAULT_ACTION']) exit('DEFAULT_LOGIN_ACTION equal DEFAULT_ACTION!');

if (!array_key_exists($doPath, $ACTION_CONFIGS)) $ACTION_CONFIGS[$doPath] = $ACTION_CONFIGS["default_map"];  // 用默认的替代大多数一样的
$actionConfig = new ActionConfig($ACTION_CONFIGS);

if (!$actionConfig->setCurrentPath($doPath)) exit('Path <b>'.$doPath.'</b> isn\'t defined');


// service
$actionServer = new ActionServer();
$actionServer->init($actionConfig,$_REQUEST,$_GET,$_POST,$_COOKIE,$_FILES);
$actionServer->process();
// -------------------- 应用过程 end ----------------------- //

if (isset($_GET['debug']) && $_GET['debug']) {
    print_r($GLOBALS);
    exit;
}
