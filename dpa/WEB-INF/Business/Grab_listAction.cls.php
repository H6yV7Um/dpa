<?php
/**
 * Grab_listAction.cls.php
 * 临时用于将dpa平台中添加好了的cms的一些必要数据表导入aaaaaaa平台的cms中，并且作为以后创建cms的基本表存在.
 * 其特征是表名为:
 *  34           35       36          37       38        39        40        41         58
 *  "aups_p1","aups_p2","aups_p3","aups_p4","aups_p5","aups_p6","aups_p7","aups_p8","aups_p25"
 *  "页面碎片", "正文页", "栏目配置", "媒体配置","CSS模板","JS模板",   "栏目页", "专题页",  "功能代码"
 *
   指定库的指定数据表，导入到指定库。新库中表名、字段名将重新命名，并且需要填充表和字段定义表两张表
   主要参数是db_name=grab&if_repair, 调用如下：
 * php main.php -d "do=grab_list&db_name=dpa&db_pwd=db_pass&pro_type=system"
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once('mvc/Action.cls.php');
require_once('mod/DBR.cls.php');
require_once('mod/DBW.cls.php');
require_once("lang/chinese.utf8.lang.php");
require_once("common/lib/dbhelper.php");

class Grab_listAction extends Action {
  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){
    // 主要参数是db_name=grab&if_repair
    // 主要功能是创建数据库（如果数据库存在则按照项目特点进行补充和修正）、数据表和字段
    // 具备修复功能，保持同真实数据表一致的功能

    // 只允许cli后台运行，主要是完成一些任务。输出调试信息等  web测试的时候有&&&&安全隐患&&&&
    /*if (php_sapi_name()!='cli') {
    $response['html_content'] = date("Y-m-d H:i:s") . "this must be run command line module!!";
    return null;  // 总是返回此结果
    }*/

    // 1 连接数据库
    $dbR = new DBR();
    $l_err = $dbR->errorInfo();
    if ($l_err[1]>0){
      // 数据库连接失败后
      $response['html_content'] = date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
      $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
      return null;
    }
    $l_name0 = key($GLOBALS['mdb2_conns']);  // 记录下别名,便于后面共享使用
    $l_srv_db_dsn = $dbR->getDSN("array");

    // 切换到第一次的数据库连接, 同时将数据库切换为it
    $dbR->dbo = &DBO($l_name0);
    $dbR->SetCurrentSchema("it");
    $dbR->table_name = TABLENAME_PREF."table_def";
    if (($get["tbl_id"])>0) $l_where = "where id=".$get["tbl_id"];
    else $l_where = "where id=4";
    $l_all_tbl = $dbR->getAlls($l_where,"id, name_eng");
    //print_r($l_all_tbl);

    // 连另外一个库, 本可以共享前一个库，为避免数据库的频繁切换，姑且多增加一个
    $l_srv_db_dsn_from = $l_srv_db_dsn;
    // $l_srv_db_dsn_from["port"] = 3307;
    $l_srv_db_dsn_from["database"] = "test3";    // 数据库连接信息修改
    $dbR->dbo = &DBO("dbR2", $l_srv_db_dsn_from);  // 增加数据库连接对象


    foreach ($l_all_tbl as $l_one_t){
      $dbR->dbo = &DBO($l_name0);
      $dbR->table_name = TABLENAME_PREF."field_def";
      $l_all_fils = $dbR->getAlls("where t_id=".$l_one_t["id"]);
      $l_all_fils = cArray::Index2KeyArr($l_all_fils,array("key"=>"name_eng", "value"=>array()));
      //echo $dbR->getSQL().NEW_LINE_CHAR;
      //print_r($l_all_fils);

      $dbR->dbo = &DBO("dbR2");
      $dbR->table_name = TABLENAME_PREF."table_def";
      $l_tbl_id2 = $dbR->GetOne("where name_eng='".$l_one_t["name_eng"]."'", "id");
      $dbR->table_name = TABLENAME_PREF."field_def";
      $l_all_fils2 = $dbR->getAlls("where t_id=".$l_tbl_id2["id"]);
      $l_all_fils2 = cArray::Index2KeyArr($l_all_fils2,array("key"=>"name_eng", "value"=>array()));
      //echo $dbR->getSQL().NEW_LINE_CHAR;
      //print_r($l_all_fils2);

      // 将旧有的一些算法更新到新的自动填充的，生成相应的sql语句。
      $l_f_tri = array('id','t_id','name_eng','name_cn','creator', 'createdate', 'createtime', 'mender', 'menddate', 'mendtime','source','description','last_modify');
      $l_insert_arr = array();  // insert的单独放在一起
      $l_no_diff = array();
      foreach ($l_all_fils as $l_eng => $l_fd){
        if (array_key_exists($l_eng, $l_all_fils2)) {
          //
          $l_str = "";
          foreach ($l_fd as $l_k=>$l_v){
            if (!in_array($l_k,$l_f_tri)){
              // 字段的逐个属性进行比较，剔除相同的数据
              // null，'' 会被认为是一样的, 但与'NULL'则认为是不同的值
              if ("default"==$l_k){
                // 默认值部分可能是 'NULL' 在上一步判断中认为不等，在我们这里则认为相等
                if ('NULL'==$l_all_fils2[$l_eng][$l_k]) $l_all_fils2[$l_eng][$l_k] = null;
                if ('NULL'==$l_v) $l_v = null;
              }
              if ($l_v!=$l_all_fils2[$l_eng][$l_k]) {
                $l_v = str_replace("\r\n",'\r\n',$l_v);
                $l_v = str_replace("'","''",$l_v);
                $l_str .= "`".$l_k."`='".$l_v."',";
              }
            }
          }
          $l_str = rtrim($l_str," ,");
          if (""!=$l_str) {
            //$l_str .= ',`menddate`=DATE_FORMAT(NOW(), \'%Y-%m-%d\'),`mendtime`=DATE_FORMAT(NOW(), \'%H:%i:%s\')';
            echo "update `" . TABLENAME_PREF . "field_def` set $l_str where `name_eng`='".$l_fd["name_eng"]."' and `t_id` = (select id from " . TABLENAME_PREF . "table_def where name_eng='".$l_one_t["name_eng"]."');".NEW_LINE_CHAR;
          }else {
            echo "-- $l_eng no change ".NEW_LINE_CHAR;
          }
        }else {
          // 表外字段则需要insert
          $l_str = "";
          foreach ($l_fd as $l_k=>$l_v){
            if (!in_array($l_k,array('id','creator','createdate','createtime', 'mender', 'menddate', 'mendtime','last_modify'))){
              if ("default"==$l_k){
                // 默认值部分可能是 'NULL' 在上一步判断中认为不等，在我们这里则认为相等
                if ('NULL'==$l_v) {
                  //$l_v = null;
                  $l_str .= '`'.$l_k.'`=null,';
                  continue;
                }
              }
              if ("t_id"==$l_k) {
                // 所属表id需要进行一定的替换 // $l_v = $l_tbl_id2["id"];
                $l_str .= '`'.$l_k."`=(select id from " . TABLENAME_PREF . "table_def where name_eng='".$l_one_t["name_eng"]."'),";
                continue;
              }
              if (is_null($l_v)) {
                $l_str .= '`'.$l_k.'`=null,';
                continue;
              }
              $l_v = str_replace("\r\n",'\r\n',$l_v);
              $l_v = str_replace("'","''",$l_v);
              $l_str .= "`".$l_k."`='".$l_v."',";
            }
          }
          $l_str = rtrim($l_str," ,");
          //$l_str .= ',`createdate`=DATE_FORMAT(NOW(), \'%Y-%m-%d\'),`createtime`=DATE_FORMAT(NOW(), \'%H:%i:%s\')';
          $l_insert_arr[] = "INSERT INTO `" . TABLENAME_PREF . "field_def` set $l_str ;";
        }
      }
      if (!empty($l_insert_arr)) {
        foreach ($l_insert_arr as $l_line){
          echo $l_line.NEW_LINE_CHAR;
        }
      }

      echo NEW_LINE_CHAR . "SELECT * FROM `" . TABLENAME_PREF . "field_def` WHERE `t_id`=(select id from " . TABLENAME_PREF . "table_def where name_eng='".$l_one_t["name_eng"]."') ORDER BY id" . NEW_LINE_CHAR;
    }
    exit;

    /* 测试内存增量
    $_mem = array();$_diff=array();
    for ($i=0;$i<10;$i++){
    $_mem[$i] = memory_get_usage();
    if($i>=1) {
    $_diff[$i] = $_mem[$i] - $_mem[$i-1];
    }else $_diff[$i] = 0;
    $dbR->dbo = &DBO("dbR2");
    }
    print_r($_mem);
    print_r($_diff);
    exit;*/

    // 参数检验可以放到validate中去，先放到此处检验一下。
    /*if (!array_key_exists("db_name",$request)) {
    $response['html_content'] = date("Y-m-d H:i:s") . " db_name must not be empty!! ". NEW_LINE_CHAR;
    return null;  // 总是返回此结果
    }
    if (!array_key_exists("db_pwd",$request)) {
    $response['html_content'] = date("Y-m-d H:i:s") . " db_pwd must not be empty!! ". NEW_LINE_CHAR;
    return null;  // 总是返回此结果
    }
    if (!array_key_exists("pro_type",$request)) {
    $response['html_content'] = date("Y-m-d H:i:s") . " pro_type must not be empty!! ". NEW_LINE_CHAR;
    return null;  // 总是返回此结果
    }*/

    // 调试信息字符串
    $l_str = "";

    // 1 连接目的库 aaaaaaa_p1
    $dbR = new DBR();
    $l_srv_db_dsn = $dbR->getDSN("array");
    $l_srv_db_dsn["database"] = "aaaaaaa_p1";
    //$dbR_to = new DBR(cString::getMysqlDsnStrFromMDB2DSN($l_srv_db_dsn));
    /*$l_err = $dbR_to->errorInfo();
    if ($l_err[1]>0){
    // 数据库连接失败后
    $response['html_content'] = date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
    return null;
    }*/
    //$dbW_to = new DBW(cString::getMysqlDsnStrFromMDB2DSN($l_srv_db_dsn));
    //$dbW_to->SetCurrentSchema($l_srv_db_dsn["database"]);

    // 2 连接来源库 aups_p1 库
    $l_srv_db_dsn_from = $l_srv_db_dsn;  // 数据库连接信息
    $l_srv_db_dsn_from["database"] = "aups_p1";
    $dbR = new DBR(cString::getMysqlDsnStrFromMDB2DSN($l_srv_db_dsn_from));

    $l_allow_tbl = array("页面碎片","正文页","栏目配置","媒体配置","CSS模板","JS模板","栏目页","专题页","功能代码");
    // 获取aups_p1库的表定义表中中文表名为指定的几张表
    $dbR->table_name = TABLENAME_PREF."table_def";
    $l_all_tbl = $dbR->getAlls();
    //print_r($l_all_tbl);exit;

    foreach ($l_all_tbl as $l_tbl_v) {
      if (in_array($l_tbl_v["name_cn"], $l_allow_tbl)) {
        // 3 依据来源库的表定义信息创建数据表，并将信息插入目标库的表定义表中
        $dbR = new DBR(cString::getMysqlDsnStrFromMDB2DSN($l_srv_db_dsn_from));
        $dbR->table_name = $l_tbl_v["name_eng"];
        $l_rlt = $dbR->getAlls();

        if (("tmpl_design"==$l_tbl_v["name_eng"])) {
          $l_sql = file_get_contents($GLOBALS['cfg']['PATH_RUNTIME']."/DataDriver/sql/tmpl_design.sql");
        }else {
          $rlt = $dbR->SHOW_CREATE_TABLE($l_tbl_v["name_eng"]);
          if (PEAR::isError($rlt)) {
            echo " error message： " .$rlt->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
            return null;
          }
          $l_sql = $rlt[0]["Create Table"];
        }
        $l_sql = str_ireplace("DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP", "", $l_sql);
        //if ("aups_t1"==$l_tbl_v["name_eng"]) echo "---- LINE:". __LINE__ . ",".$l_sql. NEW_LINE_CHAR;
        if (!empty($l_sql)) {
          if (false!==strpos($l_sql,"AUTO_INCREMENT=")) {
            $l_sql = preg_replace("/AUTO_INCREMENT=\d+/","",$l_sql);
          }
          $l_sql = str_replace('lastmodify','last_modify',$l_sql);

          // 将信息插入表定义表中
          $data_arr = $l_tbl_v;
          unset($data_arr["id"]);  // id需要去掉
          $data_arr["p_id"] = 2;  // p_id人工指定为2，cms财经频道
          // 表名稍微麻烦一点，自动表名的需要重新开始编号，并且需要连续
          if ( false!==strpos($data_arr["name_eng"], $GLOBALS['cfg']['DB_TB_DEFALUT_TYPE']) ) {
            $dbR_to = new DBR(cString::getMysqlDsnStrFromMDB2DSN($l_srv_db_dsn));
            $dbR_to->table_name = TABLENAME_PREF."table_def";    // 获取到表定义表中的所有表英文名
            $a_tmpl = $dbR_to->getAlls("","name_eng");
            $data_arr["name_eng"] = DbHelper::getAutocreamentTBname($a_tmpl,"name_eng");
            // 表名修改，所以同时需要将表名修改为新的表名
            $l_sql = str_replace('`'.$l_tbl_v["name_eng"].'`','`'.$data_arr["name_eng"].'`',$l_sql);
          }

          if ("tmpl_design"==trim($data_arr["name_eng"])) $data_arr["name_eng"] = "" . TABLENAME_PREF . "tmpl_design";

          // insert的时候，由于字段修改了可以去掉此字段
          if (array_key_exists("lastmodify",$data_arr)) {
            //$data_arr["last_modify"] = $data_arr["lastmodify"];
            unset($data_arr["lastmodify"]);
          }
          if (""==$data_arr["yingshe"]) {
            unset($data_arr["yingshe"]);
          }
          $l_table_name = $data_arr["name_eng"];

          $dbW_to = new DBW(cString::getMysqlDsnStrFromMDB2DSN($l_srv_db_dsn));
          $dbW_to->table_name = TABLENAME_PREF."table_def";
          $dbW_to->insertOne($data_arr);
          $l_err = $dbW_to->errorInfo();
          if ($l_err[1]>0){
            // 增加失败后
            $response['html_content'] = date("Y-m-d H:i:s") . var_export($l_err, true). " 发生错误,sql: ". $dbW_to->getSQL();
            return null;
          }else {
            $l_new_t_id = $dbW_to->LastID();  // instert后产生的文档id。后面需要用到
          }
        }


        // 4 来源字段定义表中的字段定义需要insert到目标库的字段定义表中. 特别要注意
        // 本应该由创建的数据表自动获取字段信息并入库的，但有些可能是form::select等类型
        // 由于表名可能被修改，因此用原来的表名进行获取来源字段信息
        $dbR = new DBR(cString::getMysqlDsnStrFromMDB2DSN($l_srv_db_dsn_from));
        $dbR->table_name = TABLENAME_PREF."field_def";
        $l_all_fis = $dbR->getAlls(" where t_id=".$l_tbl_v["id"] . " order by list_order");
        /*if ("aups_t1"==$l_tbl_v["name_eng"]) {
        print_r($l_all_fis);
        echo "---- LINE:". __LINE__ . ",". $l_sql. NEW_LINE_CHAR;
        }*/

        // 哪些字段替换成了对应的什么字段，需要转码的数组进行记录，并且进行统一替换，不能单独一个个的替换，因为可能导致同名字段的覆盖。
        $l_replace_fld = array();
        $l_replace_fld2 = array();
        foreach ($l_all_fis as $l_fid_v) {
          // 逐条插入目的库的字段定义表，特别要注意将数据的对应关系
          $data_arr = $l_fid_v;

          unset($data_arr["id"]);  // id 需要去掉
          $data_arr["t_id"] = $l_new_t_id; // p_id人工指定为2，cms财经频道
          // 字段名需要重新开始编号，并且需要连续
          if ( false!==strpos($data_arr["name_eng"], $GLOBALS['cfg']['DB_FIELD_DEFALUT_TYPE']) ) {
            $dbR_to = new DBR(cString::getMysqlDsnStrFromMDB2DSN($l_srv_db_dsn));
            $dbR_to->table_name = TABLENAME_PREF."field_def";// 获取到表定义表中的所有表英文名
            $a_tmpl = $dbR_to->getAlls("","name_eng");
            $data_arr["name_eng"] = DbHelper::getAutocreamentFieldname($a_tmpl,"name_eng");

            $l_replace_fld['`'.$l_fid_v["name_eng"].'`'] = '`'.$data_arr["name_eng"].'`';
            $l_replace_fld2[$l_fid_v["name_eng"]] = $data_arr["name_eng"];
          }

          if ("lastmodify"==$data_arr["name_eng"])$data_arr["name_eng"] = "last_modify";
          if (""==$data_arr["yingshe"]) {
            unset($data_arr["yingshe"]);
          }

          $dbW_to = new DBW(cString::getMysqlDsnStrFromMDB2DSN($l_srv_db_dsn));
          $dbW_to->table_name = TABLENAME_PREF."field_def";
          $dbW_to->insertOne($data_arr);
          $l_err = $dbW_to->errorInfo();
          if ($l_err[1]>0){
            // 增加失败后
            $response['html_content'] = date("Y-m-d H:i:s") . var_export($l_err, true). " 发生错误,sql: ". $dbW_to->getSQL();
            return null;
          }else {
            $l_new_f_id = $dbW_to->LastID();  // instert后产生的文档id。后面需要用到
          }

          usleep(1000);
        }

        //进行字段的替换，必须统一替换否则字符替换可能有问题
        // 字段英文名修改，建表语句相应地要修改一下
        //echo "@@@@";print_r($l_replace_fld);echo "@@@@\n";
        $l_sql = cString::str__replace($l_replace_fld,$l_sql);
        /*if ("aups_t1"==$l_tbl_v["name_eng"]) {
        echo "---- LINE:". __LINE__ . ",".$l_sql. NEW_LINE_CHAR;
        //exit;
        }*/
        $dbW_to = new DBW(cString::getMysqlDsnStrFromMDB2DSN($l_srv_db_dsn));
        DbHelper::execDbWCreateInsertUpdate($dbW_to,$l_sql);

        // 向数据表中插入数据
        if (!empty($l_rlt)) {
          //
          foreach ($l_rlt as $l_record){
            foreach ($l_record as $l_k => $l_v){
              if (array_key_exists($l_k, $l_replace_fld2)) {
                // insert的时候，由于字段修改了可以去掉此字段

                unset($l_record[$l_k]); // 注销该值
                $l_record[$l_replace_fld2[$l_k]] = $l_v; // 用新字段
              }
              // 字段有变动
              if ( "lastmodify"==$l_k || ("yingshe"==$l_k && ""==$l_v)) {
                unset($l_record[$l_k]);
              }
            }
            //
            $dbW_to->table_name = $l_table_name;
            $dbW_to->insertOne($l_record);
            $l_err = $dbW_to->errorInfo();
            if ($l_err[1]>0){
              // 增加失败后
              echo "@@@@@LINE:". __LINE__ . var_export($l_err, true). " 发生错误,sql: ". $dbW_to->getSQL() . "\n";
              //return null;
            }else {

            }
          }
        }
      }
      usleep(100000);
    }

    $response['html_content'] = date("Y-m-d H:i:s") . var_export($l_str, true);
    return null;
  }
}
