<?php
/**
 * Fill_db_table_fieldAction.cls.php
 * 只允许后台运行, 填充并具备修复field_def表保持同真实的表结构一致
 *
  主要功能是创建数据库（如果数据库存在则按照项目特点进行补充和修正）、数据表和字段
  具备修复功能，保持同真实数据表一致的功能
  主要参数是db_name=grab&if_repair, 调用如下：
   D:/php5210/php D:/www/dpa/main.php -d "do=fill_db_table_field&db_name=dpa&db_pwd=db_pass&pro_type=system&if_repair=1"
   D:/php5210/php D:/www/dpa/main.php -d "do=fill_db_table_field&db_name=aups_p1&db_pwd=db_pass&table_name=tmpl_html&pro_type=system&if_repair=1"
   /usr/bin/php /data0/htdocs/admin/dpa/main.php -d "do=fill_db_table_field&db_name=dpa&db_pwd=10y9c2U5&pro_type=system"

   D:/php5210/php E:/www/hiexhibition_svn/trunk/php/admin/dpa/main.php -d "do=fill_db_table_field&db_port=3509&db_name=wangzhan&db_pwd=10y9c2U5&table_name=signupzhanhui&pro_type=system&if_repair=1"

 *
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once('mvc/Action.cls.php');
require_once("lang/chinese.utf8.lang.php");
require_once("common/lib/dbhelper.php");

class Fill_db_table_fieldAction extends Action {

  /**
   *
   * @access public
   * @param array &$request
   * @param array &$files
   */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()) {
    // 主要参数是db_name=grab&if_repair
    // 主要功能是创建数据库（如果数据库存在则按照项目特点进行补充和修正）、数据表和字段
    // 具备修复功能，保持同真实数据表一致的功能

    // 只允许cli后台运行，主要是完成一些任务。输出调试信息等  web测试的时候有&&&&安全隐患&&&&
    if (php_sapi_name()!='cli') {
      $response['html_content'] = date("Y-m-d H:i:s") . "this must be run command line module!!";
      $response['ret'] = array('ret'=>1);
      return null;  // 总是返回此结果
    }

    // 参数检验可以放到validate中去，先放到此处检验一下。
    if (!array_key_exists("db_name", $request)) {
      $response['html_content'] = date("Y-m-d H:i:s") . " db_name must not be empty!! ". NEW_LINE_CHAR;
      $response['ret'] = array('ret'=>1);
      return null;  // 总是返回此结果
    }
    if (!array_key_exists("db_pwd", $request)) {
      $response['html_content'] = date("Y-m-d H:i:s") . " db_pwd must not be empty!! ". NEW_LINE_CHAR;
      $response['ret'] = array('ret'=>1);
      return null;  // 总是返回此结果
    }
    if (!array_key_exists("pro_type", $request)) {
      $response['html_content'] = date("Y-m-d H:i:s") . " pro_type must not be empty!! ". NEW_LINE_CHAR;
      $response['ret'] = array('ret'=>1);
      return null;  // 总是返回此结果
    }

    // 调试信息字符串
    $l_str = "";

    // 项目增加以及修改项目联合起来，建表、创建字段，添加数据。修改表结构
    //
    $dbR = new DBR();
    $l_err = $dbR->errorInfo();
    if ($l_err[1] > 0) {
      // 数据库连接失败后
      $response['html_content'] = date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
      $response['ret'] = array('ret'=>1);
      return null;
    }
    $l_srv_db_dsn = $dbR->getDSN();  // 采用默认的数据库连接信息

    //
    $dbR->table_name = TABLENAME_PREF."project";
    $p_arr = $dbR->getOne(" where db_name = '".$request["db_name"]."'");
    if (PEAR::isError($p_arr)) {
      $response['html_content'] = var_export($dbR->errorInfo(), true). " error sql:" .$dbR->getSQL() ." FILE:".__FILE__." LINE:".__LINE__.NEW_LINE_CHAR;
      //echo $response['html_content'];
      $response['ret'] = array('ret'=>1);
      return null;
    }

    // 项目表中没有此项目的话，则需要入库一下，同时创建一个数据库
    if (empty($p_arr)) {

      // 自动获取默认数组
      $l_ins_arr = $dbR->getInSertArr();
      $data_arr = $l_ins_arr[1];  // 只需要必须的字段
      //print_r($data_arr);

      // 需要在外部修改一下
      $data_arr["name_cn"]   = array_key_exists("name_cn",$request)?$request["name_cn"]:$request["db_name"];
      $data_arr["type"]    = $request["pro_type"];
      $data_arr["db_name"]  = $request["db_name"];
      $data_arr["db_pwd"]    = array_key_exists("db_pwd",$request)?$request["db_pwd"]:$l_srv_db_dsn["password"];
      if (array_key_exists("db_port",$request)) $data_arr["db_port"] = $request["db_port"];

      // 系统本身不应该在此处默认的dbr连接信息的数据库中
      //if ("SYSTEM"!=strtoupper($request["pro_type"])) {
      $dbW = new DBW();
      $l_err = $dbW->errorInfo();
      if ($l_err[1]>0){
        // 数据库连接失败后
        $response['html_content'] = date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
        $response['ret'] = array('ret'=>1,'msg'=>$l_err[1]);
        return null;
      }
      $dbW->table_name = TABLENAME_PREF."project";
      $dbW->insertOne($data_arr);
      $l_err = $dbW->errorInfo();
      if ($l_err[1]>0){
        // sql有错误，后面的就不用执行了。
        $response['html_content'] = NEW_LINE_CHAR . date("Y-m-d H:i:s") . " FILE: ".__FILE__." ". " FUNCTION: ".__FUNCTION__." Line: ". __LINE__ . " SQL: ".$dbW->getSQL().", _arr:" . var_export($l_err, TRUE);
        $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
        return null;
      }
      $pid = $dbW->LastID();
      if ($pid>0) {
        $data_arr["id"] = $pid;  // 该项目id, 创建记录成功才会有此项
      }
      //}else {
      // 还需要补充几个可能没有提供的数据
      //$data_arr["db_host"]   = array_key_exists("db_host",$request)?$request["db_host"]:$l_srv_db_dsn["hostspec"];
      //$data_arr["db_port"]   = array_key_exists("db_port",$request)?$request["db_port"]:$l_srv_db_dsn["port"];
      //$data_arr["db_user"]   = array_key_exists("db_user",$request)?$request["db_user"]:$l_srv_db_dsn["username"];
      //}

      // 增加项目记录成功后，需要创建相应的数据库和建立相应的数据表以及填充必要的数据
      // 依据项目的类型，确定需要建立哪几张基本表，后续需要在这个成功的基础上进行????
      $rlt = DbHelper::createDBandBaseTBL($data_arr);

    }else {
      if ( !array_key_exists("table_name",$request) ) {
        // 如果已经存在，则需要进行必要的表修补
        $rlt = DbHelper::createDBandBaseTBL($p_arr);
      } else {
        // 如果带有表名称是进行表的字段修复，
        // 1 检查表是否存在，不存在则返回

        // 2 存在则继续依据真实的表结构填充字段定义表
        $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr);$dbR->dbo = &DBO('', $dsn);
        //$dbR = null;$dbR = new DBR($p_arr);
        $dbW = new DBW($p_arr);
        $l_data_arr = array("source"=>"db","creator"=>empty($_SESSION["user"]["username"])?"admin":$_SESSION["user"]["username"]);
        //print_r($l_data_arr);
        DbHelper::fill_field($dbR,$dbW,$l_data_arr,$request["table_name"],TABLENAME_PREF."field_def",TABLENAME_PREF."table_def", $request['if_repair']);
      }
    }

    $response['html_content'] = date("Y-m-d H:i:s") . var_export($l_str, true);
    $response['ret'] = array('ret'=>0);
    return null;
  }
}
