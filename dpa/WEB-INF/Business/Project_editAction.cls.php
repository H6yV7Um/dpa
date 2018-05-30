<?php
/**
 * Project_editAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/lib/dbhelper.php");
require_once("common/lib/Parse_Arithmetic.php");
require_once('mvc/AddAction.cls.php');

class Project_editAction extends AddAction {
  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie,$files=array()){

    if (1!=$_SESSION["user"]["if_super"]) {
      $response['html_content'] = "权限不够!";
      $response['ret'] = array('ret'=>1);
      return null;  // 总是返回此结果
    }
    $table_name = TABLENAME_PREF."project";
    $arr = array();
    $arr["html_title"] = $GLOBALS['language']['TPL_XIUGAI_STR'].$GLOBALS['language']['TPL_XIANGMU_STR'];
    $arr["html_name"]  = $arr["html_title"];
    $arr["tpl_zengjia"]  = $GLOBALS['language']['TPL_XIUGAI_STR'];
    $arr["parent_ids_arr"] = array(1=>"id");  // 本级元素
    $arr["table_name"] = $table_name;

    $TBL_def = TABLENAME_PREF."table_def";
    $FLD_def = TABLENAME_PREF."field_def";

    $arr["dbR"] = new DBR();
    $l_err = $arr["dbR"]->errorInfo();
    if ($l_err[1]>0){
      // 数据库连接失败后
      $response['html_content'] = date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
      $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
      return null;
    }
    $arr["TBL_def"] = $TBL_def;
    $arr["FLD_def"] = $FLD_def;

    $this->Init($request, $arr);  // 需要初始化一下

    parent::getFieldsInfo($arr);
    if(!array_key_exists("f_info",$arr)) {
      $response['ret'] = array('ret'=>1,'msg'=>"the f_info not exist!");
      return null;
    }

    // 需要依据数据表的字段进行显示表单;同时应当填充本身所具有的数据进行呈现
    $dbR = $arr["dbR"];
    $dbR->table_name = $arr["table_name"];
    $l_rlt = $dbR->getOne(" where id=".$request["id"]);  // 获取数据库中当前数据

    // 需要获取到必须填写的一个字段, 依据数据库结构进行判断
    $l_bixuziduanform = DbHelper::getBiXuFields($arr["dbR"], array("table_name"=>$table_name, "f_info"=>$arr["f_info"]));

    if (empty($form) || empty($form[$l_bixuziduanform[0]])) {
      // 要格式化成"value"，"hidden"附加属性的数组
      $a_over = array();  // 暂时没有附加属性进行添加，之转变为value即可
      $l_self_arr = FmtDataAddAtr($l_rlt, $a_over);

      $arr["default_over"] = $l_self_arr;

      // 先将字段定义的算法进行必要的解析并填充到$arr数组的字段信息f_info中去
      Parse_Arithmetic::parse_for_list_form($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);
      $l_resp = parent::executeListForm($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);
      $data_arr = array(
        "l_other" => ""
      );

      $response['html_content'] = replace_template_para($data_arr,$l_resp);
      $response['ret'] = array('ret'=>0);
      return null;  // 总是返回此结果
    }else{
      // 当有数据提交的时候，需要依据字段属性自动筛选和默认赋值等

      // 同表单呈现一样，填充之前需要将字段的各个算法执行一下，便于修正字段的相关限制和取值范围
      Parse_Arithmetic::parse_for_list_form($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);
      // 各个项目自动检测，对于没有填写的采用默认值，默认为null的则剔除该项目
      $data_arr = DbHelper::getInsertArrByFormFieldInfo($form, $arr["f_info"],true,true);
      // 如果返回有错误，则退出
      if (array_key_exists("___ERR___", $data_arr)) {
        $response['html_content'] = date("Y-m-d H:i:s") . "field empty: ". var_export($data_arr["___ERR___"], TRUE);
        $response['ret'] = array('ret'=>1);
        return null;
      }
      // 自动填充几个数据，修改者、时间的字段 if (!array_key_exists("mender", $data_arr))
      if (array_key_exists("creator", $data_arr)) $data_arr["creator"] = ("0"==$data_arr["creator"]) ? $_SESSION["user"]["username"] : $data_arr["creator"];
      if (array_key_exists("createdate", $data_arr)) $data_arr["createdate"] = ("0000-00-00"==$data_arr["createdate"]) ? date("Y-m-d") : $data_arr["createdate"];
      if (array_key_exists("createtime", $data_arr)) $data_arr["createtime"] = ("00:00:00"==$data_arr["createtime"])   ? date("H:i:s") : $data_arr["createtime"];
      if (array_key_exists("mender",   $arr["f_info"])) $data_arr["mender"]   = $_SESSION["user"]["username"];
      if (array_key_exists("menddate", $arr["f_info"])) $data_arr["menddate"] = date("Y-m-d");
      if (array_key_exists("mendtime", $arr["f_info"])) $data_arr["mendtime"] = date("H:i:s");

      $dbW = new DBW();
      $dbW->table_name = $table_name;
      $conditon = " id = ".$request["id"]." ";
      cArray::delSameValue($data_arr,$l_rlt);  // 剔除掉没有修改的数据项
      if (!empty($data_arr)) {
        $dbW->updateOne($data_arr, $conditon);
        $l_err = $dbW->errorInfo();
        if ($l_err[1]>0){
          $response['html_content'] = date("Y-m-d H:i:s") . " 更新数据出错,".NEW_LINE_CHAR . date("Y-m-d H:i:s") . " FILE: ".__FILE__." ". " FUNCTION: ".__FUNCTION__." Line: ". __LINE__ . " SQL: ".$dbW->getSQL().", _arr:" . var_export($l_err, TRUE) . var_export($data_arr,true)." <a href='?do=project_edit&id=".$request["id"]."'>重新编辑</a> ";
        } else {
          if ('del'==$form['status_']) {
            $response['ret'] = array('ret'=>0);
            $response['html_content'] = "main.php?do=".$this->type_name."_list".$arr["parent_rela"]["parent_ids_url_build_query"];  // 删除处理直接返回到列表页面
            return ;
          }
          $response['html_content'] = date("Y-m-d H:i:s") . "<br />修改的字段:". var_export(array_keys($data_arr),true) . "<br /> 成功修改信息, <a href='?do=project_list&id=".$request["id"]."'>返回列表页面</a> ";

          // 更新成功以后，顺便将表进行修复
          // 事先最好进行侦测库里面是否有table_def表以及是否有数据，在有数据的情况下无需额外的数据执行.以后完善之????
          //$rlt = DbHelper::createDBandBaseTBL($form, "", "utf8", "db", false);  // 暂时允许其补充修补相关表
        }
      } else {
        $response['html_content'] = date("Y-m-d H:i:s") . " 未修改任何数据, <a href='?do=project_edit&id=".$request["id"]."'>重新编辑</a> ";
      }

      $response['ret'] = array('ret'=>0);
      return null;  // 总是返回此结果
    }
  }
}
