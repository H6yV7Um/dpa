<?php
/**
 * Loginlog_delAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once('mvc/Action.cls.php');
require_once('mod/DBW.cls.php');

class Loginlog_delAction extends Action {
  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

    if (1!=$_SESSION["user"]["if_super"]) {
      $response['html_content'] = "权限不够!";
      $response['ret'] = array('ret'=>1);
      return null;  // 总是返回此结果
    }

    $dbW = new DBW();
    $l_err = $dbW->errorInfo();
    if ($l_err[1]>0){
      // 数据库连接失败后
      $response['html_content'] = date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
      $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
      return null;
    }
    $dbW->table_name = TABLENAME_PREF."loginlog";
    // 先验证数据合法性，此处先省略
    $id = (int)$request["id"];

    // 其次检查数据库中是否有此数据
    if(!$dbW->getExistorNot(" id=$id ")){
      // 不存在则显示相应的提示信息
      $response['html_content'] = date("Y-m-d H:i:s") . " 不存在此信息， id: ".$id;
      $response['ret'] = array('ret'=>1);
      return null;
    } else {
      // 存在则从数据库中删除
      $dbW->delOne(array("id"=>$id),"id");
      return "main.php?do=loginlog_list";  // 总是返回此结果
    }
  }
}
