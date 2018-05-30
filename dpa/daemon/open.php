<?php
/**
 * 此文件不能使用web访问，否则非常危险，只能后台用于跳过身份认证的程序。
 */
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors","On");
ini_set('magic_quotes_runtime', 0);  // 或 set_magic_quotes_runtime(0);

// 命令行模式下，全局变量 $_POST,$_GET,$_COOKIE,$_FILES,$_SERVER 都存在，不过前四个均为空数组
if (php_sapi_name()=='cli') {
  // 命令行调用方法：
  // php main.php -d "do=project_list&pt=PUB"

  // 模拟cookie， 进行身份认证。进行后台管理相关的事项的时候，需要携带cookie，公开网页不需要
  if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))){
    require_once("D:/www/dpa/common/lib/cArray.cls.php");
    $l_file = "D:/www/config/admin@wanda.cn.txt";
  } else {
    require_once("/data0/deve/runtime/common/lib/cArray.cls.php");
    $l_file = "/data0/deve/Cookies/admin@wanda.cn.txt";  // 需要cookie进行认证，特别是cli模式下
  }
  if(file_exists($l_file)) $_COOKIE = cArray::parse_cookiefile($l_file);


  $_SERVER["HTTP_HOST"] = "wanda.cn";

  // CLI模式运行的时候必须先设置一个用户名, 通过
  //$_SESSION["user"] = array("username"=>"robot","password"=>"admin");
  // 通过这些信息，去数据表中获取到robot的完整信息。可以借用main.php的整个流程。
  // 运行到后面user数组信息就会被修改了。

  // 几乎不需要$_POST数组，完全是get方式
  //$_POST =  array("username"=>"robot","password"=>"admin");

  // 同时将一些全局变量全部注册一遍，保证cli和web的一致性。
  // 获取参数列表
  require_once 'Console/Getopt.php';
  $_options = Console_Getopt::getopt($argv, 'u:g:d:f:j:', array());
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
}

if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)) )require_once("D:/www/dpa/configs/system.conf.php");
else require_once("/data0/deve/runtime/configs/system.conf.php");
// 在此修改配置参数等，不同的接口使用不同的参数
// $GLOBALS['language']['SYSTEM_NAME_STR'] = "通用发布平台";

// 立即进行do参数的判断和指定
if (key_exists("do",$_REQUEST)) $doPath = urldecode(trim($_REQUEST['do']));
if (empty($doPath)){$doPath = $GLOBALS['cfg']['DEFAULT_ACTION'];}

// 跳过身份认证的开放网页, 可以设定过期时间的，并定期索要身份认证的cookie。
// 此项千万不能打开，将会非常危险，意味着任何人都可以通过这个接口查看所有的数据库表信息, 只适合在程序中动态修改,
// 例如添加用户的时候通过mvc中配置的 o无需身份验证的do，然后在程序里面再次调用应用过程的时候进行此设置，相对安全。因为只有0，1两个信息的输出
$GLOBALS['cfg']['if_is_open_page'] = 1;

require_once("lang/".$GLOBALS['cfg']['LANG_DEFINE_FILE']);
require_once("common/functions.php");
require_once('WEB-INF/mvc.conf.php');
require_once('mvc/ActionConfig.cls.php');
require_once('mvc/ActionServer.cls.php');

if ( $GLOBALS['cfg']['DEBUG'] ){
    $g_time_start = utime();
}

// 数据复原, 保持同cli模式一致了，因为cli模式模拟的GPC数据其实是没有额外转义的
// 而且只需要对值进行复原，经过分析键名可以不用复原，不会存在sql注入漏洞问题。大部分作为字段会用``
if (function_exists('get_magic_quotes_gpc') && -1 == version_compare(PHP_VERSION, '5.2.99') && get_magic_quotes_gpc()) {
  $_GET     = digui_deep($_GET, 'stripslashes');
  $_POST     = digui_deep($_POST, 'stripslashes');
  $_COOKIE  = digui_deep($_COOKIE, 'stripslashes');
  $_REQUEST = digui_deep($_REQUEST, 'stripslashes');
}

// ------------------ 限制仅允许部分ip登录 ----------------- //
//$ip_array = array("192.168.0.170", "127.0.0.1");
//if(!in_array(getip(), $ip_array)){
//  die ("非法登录请求，来自于：" . getip());  // 应该记录到日志中
//}

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

if ( $GLOBALS['cfg']['DEBUG'] ){
    $g_time_end = utime();
    $run = $g_time_end - $g_time_start;
    $run = substr($run, 0, 5) . " secs ";
    echo 'Time Cost: [ '.$run." ]";
}
