<?php
/**
 * Project_listAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/Pager.cls.php");
require_once('mvc/ListAction.cls.php');
require_once('mod/DBR.cls.php');

class Project_listAction extends ListAction {
  /**
   *
   * @access public
   * @param array &$actionMap
   * @param array &$actionError
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
    $dbR->table_name = $table_name = TABLENAME_PREF."project";

    // 应该自动获取表定义表和字段定义表,此处省略并人为指定????
    $TBL_def = TABLENAME_PREF."table_def";
    $FLD_def = TABLENAME_PREF."field_def";

    $arr = array();
    $arr["table_name"] = $table_name;
    $arr["TBL_def"] = $TBL_def;
    $arr["FLD_def"] = $FLD_def;
    $arr["html_title"] = $GLOBALS['language']['TPL_XIANGMU_STR'].$GLOBALS['language']['TPL_LIEBIAO_STR'];
    $arr["html_name"]  = $GLOBALS['language']['TPL_XIANGMU_STR'].$GLOBALS['language']['TPL_LIEBIAO_STR'];
    $arr["sql_order"] = "order by id desc";
    $arr["dbR"] = $dbR;

    // 需要加入权限限制所能查看的数据表
    if (1!=$_SESSION["user"]["if_super"]){
      $l_ps = UserPrivilege::getSqlInProjectByPriv();
      if (""!=$l_ps) $arr["default_sqlwhere"] = "where id in ($l_ps)";
      else $arr["default_sqlwhere"] = "where id<0 ";  // 将获取不到任何数据, 如果么有权限的话
    }
    $this->Init($request, $arr); // 初始化一下, 需要用到的数据的初始化动作,在parent::之前调用

    parent::getFieldsInfo($arr);
    if(!array_key_exists("f_info",$arr)) {
      $response['ret'] = array('ret'=>1,'msg'=>"the f_info not exist!");
      return null;
    }

    $dbR->table_name = $table_name;
    $resp = parent::execute($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie, $files);

    $ziduan_arr = getZiduan("id:ID;name_cn:项目名称;db_host:数据库主机;db_name:数据库名称;db_user:数据库用户名;status_:状态");// 需要的字段
    $show_arr = buildH($arr["_arr"], $ziduan_arr);
    $show = $show_arr[0];
    $show_title = $show_arr[1];

    $data_arr = array(
    "show"=>$show,
    "show_title"=>$show_title,
    );

    $response['html_content'] = replace_template_para($data_arr,$resp);
    $response['ret'] = array('ret'=>0);
    return null;  // 总是返回此结果
  }
}
