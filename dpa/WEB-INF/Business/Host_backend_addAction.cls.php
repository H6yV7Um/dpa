<?php
/**
*
*/
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/global_func.php");
require_once('mvc/Action.cls.php');
require_once("mod/DBW.cls.php");

class Host_backend_addAction extends Action {

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

        // 如果没有提交表单
        if ( !empty($form)  ){
        	//print_r($form);exit;
	        $response['form'] = $form;

	        // 对先前的错误进行处理
	        if ( !$actionError->isEmpty() ){
	            // 是系统错误吗？
	            if ($actionError->getProp('sysError') != false){
	                return $actionMap->findForward('sysError');
	            }
	            return $actionMap->findForward('failure');
	        }
	        // 先检查是否重复，以后做此步骤

	        // 不存在则插入数据库中
        	$data_arr = array(
        		"host_name" => convCharacter($form["host_name"],true),
        		"host_label" => $form["host_label"],
        		"host_os" => $form["host_os"],
        		"host_ip" => $form["host_ip"],
        		"creator" => convCharacter($_SESSION["user"]["id"],true),
       			"createdate" => date("Y-m-d"),
       			"createtime" => date("H:i:s"),
        		"description" => convCharacter($form["description"],true)
        	);
        	//global $SHOW_SQL;$SHOW_SQL="all";
        	$dbW = new DBW();
        	$dbW->table_name = TABLENAME_PREF."host_backend_reg";
        	$rlt = $dbW -> insertOne($data_arr);
        	if ($rlt) {
        		return "main.php?do=host_backend_list";
        	}else {
        		echo "执行sql发生错误";
        		return null;
        	}
        }else {
        	$host_os_options = buildOptions(getOSArr(),"SunOS",false);

			// 先获取模板
		    $content = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/".$actionMap->getProp("path").".html");
			// 加入头尾
		    $header = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."header.html");	// 标准头
			$footer = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."footer.html");	// 标准尾
			$data_arr = array(
				"host_os_options"=>$host_os_options,
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
}
