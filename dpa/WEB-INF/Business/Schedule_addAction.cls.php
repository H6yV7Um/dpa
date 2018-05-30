<?php
/**
 * Schedule_addAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once('mvc/Action.cls.php');
require_once('mod/DBR.cls.php');
require_once('mod/DBW.cls.php');

class Schedule_addAction extends Action {
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

    // 如果没有提交表单
    if ( !empty($form) ){
      // 先检查是否重复，以后做此步骤

      // 不存在则插入数据库中
      $data_arr = array(
        "name" => convCharacter($form["name"],true),
        "host" => $form["host"],
        "server_timezone"=>$form["server_timezone"],
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

        "creator" => convCharacter($_SESSION["user"]["username"],true),
        "createdate" => date("Y-m-d"),
        "createtime" => date("H:i:s"),
        "description" => convCharacter($form["description"],true)
      );
      //global $SHOW_SQL;$SHOW_SQL="all";
      $dbW = new DBW();
      $dbW->table_name = TABLENAME_PREF."schedule";
      $rlt = $dbW->insertOne($data_arr);
      if ($rlt) {
        return "main.php?do=schedule_list";
      }else {
        echo "执行sql发生错误";
        return null;
      }
    }else {
      // 先获取模板
      $content = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/".$actionMap->getProp("path").".html");
      // 加入头尾
      $header = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."header.html");  // 标准头
      $footer = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."footer.html");  // 标准尾
      $data_arr = array(
        "RES_WEBPATH_PREF"=>$GLOBALS['cfg']['RES_WEBPATH_PREF'],
        "header"=>$header,
        "footer"=>$footer
      );
      //$content = replace_template_para($data_arr,$content);

      // 替换其中的css地址和js地址 以后采用缓存文件，而不用每次都实时取
      $content = replace_cssAndjsAndimg($content,$GLOBALS['cfg']['SOURCE_CSS_PATH'],$GLOBALS['cfg']['SOURCE_JS_PATH'],$GLOBALS['cfg']['SOURCE_IMG_PATH']);
      // 将外链的js替换为其相应js内容
      //$content = jssrc2content($content);
      // 替换其中的css地址和js地址 以后采用缓存文件，而不用每次都实时取
      $content = replace_cssAndjsAndimg($content,$GLOBALS['cfg']['SOURCE_CSS_PATH'],$GLOBALS['cfg']['SOURCE_JS_PATH'],$GLOBALS['cfg']['SOURCE_IMG_PATH']);// js中还有图片


      $response['html_content'] = replace_template_para($data_arr,$content);
      return null;  // 总是返回此结果
    }
  }
}
