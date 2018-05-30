<?php
/**
 * Template_listAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/lib/dbhelper.php");
require_once("common/Pager.cls.php");
require_once('mvc/ListAction.cls.php');
require_once('mod/DBR.cls.php');
require_once('mod/DBW.cls.php');

class Template_listAction extends ListAction {
  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie){

    $dbR = new DBR();
    $l_err = $dbR->errorInfo();
    if ($l_err[1]>0){
      // 数据库连接失败后
      $response['html_content'] = date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
      $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
      return null;
    }

    $table_name = TABLENAME_PREF."table_def";

    // 获取发布主机列表 , 用于ui
    $a_p_self_ids = array(
      1=>array("ziduan"=>"p_id"),
    );
    // 获取到前两级的数据数组
    $p_self_info = DbHelper::getProTblFldArr($dbR, $request, $a_p_self_ids);
    //print_r($p_self_info);

    // 应该自动获取表定义表和字段定义表,此处省略并人为指定????
    $TBL_def = TABLENAME_PREF."table_def";
    $FLD_def = TABLENAME_PREF."field_def";


    $arr = array();
    $arr["table_name"] = $table_name;
    $arr["TBL_def"] = $TBL_def;
    $arr["FLD_def"] = $FLD_def;
    $arr["html_title"] = $GLOBALS['language']['TPL_MOBAN_STR'].$GLOBALS['language']['TPL_LIEBIAO_STR'];
    $arr["html_name"] = $p_self_info["p_def"]["name_cn"].$arr["html_title"];
    $arr["default_sqlwhere"]  = "where `name_eng` NOT LIKE '%table_def' AND `name_eng` NOT LIKE '%field_def'"; // 表定义表和字段定义表不用显示
    $arr["sql_order"] = "order by id desc";
    $arr["parent_ids_arr"] = array(1=>"p_id");  // 父级元素列表,p2, p3...分别表示二、三级父级元素例如项目id，模板id，文档id等
    $arr["a_options"] = array(
      "nav"=>array(
        "p_id"=>array(
          "script_name"=>"main.php", // 可有可无
          "do"   =>"project_list",
          "value"=>$request["p_id"],
          "name_cn"=>$GLOBALS['language']['TPL_XIANGMU_STR'].$GLOBALS['language']['TPL_LIEBIAO_STR'],
        )
      )
    );
    $arr["dbR"] = $dbR;

    $this->Init($request, $arr); // 初始化一下, 需要用到的数据的初始化动作,在parent::之前调用

    parent::getFieldsInfo($arr);
    if(!array_key_exists("f_info",$arr)) {
      $response['ret'] = array('ret'=>1,'msg'=>"the f_info not exist!");
      return null;
    }

    $dbR->table_name = $table_name;
    $resp = parent::execute($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

    $ziduan_arr = getZiduan("id:模板ID;name_cn:表中文名称;name_eng:表名;p_id:所属项目id");// 需要的字段
    $document_show_arr = buildH($arr["_arr"],$ziduan_arr);
    $show = $document_show_arr[0];
    $show_title = $document_show_arr[1];

    $data_arr = array(
      "show"=>$show,
      "show_title"=>$show_title,
      "INPUT_other"=>'',//表的字段管理在各个表里面进行，'<input type=button onClick="action_onclick(\'main.php?do=tempdef_list&p_id='.$request["p_id"].'\',self.document.myform,\'id\',\'list\',\'t_id\');return false" value="'.$GLOBALS['language']['TPL_MOBAN_STR'].$GLOBALS['language']['TPL_YU_STR'].$GLOBALS['language']['TPL_GUANLI_STR'].'" />',
    );

    $response['html_content'] = replace_template_para($data_arr,$resp);
    $response['ret'] = array('ret'=>0);
    return null;  // 总是返回此结果
  }
}
