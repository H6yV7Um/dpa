-- 表定义表进行‘频道首页’的分页发布
update `dpps_table_def` set `arithmetic`='[publish]<?php
if (!isset($dbR)) $dbR = $arr[''dbR''];
$dbR->table_name = ''aups_t002'';				// 正文页

// 总条目数
$sql_where = ''where status_="use"'';
$l_itemSum = $dbR->getCountNum($sql_where);	

// 页面条目数
if (isset($arr[''f_def_duo''][''pagesize''][''default''])) $l_pageSize = $arr[''f_def_duo''][''pagesize''][''default''] + 0;
$l_pageSize = ($l_pageSize<1)?1:$l_pageSize;	// 防止除数为0的情况

// 总页数, 非cli模式下需要对总页码进行限制
$l_totalpage = ceil($l_itemSum/$l_pageSize);
if (''cli''!=php_sapi_name()) {
	$l_totalpage = ($l_totalpage>100)?100:$l_totalpage;	// 非cli模式下只能生成100页, 即循环100次
}

// 初始的文件名必须保持一致
$l_url = Publish::getUrl($arr,$actionMap,$actionError,$request,$response);	// 获取替换的url
$l_dir_url  = dirname($l_url);

// 页码进行循环, 同时执行发布
$l_old_id = $arr[''f_data''][''id''];
for ($l_p=1;$l_p<=$l_totalpage;$l_p++){
	$arr[''f_data''][''id''] = $l_p;	// 页码
	
	// 文件名路径需要更改. 翻页当前的文件名 1则index.shtml, 从_2开始index_2.shtml
	$l_filename = basename($l_url);	// 循环过程中在不断修改，因此需要不断地初始化
	if ($l_p>1) {
		$l_extt = substr( $l_filename, strrpos($l_filename,".") );
		$l_filename = str_replace($l_extt, "_".$l_p . $l_extt, $l_filename);
	}
	$l_new_url = $l_dir_url.''/''.$l_filename;
	
	// 获取的内容也需要做一些修改, 只需调用一下相关的字段算法即可，因为翻页、内容列表均在算法中灵活处理
	// 其中的内容需要修改，只需要重新执行一下 文章摘要列表(aups_f119) 的算法即可. 
	Parse_Arithmetic::do_arithmetic_by_add_action($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);
    Parse_Arithmetic::Int_FillDefDuo($arr, $response, $request);
	
	Publish::toPublishing($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie,$l_data_arr,$l_tmpl_one,$l_new_url,$a_other_arr["if_delete"]);
}
$arr[''f_data''][''id''] = $l_old_id;	// 恢复原值
' where `name_cn`='频道首页';

-- 首页模板代码替换
update `dpps_tmpl_design` set `default_html`='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[${_PROJECT_id},${_PROJECT_TABLE_id},${id}] published at ${_SYSTEM_date} ${_SYSTEM_time} by ${_USER_id}-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="${_PROJECT_name_cn},${_PROJECT_website_name_cn}" />
<meta name="description" content="" />
<title>首页_${_PROJECT_name_cn}_${_PROJECT_website_name_cn}</title>
<style type="text/css">
<!--
/* 全局样式begin */
html{color:#000;background:#FFF;}
body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,code,form,fieldset,legend,input,textarea,p,blockquote,th,td{margin:0;padding:0;}
body{background:#fff;font-size:12px; font-family:"宋体";}
table{border-collapse:collapse;border-spacing:0;}
fieldset,img{border:0;}
ul,ol{list-style-type:none;}
select,input,img,select{vertical-align:middle;}

a{text-decoration:underline;}
a:link{color:#009;}
a:visited{color:#800080;}
a:hover,a:active,a:focus{color:#c00;}

.clearit{clear:both;}
.clearfix:after{content:"ffff";display:block;height:0;visibility:hidden;clear:both;}
.clearfix{zoom:1;}
/* 全局样式 end */

/* header begin */
#gog{padding:3px 8px 0;background:#fff}
#gbar,#guser{padding-top:1px !important}
#gbar{float:left;height:22px}
#guser{padding-bottom:7px !important;text-align:right}
.gbh{border-top:1px solid #c9d7f1;font-size:1px;height:0;position:absolute;top:24px;width:100%}
.gb1{margin-right:.5em;zoom:1}
/* header end */

/* header center footer */
#header ,#centers ,#footer{ margin:0 auto; clear:both;}

/* footer */
.footer{margin: 20px 0;text-align: center;line-height: 24px;color:#333;}
.footer a{color:#333;}


/* ====================== wrap ====================== */
#wrap{margin:0 auto;}
.wrap { width:950px;overflow:hidden;position:relative; margin-top:26px; padding-top: 26px; }


/* ====================== main content ====================== */
.main { background:url(http://img3.ni9ni.com/book/kh/2012/0118/dushu_mainBg.gif) left repeat-y; width:950px; overflow:hidden; border-top:1px solid #c2d9f2; }

/* === main content left === */
.main .mainL { width:690px; float:left; }
/* === main content right === */
.main .mainR { width:250px; float:right; border-top:10px solid #fff; }
.main .mainR ul { padding:10px 15px; }
.main .mainR li { padding-left:9px; line-height:22px; }
.main .mainR li a { color:#009; }
.main .mainR li a:hover { color:#f00; }
/* 二级导航 */
.subMenu { margin:8px 24px 0; border-bottom:1px solid #c8d8f2; line-height:37px; height:33px; overflow:hidden; }
.subMenu img { float:left; margin-right:10px; }
.subMenu a { color:#023296; }
.subMenu a:hover { color:#f00; }

#content {
	float: left;
	padding-top: 32px;
	
}
#content h2 {
	margin: 0 0 18px
}
.entry {
	margin: 0 30px 20px
}
.entry p {
	line-height: 20px;
	padding: 0 0 18px
}
.entry a{color:#009193;text-decoration: none}
.entry h2 a:hover{color:#1BA6B2;}

.entry .info {
	border-bottom: 1px solid #F3F4F4;
	border-top: 1px solid #F3F4F4;
	font-size: .9em;
	margin-top: -3px;
	position: relative;
	background-color: #FAFAFA;
	padding: 3px
}
.entry .info a {
	border-right: 1px solid #949494;
	margin-right: 6px;
	padding-right: 9px
}
.entry .info em {
	font-style: normal;
	padding-right: 6px
}
.entry .author, .entry .editlink a {
	border-left: 1px solid;
	border-right: medium none;
	margin: 0;
	padding: 0 0 0 10px
}

.entry .info .date {
	
	padding-left: 15px
}
.entry .moretext {
	/*background: transparent url(http://img3.ni9ni.com/book/kh/2012/0307/content_more.gif) no-repeat scroll 100% 3px;*/
	text-decoration: none;
	padding: 0 23px 0 0
}
.entry .info .editlink a, #comments li .editlink a {
	
	border: medium none;
	display: block;
	font: 1px/0 Arial;
	height: 14px;
	text-indent: -9999px;
	width: 14px;
	margin: 0;
	padding: 0
}
.entry .info .editlink a: hover,#comments li .editlink a: hover {
	background: transparent none repeat scroll 0 0
}
.entry .info .author {
	
	padding-left: 23px;
	border-color: #949494
}

/*左侧列表
.list03{ width:542px; padding:0 0 0 10px; }
.list03 ul{float:left;width:542px;  padding:20px 0 8px 20px;border-bottom:1px #ddd dashed}
.list03 li{float:left; height:32px;}
.list03 li .txt01{ float:left; width:426px; font-size:14px;}
.list03 li .time01{ float:left; width:116px;font-size:12px;color:#9A9A9A;}*/
.box02{ width:550px; padding:16px 0; }
/*翻页*/
.pages {height:30px; text-align:center; line-height:30px; margin:10px 0 5px;}
.pages span, .pages a {margin-right:4px; padding:2px 6px;}
.pages span {border:1px solid #D4D9D3; color:#979797;}
.pages a {border:1px solid #9AAFE4;}
.pages a:link {color:#3568B9; text-decoration:none;}
.pages a:visited {color:#3568B9; text-decoration:none;}
.pages a:hover {color:#000; text-decoration:none; border:1px solid #2E6AB1;}
.pages a:active {color:#000; text-decoration:none; border:1px solid #2E6AB1;}
.pages a.now:link, .pages a.now:visited, .pages a.now:hover, .pages a.now:active {text-decoration:none; background:#2C6CAC; border:1px solid #2C6CAC; color:#fff; cursor:default;}

-->
</style>
<script language="javascript" type="text/javascript">
<!--//--><![CDATA[//><!--
function doZoom(l_type) {
	var l_old = document.getElementById("zoomtext").style.fontSize;
	if (null==l_old || undefined==l_old || "undefined"==l_old || ""==l_old) {
		var l_size = 16;
	}else {
		var l_size = l_old.replace("px","");
	}
	if ("-"==l_type) l_size = (l_size*1 - 2);
	else l_size = (l_size*1 + 2);
	l_size = l_size < 0 ? 0 : l_size;
	
	document.getElementById("zoomtext").style.fontSize = l_size+"px";
}
//--><!]]>
</script>
</head>
<body>
<!--#include file="/ssi/header.ssi"-->
<div class="clearit"></div>
<div id="wrap" class="wrap">
  <div class="main">
    <div class="mainL">
      <div class="subMenu"> ${_PROJECT_name_cn}首页 </div>
      <div id="content">
        ${文章摘要列表}
        <div class="pages">${翻页链接}</div>
      </div>
      <div class="mainR">
        <!--#include virtual="/ads/common/1.html"-->
      </div>
    </div>
  </div>
</div>
<!--#include virtual="/ssi/footer.ssi"-->
</body>
</html>' where `tbl_id`= (select id from dpps_table_def where name_cn='频道首页');

-- 增加‘频道首页’表的3个字段 DELETE FROM `dpps_field_def` WHERE t_id=(select id from dpps_table_def where name_cn='频道首页') and name_eng in ('aups_f119','aups_f120','pagesize'); ALTER TABLE dpps_field_def AUTO_INCREMENT =1 ;
INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES
( (select id from dpps_table_def where name_cn='频道首页'), 'pagesize', '每页条目数', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'NO', '', '', 'SMALLINT', 'Application::CodeResult', '3', '', NULL, '10', 'use', '[code]
// 实际上使用的时候只能是使用默认值，此处算法可能还未执行
return 10;', '0', 1000, 'none', NULL),
( (select id from dpps_table_def where name_cn='频道首页'), 'aups_f119', '文章摘要列表', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Application::CodeResult', '255', '', NULL, NULL, 'use', '[sql]
select {文档标题},{创建日期},{创建时间},{文档发布成html的外网url},{正文},{权重},{来源} from {正文页}

[code]<?php
$dbR = $a_arr[''dbR''];

if (isset($a_arr[''f_def_duo''][''pagesize''][''default''])){
	$l_pageSize = $a_arr[''f_def_duo''][''pagesize''][''default''] + 0;
}else {
	$l_pageSize = 10;
}
if (isset($a_arr[''f_data''][''id''])){
	$l_p = $a_arr[''f_data''][''id''] + 0; 	// 当前页码用文档的id。因为一般首页都是从id为1开始
}else {
	$l_p = 1;
}
$l_p = ($l_p<1)?1:$l_p;

$offset = ($l_p-1) * $l_pageSize;
$dbR->table_name = ''{正文页}'';
$l_rlt = $dbR->getAlls(''where status_="use" order by createdate desc, createtime desc limit ''.$offset.'','' . $l_pageSize, ''{文档标题},{正文},{创建日期},{创建时间},{文档发布成html的外网url},{来源}'');

$html = '''';
// 逐项进行处理, 特别是要进行截字
if (!empty($l_rlt)) {
	foreach ($l_rlt as $l_v){
		$l_title = $l_v[''{文档标题}''];
		$l_url   = $l_v[''{文档发布成html的外网url}''];
		$l_date  = $l_v[''{创建日期}''];
		$l_time  = $l_v[''{创建时间}''];
		$l_laiy  = $l_v[''{来源}''];
		$l_zhy   = trim($l_v[''{正文}'']);
		// 判断是否有继续阅读
		$l_sub_zw = cn_substr($l_zhy,1000);
		if ( $l_zhy != $l_sub_zw) {
			$l_more  = '' <a href="''.$l_url.''#more" class="more-link"><span class="moretext"> 继续阅读<img src="http://img3.ni9ni.com/book/kh/2012/0307/content_more.gif" /></span></a>'';
		}else {
			$l_more = '''';
		}
		
		$html .= ''<div class="entry">
          <h2><a href="''.$l_url.''" title="''.$l_title.''">''.$l_title.''</a></h2>
          <p>'' . $l_zhy . $l_more.'' </p>
          <p class="info">抢沙发<em class="date">''.$l_date." ".$l_time.''</em> <em class="author">''.$l_laiy.''</em> </p>
        </div>
        '';
	}
}

return $html;', '0', 1000, 'none', NULL),
( (select id from dpps_table_def where name_cn='频道首页'), 'aups_f120', '翻页链接', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Application::CodeResult', '255', '', NULL, NULL, 'use', '[sql]
select {文档标题},{创建日期},{创建时间},{文档发布成html的外网url},{摘要},{权重} from {正文页}

[code]<?php
require_once("common/Pager.cls.php");
$dbR = $a_arr[''dbR''];

// 总数
$dbR->table_name = ''{正文页}'';
$sql_where = ''where status_="use"'';
$itemSum = $dbR->getCountNum($sql_where);

// 页面条目数
if (isset($a_arr[''f_def_duo''][''pagesize''][''default''])) $l_pageSize = $a_arr[''f_def_duo''][''pagesize''][''default''] + 0;
$l_pageSize = ($l_pageSize<1)?1:$l_pageSize;	// 保证非0, 后面的除法避免除数为0

// 第几页
if (isset($a_arr[''f_data''][''id''])) $l_p = $a_arr[''f_data''][''id''] + 0; 	// 当前页码用文档的id。因为一般首页都是从id为1开始
else $l_p = 1;
$l_p = ($l_p>ceil($itemSum/$l_pageSize))?ceil($itemSum/$l_pageSize):$l_p;
$l_p = ($l_p<1)?1:$l_p;


$l_flag = ''p'';
//$page = new Pager(''index.shtml'',$itemSum,$l_pageSize,$l_p,$l_flag,array($l_flag),'''');
$page = new Pager(''index.shtml'',$itemSum,$l_pageSize,$l_p,$l_flag,array($l_flag));
$html = $page->getBar();

return $html;', '0', 1000, 'none', NULL);

-- 增加正文页的一个算法，用于发布频道首页
INSERT INTO `dpps_field_def` (`t_id`, `creator`, `createdate`, `createtime`, `name_eng`, `name_cn`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES
((select id from dpps_table_def where name_eng='aups_t002'), "admin", DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'aups_f121', '相关发布-频道首页', '0', 'YES', '', '', 'VARCHAR', 'Application::PostInPage', '255', '', NULL, NULL, 'use', 'allow=post_1\r\n\r\n[post_1]\r\nwhere={频道首页}:id=1', '0', 1000, 'none', NULL);

-- 栏目页增加生成首页ssi碎片
update `dpps_field_def` set `arithmetic`='[sql]
select {栏目配置}.{级别},{栏目配置}.{栏目名称},{正文页}.{文档标题},{正文页}.status_ from {栏目配置},{正文页}

[code]<?php
if (!function_exists(''shouye_ssi'')) {
	include_once(''common/lib/xxtea.cls.php'');
	function shouye_ssi(&$a_arr,$arr, $num=5){
		$str = "";
		if (is_array($arr) && count($arr)>0) {
			$str .= ''<ul>'';
			foreach ($arr as $l_k => $val){
				if ($l_k>=$num) break;
				if (false===strpos($val[''url_1''],''://'')) {
					$val[''url_1''] = rtrim($a_arr["p_def"]["waiwang_url"]," /")."/".ltrim($val[''url_1'']," /");
				}
				$str .= ''
						<li><a href="''.$val[''url_1''].''" title="''.str_replace(''"'',''&quot;'',$val[''{文档标题}'']).''" target="_blank">''.cn_substr($val[''{文档标题}''],32).''</a></li>'';
			}
			$str .= ''<br /><br /></ul>'';
			$date_info = "Page Update: ".date("Y-m-d H:i:s")."\\r\\n";
			$ip_info = "Server IP: ".getLocalIP()."\\r\\n";
			$whole_info = $date_info.$ip_info. __FILE__ ." ".__FUNCTION__."\\r\\n";
			$xxtea = new XXTEA();
			$xxtea->setOprStrKey($whole_info);
			$encrypt_info = base64_encode($xxtea->XXTEA_Encrypt());
			$str .= ''
			<!-- ''.date("Y-m-d H:i:s"). $encrypt_info.'' -->'';
		}
		
		file_put_contents( rtrim($a_arr[''p_def''][''bendi_uri'']," /").''/ssi/''.$a_arr[''p_def''][''db_name''].''.ssi'',$str);
		return '''';
	}
}

function getProjectListHtml2($arr, $num=5){
	$str = "";
	if (is_array($arr) && count($arr)>0) {
		$l_arr = array_chunk($arr,$num);
		foreach ($l_arr as $l__a){
			$str .= ''<ul>'';
			foreach ($l__a as $val){
				$str .= ''
	   				<li><span class="txt01"><a href="''.$val[''url_1''].''" target="_blank">''.$val[''{文档标题}''].''</a></span><span class="time01">(''.$val["createdate"].'' ''.substr($val["createtime"],0,5).'')</span></li>'';
			}
			$str .= ''</ul>'';
		}
	}
	return $str;
}

$dbR = &$a_arr[''dbR''];

$name = ''${栏目名称}'';
$level = '''';
if (!empty($name)) {
	$dbR->table_name = ''{栏目配置}'';
	$level = $dbR->GetOne("where {栏目配置}.{栏目名称}=''$name'' order by id desc limit 1 ",''{栏目配置}.{级别}'');
	//echo $dbR->getSQL();
	//print_r($level);
	if (!empty($level)) {
		$level = $level[''{级别}''];
	}else {
		$level = '''';
	}
}

if (1==$level){
 	$sql = "select {正文页}.{文档标题},url_1,createdate,createtime from {正文页} where {正文页}.{所属栏目}=''$name'' and {正文页}.status_=''use'' order by createdate desc,createtime desc limit 200";
}else if (2 == $level){
	$sql = "select {正文页}.{文档标题},url_1,createdate,createtime from {正文页} where {正文页}.{所属子栏目}=''$name'' and {正文页}.status_=''use'' order by createdate desc,createtime desc limit 200";
}

$column_path2=$dbR->query_plan($sql);
//print_r($column_path2);exit;
if (!empty($column_path2)) {
	$html = getProjectListHtml2($column_path2);
	shouye_ssi($a_arr,$column_path2,6);
}else {
	$html = '''';
}


return $html;' where `name_eng`='aups_f095' and `t_id`=(select id from dpps_table_def where name_eng='aups_t007');
