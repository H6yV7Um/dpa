<?php
/**
  根据提供的新增接口，并自动生成control，model，dao，cache文件，并修改 InitRouter.php 路由

D:/php5210/php D:/www/dpa/common/tools/transcode/hiexhibition_autoGener.php -r user/updatafollowinfo -B user/UpdataFollowinfo -d User

 */

if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
  // web目录
  define("SOURCE_PATH_PRE", "E:/www/hiexhibition_svn/trunk/php/api");
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
    echo "please use like : php D:/www/dpa/common/tools/transcode/hiexhibition_autoGener.php -r choosepkuser -B ChoosePkUser" . "\r\n";
    return ;
  }
  $type = isset($_o['t']) ? $_o['t'] : 'json';
  $database = isset($_o['d']) ? $_o['d'] : '';
  $route = $_o['r']; // 路由, 数据表名称
  $name = $_o['B'];   // 大写的方法名等
  $source_path = isset($_o['C']) ? $_o['C'] : SOURCE_PATH_PRE;   // 大写的方法名等
  $http_method = isset($_o['m']) ? $_o['m'] : 'request';

  // 1. 修改文件 index.php，添加路由
  ModifyRouteFile($source_path, $route, $name, $type);

  // 2. 新增control
  BuildControl($source_path . "/controllers", $route, $name, $type);

  // 3. 新增model
  BuildModel($source_path . "/models", $route, $name, $http_method, $database);

  if ($database) {
    // 4. 新增dao 和 daoImpl
    BuildDao($source_path ."/dao", $route, $database);
    BuildDaoImpl($source_path . "/dao", $route, $database);

    // 5. 新增cache
    BuildCache($source_path . "/dao", $route, $database);
  }
}

// 增加一个Dao文件
function BuildDao($path, $route, $name){
  // 生成dao，需要有数据库名和表名
  $l_tmpl = "<?php
" . GetComment($route, $name, 'DAO') . "
require_once(PHP_ROOT . 'libs/dao/DaoProxyBase.php');
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
    const DB_NAME = 'wangzhan';
    const TABLE_NAME = '" . $name . "';

    public static \$table_fields_ = array('id','createtime');

    public static function GetTableFields() {
        \$sql = 'DESC '. self::TABLE_NAME;
        \$field_info = MysqlClient::ExecuteQuery(self::DB_NAME, \$sql);
        \$field_list = array();
        if (\$field_info) {
            foreach (\$field_info as \$row){
                \$field_list[] = \$row['Field'];
            }
        }
        return \$field_list;
    }

    public function Insert(array \$record) {
        return MysqlClient::InsertData(self::DB_NAME,
                                   self::TABLE_NAME,
                                   self::GetTableFields(),
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
require_once('" . $name . "DaoImpl.php');

class Cached" . $name . "DaoImpl extends " . $name . "DaoImpl {
    const MEMCACHE_GROUP = 'default';
    const CACHE_EXPIRE_TIME = 10;

    private static function GetCacheKey(\$id) {
        return '" . $name . "-id-' . \$id;
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
/*


        // 参数检查，只是示例，其实sessionid不需要验证
        \$null_check = array();
        \$null_check[] = array(\$sessionid, ErrorMsg::APP_VERSION_EMPTY);
        \$error_code = Utility::ValidateIsSet(\$null_check);
        if (0 != \$error_code) {
            ErrorMsg::FillResponseAndLog(\$response, \$error_code);
            return \$response;
        }

        // 检查是否登录
        if (!Utility::CheckSessionValid()) {
            ErrorMsg::FillResponseAndLog(\$response, ErrorMsg::USER_AUTH_FAILED);
            return \$response;
        }
        \$uid = \$_SESSION[Utility::SESSION_UID_KEY];

 */
function BuildModel($path, $route, $name, $http_method, $database){
  $base_name = basename($name);
  $http_method = ('get' == $http_method)? 'GetParam' : 'GetRequestParam';
  if ($database) $database = "
require_once(WEB_ROOT . 'dao/" . $database . "Dao.php');";
  else $database = "";

  $l_tmpl = "<?php
" . GetComment($route, $name, 'Model') . "
require_once(PHP_ROOT . 'libs/util/Utility.php');
require_once(PHP_ROOT . 'libs/util/HttpRequestHelper.php');
require_once(WEB_ROOT . 'models/extra/Response.php');
require_once(WEB_ROOT . 'models/extra/ErrorMsg.php');
require_once(WEB_ROOT . 'models/extra/BaseModel.php');" . $database . "

class " . $base_name . "Model extends BaseModel {
    public function DoModel() {
        \$sessionid = HttpRequestHelper::$http_method('SESSIONID', session_id());

        \$response = new Response();
        \$response->SESSIONID = \$sessionid;

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
  $base_name = basename($name);
  if ('html' == $type) {
    $view = "require_once(WEB_ROOT . 'controllers/extra/HiController.php');
require_once(PHP_ROOT . 'libs/mvc/SmartyView.php');";
    $viewname = "Html";
    $settemplate = "
    \$view->SetTemplate('pptv/" . $route . ".tpl');";
  } else {
    $view = "require_once(WEB_ROOT . 'controllers/extra/JsonController.php');
require_once(WEB_ROOT . 'views/WebapiView.php');";
    $viewname = "Webapi";
    $settemplate = "";
  }

  $l_tmpl = "<?php
" . GetComment($route, $name, 'Controller') . "
" . $view . "
require_once(WEB_ROOT . 'models/" . $name . "Model.php');

class " . $base_name . "Controller extends JsonController {
    public function __construct() {}

    public function Run() {
        \$model = new " . $base_name . "Model();
        \$result = \$model->DoSafeModel();
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
function ModifyRouteFile($path, $route, $name, $type) {
  //$filename = "index.php";
  $filename = "common/InitRouter.php";

  // 在 '/' => '/MainPHPController', 后面加上一行
  $l_sep1 = "'/' => '/MainPHPController',";

  $l_default1 = "
    '/v1/$route' => '" . $name . "Controller',";

  $o_content = file_get_contents($path . "/" . $filename);
  if (false === strpos($o_content, "'/v1/$route'") ) {
    if ( false !== strpos($o_content, $l_sep1)) {
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
 * @author dev@hiexhibition.com
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
