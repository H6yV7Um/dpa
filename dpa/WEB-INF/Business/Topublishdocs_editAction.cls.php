<?php
/**
 * Topublishdocs_editAction.cls.php 只能在命令行向运行，示例:
 *   D:/php/php D:/www/dpa/main.php -g "do=topublishdocs_edit&p_id=4&t_id=9&id=3"
 *
 *   频道首页后台发布
 *   D:/php/php D:/www/dpa/main.php -g "do=topublishdocs_edit&p_id=15&t_id=3&id=1"
 *
 *   php /data0/htdocs/admin/dpa/main.php -g 'do=topublishdocs_edit&p_id=5&t_id=3&id=1' > /dev/null
 *   php /data0/htdocs/admin/dpa/main.php -g 'do=topublishdocs_edit&p_id=8&t_id=15&publish_type=biaoji' > /dev/null
 *   php /data0/htdocs/admin/dpa/main.php -g 'do=topublishdocs_edit&p_id=10&t_id=16&publish_type=biaoji' > /dev/null
 *
 * 功能：主要是对表的元素进行发布成静态文件。当然需要提前提供发布模板，否则无法完成发布工作
 * 另：也提供对整张表的发布----上线以后由于表数据大因此会关闭。理论上也支持对整个数据库的所有的表进行发布的功能----但只对部分人员开放权限
 *   D:/php/php D:/www/dpa/main.php -g "do=topublishdocs_edit&p_id=19&t_id=15&publish_type=biaoji"
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/lib/dbhelper.php");
require_once("common/lib/Parse_Arithmetic.php");
require_once("common/lib/Publish.cls.php");
require_once('mvc/AddAction.cls.php');

class Topublishdocs_editAction extends AddAction {
    /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
    function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

        // 只允许cli后台运行，主要是完成一些任务。输出调试信息等  web测试的时候有&&&&安全隐患&&&&
        if (!array_key_exists("id",$request) && php_sapi_name()!='cli') {
          $response['html_content'] = date("Y-m-d H:i:s") . "this must be run command line module!!";
      $response['ret'] = array('ret'=>1);
          return null;
        }

        // 参数检验可以放到validate中去，先放到此处检验一下。
        if (!array_key_exists("p_id",$request)) {
          $response['html_content'] = date("Y-m-d H:i:s") . " p_id must not be empty!! ". NEW_LINE_CHAR;
      $response['ret'] = array('ret'=>1);
          return null;  // 总是返回此结果
        }
        if (!array_key_exists("t_id",$request)) {
          $response['html_content'] = date("Y-m-d H:i:s") . " t_id must not be empty!! ". NEW_LINE_CHAR;
      $response['ret'] = array('ret'=>1);
          return null;  // 总是返回此结果
        }
        // 为了支持整张表的发布，也可不必提供具体id，不过在请求的时候需要注明是什么级别的请求
        // 然后调用获取整张表的所有id循环执行: exec('php main.php -g "do=topublishdocs_edit&p_id=4&t_id=9&id=3" ');
        // 当前只支持单个id的发布工作。
        if (array_key_exists('publish_type',$request) && 'biaoji'==$request['publish_type']) {
          // 获取表的所有记录, 然后逐一拼装shell command
          $l_name0_r = $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R'];
          $dbR = new DBR($l_name0_r);
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
          $p_self_info = DbHelper::getProTblFldArr($dbR, $request, $a_p_self_ids);
          // 获取当前需要操作的表名
          if(array_key_exists("t_def", $p_self_info) && array_key_exists("name_eng", $p_self_info["t_def"]) && !empty($p_self_info["t_def"]["name_eng"])){
            $table_name = $dbR->table_name = $p_self_info["t_def"]["name_eng"];
          }else {
            $response['html_content'] = date("Y-m-d H:i:s") . "err!!!!";
            $response['ret'] = array('ret'=>1);
            return null;
          }
          $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_self_info["p_def"]);$dbR->dbo = &DBO('', $dsn);
          //$dbR = null;$dbR = new DBR($p_self_info["p_def"]);  // 连接到对应数据库中上
          $dbR->table_name = $table_name;  // 所指向的表
          $l_rlt = $dbR->getAlls("where status_='use'",'id');

          if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
            $l_cmd_pre = 'D:/php/php D:/www/dpa/main.php';
          }else {
            $l_cmd_pre = 'php /data0/htdocs/admin/dpa/main.php';
          }
          foreach ($l_rlt as $l_row){
            $l_cmd = $l_cmd_pre . ' -g "do=topublishdocs_edit&p_id='.$request['p_id'].'&t_id='.$request['t_id'].'&id='.$l_row['id'].'" ';
            // echo $l_cmd. NEW_LINE_CHAR;
            exec($l_cmd);
            sleep(1);
          }
          $response['html_content'] = date("Y-m-d H:i:s") . " biao ji fa bu wan cheng! ". NEW_LINE_CHAR;
          $response['ret'] = array('ret'=>0);
          return null;
        }else {
          if (!array_key_exists("id",$request)) {
            $response['html_content'] = date("Y-m-d H:i:s") . " id must not be empty!! ". NEW_LINE_CHAR;
        $response['ret'] = array('ret'=>1);
            return null;  // 总是返回此结果
          }
        }
        // 获取到前几级的数据数组，包括表定义表和字段定义表等围绕目标项的直系亲属
        // 父级、祖父级或更高。p_def
        $l_name0_r = $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R'];
        $dbR = new DBR($l_name0_r);
        //$dbR = null;$dbR = new DBR();
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
        $p_self_info = DbHelper::getProTblFldArr($dbR, $request, $a_p_self_ids);

        // 获取当前需要操作的表名
        if(array_key_exists("t_def", $p_self_info) && array_key_exists("name_eng", $p_self_info["t_def"]) && !empty($p_self_info["t_def"]["name_eng"])){
          $table_name = $dbR->table_name = $p_self_info["t_def"]["name_eng"];
        }else {
          $response['ret'] = array('ret'=>1,'msg'=>date("Y-m-d H:i:s") . " err!!!!");
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

        $arr = array_merge($arr, $p_self_info);
        if(!array_key_exists("f_info",$arr) || !array_key_exists("f_data",$arr)) {
          $response['ret'] = array('ret'=>1,'msg'=>"the f_info not exist!");
          return null;
        }

        // 此步骤不可缺少, 否则无法发布，填充之前需要将字段的各个算法执行一下，先将字段定义的算法进行必要的解析并填充到$arr数组的字段信息f_info中去, 便于修正字段的相关限制和取值范围
        Parse_Arithmetic::parse_for_list_form($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

        // 然后结合填充好的f_info完善到f_data 中去。
        // 第三个参数一定要设置为true, true表示需要携带该字段哪怕值为空的。表单提交的数据，因有算法的字段已经被填充上了，不存在为空的情况;后台发布则可能为空需要携带该字段
        $data_arr = DbHelper::getInsertArrByFormFieldInfo($arr['f_data'], $arr["f_info"], true);
        if (array_key_exists("___ERR___", $data_arr)) {
          $response['html_content'] = date("Y-m-d H:i:s") . " field empty: ". var_export($data_arr["___ERR___"], TRUE);
          $response['ret'] = array('ret'=>1);
          return null;
        }

        // 模拟表提交动作
        if (empty($form)) {
          $form = array_merge($arr['f_data'],$data_arr);  // $data_arr携带了字段算法计算完成的数据
          $request = array_merge($request,$form);  // 为了一致性，cli模式下$request数组需人工变
        }

        // 每个模板可能有其他算法
        $l_arith = array();
        if (isset($arr["t_def"]['arithmetic']) && !empty($arr["t_def"]['arithmetic'])) {
      $l_arith = Parse_Arithmetic::parse_like_ini_file($arr["t_def"]['arithmetic']); // 首先将算法解析为一维数组
        }

        if (!empty($form)) {
          // ---- 可有可无步骤 begin-----  如果没有此步骤则原来数据将不做任何修改，包括修改者、修改时间、外网地址变量需要替换的也不会被替换，但文档依然会被正确地发布出去，所以说可有可无
           // 自动填充几个数据，修改者、时间的字段 if (!array_key_exists("mender", $data_arr))
           if (array_key_exists("creator", $data_arr)) $data_arr["creator"] = ("0"==$data_arr["creator"]) ? $_SESSION["user"]["username"] : $data_arr["creator"];
           if (array_key_exists("createdate", $data_arr)) $data_arr["createdate"] = ("0000-00-00"==$data_arr["createdate"]) ? date("Y-m-d") : $data_arr["createdate"];
           if (array_key_exists("createtime", $data_arr)) $data_arr["createtime"] = ("00:00:00"==$data_arr["createtime"])   ? date("H:i:s") : $data_arr["createtime"];
           if (array_key_exists("mender", $data_arr)) $data_arr["mender"] = $_SESSION["user"]["username"];
           if (array_key_exists("menddate", $data_arr)) $data_arr["menddate"] = date("Y-m-d");
           if (array_key_exists("mendtime", $data_arr)) $data_arr["mendtime"] = date("H:i:s");

           $dbW = new DBW($p_self_info["p_def"]);
           $dbW->table_name = $table_name;  // 当前需要操作的表名由t_id,t_def获取到
           $conditon = " id = ".$request["id"]." ";
           cArray::delSameValue($data_arr,$arr["f_data"]);
           if (!empty($data_arr)) {
             $l_rlt = $dbW->updateOne($data_arr, $conditon);
             $l_err = $dbW->errorInfo();
             if ($l_err[1]>0){
               // 增加失败后
               $response['html_content'] =date("Y-m-d H:i:s") .  var_export($l_err, true). " 更新数据发生错误,sql: ". $dbW->getSQL() . " <a href='?do=".$this->type_name."_edit".$arr["parent_rela"]["parent_ids_url_build_query"]."'>重新编辑</a> ";
               $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
               return null;
             }
             $arr['f_data'] = array_merge($arr["f_data"],$data_arr);  // 最终完整结果数据
           }
           // ---- 可有可无步骤 end-----

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
             if (!isset($dbW)) $dbW = new DBW($p_self_info["p_def"]);
             $arr["dbW"] = $dbW;

             // 需要进行文档发布
             $l_data_arr = array_merge($form,$data_arr);
             // 可能有不同平台的模板, 例如pc、iphone、android、ipad不同平台上
             foreach ($l_tmpl as $l_tmpl_one){
               // 每个模板可能有多个分页. 在算法中进行分页发布
               if (array_key_exists('publish', $l_arith)) {
                 $l_func = preg_replace('/\W/',"_",basename(__FILE__) . "_publish_" .$arr['p_def']["id"] . "_" .$arr['t_def']["id"]."_".$arr['f_data']["id"]."_". utime());
                 $l_func_str = pinzhuangFunctionStr(array('code'=>$l_arith['publish']), $l_func, '&$arr,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie,$l_data_arr,$l_tmpl_one');
                 if (!function_exists($l_func)) eval($l_func_str);
                 $l_func($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie,$l_data_arr,$l_tmpl_one);
               } else
               Publish::toPublishing($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie,$l_data_arr,$l_tmpl_one);
             }

             $response['html_content'] = date("Y-m-d H:i:s") . " publish succ! <a href='?do=document_list".$arr["parent_rela"]["parent_ids_url_build_query"]."'>back to list</a> ".NEW_LINE_CHAR;
             $response['ret'] = array('ret'=>0);
             return null;  // 总是返回此结果
           }else {
             $response['html_content'] = date("Y-m-d H:i:s") . " no template_design! <a href='?do=document_list".$arr["parent_rela"]["parent_ids_url_build_query"]."'>back to list</a> ".NEW_LINE_CHAR;
             $response['ret'] = array('ret'=>1);
             return null;  // 总是返回此结果
           }
        }
    }
}
