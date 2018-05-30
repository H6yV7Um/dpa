<?php
/**
 * Document_editAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/lib/dbhelper.php");
require_once("common/lib/Parse_Arithmetic.php");
require_once("common/lib/Publish.cls.php");
require_once('mvc/AddAction.cls.php');

class Document_editAction extends AddAction {
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
        $a_p_self_ids = array(
          1=>array("ziduan"=>"p_id"),
          2=>array("ziduan"=>"t_id"),
          3=>array("ziduan"=>"id"),
        );
        // 获取到前几级的数据数组，包括表定义表和字段定义表等围绕目标项的直系亲属
        // 父级、祖父级或更高。p_def
        $p_self_info = DbHelper::getProTblFldArr($dbR, $request, $a_p_self_ids);

        // 获取当前需要操作的表名
        if(array_key_exists("t_def", $p_self_info) && array_key_exists("name_eng", $p_self_info["t_def"]) && !empty($p_self_info["t_def"]["name_eng"])){
          $table_name = $dbR->table_name = $p_self_info["t_def"]["name_eng"];
        }else {
          $response['ret'] = array('ret'=>1,'msg'=>"err!!!!");
          return null;
        }

        $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_self_info["p_def"]);$dbR->dbo = &DBO('', $dsn);
        //$dbR = null;$dbR = new DBR($p_self_info["p_def"]);  // 连接到相关数据库中去，如果有多级则需要循环进行直到找到对应的数据库和表
        // 应该自动获取表定义表和字段定义表,此处省略并人为指定????
        $TBL_def = TABLENAME_PREF."table_def";
        $FLD_def = TABLENAME_PREF."field_def";

        $arr = array();
        $arr["dbR"] = $dbR;
        $arr["table_name"] = $TBL_def;  // 执行插入操作的数据表
        $arr["parent_ids_arr"] = array(1=>"p_id",2=>"t_id",3=>"id");//,2=>"id"可有可无，编辑的时候一定要有
        $arr["tpl_zengjia"]  = $GLOBALS['language']['TPL_XIUGAI_STR'];
        $arr["TBL_def"] = $TBL_def;
        $arr["FLD_def"] = $FLD_def;
        $arr["html_title"] = $GLOBALS['language']['TPL_BIANJI_STR'].$GLOBALS['language']['TPL_WENDANG_STR'];
        $arr["html_name"] = $arr["html_title"];
        $arr["a_options"] = array(
         "nav"=>array(
           "p_id"=>array(
             "script_name"=>"main.php", // 可有可无
             "do"   =>"project_list",
             "value"=>$request["p_id"],
             "name_cn"=>$GLOBALS['language']['TPL_XIANGMU_STR'].$GLOBALS['language']['TPL_LIEBIAO_STR'],
           ),
           "t_id"=>array(
             "do"   =>"template_list",
             "value"=>$request["t_id"],
             "name_cn"=>$GLOBALS['language']['TPL_MOBAN_STR'].$GLOBALS['language']['TPL_LIEBIAO_STR'],
           )
         )
       );

       $this->Init($request, $arr);  // 需要初始化一下
       //
        $arr = array_merge($arr, $p_self_info);
        if(!array_key_exists("f_info",$arr)) {
          $response['ret'] = array('ret'=>1,'msg'=>"the f_info not exist!");
          return null;
        }

        // 每个模板可能有其他算法
        $l_arith = array();
        if (isset($arr["t_def"]['arithmetic']) && !empty($arr["t_def"]['arithmetic'])) {
        $l_arith = Parse_Arithmetic::parse_like_ini_file($arr["t_def"]['arithmetic']); // 首先将算法解析为一维数组
        }
        if (empty($form)) {
          // 作为上级目录, 暂不提供修改权限，因此隐藏，但列出中文名
          // 要格式化成"value"，"hidden"附加属性的数组
          $a_over = array(
              "p_id"=>array("value"=>$request["p_id"],"hidden"=>1),
              "t_id"=>array("value"=>$request["t_id"],"hidden"=>1)
          );  // 变为value即可

          $l_self_arr = FmtDataAddAtr($p_self_info["f_data"], $a_over);

          $arr["default_over"] = $l_self_arr;  // 作为参数传递过来的, 最好是隐藏的，并且列出中文名

          // 先将字段定义的算法进行必要的解析并填充到$arr数组的字段信息f_info中去
          Parse_Arithmetic::parse_for_list_form($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);
          $arr["no_need_field"] = array("mender","menddate","mendtime");
          $l_resp = parent::executeListForm($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

          $response['html_content'] = $l_resp;
          $response['ret'] = array('ret'=>0);
          return null;  // 总是返回此结果
        }else {
          // 同表单呈现一样，填充之前需要将字段的各个算法执行一下，便于修正字段的相关限制和取值范围
          Parse_Arithmetic::parse_for_list_form($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

           // 依据字段定义表，填充数据
           $data_arr = DbHelper::getInsertArrByFormFieldInfo($form, $arr["f_info"],true,true,$files);
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

           $dbW = new DBW($p_self_info["p_def"]);
           $dbW->table_name = $table_name;  // 当前需要操作的表名由t_id,t_def获取到
           $conditon = " id = ".$request["id"]." ";


          // 可能是增加计数(减少就是增加一个负值即可)
          if (isset($request['__if_add_count__']) && $request['__if_add_count__']
            && isset($request['add_field']) && trim($request['add_field'])
            && isset($request['add_type'])) {
            $data_arr = array(trim($request['add_field']) => $request['add_type'] + 0); // 目前支持单一字段计数修改，只增加计数不修改时间和修改者
            $l_rlt = $dbW->updateOne($data_arr, $conditon, $request['__if_add_count__']); // 参数3表示仅仅增加计数 // echo $dbW->getSQL();
            $l_err = $dbW->errorInfo();
            if ($l_err[1]>0){
              // 增加失败后
              $response['html_content'] = date("Y-m-d H:i:s") . var_export($l_err, true). " 更新数据发生错误,sql: ". $dbW->getSQL() . " <a href='?do=".$this->type_name."_edit".$arr["parent_rela"]["parent_ids_url_build_query"]."'>重新编辑</a> ";
              $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
              return null;
            }
            // 增加计数成功后
            $response['html_content'] = date("Y-m-d H:i:s") . "<br />修改的字段:". var_export(array_keys($data_arr),true) . "<br /> 成功修改信息, <a href='?do=".$this->type_name."_list".$arr["parent_rela"]["parent_ids_url_build_query"]."'>返回列表页面</a> ".NEW_LINE_CHAR;
            $response['ret'] = array('ret'=>0);
            return null;  // 总是返回此结果
          }

          // 修改数据
           cArray::delSameValue($data_arr,$arr["f_data"]);
           if (!empty($data_arr)) {
             $l_rlt = $dbW->updateOne($data_arr, $conditon);
             $l_err = $dbW->errorInfo();
             if ($l_err[1]>0){
               // 增加失败后
               $response['html_content'] = date("Y-m-d H:i:s") . var_export($l_err, true). " 更新数据发生错误,sql: ". $dbW->getSQL() . " <a href='?do=".$this->type_name."_edit".$arr["parent_rela"]["parent_ids_url_build_query"]."'>重新编辑</a> ";
               $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
               return null;
             }
             $arr['f_data'] = array_merge($arr["f_data"],$data_arr);  // 最终完整结果数据
           }
           // 修改成功(或未修改)以后，需要对定义的各种任务需要一一完成(即执行相应的算法)
           Parse_Arithmetic::do_arithmetic_by_add_action($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

           Parse_Arithmetic::Int_FillALL($arr, $response, $request);  // 变量注册、替换等

           // 还需要发布本页，即生成静态文件存放在相应路径下并进行同步分发出去
           // 获取url、模板等数据, 要是模板不存在也得判断一下, 类型的数据库可能没有模板设计表
           if (isset($arr["t_def"]["tmpl_design"])) {
             $l_tmpl = $arr["t_def"]["tmpl_design"];
           }else {
             $l_tmpl = array();
           }
           if (!empty($l_tmpl)) {
             $arr["dbW"] = $dbW;

             // 需要进行文档发布
             $l_data_arr = array_merge($request,$data_arr);
             // 可能有不同平台的模板, 例如pc、iphone、android、ipad不同平台上
             foreach ($l_tmpl as $l_tmpl_one){
               // 每个模板可能有多个分页. 在算法中进行分页发布
               // 如果是删除，则需要将静态文件也一同删除掉
               if ('del'==$form['status_']) $l_if_delete = true;
               else $l_if_delete = false;

               // 其他参数均注册到此数组中去
               $l_other_arr = array(
                 'if_delete' => $l_if_delete
               );

               if (array_key_exists('publish', $l_arith)) {
                 // 娱乐频道和it技术的“频道首页”均采用翻页，就用到了此处发布的方法, 其他频道通常么有用到这样的做法
                 $l_func = preg_replace('/\W/',"_",basename(__FILE__)) . "_publish_" .$arr['p_def']["id"] . "_" .$arr['t_def']["id"]."_".$arr['f_data']["id"]."_". time();
                 $l_func_str = pinzhuangFunctionStr(array('code'=>$l_arith['publish']), $l_func, '&$arr,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie,$l_data_arr,$l_tmpl_one,$a_other_arr');
                 eval($l_func_str);
                 $l_func($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie,$l_data_arr,$l_tmpl_one,$l_other_arr);
               } else {
                 Publish::toPublishing($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie,$l_data_arr,$l_tmpl_one,'',$l_if_delete);
               }
             }
             // 删除处理直接返回到列表页面
             if ('del'==$form['status_']) {
               $response['ret'] = array('ret'=>0);
               return "main.php?do=".$this->type_name."_list".$arr["parent_rela"]["parent_ids_url_build_query"];
             }
             if (empty($data_arr)) {
               $l_html = "<br />未修改任何数据";
             }else {
               $l_html = "<br />修改的字段:". var_export(array_keys($data_arr),true) ;
             }
             $response['html_content'] = date("Y-m-d H:i:s") . $l_html . "<br /> 成功发布并保存了信息, <a href='?do=".$this->type_name."_list".$arr["parent_rela"]["parent_ids_url_build_query"]."'>返回列表页面</a> ".NEW_LINE_CHAR;
             $response['ret'] = array('ret'=>0);
             return null;  // 总是返回此结果
           }else {
             // 成功就跳转到列表页面
             $response['html_content'] = date("Y-m-d H:i:s") . "<br />修改的字段:". var_export(array_keys($data_arr),true) . "<br /> 成功修改信息, <a href='?do=".$this->type_name."_list".$arr["parent_rela"]["parent_ids_url_build_query"]."'>返回列表页面</a> ".NEW_LINE_CHAR;
             $response['ret'] = array('ret'=>0);
             return null;  // 总是返回此结果
           }
        }
    }
}
