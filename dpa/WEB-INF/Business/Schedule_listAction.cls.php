<?php
/**
 * Schedule_listAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/global_func.php");
require_once("common/Pager.cls.php");
require_once('mvc/Action.cls.php');
require_once('mod/DBR.cls.php');
require_once('mod/DBW.cls.php');

class Schedule_listAction extends Action {
  //
  var $pageSize = 20;
  var $flag = "p";          // 当前页面标记
  var $pagesize_flag = "pagesize";  // 每页显示多少条目标记

    /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
    function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

        if (1!=$_SESSION["user"]["if_super"]) {
          $response['html_content'] = "权限不够!";
          return null;  // 总是返回此结果
        }
        // 如果对列表中单个计划任务进行启动、停止或删除，执行完以后，依然显示列表页面 begin
        // 本应该分离出来, 但为了页面的所有post数据都能记忆上，便于搜索功能,以后连同js一起进行分离出去
        if("delete"==$request["_action"]){
          // 可以调用 main.php?do=schedule_del&id=$request["id"]
           $dbW = new DBW();
           $dbW->table_name = TABLENAME_PREF."schedule";
           $ar = array("id"=>$request["id"]);
           $dbW->delOne($ar,"id");
        }else if("start"==$request["_action"]){
           $dbW = new DBW();
           $dbW->table_name = TABLENAME_PREF."schedule";
           $ar = array("status_"=>'1');
           $dbW->updateOne($ar,"id=".$request["id"]);
        }else if("stop"==$request["_action"]){
           $dbW = new DBW();
           $dbW->table_name = TABLENAME_PREF."schedule";
           $ar = array("status_"=>'0');
           $dbW->updateOne($ar,"id=".$request["id"]);
        }
        //  end

        // 显示页面数据
        $dbR = new DBR();
        $dbR -> table_name = TABLENAME_PREF."schedule";

        // 有查询的时候，查询sql语句保留
        $sql_where = isset($request["sql_where"]) ? urldecode($request["sql_where"]) : "";
        // 如果有查询条件 begin
        if (key_exists("search_field_1",$request)) {

          $sql_where = getWhere($sql_where,$request);

          // 有查询条件的时候，同时将sql语句注入到 request 数组中，便于作为链接的一部分
          $request["sql_where"] = $sql_where;
        }
        //$field_option  = buildOptions(array("host"=>"发布主机ip","shell_command"=>"执行命令","description"=>"说明"),"",false);
        $field_option  = buildOptions(getFieldArr($dbR->getTblFields()),"",false);
        $method_option = get_method_option();
        // 查询 end


        // 分页部分 开始
    if (intval($request["pagesize_form"])>=1) {
      $pageSize = intval($request["pagesize_form"]); // 替换掉request中旧的
      $request[$this->pagesize_flag] = $request["pagesize_form"];
      unset($request["pagesize_form"]);
    }else {
      $pageSize = ($request[$this->pagesize_flag]>=1)?(int)$request[$this->pagesize_flag]:$this->pageSize;  // how many  per page
    }
    $itemSum = $dbR->getCountNum($sql_where);
    $_p = isset($request[$this->flag])?$request[$this->flag]:1; // page number $currentPageNumber
    $_p = (int)$_p;                   // int number
    $_p = ($_p>ceil($itemSum/$pageSize))?ceil($itemSum/$pageSize):$_p;
    $_p = ($_p<1)?1:$_p;
        $pager = new Pager("?".http_build_query(get_url_gpc($request)),$itemSum,$pageSize,$_p,$this->flag);
    $pagebar = $pager->getBar();
    $page_bar_size = $pagebar." &nbsp;&nbsp;&nbsp;&nbsp; "."(共有：".$pager->itemSum." 条)";
                  //每页显示 <a href='".$pager->buildurl(array($this->pagesize_flag=>5))."'>5条</a> <a href='".$pager->buildurl(array($this->pagesize_flag=>50))."'>50条</a> <a href='".$pager->buildurl(array($this->pagesize_flag=>100))."'>100条</a>";
                  //." 共有：".$pager->itemSum." 条";
    // 分页部分 结束

    // 具体数据
    $offset = ($_p-1)*$pageSize;
        $_arr = $dbR->getAlls("$sql_where order by id desc limit $offset , $pageSize ");

        $ziduan_arr = getZiduan("id:任务号;name:任务名称;__minute_hour_day_month_week__:时间设置:width,300px|overflow,hidden|text-overflow,ellipsis;status_:执行状态:1,blue|0,red;host:任务执行主机;creator:创建者;__create_datetime__:创建时间:width,150px;shell_command:shell命令:text-align,left");// 需要的字段
        $show_arr = buildH($_arr,$ziduan_arr,array("status_"=>array(0=>"停止",1=>"启动")));
        $show = $show_arr[0];
        $show_title = $show_arr[1];

    // 先获取模板
      $content = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/".$actionMap->getProp("path").".html");
    // 加入头尾
      $header = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."header.html");  // 标准头
    $footer = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."footer.html");  // 标准尾
    $data_arr = array(
      "do"=>$request["do"],
      $this->flag=>$_p,
      $this->pagesize_flag=>$pageSize,

      "sql_where"=>urlencode($sql_where),

      "field_option"=>$field_option,
      "method_option"=>$method_option,

      "flag"=>$this->flag,
      "pagesize_flag"=>$this->pagesize_flag,
      "schedule_show"=>$show,
      "schedule_show_title"=>$show_title,
      "nav"=>"计划任务列表",
      "pagebar"=>$page_bar_size,
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


    $response['html_content'] = replace_template_para($data_arr,$content);
    return null;  // 总是返回此结果
    }

}
