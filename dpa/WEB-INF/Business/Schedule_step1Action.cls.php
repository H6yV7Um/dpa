<?php
/**
 * Schedule_step1Action.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/global_func.php");
require_once('mvc/Action.cls.php');
require_once('mod/DBR.cls.php');

class Schedule_step1Action extends Action {
  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

    $id = (int)$request["id"];
    $dbR = new DBR();
    $dbR -> table_name = TABLENAME_PREF."host_backend_reg";
    $host_arr = $dbR->getCol("distinct host_ip");
    $_host_arr = array();
    foreach($host_arr as $val){
      $_host_arr[$val] = $val;
    }
    $host_options = buildOptions($_host_arr,getServerIp(),false); // 默认值
    $server_timezone_options = buildOptions(getTimezone(),8,false);

    //
    $_arr = array();
    // 点击 上一步
    if (1==$request["rewind"]) {
      $_arr["name"] = $request["name"];
      $_arr["host_options"] = buildOptions($_host_arr,$request["host"],false);
      $_arr["server_timezone_options"] = buildOptions(getTimezone(),$request["server_timezone"]);
    }else {
      if ("update"==$request["_action"]) {
        $dbR->table_name = TABLENAME_PREF."schedule";
        $_arr = $dbR->getOne("where id=".$id);
        $_arr["name"] = convCharacter($_arr["name"]);

        $_arr["host_options"] = buildOptions($_host_arr,$_arr["host"],false);
        $_arr["server_timezone_options"] = buildOptions(getTimezone(),$_arr["server_timezone"]);
      }else {
        $_arr["name"] = "";    // 默认值
        $_arr["host_options"] = $host_options;  // 默认值
        $_arr["server_timezone_options"] = $server_timezone_options;  // 默认值
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
