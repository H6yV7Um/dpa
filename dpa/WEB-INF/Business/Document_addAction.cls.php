<?php
/**
 * Document_addAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/lib/dbhelper.php");
require_once("common/lib/Parse_Arithmetic.php");
require_once("common/lib/Publish.cls.php");
require_once('mvc/AddAction.cls.php');
require_once('mod/DBR.cls.php');
require_once('mod/DBW.cls.php');

class Document_addAction extends AddAction {
    /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
    function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

        // 配置其父级、自身级别字段列表。
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
        );
        // 获取到前几级的数据数组，包括表定义表和字段定义表等围绕目标项的直系亲属
        // 父级、祖父级或更高。p_def
        $p_self_info = DbHelper::getProTblFldArr($dbR, $request, $a_p_self_ids);

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
        $arr["parent_ids_arr"] = array(1=>"p_id",2=>"t_id");//,2=>"id"可有可无，编辑的时候一定要有
        $arr["TBL_def"] = $TBL_def;
        $arr["FLD_def"] = $FLD_def;
        $arr["html_title"] = $GLOBALS['language']['TPL_ZENGJIA_STR'].$GLOBALS['language']['TPL_WENDANG_STR'];
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
             "do"   =>"template_list",
             "value"=>$request["t_id"],
             "name_cn"=>$GLOBALS['language']['TPL_MOBAN_STR'].$GLOBALS['language']['TPL_LIEBIAO_STR'],
           )
         )
       );

       $this->Init($request, $arr);  // 需要初始化一下

        // 需要获取到当前针对哪张表进行加入数据 $table_name

        $arr = array_merge($arr, $p_self_info);
        if(!array_key_exists("f_info",$arr)) {
          $response['ret'] = array('ret'=>1,'msg'=>"the f_info not exist!");
          return null;
        }


        // 可能是
        $arr["default_over"] = array(
          "p_id"=>array("value"=>$request["p_id"],"hidden"=>1),
          "t_id"=>array("value"=>$request["t_id"],"hidden"=>1)
        );  // 作为参数传递过来的, 最好是隐藏的，并且列出中文名

        // 在列出表单之前，先将字段定义的算法进行必要的解析以后再列出表单。
        // 列出表单只解析真实表结构本身的字段
        Parse_Arithmetic::parse_for_list_form($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

        // 每个模板可能有其他算法, 表定义表中的发布，通常是将整张表进行分页发布
        $l_arith = array();
        if (isset($arr["t_def"]['arithmetic']) && !empty($arr["t_def"]['arithmetic'])) {
        $l_arith = Parse_Arithmetic::parse_like_ini_file($arr["t_def"]['arithmetic']); // 首先将算法解析为一维数组
        }

        //$l_bixuziduanform = DbHelper::getBiXuFields($arr["dbR"], array("table_name"=>$table_name, "f_info"=>$arr["f_info"]));
        // empty($form[$l_bixuziduanform["one"]])
        if (empty($form)) {
          $arr["no_need_field"] = array("mender","menddate","mendtime","creator","createdate","createtime",'published_1','unicomment_id','audited','expireddate','flag');
          $l_resp = parent::executeListForm($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie, $files);

          // 也可以采用json串的方式返回，而不急于采用html_页面
          $response['html_content'] = $l_resp;
          $response['ret'] = array('ret'=>0);
        }else {
          // 实际上除了表象的验证外，还需要进行数据库层面的验证，例如用户注册的时候，除了用户中心库外还需要验证管理系统中的用户
          // 应该放到数据库中作为一个算法存在。以后完善此步骤????

           // 依据字段定义表，填充数据
           $data_arr = DbHelper::getInsertArrByFormFieldInfo($form, $arr["f_info"], false, false, $files);

           if (array_key_exists("___ERR___", $data_arr)) {
             $response['ret'] = array('ret'=>1,'msg'=>var_export($data_arr["___ERR___"], TRUE));
             $response['html_content'] = date("Y-m-d H:i:s") . "field empty: ". var_export($data_arr["___ERR___"], TRUE);
             return null;
           }
           // 自动填充几个数据，关于创建者、时间的字段. 首先确保数据表有这些字段
           if (array_key_exists("creator",    $arr["f_info"])) $data_arr["creator"] = isset($_SESSION["user"]["username"]) ? $_SESSION["user"]["username"] : $form['username'];
           if (array_key_exists("createdate", $arr["f_info"])) $data_arr["createdate"] = ("0000-00-00"==$data_arr["createdate"] || empty($data_arr["createdate"])) ? date("Y-m-d") : $data_arr["createdate"];
           if (array_key_exists("createtime", $arr["f_info"])) $data_arr["createtime"] = ("00:00:00"==$data_arr["createtime"] || empty($data_arr["createtime"]))   ? date("H:i:s") : $data_arr["createtime"];
           // 同时修改form中的创建时间，因为用户没有填写的话需要系统自动生成一个. 管理员编辑后台能修改创建时间，如果是外部用户，此创建时间只能是系统产生，不可在外部修改。
           if (!isset($form['createdate']) || "0000-00-00"==$form["createdate"]) $request["createdate"] = $form["createdate"] = date("Y-m-d");
           if (!isset($form['createtime']) || "00:00:00"==$form["createtime"]) $request["createtime"] = $form["createtime"] = date("H:i:s");

           $dbW = new DBW($p_self_info["p_def"]);
           $dbW->table_name = $table_name;  // 字段定义表
           $rlt = $dbW->insertOne($data_arr);
           $l_err = $dbW->errorInfo();
           if ($l_err[1]>0){
             // 增加失败后
             $response['html_content'] = date("Y-m-d H:i:s") . var_export($l_err, true). " 发生错误,sql: ". $dbW->getSQL();
             $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
             return null;
           } else {
             $l_id = $dbW->LastID();  // instert后产生的文档id。后面需要用到
             $data_arr["id"] = $l_id;  // 算是比较完整的信息了，最好是从数据库中获取获取按照之前的规则以及默认值计算得出数据表中的该记录所有数据
             $arr["f_data"] = $data_arr;  // 注册到数组中去，发布的时候可能需要用到这些数据

             // 添加成功以后,添加成功之前的一些算法是否应该进行区分呢？，需要对定义的各种任务需要一一完成(即执行相应的成功后算法)
            Parse_Arithmetic::do_arithmetic_by_add_action($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

            Parse_Arithmetic::Int_FillALL($arr, $response, $request);  // 变量注册、替换等

            //---------- 执行发布 begin 执行生成静态文件
            // 依据nginx.conf配置中关于该web前端项目的项目对应的本地目录
            // 还需要发布本页，即生成静态文件存放在相应路径下并进行同步分发出去
             // 获取url、模板等数据, 要是模板不存在也得判断一下, 类型的数据库可能没有模板设计表
             if (isset($arr["t_def"]["tmpl_design"])) {
               $l_tmpl = $arr["t_def"]["tmpl_design"];
             } else {
               $l_tmpl = array();
             }
             if (!empty($l_tmpl)) {
               // 需要进行文档发布, 需要用到主库操作
               $arr["dbW"] = $dbW;

               $l_data_arr = array_merge($request,$data_arr);
               foreach ($l_tmpl as $l_tmpl_one){
                 // 每个模板可能有多个分页. 在算法中进行分页发布
                 if (array_key_exists('publish', $l_arith)) {
                   $l_func = preg_replace('/\W/',"_",basename(__FILE__)) . "_publish_" .$arr['p_def']["id"] . "_" .$arr['t_def']["id"]."_".$arr['f_data']["id"]."_". time();
                   $l_func_str = pinzhuangFunctionStr(array('code'=>$l_arith['publish']), $l_func, '&$arr,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie,$l_data_arr,$l_tmpl_one');
                   eval($l_func_str);
                   $l_func($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie,$l_data_arr,$l_tmpl_one);
                 } else
                 Publish::toPublishing($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie,$l_data_arr,$l_tmpl_one);
               }
               // 成功就跳转到列表页面
               $response['html_content'] = date("Y-m-d H:i:s") . " 成功发布并保存了信息, <a href='?do=".$this->type_name."_list".$arr["parent_rela"]["parent_ids_url_build_query"]."'>返回列表页面</a> ".NEW_LINE_CHAR;
               $response['ret'] = array('ret'=>0);

             } else {
             //----------- 发布 end

               // 成功就跳转到列表页面, 有时候作为接口或者内部被调用的时候，并不希望进行跳转动作。是否跳转依据参数 page_no_jump
               $response['ret'] = array_merge(array('ret'=>0),$data_arr);

               $response['html_content'] = date("Y-m-d H:i:s") . " 成功添加了信息, <a href='?do=" . $this->type_name . "_list".$arr["parent_rela"]["parent_ids_url_build_query"]."'>返回列表页面</a> ".NEW_LINE_CHAR;

             }
           }
        }

        if (isset($_GET['debug']) && $_GET['debug']) {
            print_r($arr);
            exit;
        }

        return null;  // 总是返回此结果
    }
}
