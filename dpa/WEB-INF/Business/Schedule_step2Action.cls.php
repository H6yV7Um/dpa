<?php
/**
 * Schedule_step2Action.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once('mvc/Action.cls.php');
require_once('mod/DBR.cls.php');

class Schedule_step2Action extends Action {
  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

    $id = (int)$request["id"];
    $default_belong_user = "www";

    $_arr = array();
    // 点击 上一步
    if (1==$request["rewind"]) {
      $_arr["belong_user"] = empty($request["belong_user"])?$default_belong_user:$request["belong_user"];
    }else {
      if ("update"==$request["_action"]) {
        $dbR = new DBR();
        $dbR->table_name = TABLENAME_PREF."schedule";
        $_arr = $dbR->getOne("where id=".$id);
        $_arr["belong_user"] = ($_arr["belong_user"]);
      }else {
        $_arr["belong_user"] = $default_belong_user;  // 默认值
      }
    }

    // 先获取模板
    $content = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/".$actionMap->getProp("path").".html");
    // 加入头尾
    $header = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."header.html");  // 标准头
    $footer = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."footer.html");  // 标准尾
    $data_arr = array(
      "id"=>$id,
      "_action"=>$request["_action"],
      "name"=>$request["name"],
      "host"=>$request["host"],
      "server_timezone"=>$request["server_timezone"],
      "RES_WEBPATH_PREF"=>$GLOBALS['cfg']['RES_WEBPATH_PREF'],
      "header"=>$header,
      "footer"=>$footer
    );
    $content = replace_template_para($data_arr,$content);

    $content = replace_template_para($_arr,$content);

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
