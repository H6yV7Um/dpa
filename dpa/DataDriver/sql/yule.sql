-- 表定义表进行‘频道首页’的算法 可以完全照搬 it.sql
-- update `dpps_table_def` set `arithmetic`='' where `name_cn`='频道首页';

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

-- aups_f095 栏目页的算法 可以完全照搬 it.sql
-- update `dpps_field_def` set `arithmetic`='' where `name_eng`='aups_f095' and `t_id`=(select id from dpps_table_def where name_eng='aups_t007');

-- 增加‘频道首页’表的3个字段 DELETE FROM `dpps_field_def` WHERE t_id=(select id from dpps_table_def where name_cn='频道首页') and name_eng in ('aups_f119','aups_f120','pagesize'); ALTER TABLE dpps_field_def AUTO_INCREMENT =1 ;
INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES
( (select id from dpps_table_def where name_cn='频道首页'), 'aups_f119', '文章摘要列表', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Application::CodeResult', '255', '', NULL, NULL, 'use', '[sql]
select id,{文档标题},{创建日期},{创建时间},{文档发布成html的外网url},{摘要},{权重},{支持数} from {正文页}

[code]<?php
$l_table_name = ''{正文页}'';
// 生成js使用的当前文章id序列
if (!function_exists(''yule_js_ssi_'')) {
	include_once("common/Files.cls.php");
	function yule_js_ssi_(&$a_arr, $arr, $page_num){
		if (!function_exists(''json_decode'')) {
			require_once(''JSON.php'');
			$json = new Services_JSON();
		}
		$str = "";
		if (!empty($arr)) {
			if (function_exists(''json_decode'')) {
				$str = json_encode($arr);
			}else{
				$str = $json->encode($arr);
			}
			$str = "var l_docs_list=" .$str.";";	// 用对象
		}
		$files = new Files();
		$files->overwriteContent($str, rtrim($a_arr[''p_def''][''bendi_uri'']," /").''/ssi/yule_shouye/''.$page_num.''.ssi'');
		return '''';
	}
}

if (!function_exists(''get_article_p_t_id_'')) {
	function get_article_p_t_id_(&$dbR, $a_table_name){
		$dbR->table_name = "dpps_table_def";
		$l_rlt = $dbR->getOne("where name_eng=''" .$a_table_name. "''", ''id, p_id'');
		return array("p_id"=>$l_rlt["p_id"],"t_id"=>$l_rlt["id"]);
	}
}


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
$dbR->table_name = $l_table_name;
$l_rlt = $dbR->getAlls(''where status_="use" order by createdate desc, createtime desc limit ''.$offset.'','' . $l_pageSize, ''id,{文档标题},{摘要},{创建日期},{创建时间},{文档发布成html的外网url},{支持数}'');

$l_p_t_arr = get_article_p_t_id_($dbR, $l_table_name);	// 拼装文章的p_t_id字符串

$l_ssi_js_ids = array();
$html = '''';
// 逐项进行处理, 特别是要进行截字
if (!empty($l_rlt)) {
	foreach ($l_rlt as $l_v){
		$l_title = $l_v[''{文档标题}''];
		$l_url   = $l_v[''{文档发布成html的外网url}''];
		$l_date  = $l_v[''{创建日期}''];
		$l_time  = $l_v[''{创建时间}''];
		$l_zhy   = $l_v[''{摘要}''];
		$l_zcs   = $l_v[''{支持数}''];
		// 判断是否有继续阅读
		if (false!==strpos($l_zhy,''<span class="moretext"></span>'')) {
			$l_more  = '' <a href="''.$l_url.''#more" class="more-link"><span class="moretext"> 继续阅读<img src="http://img3.ni9ni.com/book/kh/2012/0307/content_more.gif" /></span></a>'';
		}else {
			$l_more = '''';
		}
		
		// 判断初始支持率, 需要更新数据表
		if ($l_zcs<=0) {
			if (isset($a_arr[''dbW''])) {
				$dbW = $a_arr[''dbW''];
			}else {
				$dbW = new DBW($a_arr[''p_def'']);
			}
			$dbW->table_name = ''{正文页}'';
			$l_data_arr = array(
				''{支持数}'' => mt_rand(1, 10)
			);
			$dbW->updateOne($l_data_arr, " id=". $l_v[''id'']);
		}
		
		$l_p_t_id_str = $l_p_t_arr["p_id"].''_''.$l_p_t_arr["t_id"].''_''.$l_v["id"];
		$l_ssi_js_ids[$l_p_t_id_str] = $l_zcs;	// 本页组成的id列表
		
		$l_cai_zhenshi = $l_zcs_zhenshi = 0;
		// 从redis中获取顶数
		if (extension_loaded(''redis'')) {
			$redis = new redis();
			$redis->connect(''127.0.0.1'',6379);
			$l_zcs_zhenshi = $redis->get("vote_".$l_p_t_id_str."_up")+0;	// 顶
			$l_cai_zhenshi = $redis->get("vote_".$l_p_t_id_str."_dn")+0;	// 踩
			$l_liuyan  	   = $redis->get("liuyan_".$l_p_t_id_str."_num")+0;	// 留言数
		}
		
		$html .= ''<div class="entry">
          <h2><a href="''.$l_url.''" title="''.$l_title.''">''.$l_title.''</a></h2>
          <p>'' . $l_zhy . $l_more.'' </p>
          <p class="info"><span id="support_before_''.$l_p_t_id_str.''" style="display:inline; cursor:pointer; color:#009193" onclick="vote_(\''''.$l_p_t_id_str.''\'',1)" data-valid-url="http://comment.ni9ni.com/add/">顶[<span style="color:#FF0000" id="support_before_num_''.$l_p_t_id_str.''">''. ($l_zcs+$l_zcs_zhenshi).''</span>]</span>
		  				  <span id="support_after_''.$l_p_t_id_str.''"  style="display:none">顶[<span style="color:#FF0000" id="support_after_num_''.$l_p_t_id_str.''">0</span>]</span>
						  <span>&nbsp;</span>
						  <span id="oppose_before_''.$l_p_t_id_str.''" style="display:inline; cursor:pointer; color:#009193" onclick="vote_(\''''.$l_p_t_id_str.''\'',-1)">踩[<span style="color:#FF0000" id="oppose_before_num_''.$l_p_t_id_str.''">''. $l_cai_zhenshi.''</span>]</span>
		  				  <span id="oppose_after_''.$l_p_t_id_str.''"  style="display:none">踩[<span style="color:#FF0000" id="oppose_after_num_''.$l_p_t_id_str.''">0</span>]</span>
						  <span>&nbsp;</span>
						  <span style="cursor:pointer; color:#009193" onclick="return liuyan_display(\''''.$l_p_t_id_str.''\'');"><span id="liuyan_''.$l_p_t_id_str.''_num">''. $l_liuyan.''条流言</span></span>
						  <em class="date">''.$l_date." ".$l_time.''</em></p>
		  <div class="replies" id="mainReplies_''.$l_p_t_id_str.''" style="display:none"></div>
        </div>
        '';
	}
}

// 需要生成页面js使用的ssi碎片
if (!empty($l_ssi_js_ids)) {
	yule_js_ssi_($a_arr, $l_ssi_js_ids, $l_p);
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

return $html;', '0', 1000, 'none', NULL),
( (select id from dpps_table_def where name_cn='频道首页'), 'pagesize', '每页条目数', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'NO', '', '', 'SMALLINT', 'Application::CodeResult', '3', '', NULL, '10', 'use', '[code]
// 实际上使用的时候只能是使用默认值，此处算法可能还未执行
return 10;', '0', 1000, 'none', NULL);

-- 增加正文页的一个算法，用于发布频道首页
INSERT INTO `dpps_field_def` (`t_id`, `creator`, `createdate`, `createtime`, `name_eng`, `name_cn`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES
((select id from dpps_table_def where name_eng='aups_t002'), "admin", DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'aups_f121', '相关发布-频道首页', '0', 'YES', '', '', 'VARCHAR', 'Application::PostInPage', '255', '', NULL, NULL, 'use', 'allow=post_1\r\n\r\n[post_1]\r\nwhere={频道首页}:id=1', '0', 1000, 'none', NULL);

-- 栏目配置中增加‘笑话’栏目配置
INSERT INTO `aups_t003` (`creator`, `createdate`, `createtime`, `expireddate`, `audited`, `status_`, `flag`, `arithmetic`, `unicomment_id`, `published_1`, `url_1`, `aups_f070`, `aups_f071`, `aups_f072`, `aups_f073`, `aups_f074`, `aups_f075`, `aups_f076`, `aups_f077`, `aups_f078`) VALUES
('admin', '2012-03-17', '16:47:43', '0000-00-00', '0', '0', 0, NULL, NULL, '0', '/yule/5/2012/0317/9.shtml', '笑话', '1', 'xiaohua', '/xiaohua/', 'http://e.ni9ni.com/yule/xiaohua/', 100, '笑话,娱乐', NULL, NULL);

-- 栏目页增加记录
INSERT INTO `aups_t007` (`creator`, `createdate`, `createtime`, `mender`, `menddate`, `mendtime`, `expireddate`, `audited`, `status_`, `flag`, `arithmetic`, `unicomment_id`, `published_1`, `url_1`, `aups_f090`, `aups_f097`) VALUES
('admin', '2012-03-17', '16:48:15', NULL, NULL, NULL, '0000-00-00', '0', '0', 0, NULL, NULL, '0', '/yule/xiaohua/index.shtml', NULL, '笑话');

-- 最后还需要手动发布一下 栏目页 和 首页
