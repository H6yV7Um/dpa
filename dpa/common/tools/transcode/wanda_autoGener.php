<?php
/**

  根据提供的新增接口，并自动生成control，model，dao，cache文件，并修改 index.php 路由

D:/php5210/php D:/www/dpa/common/tools/transcode/wanda_autoGener.php -r pptvrank -B PPTVRank -t html -d 1

D:/php5210/php D:/www/dpa/common/tools/transcode/wanda_autoGener.php -r channelsalesman -B ChannelSalesMan -t html -d 1

D:/php5210/php D:/www/dpa/common/tools/transcode/wanda_autoGener.php -r pptvrankrule -B PPTVRankRule -t html


 */

if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
  // web目录
  define("SOURCE_PATH_PRE", "D:/www/wanda_svn/trunk/php/dagexing");
} else {
  // web目录
  define("SOURCE_PATH_PRE", "/home/chengfeng1/svn/trunk/php/dagexing");
}

function GetMyOpt($argv, $para_short='i:d:t:',$para_long=array()){
  // 获取参数列表
  require_once 'Console/Getopt.php';
  $_options = Console_Getopt::getopt($argv, $para_short, $para_long);
  $_o = array();
  if (!PEAR::isError($_options)) {
    foreach ($_options[0] as $l_v) {
      $_o[$l_v[0]] = $l_v[1];
    }
  }
  return $_o;
}

// 通过参数获取到新增接口名
$_o = GetMyOpt($argv, 'r:B:t:m:d:C:', array());
main($_o);

function main($_o){
  if (!isset($_o['r']) || !isset($_o['B']) || !$_o['r'] || !$_o['B']) {
    // 输出提示
    echo "please use like : php D:/www/dpa/common/tools/transcode/wanda_autoGener.php -r choosepkuser -B ChoosePkUser" . "\r\n";
    return ;
  }
  $type = isset($_o['t']) ? $_o['t'] : 'json';
  $database = isset($_o['d']) ? $_o['d'] : 0;
  $route = $_o['r']; // 路由
  $name = $_o['B'];   // 大写的方法名等
  $source_path = isset($_o['C']) ? $_o['C'] : SOURCE_PATH_PRE;   // 大写的方法名等
  $http_method = isset($_o['m']) ? $_o['m'] : 'get';

  // 1. 修改文件 index.php，添加路由
  ModifyIndex($source_path, $route, $name, $type);

  // 2. 新增control
  BuildControl($source_path . "/controllers", $route, $name, $type);

  // 3. 新增model
  BuildModel($source_path . "/models", $route, $name, $http_method, $database);

  if ($database) {
    // 4. 新增dao 和 daoImpl
    BuildDao($source_path . "/models/dao", $route, $name);
    BuildDaoImpl($source_path . "/models/dao", $route, $name);

    // 5. 新增cache
    BuildCache($source_path . "/models/dao", $route, $name);
  }
}

// 增加一个Dao文件
function BuildDao($path, $route, $name){
  // 生成dao，需要有数据库名和表名
  $l_tmpl = "<?php
" . GetComment($route, $name, 'DAO') . "
require_once('DaoProxyBase.php');
require_once('Cached" . $name . "DaoImpl.php');

class " . $name . "Dao extends DaoProxyBase {
  protected static \$client_;

  public static function GetClient() {
    if (!isset(self::\$client_))
      self::\$client_ = new Cached" . $name . "DaoImpl();
    return self::\$client_;
  }
}
";

  $filename = $name . "Dao.php";
  writefile($l_tmpl, $path, $filename);
}

// 增加一个Dao文件
function BuildDaoImpl($path, $route, $name){
  // 生成dao，需要有数据库名和表名
  $l_tmpl = "<?php
" . GetComment($route, $name, 'DaoImpl') . "
class " . $name . "DaoImpl {
  const DB_NAME = 'ktv';
  const TABLE_NAME = '" . $route . "';

  public static \$table_fields_ = array('uplid','createtime');

  public function Insert(array \$record) {
    return MysqlClient::InsertData(self::DB_NAME,
                                   self::TABLE_NAME,
                                   self::\$table_fields_,
                                   array(\$record));
  }

  public function GetInsertID() {
    return MysqlClient::GetInsertID(self::DB_NAME);
  }
}
";

  $filename = $name . "DaoImpl.php";
  writefile($l_tmpl, $path, $filename);
}

function BuildCache($path, $route, $name){
  // 生成cache，需要有数据库名和表名
  $l_tmpl = "<?php
" . GetComment($route, $name, '') . "
require_once(PHP_ROOT . 'libs/util/MemCachedClient.php');
require_once(WEB_ROOT . 'models/dao/" . $name . "DaoImpl.php');

class Cached" . $name . "DaoImpl extends " . $name . "DaoImpl {
  const MEMCACHE_GROUP = 'default';
  const CACHE_EXPIRE_TIME = 1800;

  private function GetCacheKey(\$id) {
    return '" . $route . "-id-' . \$id;
  }

  public function Insert(array \$record) {
    if (!parent::Insert(\$record)) return false;
    // TODO
    return true;
  }
}
";
  $filename = "Cached" . $name . "DaoImpl.php";
  writefile($l_tmpl, $path, $filename);
}

// 增加一个model文件
function BuildModel($path, $route, $name, $http_method, $database){
  $http_method = ('get' == $http_method)? 'GetParam' : 'PostParam';
  if ($database) $database = "
require_once(WEB_ROOT . 'models/dao/" . $name . "Dao.php');";
  else $database = "";

  $l_tmpl = "<?php
" . GetComment($route, $name, 'Model') . "
require_once(PHP_ROOT . 'libs/util/Log.php');
require_once(PHP_ROOT . 'libs/util/Utility.php');
require_once(PHP_ROOT . 'libs/util/HttpRequestHelper.php');
require_once(WEB_ROOT . 'models/ErrorMsg.php');
require_once(WEB_ROOT . 'models/Response.php');" . $database . "

class " . $name . "Model {
  public function Get" . $name . "() {
    \$uid = Cookie::Get(UID);
    \$version = HttpRequestHelper::$http_method('version');

    \$response = new Response();
    \$response->data = array();

    // 参数检查
    \$null_check = array();
    \$null_check[] = array(\$version, ErrorMsg::APP_VERSION_EMPTY);
    \$error_code = Utility::ValidateIsSet(\$null_check);
    if (0 != \$error_code) {
      ErrorMsg::FillResponseAndLog(\$response, \$error_code);
      return \$response;
    }
    // 其他参数检查 TODO

    // 具体的业务逻辑 TODO

    return \$response;
  }
}
";

  $filename = $name . "Model.php";
  writefile($l_tmpl, $path, $filename);
}

// 增加一个control文件
function BuildControl($path, $route, $name, $type){
  if ('html' == $type) {
    $view = "require_once(PHP_ROOT . 'libs/mvc/SmartyView.php');";
    $viewname = "Smarty";
    $settemplate = "
    \$view->SetTemplate('pptv/" . $route . ".tpl');";
  } else {
    $view = "require_once(WEB_ROOT . 'views/WebapiView.php');";
    $viewname = "Webapi";
    $settemplate = "";
  }

  $l_tmpl = "<?php
" . GetComment($route, $name, 'Controller') . "
require_once(PHP_ROOT . 'libs/mvc/SessionController.php');
" . $view . "
require_once(WEB_ROOT . 'models/" . $name . "Model.php');

class " . $name . "Controller extends SessionController {
  public function Run() {
    \$model = new " . $name . "Model();
    \$result = \$model->Get" . $name . "();
    \$view = new " . $viewname . "View();
    \$view->SetData(\$result);" . $settemplate . "
    \$view->Display();
  }
}
";

  $filename = $name . "Controller.php";
  writefile($l_tmpl, $path, $filename);
}

function svn_add($path, $filename) {
  $cmd = '"C:/Program Files/TortoiseSVN/bin/svn" add ' . $path . "/" . $filename . ' 2>&1';
  exec($cmd, $out_put, $ret);
  echo date("Y-m-d H:i:s") . " " . $path . "/" . $filename . " svn_add succ!" . "\r\n";
  return ;
}

// 修改index.php文件，添加路由和反作弊路由
function ModifyIndex($path, $route, $name, $type) {
  $filename = "index.php";

  // 在 '/' => 'IndexController', 后面加上一行
  $l_sep1 = "'/storelist' => 'StoreListController',";
  $l_sep2 = "'/' => array('open' => true, 'type' => 'html', 'code' => ANTISPAM_ACTION_CODE_OTHER, 'src' => ANTISPAM_DAGEXING),";

  $n_para = strtoupper((substr($para,0,1))).(substr($para,1));
  $l_default1 = "
  '/$route' => '" . $name . "Controller',";

  $l_default2 = "
  '/$route' => array('open' => true, 'type' => '" . $type . "', 'code' => ANTISPAM_ACTION_CODE_OTHER, 'src' => ANTISPAM_DAGEXING),";

  $o_content = file_get_contents($path . "/" . $filename);
  if (false === strpos($o_content, "'/$route'") ) {
    if ( false !== strpos($o_content, $l_sep1) && false !== strpos($o_content, $l_sep2)) {
      // 进行替换
      $new_cont = str_replace($l_sep1, $l_sep1 . $l_default1, $o_content);
      $new_cont = str_replace($l_sep2, $l_sep2 . $l_default2, $new_cont);

      writeContent($new_cont, $path . "/" . $filename);
      echo date("Y-m-d H:i:s") . " " . $path . "/" . $filename . "   succ! "."\r\n";
    } else if ( false !== strpos($o_content, $l_sep1)) {
      // 进行替换
      $new_cont = str_replace($l_sep1, $l_sep1 . $l_default1, $o_content);

      writeContent($new_cont, $path . "/" . $filename);
      echo date("Y-m-d H:i:s") . " " . $path . "/" . $filename . "   succ! "."\r\n";
    } else {
      echo date("Y-m-d H:i:s")." index.php modify! " . "\r\n";
    }
  } else {
    echo date("Y-m-d H:i:s")." $route exist! " . "\r\n";
  }
}

function GetComment($route, $name, $a_t='Model') {
  $str = "/**
 * $name$a_t
 * @author chengfeng1@wanda.cn
 * @since " . date("Y-m-d") . "
 */";

  return $str;
}

function writefile($l_tmpl, $path, $filename) {
  if (!file_exists($path . "/" . $filename)) {
    writeContent($l_tmpl, $path . "/" . $filename);
    echo date("Y-m-d H:i:s") . " " . $path . "/" . $filename . " succ!" . "\r\n";
  } else {
    // 可以不用输出调试信息
    echo date("Y-m-d H:i:s") . " " . $path . "/" . $filename . " file exist!" . "\r\n";
  }
  svn_add($path, $filename);
}

//建立目地文件夹
function createdir($dir='')
{
  if (!is_dir($dir)){
    // 该参数本身不是目录 或者目录不存在的时候
    $temp = explode('/',$dir);
    $cur_dir = '';
    for($i=0;$i<count($temp);$i++)
    {
      $cur_dir .= $temp[$i].'/';
      if (!is_dir($cur_dir))
      {
        @mkdir($cur_dir,0775);
      }
    }
  }
}

function writeContent( $content, $filePath, $mode='w' ){
    createdir(dirname($filePath));
    if ( !file_exists($filePath) || is_writable($filePath) ) {

      if (!$handle = @fopen($filePath, $mode)) {
        return "can't open file $filePath";
      }

      if (!fwrite($handle, $content)) {
        return "cann't write into file $filePath";
      }

      fclose($handle);

      return '';

    } else {
      return "file $filePath isn't writable";
    }
}
