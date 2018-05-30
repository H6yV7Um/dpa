<?php
/**
 * Document_listAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/lib/dbhelper.php");
require_once("common/lib/cArray.cls.php");
require_once("common/Pager.cls.php");
require_once('mvc/ListAction.cls.php');
require_once('mod/DBR.cls.php');
require_once('mod/DBW.cls.php');

class Document_listAction extends ListAction {
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
          $response['ret'] = array('ret'=>0, 'msg'=>$l_err[2]);
          return null;
        }
        $a_p_self_ids = array(
          1=>array("ziduan"=>"p_id"),
          2=>array("ziduan"=>"t_id"),
        );
        // 获取到前几级的数据数组，包括表定义表和字段定义表等围绕目标项的直系亲属
        // 父级、祖父级或更高。
        $p_self_info = DbHelper::getProTblFldArr($dbR, $request, $a_p_self_ids);

        if(array_key_exists("t_def", $p_self_info) && array_key_exists("name_eng", $p_self_info["t_def"]) && !empty($p_self_info["t_def"]["name_eng"])){
          $dbR->table_name = $table_name = $p_self_info["t_def"]["name_eng"];
        }else {
          $response['ret'] = array('ret'=>1,'msg'=>"err!!!!");
          return null;
        }
        //print_r($tbl_def);
        /*if (empty($tbl_def)) {
          require_once("common/lib/dbhelper.php");
          $dbW = new DBW($p_arr);
          $a_data_arr = array("source"=>"db","creator"=>$_SESSION["user"]["username"]);  // 能在外部增加字段的
      DbHelper::fill_field($dbR,$dbW,$a_data_arr,"all",TABLENAME_PREF."field_def",TABLENAME_PREF."table_def");

        }*/
        // 应该自动获取表定义表和字段定义表,此处省略并人为指定????
        $TBL_def = TABLENAME_PREF."table_def";
        $FLD_def = TABLENAME_PREF."field_def";

        $arr = array();
        $arr["dbR"] = $dbR;
        $arr["table_name"] = $table_name;
         $arr["TBL_def"] = $TBL_def;
        $arr["FLD_def"] = $FLD_def;
         $arr["html_title"] = $GLOBALS['language']['TPL_WENDANG_STR'].$GLOBALS['language']['TPL_LIEBIAO_STR'];
         $arr["html_name"]  = $p_self_info["t_def"]["name_cn"].$arr["html_title"];
         $arr["sql_order"] = "order by id desc";
         $arr["parent_ids_arr"] = array(1=>"p_id", 2=>"t_id");  // 父级元素列表,p2, p3...分别表示二、三级父级元素例如项目id，模板id，文档id等
         $arr["a_options"] = array(
         "nav"=>array(
           "p_id"=>array(
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

         $this->Init($request, $arr); // 初始化一下, 需要用到的数据的初始化动作,在parent::之前调用

         $arr = array_merge($arr, $p_self_info);
        if(!array_key_exists("f_info",$arr)) {
          $response['ret'] = array('ret'=>1,'msg'=>"the f_info not exist!");
          return null;
        }

        $dbR->table_name = $table_name;
         $content = parent::execute($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie, $files);

         // 显示的字段需要过滤掉text类型的数据，防止列表页显示太长东西
         $l_list_f = array();
         foreach ($p_self_info["f_info"] as $l_k => $l_v){
           if (false===strpos($l_v["type"],"text")) {
             $l_list_f[$l_k] = $l_v;
           }
         }
        $ziduan_arr = getZiduan( $this->tem_func( cArray::Index2KeyArr($l_list_f, $a_val=array("key"=>"name_eng", "value"=>"name_cn"))));// 需要的字段
        $show_arr = buildH($arr["_arr"],$ziduan_arr,array(),array("id", "last_modify"), $arr['p_def']['waiwang_url']);
        $show = $show_arr[0];
        $show_title = $show_arr[1];

        $mobanyu_guanli = '';
        if (1 == $_SESSION["user"]["if_super"]) {
          $mobanyu_guanli = '<a href="main.php?do=tempdef_list&p_id='.$request["p_id"].'&t_id='.$request["t_id"].'" target="_self">'.$GLOBALS['language']['TPL_MOBAN_STR'].$GLOBALS['language']['TPL_YU_STR'].$GLOBALS['language']['TPL_GUANLI_STR'].'</a>';
        }
        $data_arr = array(
          "show"=>$show,
           "show_title"=>$show_title,
           "INPUT_other"=> '<input type="button" onClick="action_onclick(\'main.php?do=topublishdocs_edit&type_name=topublishdocs&action=edit&p_id='.$request["p_id"].'&t_id='.$request["t_id"].'\',self.document.myform,\'id\',\'edit\');return false" value="发布" />'.$mobanyu_guanli,
        );

    $response['html_content'] = replace_template_para($data_arr,$content);
    $response['ret'] = array('ret'=>0);
    return null;  // 总是返回此结果

    }

  function tem_func($arr)
  {
    $l_str = "";

    if (!empty($arr))
    {
      $i=0;
      foreach ($arr as $l_k=>$l_v){
        if ($i>0) {
          $l_str .= ";";
        }
        $l_str .= $l_k.":".$l_v;
        $i++;
      }
    }
    return $l_str;
  }
}

