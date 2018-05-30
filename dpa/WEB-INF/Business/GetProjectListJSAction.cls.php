<?php
/**
 * GetProjectListJSAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once('mvc/Action.cls.php');
require_once('mod/DBR.cls.php');

class GetProjectListJSAction extends Action {

  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

    $pt = strtoupper($request["pt"]);

    $dbR = new DBR();
    $l_err = $dbR->errorInfo();
    if ($l_err[1]>0){
      // 数据库连接失败后
      $response['html_content'] = date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
          $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
      return null;
    }
    $dbR->table_name = TABLENAME_PREF."project";

    // 需要根据用户权限显示其具有操作权限的项目库 begin
    if (1==$_SESSION["user"]["if_super"]) {
      $l_where = " where status_!='del' ";  // " where type='$pt' "
    } else {
      $l_ps = UserPrivilege::getSqlInProjectByPriv();
      if (""!=$l_ps) $l_where = "where id in ($l_ps)";
      else $l_where = "where id<0 ";  // 将获取不到任何数据, 如果么有权限的话
    }
    $arr = $dbR->getAlls($l_where);
    // 过滤掉"发布系统",如果是非超级管理员的话
    if (1 != $_SESSION["user"]["if_super"] && isset($arr['0']['id']) && 1 == $arr['0']['id'])
      unset($arr['0']);
    // 用户权限 end

    if ("RES"==$pt) {
      $contentjs = $this->buildjsRES($arr,$request["node"]);
    }else {
      $contentjs = $this->buildjsPUB($arr,$request["node"]);
    }

    // 先获取模板
    $content = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/".$actionMap->getProp("path").".html");
    // 加入头尾
    $header = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."header.html");  // 标准头
    $footer = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."footer.html");  // 标准尾
    $data_arr = array(
      "contentjs"=>$contentjs,
      "RES_WEBPATH_PREF"=>$GLOBALS['cfg']['RES_WEBPATH_PREF'],
      "header"=>$header,
      "footer"=>$footer
    );
    $content = replace_template_para($data_arr,$content);

    // 替换其中的css地址和js地址 以后采用缓存文件，而不用每次都实时取
    $content = replace_cssAndjsAndimg($content,$GLOBALS['cfg']['SOURCE_CSS_PATH'],$GLOBALS['cfg']['SOURCE_JS_PATH'],$GLOBALS['cfg']['SOURCE_IMG_PATH']);
    // 将外链的js替换为其相应js内容
    //$content = jssrc2content($content);
    // 替换其中的css地址和js地址 以后采用缓存文件，而不用每次都实时取
    $content = replace_cssAndjsAndimg($content,$GLOBALS['cfg']['SOURCE_CSS_PATH'],$GLOBALS['cfg']['SOURCE_JS_PATH'],$GLOBALS['cfg']['SOURCE_IMG_PATH']);// js中还有图片


    $response['html_content'] = replace_template_para($data_arr,$content);
    $response['ret'] = array('ret'=>0);
    return null;  // 总是返回此结果
  }

  function buildjsRES($arr, $nodeid=2){
    $str = "
<script language='javascript'>
var parentNode = null;
if(parent.tree && parent.tree != 'undefined')
{
  parentNode=parent.tree.getNode($nodeid);
}
if(parentNode && parentNode.loaded != true)
{
  parentNode.loaded=true;
  ";
    if (!empty($arr)) {
      foreach ($arr as $val){
        $cn_name = convCharacter($val["name_cn"]);  // 昵称

        $str .= "
  var cur_node=parentNode.addChild(parent.Tree_LAST, '".$cn_name."');
  cur_node = cur_node.addChild(parent.Tree_LAST, '资源管理');
  cur_node.setLink('main.php?do=resource_list&p_id=".$val["id"]."', '');
  cur_node = cur_node.addSibling(parent.Tree_LAST, '资源同步配置');
  cur_node.setLink('main.php?do=res_sync_list&p_id=".$val["id"]."', '');
    ";
      }
    }

    $str .= "
  parentNode.delChild(0);
}
</script>
       ";

    return $str;
  }

  function buildjsPUB($arr, $nodeid=2){
    $str = "
<script type=\"text/javascript\">
var parentNode = null;
if(parent.tree && parent.tree != 'undefined')
{
  parentNode=parent.tree.getNode($nodeid);
}
if(parentNode && parentNode.loaded != true)
{
  parentNode.loaded=true;
    ";

    if (!empty($arr)) {
      foreach ($arr as $val){
        $cn_name = convCharacter($val["name_cn"]);  // 昵称

        $str .= "
     var cur_node=parentNode.addChild(parent.Tree_LAST, '".$cn_name."');
  var cur_node = cur_node.addChild(parent.Tree_LAST, '发布列表');
  cur_node.setScript('LoadTemplateListMenu(tree.getSelect().id, ".$val["id"].")');
  cur_node.addChild(".$val["parent_id"].",'loading...');
        ";
		if (isset($_SESSION["user"]["if_super"]) && 1 == $_SESSION["user"]["if_super"]) {
         $str .= "
  cur_node = cur_node.addSibling(parent.Tree_LAST, '模板管理');
  cur_node.setLink('main.php?do=template_list&p_id=".$val["id"]."', '');
      ";
		}
      }
    }

    $str .= '
     parentNode.delChild(0);
}
</script>';

    return $str;
  }
}
