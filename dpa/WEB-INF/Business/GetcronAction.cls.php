<?php
/**
 * GetcronAction.cls.php
 */

require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once('mvc/Action.cls.php');
require_once('mod/DBR.cls.php');

class GetcronAction extends Action {
  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

    $user   = isset($request["belong_user"])?$request["belong_user"]:"www";  // 通常就是finance账号执行crontab
    $status = isset($request["status_"])?( 0+$request["status_"] ):1;  // 强制类型转换
    $host   = $request["host"];

    $dbR = new DBR();
    $l_err = $dbR->errorInfo();
    if ($l_err[1]>0){
      // 数据库连接失败后
          $response['html_content'] = date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
          $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
          return null;
    }
    $dbR->table_name = TABLENAME_PREF."schedule";
    // 必须是本机器ip的、并有权限的账号下的、状态为启动状态的，三个条件下的计划任务

    $sql_where = "where host='$host' and belong_user='$user' and status_='$status' order by id desc";
    $_sch = $dbR->getAlls($sql_where);

    $response['html_content'] = $_sch;
    $response['ret'] = array('ret'=>0);
    return null;  // 总是返回此结果
  }
}
