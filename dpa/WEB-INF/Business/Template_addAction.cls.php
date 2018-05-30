<?php
/**
 * Template_addAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/lib/dbhelper.php");
require_once("common/lib/Parse_Arithmetic.php");
require_once("common/lib/cArray.cls.php");
require_once('mvc/AddAction.cls.php');
require_once('mod/DBR.cls.php');
require_once('mod/DBW.cls.php');

class Template_addAction extends AddAction {
  /**
   * 如果有是导入项目的其他表, 的相应字段
   *
   * @var unknown_type
   */
  var $other_tbl_daoru_source = "other_tbl_daoru_source";

  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

    // 需要建表同时要在表定义表中增加一条记录

    // 对什么表进行操作，需要依据参数而定
    // 找到父级元素, 只有一级父级
    $p_id = $request["p_id"];  // 第一个父级id, 也是project id。
    $l_name0_r = $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R'];
    $dbR = new DBR($l_name0_r);  // 系统默认数据库连接信息，开始都从这个入口
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
    //print_r($p_self_info);

    // 应该自动获取表定义表和字段定义表,此处省略并人为指定????
    $TBL_def = TABLENAME_PREF."table_def";
    $TMPL_DESIGN_def = TABLENAME_PREF."tmpl_design";
    $FLD_def = TABLENAME_PREF."field_def";

    $arr = array();
    $arr["dbR"] = $dbR;
    $arr["table_name"] = $TBL_def;  // 执行插入操作的数据表
    $arr["parent_ids_arr"] = array(1=>"p_id");//,2=>"id"可有可无，编辑的时候一定要有
    $arr["TBL_def"] = $TBL_def;
    $arr["FLD_def"] = $FLD_def;
    $arr["html_title"] = $GLOBALS['language']['TPL_ZENGJIA_STR'].$GLOBALS['language']['TPL_MOBAN_STR'];
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

    //$l_bixuziduanform = DbHelper::getBiXuFields($arr["dbR"], array("table_name"=>$table_name, "f_info"=>$arr["f_info"]));
    if (empty($form)) {
      // 因为还没有数据表，因此可以执行外部sql，也可以选择某些类型还可以使用默认表
      // 需要根据项目类型(是否CMS)来决定显示相应的静态模板
      $arr["default_over"] = array("p_id"=>array("value"=>$p_id,"hidden"=>1));  // 作为参数传递过来的, 最好是隐藏的，并且列出中文名
      $arr["no_need_field"] = array("creator","createdate","createtime","mender","menddate","mendtime");

      // 在列出表单之前，先将字段定义的算法进行必要的解析以后再列出表单。
      // 列出表单只解析真实表结构本身的字段
      Parse_Arithmetic::parse_for_list_form($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

      $l_resp = parent::executeListForm($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

      // 将该项目的所有表进行列出拼装成，以后需要剔除表定义表、字段定义表、模板设计表三张表????
      $l_tbl_daoru_options = buildOptions($arr['t_all_'],2,true,"name_eng","name_cn");
      $l_other = $this->getOtherStr($l_tbl_daoru_options, $this->other_tbl_daoru_source);

      $data_arr = array(
        "l_other" => $l_other
      );

      $response['html_content'] = replace_template_para($data_arr,$l_resp);
      $response['ret'] = array('ret'=>0);
      return null;  // 总是返回此结果
    }else {

      // 如果提交的数据的唯一项是数据库中的默认值，则需要进行替换成程序安排的默认递增值
      // 默认的数据表名称 aups_t 加数字 ，需要执行查询统计
      $n_finfo = cArray::Index2KeyArr($arr["f_info"],array("key"=>"name_eng","value"=>array()));// 默认表名不是想要的
      if ( !isset($form["name_eng"]) || ""==$form["name_eng"] || trim($n_finfo["name_eng"]["default"]) == trim($form["name_eng"]) ) {
        //$a_tmpl = $dbR->getDBTbls();  // 获取实际的表而不是从表定义表中获取
        $dbR->table_name = $table_name;    // 获取到表定义表中的所有表英文名
        $a_tmpl = $dbR->getAlls("","name_eng");
        $request["name_eng"] = $form["name_eng"] = DbHelper::getAutocreamentTBname($a_tmpl,"name_eng");
      }

      // 如果是导入项目的其他表的话，表结构采用数据库中获取，包括中、英文、注释三项
      if (array_key_exists($this->other_tbl_daoru_source,$form)) {
        $l_t_all_ = cArray::Index2KeyArr($arr['t_all_'],array("key"=>"id","value"=>"name_eng"));
        $l_tablename = $l_t_all_[$form[$this->other_tbl_daoru_source]];

        // 首先获取来源表的表定义表、字段定义表中的数据
        $dbR->table_name = $TBL_def;
        $l_form_old = $dbR->GetOne(" where id=".$form[$this->other_tbl_daoru_source]);
        $dbR->table_name = $FLD_def;
        $l_fields_old = $dbR->getAlls(" where t_id=".$form[$this->other_tbl_daoru_source] . " order by id ");
        $dbR->table_name = $TMPL_DESIGN_def;
        $l_tmpl_designs_old = $dbR->getAlls(" where tbl_id=".$form[$this->other_tbl_daoru_source] . " order by id ");

        // 然后获取来源表的真实表结构
        $l_tmp = $dbR->SHOW_CREATE_TABLE($l_tablename);
        if (!array_key_exists("Create Table", $l_tmp[0]) || empty($l_form_old)){
          $response['html_content'] = date("Y-m-d H:i:s") . " ----- error! " . NEW_LINE_CHAR;
          $response['ret'] = array('ret'=>1);
          return null;
        }else {
          $l_sql = $l_tmp[0]["Create Table"];
        }
        // 用新的表名
        $l_tablename_new = $form["name_eng"];
        $l_sql = preg_replace('/^CREATE TABLE( IF NOT EXISTS)? `('.$l_tablename.')`/i', 'CREATE TABLE IF NOT EXISTS `'.$l_tablename_new.'`', $l_sql, 1);

        // 去掉sql中自增的数字
        if (false!==stripos($l_sql,"AUTO_INCREMENT=")) {
          $l_sql = preg_replace("/AUTO_INCREMENT=\d+/","",$l_sql);
        }
        // 替换表注释, 如果表单中未提供中文表名
        if (""==trim($form["name_cn"])) {
          $l_name_cn = $form["name_eng"].$l_form_old["name_cn"];
        }else {
          $l_name_cn = $form["name_cn"];
        }
        if (""==trim($form["description"])) {
          $l_description = $l_name_cn;
        }else {
          $l_description = $form["description"];
        }
        if (preg_match("/CHARSET=(\w+)\s+COMMENT=['\"][^'\"]+['\"]/", $l_sql, $l_match)) {
          $l_sql = str_replace($l_match[0], "CHARSET=".$l_match[1]." COMMENT='".$l_description."'", $l_sql);
        }

        // 创建该表
        $dbW = new DBW($arr["p_def"]);
        $dbW->Query($l_sql);
        $l_err = $dbW->errorInfo();
        if ($l_err[1]>0){
          $response['html_content'] = "\r\n".  date("Y-m-d H:i:s") . " FILE: ".__FILE__." ". " FUNCTION: ".__FUNCTION__." Line: ". __LINE__."\n" . " l_err:" . var_export($l_err, TRUE);
          $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
          return null;
        }
        // ----- 建表完成end

        // 写入表定义表中去，除了9项不一样外，暂时不需要结合外界填写的数据即form。直接使用
        unset($l_form_old["id"]);
        unset($l_form_old["mender"]);
        unset($l_form_old["menddate"]);
        unset($l_form_old["mendtime"]);
        unset($l_form_old["last_modify"]);
        $l_form_new = array(
          "p_id"    =>$request["p_id"],
          "name_eng"  =>$request["name_eng"],
          "name_cn"  =>$l_name_cn,
          "description"=>$l_description,
        );
        // 创建时间添加
        $l_form_new["creator"] = $_SESSION["user"]["username"];
        $l_form_new["createdate"] = date("Y-m-d");
        $l_form_new["createtime"] = date("H:i:s");
        // 覆盖来源表的数据
        $l_form_new = array_merge($l_form_old,$l_form_new);

        // 1) 先入表定义表
        $dbW->table_name = $TBL_def;  // 表定义表
        $dbW->insertOne($l_form_new);
        $l_err = $dbW->errorInfo();
        if ($l_err[1]>0){
          // 数据库连接失败后
          $response['html_content'] = date("Y-m-d H:i:s") . " ----- insert error! " . $l_err[2]. ".";
          $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
          return null;
        }else {
          $tid = $dbW->LastID();  // 获取表id
        }

        // 1.2) 模板设计表作为表定义表的延伸，也一起复制一份
        foreach ($l_tmpl_designs_old as $l_tmpl_design_old){
          unset($l_tmpl_design_old["id"]);
          unset($l_tmpl_design_old["mender"]);
          unset($l_tmpl_design_old["menddate"]);
          unset($l_tmpl_design_old["mendtime"]);
          unset($l_tmpl_design_old["last_modify"]);

          // 创建时间添加
          $l_tmpl_design_old["tbl_id"]   = $tid;
          $l_tmpl_design_old["creator"] = $_SESSION["user"]["username"];
          $l_tmpl_design_old["createdate"] = date("Y-m-d");
          $l_tmpl_design_old["createtime"] = date("H:i:s");
          // 覆盖来源表的数据
          $dbW->table_name = $TMPL_DESIGN_def;  // 模板设计表
          $dbW->insertOne($l_tmpl_design_old);
          $l_err = $dbW->errorInfo();
          if ($l_err[1]>0){
            // 数据库连接失败后
            $response['html_content'] = date("Y-m-d H:i:s") . " ----- insert error! " . $l_err[2]. ".";
            $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
            return null;
          }else {
            //$fid = $dbW->LastID();  // 获取表id
          }
        }

        // 2) 再入字段定义表, 将刚才创建的表自动提取字段性质并入库
        // 将来源表的所有字段信息，直接写入字段定义表中，注意一下父级id修改一下即可
        foreach ($l_fields_old as $l_field_old){
          unset($l_field_old["id"]);
          unset($l_field_old["mender"]);
          unset($l_field_old["menddate"]);
          unset($l_field_old["mendtime"]);
          unset($l_field_old["last_modify"]);

          // 创建时间添加
          $l_field_old["t_id"]   = $tid;
          $l_field_old["creator"] = $_SESSION["user"]["username"];
          $l_field_old["createdate"] = date("Y-m-d");
          $l_field_old["createtime"] = date("H:i:s");
          // 覆盖来源表的数据
          $dbW->table_name = $FLD_def;  // 字段定义表
          $dbW->insertOne($l_field_old);
          $l_err = $dbW->errorInfo();
          if ($l_err[1]>0){
            // 数据库连接失败后
            $response['html_content'] = date("Y-m-d H:i:s") . " ----- insert error! " . $l_err[2]. ".";
            $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
            return null;
          }else {
            //$fid = $dbW->LastID();  // 获取表id
          }
          usleep(100);
        }
      }else {

        // 先创建一张数据表, 数据库连接信息使用$p_arr
        // 应当依据模板类型，对应地创建相应的数据字段????甚至是同时创建多张表，
        // 此处仅仅限定最基本的数据表字段, 另外新增的字段在模板域中去添加。
        $dbW = new DBW($arr["p_def"]);
        $sql_q = "`id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',`creator` varchar(100) NOT NULL default '0' COMMENT '创建者',`createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',`createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',`mender` varchar(100) default NULL COMMENT '修改者',`menddate` date default NULL COMMENT '修改日期',`mendtime` time default NULL COMMENT '修改时间',`expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',`audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核',`status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',`flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',`arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',`unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',`published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',`url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',`last_modify` timestamp NOT NULL COMMENT '最近修改时间', PRIMARY KEY  (`id`),KEY `createdate` (`createdate`,`createtime`),KEY `menddate` (`menddate`,`mendtime`),KEY `expireddate` (`expireddate`),KEY `audited` (`audited`),KEY `status_` (`status_`),KEY `published_1` (`published_1`),KEY `url_1` (`url_1`)";
        $dbW->create_table($form["name_eng"], $sql_q);
        $l_err = $dbW->errorInfo();
        if ($l_err[1]>0){
          // 数据库连接失败后
          $response['html_content'] = date("Y-m-d H:i:s") . " create_table faild! " . $l_err[2]. ".";
          $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
          return null;
        }

        // 同表单呈现一样，填充之前需要将字段的各个算法执行一下，便于修正字段的相关限制和取值范围
        Parse_Arithmetic::parse_for_list_form($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);

        // 表创建成功以后,需要入表定义表和字段定义表
        // 1) 先入表定义表
        $data_arr = DbHelper::getInsertArrByFormFieldInfo($form, $arr["f_info"], false);
        if (array_key_exists("___ERR___", $data_arr)) {
          $response['html_content'] = date("Y-m-d H:i:s") . "field empty: ". var_export($data_arr["___ERR___"], TRUE);
          $response['ret'] = array('ret'=>1);
          return null;
        }
        // 自动填充几个数据，关于创建者、时间的字段 if (!array_key_exists("creator", $data_arr))
        if (array_key_exists("creator",    $arr["f_info"])) $data_arr["creator"] = $_SESSION["user"]["username"];
        if (array_key_exists("createdate", $arr["f_info"])) $data_arr["createdate"] = ("0000-00-00"==$data_arr["createdate"] || empty($data_arr["createdate"])) ? date("Y-m-d") : $data_arr["createdate"];
        if (array_key_exists("createtime", $arr["f_info"])) $data_arr["createtime"] = ("00:00:00"==$data_arr["createtime"] || empty($data_arr["createtime"]))   ? date("H:i:s") : $data_arr["createtime"];
        $dbW->table_name = $table_name;  // 表定义表
        $dbW->insertOne($data_arr);
        $l_err = $dbW->errorInfo();
        if ($l_err[1]>0){
          // 数据库连接失败后
          $response['html_content'] = date("Y-m-d H:i:s") . " ----- insert error! " . $l_err[2]. ".";
          $response['ret'] = array('ret'=>1,'msg'=>$l_err[2]);
          return null;
        }else {
          $tid = $dbW->LastID();  // 获取表id
        }

        // 2) 再入字段定义表, 将刚才创建的表自动提取字段性质并入库
        $dbW->table_name = $FLD_def;  // 字段定义表
        // 字段有固有算法的同时需要更新字段类型和算法, 用于外部修改字段定义表自动获取数据的数组
        $l_data_arr = array(
          "id"   =>array("list_order"=>1),
          "creator"=>array("f_type"=>'Form::CodeResult',"arithmetic"=>'[html]
$_SESSION["user"]["username"]'),
          "url_1"=>array("f_type"=>'Form::CodeResult',"list_order"=>2000,"arithmetic"=>'[code]<?php
// 该字段的名称为 $a_key;
$l_tmpl_design_arr = array();
if (isset($a_arr["t_def"]["tmpl_design"][0]["default_field"])) {
  $l_tmpl_design_arr = cArray::Index2KeyArr($a_arr["t_def"]["tmpl_design"],array("key"=>"default_field","value"=>array()));
}

if (isset($form[$a_key])){
    $l_url=$form[$a_key];
}else if (""!=trim($a_arr["f_data"][$a_key])){
    $l_url=$a_arr["f_data"][$a_key];
}else if (""!=isset($l_tmpl_design_arr[$a_key]["default_url"])){
    $l_url=$l_tmpl_design_arr[$a_key]["default_url"];
}else if (""!=trim($a_arr["t_def"]["waiwang_url"])){
    $l_url=$a_arr["t_def"]["waiwang_url"];
}else if (""!=trim($a_arr["p_def"]["waiwang_url"])){
    $l_url=$a_arr["p_def"]["waiwang_url"];
}else {
    $l_url="";
}

return $l_url;')
        );
DbHelper::ins2field_def($dbR,$dbW,$l_data_arr,$tid,$FLD_def,$TBL_def);

// 添加成功以后，需要对定义的各种任务需要一一完成(即执行相应的成功后算法)
Parse_Arithmetic::do_arithmetic_by_add_action($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);
      }
      //$response['html_content'] = "";
      //return "main.php?do=template_list".$arr["parent_rela"]["parent_ids_url_build_query"];  // 总是返回此结果
      $response['html_content'] = "<script type='text/javascript'>window.parent.frames['frmMainMenu'].location.reload();window.parent.frames['frmCenter'].location.href='?do=".$this->type_name."_list".$arr["parent_rela"]["parent_ids_url_build_query"]."';</script>".NEW_LINE_CHAR;
      $response['ret'] = array('ret'=>0);
      return null;  // 总是返回此结果
    }
  }

  function getOtherStr($l_tbl_daoru_options,$other_tbl_daoru_source){
    return '
<script type="text/javascript" src="http://img3.' . $GLOBALS['cfg']['WEB_DOMAIN'] . '/js/jquery.min.js"></script>
<script type="text/javascript">
        $(function(){

$("#id_project_add_1").hide();//可以先隐藏表单

$("#id_project_add_3").click(function(){
  // 在form表单中
  $("form > table").prepend(\'<tr><td nowrap="nowrap">来源模板</td><td><select id="'.$other_tbl_daoru_source.'" name="'.$other_tbl_daoru_source.'">'.$l_tbl_daoru_options.'</select></td></tr>\');

  $("#id_project_add_2").hide();
  $("#id_project_add_3").hide();
  $("#id_project_add_4").hide();
  $("#id_project_add_5").hide();
  $("#id_project_add_6").hide();
  $("#id_project_add_1").show();
});

$("#id_project_add_2").click(function(){
  $("#id_project_add_2").hide();
  $("#id_project_add_3").hide();
  $("#id_project_add_4").hide();
  $("#id_project_add_5").hide();
  $("#id_project_add_6").hide();
  $("#id_project_add_1").show();
});

        });
</script>
      '."<tr style='display:' id='id_project_add_3'>
    <td><a href='#' title='暂时只支持从本项目的其他模板中导入'>导入项目模板</a></td>
  </tr>
  <tr style='display:' id='id_project_add_4'>
    <td><a title='暂未实现'>从文件导入</a></td>
  </tr>
  <tr style='display:' id='id_project_add_5'>
    <td><a title='暂未实现'>从模板库导入</a></td>
  </tr>
  <tr style='display:' id='id_project_add_6'>
    <td><a title='暂未实现'>直接输入</a></td>
  </tr>
  <tr style='display:' id='id_project_add_2'>
    <td><a href='#'>新建空白模板</a></td>
  </tr>";
  }
}
