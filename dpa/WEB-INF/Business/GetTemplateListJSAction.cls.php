<?php
/**
 * GetTemplateListJSAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once('mvc/Action.cls.php');
require_once('mod/DBR.cls.php');

class GetTemplateListJSAction extends Action {
  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

    $dbR = new DBR();
    $l_err = $dbR->errorInfo();
    if ($l_err[1]>0){
      // 数据库连接失败后
      $response['html_content'] = date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
          $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
      return null;
    }
    $dbR -> table_name = TABLENAME_PREF."project";
    $p_arr = $dbR->getOne(" where id = ".($request["p_id"]+0));
    //print_r($p_arr); // 模板信息需要从另一个库中获取信息
    if (empty($p_arr)) {
      // 漏洞:如果攻击者使用一个不存在的id，则$p_arr返回的是NULL????其他类似漏洞有时间的时候全部处理一下。
      $response['html_content'] = date("Y-m-d H:i:s") . "project not exist!";
      $response['ret'] = array('ret'=>1);
      return null;
    }
    $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr);$dbR->dbo = &DBO('', $dsn);
    //$dbR = null;$dbR = new DBR($p_arr);
    $l_err = $dbR->errorInfo();
    if ($l_err[1]>0){
      $arr = array();  // 防止js报错所做的空值
      // 数据库连接失败后
      $response['html_content'] = date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
    }else {
      $dbR->table_name = TABLENAME_PREF."table_def";

      // 需要根据用户权限显示其具有操作权限的表
      if (1==$_SESSION["user"]["if_super"]) {
        $l_where = "";  // " where type='$pt' "
      } else {
        $l_ts = UserPrivilege::getSqlInTableByPid($request['p_id']);
        if (""!=$l_ts) $l_where = " and id in ($l_ts)";
        else $l_where = "and id<0 ";  // 将获取不到任何数据, 如果么有权限的话
      }
      $arr = $dbR->getAlls("where `name_eng` NOT LIKE '%table_def' and `name_eng` NOT LIKE '%field_def' " . $l_where);

      if (PEAR::isError($arr)) {
        $arr = array();  // 防止js报错所做的空值
      }

      // 如果还有t_id的话，则需要获取指定的表的数据
      if (isset($request['t_id'])) {
        $dbR->table_name = TABLENAME_PREF."table_def";
        $l_t_info = $dbR->getOne('where id="'. ($request['t_id']+0) .'" or name_eng="'. $request['t_id'] .'"');

        if (!empty($l_t_info)) {
          $dbR->table_name = $l_t_info["name_eng"];
          $l_arr_tbl = $dbR->getAlls();
        }
      }
    }
    if ( isset($request["cont_type"]) && "json"==trim($request["cont_type"])) {
      if (isset($request['t_id']) && isset($l_arr_tbl)) {
        //
        $for_json = format_for_json($l_arr_tbl,"s_shu_xingqiu_id");
        $contentjs = getJson($for_json,"project","name_cn","pingyin_shouzimu");
      }else {
        // 获取所有的project及其所有的表定义表，而不用一个去获取，以后完善此方式????
        $for_json = format_for_json($arr,"p_id");
        $contentjs = getJson($for_json,$name="project","id","name_cn");
      }
    }else {
      $contentjs = $this->buildjs($arr,$request["node"],$p_arr);
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

  function buildjs($arr, $nodeid=24, $p_arr){
    $p_id = $p_arr["id"];
    $l_type = $p_arr["type"];
    if ("DATA"==$l_type) {
      $l_do = "dbdocs_list";
    }else {
      $l_do = "document_list";
    }

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
        // 如果设置了链接，则不自动拼装，因所用模板也可能是自定义
        $tree_link = "main.php?do={$l_do}&p_id=$p_id&t_id=".$val["id"];
        if (@$val['tree_link']) $tree_link = $val['tree_link'];

        $str .= "
    var node=parentNode.addChild(parent.Tree_LAST, '".$val["name_cn"]."');
  node.setLink('{$tree_link}');
      ";
      }
    }
    $str .= '
     parentNode.delChild(0);
}
</script>';

    return $str;
  }
}
