<?php
require_once("common/lib/cString.cls.php");

/**
 * AddAction.cls.php
 *
 * @abstract Add or edit
 *
 * @author chengfeng<biosocial@gmail.com>
 * @version 1.0
 */
class AddAction {


  /**
   * 构造函数，可进行一些初始化操作，例如修改分页默认值
   *
   * @param array|null $_arr
   */
  function __construct($_arr=null){
    //
  }
  // for php4
  function AddAction($_arr=null){
    return $this->__construct($_arr);
  }

  function Init(&$request, &$a_arr){
    // 通过do而得到的分解出来的细致动作，仅仅依赖于do动作。放在最前面
    $l_act_type = cString::getNameActType($request["do"]);
    $this->type_name = $l_act_type[0];
    $this->action = $l_act_type[1];

    // 初始化一些需要用到的默认数据
    if(!array_key_exists("parent_ids_arr", $a_arr)) $a_arr["parent_ids_arr"] = array();
    if(!array_key_exists("a_options", $a_arr)) $a_arr["a_options"] = array();
    if(!array_key_exists("sql_order", $a_arr)) $a_arr["sql_order"] = "order by id ";
    if(!array_key_exists("tplname", $a_arr)) $a_arr["tplname"] = "add"; // $actionMap->getProp("path")
    if(!array_key_exists("default_over", $a_arr)) $a_arr["default_over"] = array(); // $actionMap->getProp("path")

    // 获取父级。依据指定的父级元素、及本级元素而获取到的id列表等相关数据。放在上面初始化之后
    $a_arr["parent_rela"] = GetParentIds($request, $a_arr["parent_ids_arr"], $a_arr["a_options"]);
  }

  /**
   * actiong execute
   *
   * @param array $a_arr
   * @param Object $actionMap
   * @param Object $actionError
   * @param array $request
   * @param array $response
   * @param array $form
   */
  function executeListForm(&$a_arr,&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie){
    //$dbR = $a_arr["dbR"];

    // 依据字段定义信息，逐一生成相应的html表单项
    $l_no_need_field = array_key_exists("no_need_field",$a_arr) ? $a_arr["no_need_field"] : array();
    $peizhi = Html::FormInputByField($a_arr["f_info"], $a_arr["default_over"], $l_no_need_field);

    // 先获取模板
    $content = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/".$a_arr["tplname"].".html");
    // 加入头尾
    $header = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."header.html");  // 标准头
    $footer = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."footer.html");  // 标准尾

    // 填写或修改数据时候的js验证等js_code
    $l_js_code_add_edit = "";
    if (array_key_exists("t_def",$a_arr) && array_key_exists("js_verify_add_edit",$a_arr["t_def"]) && "TRUE"==$a_arr["t_def"]["js_verify_add_edit"]) {
      $l_js_code_add_edit = $a_arr["t_def"]["js_code_add_edit"];
    }

    $data_arr = array(
      // 导航应该放到总类里面去执行，因为具备通用性
          "html_title" => $a_arr["html_title"],
          "html_name" => $a_arr["html_name"],

          "parent_nav"=>$a_arr["parent_rela"]["parent_nav"],
          "parent_elements_str"=>$a_arr["parent_rela"]["parent_elements_str"],// 多项用逗号隔开，单项的分号后面是表名简称没有前缀的
      "parent_ids_input_hidden"=>$a_arr["parent_rela"]["parent_ids_input_hidden"],
      "parent_ids_url_build_query"=>$a_arr["parent_rela"]["parent_ids_url_build_query"],

      "do"=>$request["do"],
      "type_name"=>$this->type_name,
      "action"   =>$this->action,
      "pt"=>@$request["pt"],

      "peizhi"=>$peizhi,
      "js_code_add_edit"=>$l_js_code_add_edit,

      "tpl_zengjiaxiangmu"=>$GLOBALS['language']['TPL_ZENGJIA_STR'].$GLOBALS['language']['TPL_XIANGMU_STR'],

      "tpl_xiangmuliebiao"=>$GLOBALS['language']['TPL_XIANGMU_STR'].$GLOBALS['language']['TPL_LIEBIAO_STR'],
      "tpl_dangqianweizhi"=>$GLOBALS['language']['TPL_DANGQIANWEIZHI_STR'],
      "tpl_xitongtongzhi"=>$GLOBALS['language']['TPL_XITONGTONGZHI_STR'],
      "tpl_zengjia"=>array_key_exists("tpl_zengjia",$a_arr)?$a_arr["tpl_zengjia"]:$GLOBALS['language']['TPL_ZENGJIA_STR'],
      "tpl_chongxie"=>$GLOBALS['language']['TPL_CHONGXIE_STR'],

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

    return replace_template_para($data_arr,$content);
  }

  function getFieldsInfo(&$a_arr){
    // 先去表定义表找对应的table_id，
        $dbR = $a_arr["dbR"];
        $dbR->table_name = $a_arr["TBL_def"];
        $t_info = $dbR->getOne(" where name_eng='".$a_arr["table_name"]."' ");
        if (PEAR::isError($t_info)) {
          echo " error message： " .$t_info->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
          return null;
        }
        if (!empty($t_info)) {
          $t_id = $t_info["id"];
        }else {
          echo "table_empty";//作为错误信息显示出来
          return null;
        }
        $a_arr["t_def"] = $t_info;

        // 也可以获取真实的、实时的字段定义，直接从该表结构中获取到
        //$dbR->table_name = $a_arr["table_name"];
        //$f_real_info = $dbR->getTblFields2();
        //print_r($f_real_info);

        // 获取数据表的字段。完全依据field_def，所有的字段操作必须同field_def中一致
        $dbR->table_name = $a_arr["FLD_def"];
        $f_info = $dbR->getAlls(" where t_id='$t_id' and status_='use' ".$a_arr["sql_order"]);
        if (empty($f_info)) {
          // 未获取到字段定义信息
          echo "field_empty";  // 作为错误信息，显示给用户
          return null;
        }
        // 数字索引变为字段索引
    $f_info = cArray::Index2KeyArr($f_info, array("key"=>"name_eng", "value"=>array()));
        $a_arr["f_info"] = $f_info;

        return $f_info;
  }
}
