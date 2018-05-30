<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors","On");
ini_set('magic_quotes_runtime', 0);  // 或 set_magic_quotes_runtime(0);

$l_time  = explode(' ', microtime());
$GLOBALS['cfg']['g_time_start'] = (double)$l_time[0] + (double)$l_time[1];  // 用于记录程序的运行时间

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
$GLOBALS['cfg']['PATH_ROOT'] = str_replace(array("\\","//"), "/", dirname(__FILE__));
if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)) )require_once("D:/www/dpa/configs/system.conf.php");
else require_once("/data0/deve/runtime/configs/system.conf.php");
// 立即进行do参数的判断和指定
if (key_exists("do",$_REQUEST)) $doPath = urldecode(trim($_REQUEST['do']));
if (empty($doPath)){$doPath = $GLOBALS['cfg']['DEFAULT_ACTION'];}

require_once("lang/".$GLOBALS['cfg']['LANG_DEFINE_FILE']);
require_once("common/functions.php");

////////////   以上copy自main.php文件  ////////////////////////



////////////   要测试哪个class的什么功能可以在这下面写代码  ////////////////////////

/*require_once("DataDriver/db/Nosql.cls.php");
$l_nosql = new Nosql("memcache");
$l_key = "dd";
$l_nosql->set($l_key, "bbbb", 4);
var_dump($l_nosql->get($l_key));
while (false!==$l_nosql->get($l_key)) {
  echo date("H:i:s") . " ". $l_nosql->get($l_key). "\n";
  sleep(1);
}
$l_nosql->close();
*/
$l___ = "redis";
if ("memcache" == $l___) $l_nosql = new memcache;
else $l_nosql = new redis();
$l_nosql->connect("localhost");
$l_key = "bb";
//$l_val = array("eeeeeeeee");
$l_val = new stdclass;
//$l_val = serialize($l_val);
//$l_val = "eeeeeeeee";
if ("memcache" == $l___) $l_nosql->set($l_key, $l_val, MEMCACHE_COMPRESSED, 6);
else $l_nosql->setex($l_key, 6, $l_val);

var_dump($l_nosql->get($l_key));
while (false!==$l_nosql->get($l_key)) {
  echo date("H:i:s") . " ". "\n";
  print_r($l_nosql->get($l_key));
  sleep(1);
}
$l_nosql->close();
