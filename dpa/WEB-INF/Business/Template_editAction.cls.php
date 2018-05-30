<?php
/**
 * Template_editAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/lib/dbhelper.php");
require_once("common/lib/Parse_Arithmetic.php");
require_once('mvc/AddAction.cls.php');

class Template_editAction extends AddAction {
    /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
    function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

        // 找到父级元素, 只有一级父级
        $p_id = $request["p_id"];  // 第一个父级id, 也是project id。
        $dbR = new DBR();      // 系统默认数据库连接信息，开始都从这个入口
        $l_err = $dbR->errorInfo();
        if ($l_err[1]>0){
          // 数据库连接失败后
          $response['html_content'] = date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
          $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
          return null;
        }

        // 获取发布主机列表 , 用于ui
        $a_p_self_ids = array(
          1=>array("ziduan"=>"p_id"),
        );
        // 获取到前两级的数据数组
        $p_self_info = DbHelper::getProTblFldArr($dbR, $request, $a_p_self_ids);

        // 应该自动获取表定义表和字段定义表,此处省略并人为指定????
        $TBL_def = TABLENAME_PREF."table_def";
        $FLD_def = TABLENAME_PREF."field_def";

        $arr = array();
        $arr["dbR"] = $dbR;
        $arr["table_name"] = $TBL_def;  // 执行插入操作的数据表
        $arr["parent_ids_arr"] = array(1=>"p_id",2=>"id");
        $arr["tpl_zengjia"]  = $GLOBALS['language']['TPL_XIUGAI_STR'];
        $arr["TBL_def"] = $TBL_def;
        $arr["FLD_def"] = $FLD_def;
        $arr["html_title"] = $GLOBALS['language']['TPL_BIANJI_STR'].$GLOBALS['language']['TPL_MOBAN_STR'];
        $arr["html_name"] = $p_self_info["p_def"]["name_cn"].$arr["html_title"];
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

        $table_name = $TBL_def;

        $this->Init($request, $arr);  // 需要初始化一下

        $arr = array_merge($arr, $p_self_info);
        parent::getFieldsInfo($arr);
        if(!array_key_exists("f_info",$arr)) {
          $response['ret'] = array('ret'=>1,'msg'=>"the f_info not exist!");
          return null;
        }

        $dbR = $arr["dbR"];
        $dbR->table_name = $arr["table_name"];
        $l_rlt = $dbR->getOne(" where id=".$request["id"]);  // 获取数据库中当前数据
        if (empty($l_rlt)) {
          // 漏洞:如果攻击者使用一个不存在的id，则$p_arr返回的是NULL????其他类似漏洞有时间的时候全部处理一下。
          $response['html_content'] = date("Y-m-d H:i:s") . "template not exist! id:".$request["id"];
      $response['ret'] = array('ret'=>1);
          return null;
        }

        // 需要获取到必须填写的一个字段, 依据数据库结构进行判断
        $l_bixuziduanform = DbHelper::getBiXuFields($arr["dbR"], array("table_name"=>$table_name, "f_info"=>$arr["f_info"]));
        if (empty($form) || empty($form[$l_bixuziduanform[0]])) {

          // 要格式化成"value"，"hidden"附加属性的数组
          $a_over = array("p_id"=>array("value"=>$request["p_id"],"hidden"=>1));  // 变为value即可
          $l_self_arr = FmtDataAddAtr($l_rlt, $a_over);
          $arr["default_over"] = $l_self_arr;  // 作为参数传递过来的, 最好是隐藏的，并且列出中文名

          // 先将字段定义的算法进行必要的解析并填充到$arr数组的字段信息f_info中去
          Parse_Arithmetic::parse_for_list_form($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

          $l_resp = parent::executeListForm($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

      $response['html_content'] = $l_resp;
      $response['ret'] = array('ret'=>0);
      return null;  // 总是返回此结果
        }else {
          // 索引的二维数组转变成按照某个字段唯一标识的二维数组
          //$n_finfo = cArray::Index2KeyArr($f_info,array("key"=>"name_eng","value"=>array()));// 默认表名不是想要的

          // 先将字段定义的算法进行必要的解析并填充到$arr数组的字段信息f_info中去
          Parse_Arithmetic::parse_for_list_form($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

           // 拼装更新字段数据
           $data_arr = DbHelper::getInsertArrByFormFieldInfo($form, $arr["f_info"],true,true);
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

           $dbW = new DBW($arr["p_def"]);
           $l_err = $dbW->errorInfo();
           if ($l_err[1]>0){
             // 数据库连接失败后
             $response['html_content'] = date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
             $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
             return null;
           }
           // 检查表名是否修改，如果表名称修改了，则需要先改表名，然后进行其他操作
           if (!empty($data_arr["name_eng"]) && $data_arr["name_eng"] != $l_rlt["name_eng"]) {
             $data_arr["name_eng"] = trim( str_replace("`", "",$data_arr["name_eng"]) );
             if (""!=$data_arr["name_eng"]) {
               $dbW->rename_table($l_rlt["name_eng"],$data_arr["name_eng"]);
               $l_err = $dbW->errorInfo();
               if ($l_err[1]>0){
                 $response['html_content'] = date("Y-m-d H:i:s") . var_export($l_err, true). " 修改表名error,sql: ". $dbW->getSQL() .NEW_LINE_CHAR;
                 $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
                 return null;
               }else {
                 // 修改成功以后才会进行后续操作
               }
             }else {
               $data_arr["name_eng"] = $l_rlt["name_eng"];  // 将不会修改表结构
             }
           }

           $dbW->table_name = $table_name;  // 表定义表
           $conditon = " id = ".$request["id"]." ";
           cArray::delSameValue($data_arr,$l_rlt);  // 剔除掉没有修改的数据项
           $dbW->updateOne($data_arr, $conditon);
           $l_err = $dbW->errorInfo();
           if($l_err[1] <=0){
             // 修改成功(或未修改)以后，需要对定义的各种任务需要一一完成(即执行相应的算法)
             Parse_Arithmetic::do_arithmetic_by_add_action($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

             if ('del'==$form['status_']) {
               $response['ret'] = array('ret'=>0);
               return "main.php?do=".$this->type_name."_list".$arr["parent_rela"]["parent_ids_url_build_query"];  // 删除处理直接返回到列表页面
             }
             $response['html_content'] = date("Y-m-d H:i:s") . "<br />修改的字段:". var_export(array_keys($data_arr),true) . "<br /> 成功修改信息, <a href='?do=".$this->type_name."_list".$arr["parent_rela"]["parent_ids_url_build_query"]."'>返回列表页面</a> ";
        $response['ret'] = array('ret'=>0);
             return null;  // 总是返回此结果
          }else {
            $response['html_content'] = date("Y-m-d H:i:s") . $dbW->getSQL() . " 更新数据出错, <a href='?do=".$this->type_name."_edit".$arr["parent_rela"]["parent_ids_url_build_query"]."'>重新编辑</a> ";
        $response['ret'] = array('ret'=>0);
            return null;  // 总是返回此结果
          }
        }
    }
}
