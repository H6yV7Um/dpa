<?php
/**
 * Schedule_editAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once('mvc/Action.cls.php');
require_once('mod/DBW.cls.php');


class Schedule_editAction extends Action {
  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

    if (1!=$_SESSION["user"]["if_super"]) {
          $response['html_content'] = "权限不够!";
          return null;  // 总是返回此结果
        }

    $id = (int)$request["id"]; // 强制类型转化，以保证安全性

    if ( empty($form) ){

    }else {
      // 修改操作

      //global $SHOW_SQL;$SHOW_SQL="all";
      if("start"==$request["_action"]){
        $data_arr = array("status_"=>'1');
      }else if("stop"==$request["_action"]){
        $data_arr = array("status_"=>'0');
      }else if("update"==$request["_action"]){
        $data_arr = array(
          "name" => convCharacter($form["name"],true),
              "host" => $form["host"],
              "server_timezone" => $form["server_timezone"],
              "belong_user" => $form["belong_user"],

              "minute" => $form["minute"],
              "hour" => $form["hour"],
              "day" => $form["day"],
              "month" => $form["month"],
              "week" => convCharacter($form["week"],true),
              "forbidden_date" => $form["forbidden_date"],
              "forbidden_timezone" => $form["forbidden_timezone"],

              "mode" => $form["mode"],
              "shell_command" => $form["shell_command"],

              "mender" => convCharacter($_SESSION["user"]["username"],true),
               "menddate" => date("Y-m-d"),
               "mendtime" => date("H:i:s"),
              "description" => convCharacter($form["description"],true)
        );
      }else {
        // some error
        return null;
      }
      // 执行 update 操作
      $dbW = new DBW();
      $dbW->table_name = TABLENAME_PREF."schedule";
      $conditon = " id = ".$id." ";
      if($dbW->updateOne($data_arr,$conditon)){
        $response['html_content'] = date("Y-m-d H:i:s") . " 成功修改信息, <a href='?do=schedule_list&id=".$id."'>返回列表页面</a> ";
        return "main.php?do=schedule_list";  // 总是返回此结果
      }else {
        $response['html_content'] = date("Y-m-d H:i:s") . " 更新数据出错, <a href='?do=schedule_edit&id=".$id."'>重新编辑</a> ";
        return null;  // 总是返回此结果
      }
    }
  }
}
