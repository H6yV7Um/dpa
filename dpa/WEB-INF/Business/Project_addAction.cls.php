<?php
/**
 * Project_addAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/lib/dbhelper.php");
require_once("common/lib/Parse_Arithmetic.php");
require_once('mvc/AddAction.cls.php');
require_once('mod/DBR.cls.php');
require_once('mod/DBW.cls.php');

class Project_addAction extends AddAction {
  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

    if (1!=$_SESSION["user"]["if_super"]) {
      $response['html_content'] = "权限不够!";
      $response['ret'] = array('ret'=>1);
      return null;  // 总是返回此结果
    }
    $table_name = TABLENAME_PREF."project";
    $arr = array();
    $arr["html_title"] = $GLOBALS['language']['TPL_ZENGJIA_STR'].$GLOBALS['language']['TPL_XIANGMU_STR'];
    $arr["html_name"]  = $arr["html_title"];
    $arr["table_name"] = $table_name;

    // 没有提交数据的时候，需要依据被增加数据数据表的字段进行显示表单;
    // 当有数据提交的时候，需要依据字段属性自动筛选和默认赋值等
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

    $this->Init($request, $arr);  // 需要初始化一下，并且放在调用的最前面

    parent::getFieldsInfo($arr);  // 获取表和字段定义信息
    if(!array_key_exists("f_info",$arr)) {
      $response['ret'] = array('ret'=>1,'msg'=>"the f_info not exist!");
      return null;
    }

    // 需要获取到必须填写的一个字段, 依据数据库结构进行判断
    //$l_bixuziduanform = DbHelper::getBiXuFields($arr["dbR"], array("table_name"=>$table_name, "f_info"=>$arr["f_info"]));

    if (empty($form)) {
      // 在列出表单之前，先将字段定义的算法进行必要的解析以后再列出表单。
      // 列出表单只解析真实表结构本身的字段
      Parse_Arithmetic::parse_for_list_form($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

      $l_resp = parent::executeListForm($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);
      $data_arr = array(
            //"p_id"=>isset($request["p_id"])?$request["p_id"]:1,  // 默认就是系统本身,
        // 可以不显示
        "l_other" => "<tr style='display:none' id='id_project_add_3'>
    <td><a href='main.php?do=project_import'>复制发布项目</a></td>
  </tr>
  <tr style='display:none' id='id_project_add_4'>
    <td><a href='main.php?do=project_import_from_file'>从文件导入</a></td>
  </tr>
  <tr style='display:none' id='id_project_add_2'>
    <td><a href='#' onclick=' id_project_add_3.style.display = \"none\";  id_project_add_4.style.display = \"none\";  id_project_add_2.style.display = \"none\";  id_project_add_1.style.display = \"inline\"; '>新建发布项目</a></td>
  </tr>"
          );

      $response['html_content'] = replace_template_para($data_arr,$l_resp);
      $response['ret'] = array('ret'=>0);
        return null;  // 总是返回此结果
    } else{

      if (!isset($form["db_name"]) || ""==$form["db_name"]) {
        $dbR = new DBR();
        $dbR->table_name = $table_name;
        // db_name

        // 从数据表中获取
        $a_proj = $dbR->getAlls("","db_name");

        // 同时还要从当前的数据库获取，以防止有其他未注册项目的存在而产生冲突????
        // 默认的数据库名称 aaaa 加数字 ，需要执行查询统计
        $request["db_name"] = $form["db_name"] = DbHelper::getAutocreamentDbname($a_proj, "db_name", $form);
      }

      // 同表单呈现一样，填充之前需要将字段的各个算法执行一下，便于修正字段的相关限制和取值范围
      Parse_Arithmetic::parse_for_list_form($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);
      // 各个项目自动检测，对于没有填写的采用默认值，默认为null的则剔除该项目
      $data_arr = DbHelper::getInsertArrByFormFieldInfo($form, $arr["f_info"], false);
      // 如果返回有错误，则退出
      if (array_key_exists("___ERR___", $data_arr)) {
        $response['html_content'] = date("Y-m-d H:i:s") . "field empty: ". var_export($data_arr["___ERR___"], TRUE);
        $response['ret'] = array('ret'=>1);
        return null;
      }
      // 自动填充几个数据，关于创建者、时间的字段. 首先确保数据表有这些字段
      if (array_key_exists("creator",    $arr["f_info"])) $data_arr["creator"] = $_SESSION["user"]["username"];
      if (array_key_exists("createdate", $arr["f_info"])) $data_arr["createdate"] = ("0000-00-00"==$data_arr["createdate"] || empty($data_arr["createdate"])) ? date("Y-m-d") : $data_arr["createdate"];
      if (array_key_exists("createtime", $arr["f_info"])) $data_arr["createtime"] = ("00:00:00"==$data_arr["createtime"] || empty($data_arr["createtime"]))   ? date("H:i:s") : $data_arr["createtime"];

      $dbW = new DBW();
      $dbW->table_name = $table_name;
      $dbW->insertOne($data_arr);
      $l_err = $dbW->errorInfo();
      if ($l_err[1]>0){
        // 增加失败后
        $response['html_content'] = date("Y-m-d H:i:s") . var_export($l_err, true). " 发生错误,sql: ". $dbW->getSQL();
        $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
        return null;
      }else {
        $pid = $dbW->LastID();
        if ($pid>0) {

          $form["id"] = $pid;  // 该项目id, 创建记录成功才会有此项

          // 增加项目记录成功后，需要创建相应的数据库和建立相应的数据表以及填充必要的数据
          // 依据项目的类型，确定需要建立哪几张基本表，后续需要在这个成功的基础上进行????
          $rlt = DbHelper::createDBandBaseTBL($form);

          // 添加成功以后，需要对定义的各种任务需要一一完成(即执行相应的成功后算法). 创建项目的时候基本不需要此步骤
          // Parse_Arithmetic::do_arithmetic_by_add_action($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);
        }

        // $response['html_content'] = "";
        //return "main.php?do=project_list";  // 总是返回此结果
        $response['html_content'] = "<script type='text/javascript'>window.parent.frames['frmMainMenu'].location.reload();window.parent.frames['frmCenter'].location.href='main.php?do=project_list';</script>".NEW_LINE_CHAR;
        $response['ret'] = array('ret'=>0);
        return null;  // 总是返回此结果
      }
    }
  }
}
