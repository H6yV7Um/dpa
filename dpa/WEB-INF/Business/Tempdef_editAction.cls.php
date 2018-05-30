<?php
/**
 * Tempdef_editAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/Log.php");
require_once("common/lib/dbhelper.php");
require_once("common/lib/Parse_Arithmetic.php");
require_once('mvc/AddAction.cls.php');
require_once('mod/DBR.cls.php');
require_once('mod/DBW.cls.php');
// from phpMyAdmin_2119\libraries\Table.class.php ， 用于修改表结构的一些方法。
require_once('common/lib/PMA_common.lib.php');
require_once('common/lib/PMA_Table.class.php');

class Tempdef_editAction extends AddAction {
    /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
    function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

        // 对什么表进行操作，需要依据参数而定
        // 模板定义表增加的时候其实需要修改表结构，同时在字段定义表中需要增加记录
        // 找到父级元素, 有两级父级
        $p_id = $request["p_id"];  // 第一个父级id, 也是project id。
        $dbR = new DBR();      // 系统默认数据库连接信息，开始都从这个入口
        $l_err = $dbR->errorInfo();
        if ($l_err[1]>0){
          // 数据库连接失败后
          $response['html_content'] = date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
          $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
          return null;
        }

        $a_p_self_ids = array(
          1=>array("ziduan"=>"p_id"),
          2=>array("ziduan"=>"t_id"),
        );
        // 获取到前两级的数据数组, 此处的dbr会修改数据库连接信息。
        $p_self_info = DbHelper::getProTblFldArr($dbR, $request, $a_p_self_ids);
        $p_self_info["f_info_all"] = array_merge($p_self_info["f_info"],$p_self_info["f_def_duo"],$p_self_info["f_def_stop"]);//后面需要用到


        $TBL_def = TABLENAME_PREF."table_def";
        $FLD_def = TABLENAME_PREF."field_def";

        // 依据t_id获取到数据表名称等信息, 需要从表定义表中获取其信息
        $dbR->table_name = $TBL_def;
        $t_arr = $dbR->getOne(" where id = ".$request["t_id"]);

        $dbR->table_name = $table_name = $FLD_def;

        $arr = array();
        $arr["dbR"] = $dbR;
        $arr["table_name"] = $table_name;  // 执行插入操作的数据表
        $arr["parent_ids_arr"] = array(1=>"p_id", 2=>"t_id",3=>"id");//,2=>"id"可有可无，编辑的时候一定要有
        $arr["tpl_zengjia"]  = $GLOBALS['language']['TPL_XIUGAI_STR'];
        $arr["TBL_def"] = $TBL_def;
        $arr["FLD_def"] = $FLD_def;
        $arr["html_title"] = $GLOBALS['language']['TPL_BIANJI_STR'].$GLOBALS['language']['TPL_MOBAN_STR'].$GLOBALS['language']['TPL_YU_STR'];
        $arr["html_name"] = $p_self_info["t_def"]["name_cn"].$arr["html_title"];
        $arr["a_options"] = array(
         "nav"=>array(
           "p_id"=>array(
             "script_name"=>"main.php", // 可有可无
             "do"   =>"project_list",
             "value"=>$request["p_id"],
             "name_cn"=>$GLOBALS['language']['TPL_XIANGMU_STR'].$GLOBALS['language']['TPL_LIEBIAO_STR'],
           ),
           "t_id"=>array(
             "script_name"=>"main.php", // 可有可无
             "do"   =>"tempdef_list",
             "value"=>$request["t_id"],
             "name_cn"=>$GLOBALS['language']['TPL_MOBAN_STR'].$GLOBALS['language']['TPL_DINGYI_STR'].$GLOBALS['language']['TPL_BIAO_STR'],
           )
         )
       );

        $this->Init($request, $arr);  // 需要初始化一下

        $arr = array_merge($arr, $p_self_info);
        parent::getFieldsInfo($arr);  // 实际上是用表定义表、字段定义表中的数据填充$arr的f_info
        if(!array_key_exists("f_info",$arr)) {
          $response['ret'] = array('ret'=>1,'msg'=>"the f_info not exist!");
          return null;
        }

        $l_bixuziduanform = DbHelper::getBiXuFields($arr["dbR"], array("table_name"=>$table_name, "f_info"=>$arr["f_info"]));

        $dbR->table_name = $table_name;
        $l_rlt = $dbR->getOne(" where id=".$request["id"]);  // 获取数据库中当前id数据
        $arr["f_data"] = $l_rlt;  // 当前数据注册进来

        if (empty($form) || empty($form[$l_bixuziduanform[0]])) {
          // 要格式化成"value"，"hidden"附加属性的数组
          $a_over = array("p_id"=>array("value"=>$request["p_id"],"hidden"=>1),
                  "t_id"=>array("value"=>$request["t_id"],"hidden"=>1));
          $arr["default_over"] = FmtDataAddAtr($l_rlt, $a_over);
          // 先将字段定义的算法进行必要的解析并填充到$arr数组的字段信息f_info中去
          Parse_Arithmetic::parse_for_list_form($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

          $l_resp = parent::executeListForm($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

      $response['html_content'] = $l_resp;
      $response['ret'] = array('ret'=>0);
      return null;  // 总是返回此结果
        }else {

           //有两项任务: 1)需要修改表结构， 2)并且在字段定义表中更新该记录。

           // 暂时不支持算法类型从真实数据移出的字段修改
           $l_temptype = cArray::getTempTypeCNnameArr();
           if ( 0===$l_temptype[$request["f_type"]]["if_into_db"] && 1===$l_temptype[$l_rlt["f_type"]]["if_into_db"] ) {
             $response['html_content'] = date("Y-m-d H:i:s") . " can not change f_type,please stop and re-add them! old name_eng:".$l_rlt["name_eng"] . ", new:".$form["name_eng"] ."old f_type:".$l_rlt["f_type"] . ", new:".$form["f_type"];
             $response['ret'] = array('ret'=>1);
             return null;
           }

           $dbW = new DBW($arr["p_def"]);

           $form["name_eng_old"] = $l_rlt["name_eng"]; // 需要在数组中注册上旧字段英文名

           // 如果字段算法类型为Application::SQLResult等以Application::开头的则不能修改表结构，只在字段定义表中添加一条记录即可
           if (1===$l_temptype[$request["f_type"]]["if_into_db"]) {
             // 先对照表结构是否有修改，没有修改则跳过此步骤；否则需要执行。就是对照提交的数据同旧数据的不同
             if (array_key_exists($l_rlt["name_eng"],$p_self_info["f_info_all"]) &&
                 $l_rlt["name_eng"]==$form["name_eng"] && $l_rlt["is_null"]==$form["is_null"] &&
                 $l_rlt["key"]==$form["key"] && $l_rlt["extra"]==$form["extra"] &&
                 $l_rlt["type"]==$form["type"] && $l_rlt["length"]==$form["length"] &&
                 $l_rlt["attribute"]==$form["attribute"] && $l_rlt["default"]==$form["default"]) {
               // 当字段名称相同，而七个属性也相同的时候，就表示不需要修改表结构
             }else {

               // 此处对于实际数据表中不存在的字段应当执行添加字段
               if (!array_key_exists($l_rlt["name_eng"],$p_self_info["f_info_all"])) {
                 $l_act = "ADD";
                 unset($form["name_eng_old"]);  // 必须去掉，否则会多一个字段名称在语句中
               }else {
                 // 修改表结构，同时会被修改到定义表中去
                 $l_act = "CHANGE";
               }

               // 1) 修改表结构
               $dbW->table_name = $t_arr["name_eng"];  // 需要修改表结构的表依据t_id获取
               $duoziduan = array($request["name_eng"]);  // 每次只修改一个字段
               $dbW->alter_table($duoziduan, array($request["name_eng"]=>$form), $l_act);  // 借助phpmyadmin并放到dbhelper进行封装

               $l_err = $dbW->errorInfo();
               if ($l_err[1]>0){
                 // sql有错误，后面的就不用执行了。
                 Log::Debug( " FILE: ".__FILE__." ". " FUNCTION: ".__FUNCTION__." Line: ". __LINE__ . " ". $dbW->getSQL() ." ". var_export($l_err,true));
                 $response['html_content'] = date("Y-m-d H:i:s") . var_export($l_err, true). ". SQL:".$dbW->getSQL() . " alter table err!!!!";
                 $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
                 return null;
               }
             }
           }

           // 先将字段定义的算法进行必要的解析并填充到$arr数组的字段信息f_info中去
          Parse_Arithmetic::parse_for_list_form($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

           // 2)在字段定义表中更新记录
           $data_arr = DbHelper::getInsertArrByFormFieldInfo($form, $arr["f_info"],true,true);
           // 更新字段，不必需要必须的字段，任何字段都可以更新，只要不为空，对于空的则先不用更新空数据
           // 所以直接使用 getInsertArrByFormFieldInfo这个类似插入数据一样的获取数据的方法一样。
           if (array_key_exists("___ERR___", $data_arr)) {
             unset($data_arr["___ERR___"]);
           }
           // 自动填充几个数据，修改者、时间的字段 if (!array_key_exists("mender", $data_arr))
           if (array_key_exists("creator", $data_arr)) $data_arr["creator"] = ("0"==$data_arr["creator"]) ? $_SESSION["user"]["username"] : $data_arr["creator"];
           if (array_key_exists("createdate", $data_arr)) $data_arr["createdate"] = ("0000-00-00"==$data_arr["createdate"]) ? date("Y-m-d") : $data_arr["createdate"];
           if (array_key_exists("createtime", $data_arr)) $data_arr["createtime"] = ("00:00:00"==$data_arr["createtime"])   ? date("H:i:s") : $data_arr["createtime"];
           if (array_key_exists("mender",   $arr["f_info"])) $data_arr["mender"]   = $_SESSION["user"]["username"];
           if (array_key_exists("menddate", $arr["f_info"])) $data_arr["menddate"] = date("Y-m-d");
           if (array_key_exists("mendtime", $arr["f_info"])) $data_arr["mendtime"] = date("H:i:s");

           $dbW->table_name = $table_name;  // 字段定义表
           $conditon = " id = ".$request["id"]." ";
           cArray::delSameValue($data_arr,$arr["f_data"]);  // 剔除掉没有修改的数据项，简洁
           $dbW->updateOne($data_arr, $conditon);
           $l_err = $dbW->errorInfo();
           if($l_err[1]>0){
             $l_err = $dbW->errorInfo();
            $response['html_content'] = date("Y-m-d H:i:s") . var_export($l_err,true). ". SQL:".$dbW->getSQL() . " 更新数据出错, <a href='?do=".$this->type_name."_edit".$arr["parent_rela"]["parent_ids_url_build_query"]."'>重新编辑</a> ";
        $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
            return null;  // 总是返回此结果
          }else {
            if ('del'==$form['status_']) return "main.php?do=".$this->type_name."_list".$arr["parent_rela"]["parent_ids_url_build_query"];  // 删除处理直接返回到列表页面
            // 修改成功(或未修改)以后，需要对定义的各种任务需要一一完成(即执行相应的算法)
            Parse_Arithmetic::do_arithmetic_by_add_action($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

            $response['html_content'] = date("Y-m-d H:i:s") . "<br />修改的字段:". var_export(array_keys($data_arr),true) . "<br /> 成功修改信息, <a href='?do=".$this->type_name."_list".$arr["parent_rela"]["parent_ids_url_build_query"]."'>返回列表页面</a> ";
        $response['ret'] = array('ret'=>0);
            return null;  // 总是返回此结果
          }
        }
    }
}
