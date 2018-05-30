<?php
/**
* Host_backend_listAction.cls.php
*/
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/global_func.php");
require_once("common/Pager.cls.php");
require_once('mvc/Action.cls.php');
require_once("mod/DBR.cls.php");

class Host_backend_listAction extends Action {
	//
	var $pageSize = 20;
	var $flag = "p";					// 当前页面标记
	var $pagesize_flag = "pagesize";	// 每页显示多少条目标记

   /**
    *
    * @access public
    * @param array &$request
    * @param array &$files
    */
    function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){
        /*
        // 事务处理
        // 将需要显示给用户的错误注入到 $response['action_erros'] 中
        // 给forward增加参数(在进行页面跳转时使用)
        // $actionMap->addForwardParam('key_test','value_test','name_test');
        // 返回的forward是一个数组
        //return $actionMap->findForward('success');
        //return $actionMap->findForward('sysError');
        */
        // 获取发布主机列表 , 用于ui
        $dbR = new DBR();
        $dbR->table_name = TABLENAME_PREF."host_backend_reg";

        // 有查询的时候，查询sql语句保留
        $sql_where = isset($request["sql_where"])?( get_magic_quotes_gpc()? stripslashes(urldecode($request["sql_where"])) : urldecode($request["sql_where"])):"";
        // 如果有查询条件 begin
        if (key_exists("search_field_1",$request)) {
        	// 对先前的错误进行处理
	        if ( !$actionError->isEmpty() ){
	        	// 是系统错误吗？
	        	if ($actionError->getProp('sysError') != false){
	        		return $actionMap->findForward('sysError');
	        	}
	        	print_r($actionError);
	        	return $actionMap->findForward('failure');
	        }

	        $sql_where = getWhere($sql_where,$request);

	        // 有查询条件的时候，同时将sql语句注入到 request 数组中，便于作为链接的一部分
	        $request["sql_where"] = $sql_where;
        }
        $field_option = buildOptions(getFieldArr($dbR->getTblFields()),"",false);
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
		$page_bar_size = $pagebar." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									每页显示 <a href='".$pager->buildurl(array($this->pagesize_flag=>5))."'>5条</a> <a href='".$pager->buildurl(array($this->pagesize_flag=>50))."'>50条</a> <a href='".$pager->buildurl(array($this->pagesize_flag=>100))."'>100条</a>";
									//."(共找到：".$pager->itemSum." 条)";
		// 分页部分 结束

		// 具体数据
		$offset = ($_p-1)*$pageSize;
        $_arr = $dbR->getAlls("$sql_where order by id desc limit $offset , $pageSize ");

        $ziduan_arr = getZiduan("id:ID;host_name:名称;host_label:编号;host_os:操作系统;host_ip:IP地址;description:备注");// 需要的字段
        $show_arr = buildH($_arr,$ziduan_arr);
        $show = $show_arr[0];
        $show_title = $show_arr[1];

		// 先获取模板
	    $content = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/".$actionMap->getProp("path").".html");
		// 加入头尾
	    $header = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."header.html");	// 标准头
		$footer = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."footer.html");	// 标准尾
		$data_arr = array(
			"do"=>$request["do"],
			$this->flag=>$_p,
			$this->pagesize_flag=>$pageSize,

			"sql_where"=>urlencode($sql_where),

			"field_option"=>$field_option,
			"method_option"=>$method_option,

			"flag"=>$this->flag,
			"pagesize_flag"=>$this->pagesize_flag,
			"show"=>$show,
			"show_title"=>$show_title,
			"nav"=>"后端主机列表",
			"pagebar"=>$page_bar_size,
			"RES_WEBPATH_PREF"=>$GLOBALS['cfg']['RES_WEBPATH_PREF'],
			"header"=>$header,
			"footer"=>$footer
		);
		//$content = replace_template_para($data_arr,$content);

		// 替换其中的css地址和js地址 以后采用缓存文件，而不用每次都实时取
		//$content = replace_cssAndjsAndimg($content,SOURCE_CSS_PATH,SOURCE_JS_PATH,SOURCE_IMG_PATH);
		// 将外链的js替换为其相应js内容
		//$content = jssrc2content($content);
		// 替换其中的css地址和js地址 以后采用缓存文件，而不用每次都实时取
		//$content = replace_cssAndjsAndimg($content,SOURCE_CSS_PATH,SOURCE_JS_PATH,SOURCE_IMG_PATH);// js中还有图片

		$response['html_content'] = replace_template_para($data_arr,$content);
		return null;	// 总是返回此结果
   }
}
