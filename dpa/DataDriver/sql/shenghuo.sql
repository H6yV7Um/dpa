-- 栏目配置中增加一个字段，字段定义表中也需要增加一条记录
alter table `aups_t002` add `s_shu_chengshi` varchar(60) default NULL COMMENT '所属城市, 可能是地级市，也可能是直辖市。地级市以上包括直辖市, 如可能是孝感，武汉，北京等能作为首页的';
INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES 
((select id from dpps_table_def where name_eng='aups_t002'), 's_shu_chengshi', '所属城市', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '60', '', NULL, NULL, 'use', NULL, '0', 1109, 'none', '所属城市, 可能是地级市，也可能是直辖市。地级市以上包括直辖市, 如可能是孝感，武汉，北京等能作为首页的');

alter table `aups_t002` add `article_display_type` enum('zhiding','gundong','putong') NOT NULL DEFAULT 'putong' COMMENT '显示类型, 滚动区域，置顶，还是普通';
INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES
((select id from dpps_table_def where name_eng='aups_t002'), 'article_display_type', '显示类型', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'NO', '', '', 'ENUM', 'Form::TextField', '''zhiding'',''gundong'',''putong''', '', NULL, 'putong', 'use', NULL, '0', 1000, 'none', '显示类型, 滚动区域，置顶，还是普通');


alter table `aups_t003` add `zhantai_lanmu` varchar(255) default NULL COMMENT '在站台网的栏目, 可能是id，也可能是拼音，总之用于拼装站台网的url所用';
INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES
((select id from `dpps_table_def` where name_eng='aups_t003'), 'zhantai_lanmu', '站台网的栏目', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '255', '', NULL, NULL, 'use', NULL, '0', 1000, 'none', '在站台网的栏目, 可能是id，也可能是拼音，总之用于拼装站台网的url所用');

-- 城市列表 增加三个字段
alter table `aups_t012` add `name_eng_58` varchar(100) DEFAULT NULL COMMENT '58同城的拼音';
INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES
((select id from `dpps_table_def` where name_cn='城市列表'), 'name_eng_58', '58同城的拼音', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '100', '', NULL, NULL, 'use', NULL, '0', 1000, 'none', '58同城的拼音');
alter table `aups_t012` add `name_eng_ganji` varchar(100) DEFAULT NULL COMMENT '赶集网的拼音';
INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES
((select id from `dpps_table_def` where name_cn='城市列表'), 'name_eng_ganji', '赶集网的拼音', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '100', '', NULL, NULL, 'use', NULL, '0', 1000, 'none', '赶集网的拼音');
alter table `aups_t012` add `name_eng_zhantai` varchar(100) DEFAULT NULL COMMENT '站台网的拼音';
INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES
((select id from `dpps_table_def` where name_cn='城市列表'), 'name_eng_zhantai', '站台网的拼音', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '100', '', NULL, NULL, 'use', NULL, '0', 1000, 'none', '站台网的拼音');



-- 模板的url地址和文章内容
update `dpps_tmpl_design` set `default_url`='/${所属城市}/${栏目路径}/${YYYY}${mm}${dd}/${HH}${ii}${id}.shtml', `default_html`='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[${_PROJECT_id},${_PROJECT_TABLE_id},${id}] published at ${_SYSTEM_date} ${_SYSTEM_time} by ${_USER_id}-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="${文档标题},${_PROJECT_db_name}" />
<meta name="description" content="${文档标题},${_PROJECT_db_name}" />
<title>${文档标题}_${_PROJECT_name_cn}_${_PROJECT_website_name_cn}</title>
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
/* 文章标题 */
.mainContent { border-bottom:1px solid #c2d9f2; }
.mainContent h1 { line-height:38px; text-align:center; color:#03005c; font-size:20px; font-weight:bold; margin-top:18px; }

.mainContent .secTitle { text-align:center; line-height:25px; margin-bottom:10px; }
.mainContent .secTitle em { font-style:normal; margin:0 15px; }



/* 每篇日志的总框架 */
/*.textbox{margin-bottom: 8px;border: 1px solid #bad1da;background-color: #F7FBFF;}*/

.textbox-content{
	word-wrap: break-word;
	padding: 10px;
}
.tags {
	padding-top: 1px;
	padding-bottom: 3px;
	font-size: 11px;
	color: #4c9bb0;
	text-align:left;
	padding-left: 17px;
	background-color: #eaeff0;
	border-bottom: 1px solid #bad1da;
}
/****** UBB Code Custom Styles ******/
.code {
	word-wrap: break-all;
	border-left: 3px dashed #4c9bb0;
	background-color: #EBEBEB;
	color: #000000;
	margin: 5px;
	padding: 10px;
}
.quote {
	border-left: 0px dashed #D6C094;
	margin: 10px;
    margin-bottom:0px;
	border: 1px dashed #00a0c6;
}
.quote-title {
	background-color: #edf4f6;
	border-bottom: 1px dashed #00a0c6 !important;
	border-bottom: 1px dotted #00a0c6;
	padding: 5px;
	font-weight: bold;
	color: #4c9bb0;
}
.quote-title img {
	padding-right: 3px;
}
.quote-content {
	word-wrap: break-all;

	color: #000000;
	padding: 10px; 
	background-color: #ffffff;
	border: 1px dashed #edf4f6;
	border-top: 0px;
}

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
      <div class="mainContent">
        <div class="subMenu"> <a href="${_PROJECT_waiwang_url}/${_PROJECT_db_name}/">${_PROJECT_name_cn}</a> &gt; ${栏目显示} ${专题显示} &gt; 正文 </div>
        <h1>${文档标题}</h1>
        <p class="secTitle"> <em>${创建年份}年${创建月份}月${创建日}日 ${创建小时}:${创建分钟}</em> <em>${来源}</em> [ <a href="javascript: doZoom(''+'');">大</a> | <a href="javascript: doZoom(''-'');">小</a> ]</p>
        <div class="textbox-content" id="zoomtext">
		联系EMail: ${作者}<br />
        手机/电话: ${摘要} <br />
		${正文}
		</div>
      </div>
    </div>
    <div class="mainR">
      <!--#include virtual="/ads/common/1.html"-->
    </div>
  </div>
</div>
<!--#include virtual="/ssi/footer.ssi"-->
</body>
</html>' where `tbl_id` = (select id from dpps_table_def where name_eng='aups_t002');


-- 另外还要增加三个字段的算法，非入库字段
INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES
((select id from `dpps_table_def` where `name_cn`='频道首页'), 'shengfen_option', '省份选项', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Application::CodeResult', '255', '', NULL, NULL, 'use', '[code]<?php
$l_options = ''<option value="0">-请选择-</option>'';	// 结果
// 此处涉及到共用数据库
$l_name0_r = $GLOBALS[''cfg''][''SYSTEM_DB_DSN_NAME_R''];			
$dbR = new DBR($l_name0_r);
$l_err = $dbR->errorInfo();
if ($l_err[1]>0){
	// 数据库连接失败后
	echo date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
	return $l_options;
}
$dbR->table_name = TABLENAME_PREF."project";
$p_arr_gongyong = $dbR->GetOne("where name_cn=''共用数据''");
if (PEAR::isError($p_arr_gongyong) || PEAR::isError($p_arr_shenghuo)) {
	echo " error message： " .$p_arr->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
	return $l_options;
}

require_once("PinYin.class.php");
if (''WIN'' === strtoupper(substr(PHP_OS, 0, 3)) ) {
	$py_data_path = "D:/www/pear/py.dat";
}else {
	$py_data_path = "/usr/local/webserver/php/lib/php/py.dat";
}
$pinyin = new PinYin("UTF-8",$py_data_path);

// 获取城市列表
$dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr_gongyong);
$dbR->dbo = &DBO(''gongyong'', $dsn);
$dbR->table_name = "region_sheng";
$l_city = $dbR->getAlls("where code_city=0 and code_quxian=0 and status_=''use'' order by pingyin_shouzimu ", "id,name_cn,code_sheng,pingyin_shouzimu");


// 进行拼装
if (!PEAR::isError($l_city)) {
	foreach ($l_city as $l_c) {
		if (empty($l_c["pingyin_shouzimu"])) {
			$l_py = $pinyin->getPY($l_c["name_cn"]);
			$l_c["pingyin_shouzimu"] = substr($l_py,0,1);
		}
		$l_options .= ''<option value="''.$l_c["code_sheng"].''">''.strtoupper($l_c["pingyin_shouzimu"])." ".$l_c["name_cn"].''</option>'';
	}
}
return $l_options;', '0', 1000, 'none', '省份选项, 用于省份的下拉框, 主要是<option>标签');

INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES
((select id from `dpps_table_def` where `name_cn`='频道首页'), 'city_json', '城市下拉框js数据', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Application::CodeResult', '255', '', NULL, NULL, 'use', '[code]<?php
$l_options = '''';	// 结果
// 此处涉及到共用数据库
$l_name0_r = $GLOBALS[''cfg''][''SYSTEM_DB_DSN_NAME_R''];
$dbR = new DBR($l_name0_r);
$dbR->table_name = TABLENAME_PREF."project";
$p_arr_gongyong = $dbR->GetOne("where name_cn=''共用数据''");

// 获取省份列表
$dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr_gongyong);
$dbR->dbo = &DBO(''gongyong'', $dsn);
$dbR->table_name = "region_sheng";
$l_shengfen = $dbR->getAlls("where code_quxian=0 and status_=''use'' and name_cn not in (''市辖区'', ''县'') ", "id,name_eng,name_cn,code_sheng,code_city,code_quxian,pingyin_shouzimu");

// 循环一下，重新处理
$l_zhixiashi = array(11,12,31,50);
$for_json = array();
foreach ($l_shengfen as $vals){
	if (0==$vals[''code_city'']) {
		// 省级名称不应该留下, 而直辖市应该留下
		if (!in_array($vals[''code_sheng''],$l_zhixiashi)) {
			continue ;
		}
	}else {
		if (false!==strpos($vals[''name_cn''],''直辖'')) {
			continue ;
		}
	}
	$for_json[$vals[''code_sheng'']][$vals[''code_city'']] = $vals;
}

$l_options = getJson($for_json,$name="2","name_cn","pingyin_shouzimu","name_eng");

return $l_options;', '0', 1000, 'none', '城市下拉框js数据, json串');


INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES
((select id from `dpps_table_def` where `name_cn`='频道首页'), 'city_pinyin_list', '城市拼音列表', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Application::CodeResult', '255', '', NULL, NULL, 'use', '[code]<?php
$l_options = '''';	// 结果

$l_name0_r = $GLOBALS[''cfg''][''SYSTEM_DB_DSN_NAME_R''];
$dbR = new DBR($l_name0_r);
$dbR->table_name = TABLENAME_PREF."project";
$p_arr_gongyong = $dbR->GetOne("where name_cn=''共用数据''");
if (PEAR::isError($p_arr_gongyong) || PEAR::isError($p_arr_shenghuo)) {
	echo " error message： " .$p_arr->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
	return $l_options;
}

// 获取省份列表
$dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr_gongyong);
$dbR->dbo = &DBO(''gongyong'', $dsn);
$dbR->table_name = "region_sheng";
$l_shengfen = $dbR->getAlls("where code_quxian=0 and status_=''use'' and name_cn not in (''市辖区'', ''县'') ", "id,name_eng,name_cn,code_sheng,code_city,code_quxian,pingyin_shouzimu");

// 循环一下，重新处理
$l_zhixiashi = array(11,12,31,50);
$l_sheng_name_cn = array();
$for_json = array();
foreach ($l_shengfen as $vals){
	if (0==$vals[''code_city'']) {
		$l_sheng_name_cn[$vals[''code_sheng'']] = "直辖市: ".$vals[''name_cn''];
		// 省级名称不应该留下, 而直辖市应该留下
		if (!in_array($vals[''code_sheng''],$l_zhixiashi)) {
			$l_sheng_name_cn[$vals[''code_sheng'']] = "隶属于 ".$vals[''name_cn'']." 的 :";
			continue ;
		}
	}else {
		if (false!==strpos($vals[''name_cn''],''直辖'')) {
			continue ;
		}
	}
	
	$for_json[$vals[''pingyin_shouzimu'']][] = $vals;
}

ksort($for_json);

foreach ($for_json as $l_py=>$l_vals){
	$l_py = strtoupper(substr($l_py,0,1));
	$l_options .= ''
	<dt>''.$l_py.''.</dt>'';
	$l_options .= ''<dd>'';
	
	// 需要分解成一些块
	
	if ("H"==$l_py) $l_num = 14;
	else if ("Q"==$l_py) $l_num = 12;
	else if ("X"==$l_py) $l_num = 16;
	else $l_num = 18;
	$l_arr = array_chunk($l_vals, $l_num);
	if (count($l_arr)>1) $l_br = ''<br />'';
	else $l_br = "";
	foreach ($l_arr as $l_k => $l__a){
		foreach ($l__a as $vals){
			$l_options .= ''<a href="http://shenghuo.ni9ni.com/''.trim($vals[''name_eng'']).''/" title="''.$l_sheng_name_cn[$vals[''code_sheng'']]." ".$vals[''name_cn''].''" onclick="co(\\''''.trim($vals[''name_eng'']).''\\'')">''.preg_replace("/市$/","",$vals[''name_cn'']).''</a>'';
		}
		$l_options .= $l_br;
	}
	// 去掉多余的<br />
	if (""!=$l_br) {
		if ($l_br==substr($l_options,0 - strlen($l_br))) {
			$l_options = substr($l_options,0, 0 - strlen($l_br));
		}
	}
	$l_options .= ''</dd>
	'';
}

return $l_options;', '0', 1000, 'none', '城市拼音列表, 用于生活频道首页各个地名点击进入');


CREATE TABLE IF NOT EXISTS `aups_t012` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `creator` varchar(100) NOT NULL DEFAULT '0' COMMENT '创建者',
  `createdate` date NOT NULL DEFAULT '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL DEFAULT '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) DEFAULT NULL COMMENT '修改者',
  `menddate` date DEFAULT NULL COMMENT '修改日期',
  `mendtime` time DEFAULT NULL COMMENT '修改时间',
  `expireddate` date NOT NULL DEFAULT '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否审核',
  `status_` enum('use','stop','test','del','scrap') NOT NULL DEFAULT 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL DEFAULT '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) DEFAULT NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) DEFAULT NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最近修改时间',
  `s_shu_xingqiu_id` int(10) NOT NULL COMMENT '所属星球ID',
  `s_shu_area_id` int(10) NOT NULL COMMENT '所属国家或地区ID',
  `name_eng` varchar(100) NOT NULL COMMENT '英文名称',
  `name_cn` varchar(100) NOT NULL COMMENT '中文名称',
  `pingyin_shouzimu` char(1) NOT NULL COMMENT '拼音首字母',
  `code_sheng` smallint(3) NOT NULL COMMENT '省级行政区号',
  `code_city` smallint(3) NOT NULL COMMENT '市级行政区号',
  `code_quxian` smallint(3) NOT NULL DEFAULT '0' COMMENT '区县级行政区号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_eng` (`name_eng`),
  UNIQUE KEY `s_shu_xingqiu_id` (`s_shu_xingqiu_id`,`s_shu_area_id`,`code_sheng`,`code_city`,`code_quxian`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='城市列表, 例如北京、天津、武汉、长沙、石家庄。主要是赶集网、58同城、站台网的一些地级市以上城市或者有影响的县级市';

ALTER TABLE `aups_t012` ADD `s_shu_xingqiu_id` int(10) NOT NULL COMMENT '所属星球ID';
ALTER TABLE `aups_t012` ADD `s_shu_area_id` int(10) NOT NULL COMMENT '所属国家或地区ID';
ALTER TABLE `aups_t012` ADD `name_eng` varchar(100) NOT NULL COMMENT '英文名称';
ALTER TABLE `aups_t012` ADD `name_cn` varchar(100) NOT NULL COMMENT '中文名称';
ALTER TABLE `aups_t012` ADD `pingyin_shouzimu` char(1) NOT NULL COMMENT '拼音首字母';
ALTER TABLE `aups_t012` ADD `code_sheng` smallint(3) NOT NULL COMMENT '省级行政区号';
ALTER TABLE `aups_t012` ADD `code_city` smallint(3) NOT NULL COMMENT '市级行政区号';
ALTER TABLE `aups_t012` ADD `code_quxian` smallint(3) NOT NULL DEFAULT '0' COMMENT '区县级行政区号';

INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES 
((select id from `dpps_table_def` where name_eng='aups_t012'), 's_shu_xingqiu_id', '所属星球ID', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '255', '', NULL, NULL, 'use', NULL, '0', 1000, 'none', '所属星球ID'),
((select id from `dpps_table_def` where name_eng='aups_t012'), 's_shu_area_id', '所属国家或地区ID', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '255', '', NULL, NULL, 'use', NULL, '0', 1000, 'none', '所属国家或地区ID'),
((select id from `dpps_table_def` where name_eng='aups_t012'), 'name_eng', '英文名称', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '255', '', NULL, NULL, 'use', NULL, '0', 1000, 'none', '英文名称'),
((select id from `dpps_table_def` where name_eng='aups_t012'), 'name_cn', '中文名称', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '255', '', NULL, NULL, 'use', NULL, '0', 1000, 'none', '中文名称'),
((select id from `dpps_table_def` where name_eng='aups_t012'), 'pingyin_shouzimu', '拼音首字母', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '255', '', NULL, NULL, 'use', NULL, '0', 1000, 'none', '拼音首字母'),
((select id from `dpps_table_def` where name_eng='aups_t012'), 'code_sheng', '省级行政区号', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '255', '', NULL, NULL, 'use', NULL, '0', 1000, 'none', '省级行政区号'),
((select id from `dpps_table_def` where name_eng='aups_t012'), 'code_city', '市级行政区号', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '255', '', NULL, NULL, 'use', NULL, '0', 1000, 'none', '市级行政区号'),
((select id from `dpps_table_def` where name_eng='aups_t012'), 'code_quxian', '区县级行政区号', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '255', '', NULL, NULL, 'use', NULL, '0', 1000, 'none', '区县级行政区号');

update `dpps_field_def` set `list_order`=10, `f_type`='Form::DB_Select', `arithmetic`='[project]\r\nname=共用数据\r\n\r\n[query]\r\nsql=select {星球名称},id from {星球表}\r\n\r\n[add_select]\r\n,0' where `name_eng`='s_shu_xingqiu_id' and t_id = (select id from `dpps_table_def` where name_cn='城市列表');
update `dpps_field_def` set `list_order`=20, `f_type`='Form::DB_Select', `arithmetic`='[project]\r\nname=共用数据\r\n\r\n[query]\r\nsql=select concat({拼音首字母},"-",{国家或地区名称},"-",id),id from {国家或地区代码} where {级别}=1\r\n\r\n[add_select]\r\n-请选择-,0' where `name_eng`='s_shu_area_id' and t_id = (select id from `dpps_table_def` where name_cn='城市列表');


--
INSERT INTO `dpps_tmpl_design` (`tbl_id`, `creator`, `createdate`, `createtime`, `default_url`, `default_html`, `status_`) VALUES
((select id from dpps_table_def where name_cn='城市列表'), "admin", DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '/${英文名称}/index.shtml', '${英文名称} - ${中文名称}', 'use');

-- 正文页 "所属城市中文" 字段算法 , 其他正文页将 `name_cn`='正文页' 换成相应的就行了
INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`) VALUES
((select id from `dpps_table_def` where `name_cn`='正文页'), 's_shu_chengshi_cn', '所属城市中文', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Application::CodeResult', '255', '', NULL, NULL, 'use', '[sql]
select {拼音首字母},{中文名称},{英文名称} from {城市列表}
[code]<?php
$dbR = &$a_arr[''dbR''];
$l_name_eng = ''${所属城市}'';
$l_name_cn = '''';
if (!empty($l_name_eng)) {
	$dbR->table_name = ''{城市列表}'';
	$l_city_arr = $dbR->GetOne("where {英文名称}=''$l_name_eng'' limit 1 ",''{中文名称}'');
	if (!PEAR::isError($l_city_arr)) {
		$l_name_cn = $l_city_arr[''{中文名称}''];
	}
}
return $l_name_cn;', '0', 1000, 'none');

-- 增加一个算法，用于发布各个栏目首页，由于各个栏目的模板可能不一样，因此有需要定制模板。

-- 交友、火车票、房屋出租等不同类型的文章，使用不同的正文表进行存储

-- 房屋栏目页的 "所属城市中文" 的算法， 其他的栏目页的该算法也同样是将 `name_cn`='房屋栏目页' 换成相应的名称即可
INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`) VALUES
((select id from `dpps_table_def` where `name_cn`='房屋栏目页'), 's_shu_chengshi_cn', '所属城市中文', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Application::CodeResult', '255', '', NULL, NULL, 'use', '[sql]
select {拼音首字母},{中文名称},{英文名称} from {城市列表}
[code]<?php
$dbR = &$a_arr[''dbR''];
$l_name_eng = ''${所属城市}'';
$l_name_cn = '''';
if (!empty($l_name_eng)) {
	$dbR->table_name = ''{城市列表}'';
	$l_city_arr = $dbR->GetOne("where {英文名称}=''$l_name_eng'' limit 1 ",''{中文名称}'');
	if (!PEAR::isError($l_city_arr)) {
		$l_name_cn = $l_city_arr[''{中文名称}''];
	}
}
return $l_name_cn;', '0', 1000, 'none');

-- 房屋栏目页的"栏目名称"的算法
update `dpps_field_def` set `arithmetic`='[query]
sql=select concat({栏目名称},"-",{英文缩写}),{栏目名称} from {栏目配置} where {所属栏目}=''房屋'' or {栏目名称}=''房屋'' and status_=''use'' order by id' where name_cn='栏目名称' and status_='use' and `t_id` = (select id from dpps_table_def where name_cn='房屋栏目页');

-- 房屋栏目页 的新闻列表 aups_f095 算法 application::coderesult
update `dpps_field_def` set `arithmetic`='[sql]
select {栏目配置}.{级别},{栏目配置}.{栏目名称},{房屋正文页}.{文档标题},{房屋正文页}.{所属城市},{房屋正文页}.{显示类型} from {栏目配置},{房屋正文页}

[code]<?php
function getProjectListHtml_putong($arr, $num=5){
	$str = "";
	if (is_array($arr) && count($arr)>0) {
		//$l_arr = array_chunk($arr,$num);
		//foreach ($l_arr as $l__a){
			//$str .= ''<ul>'';
			foreach ($arr as $val){
				$str .= ''
        <li>''.substr($val["createdate"],5).'' <a href="''.$val[''url_1''].''" target="_blank">''.$val[''{文档标题}''].''</a></li>'';
			}
			//$str .= ''</ul>'';
		//}
	}
	return $str;
}

$dbR = &$a_arr[''dbR''];
$page_size = 50;

$name = ''${栏目名称}'';
$l_city = ''${所属城市}'';

$level = '''';
if (!empty($name)) {
	$dbR->table_name = ''{栏目配置}'';
	$level = $dbR->GetOne("where {栏目配置}.{栏目名称}=''$name'' order by id desc limit 1 ",''{栏目配置}.{级别}'');
	if (!PEAR::isError($level)) {
		$level = $level[''{级别}''];
	}else {
		$level = '''';
	}
}

if (1==$level){
 	$sql = "select {房屋正文页}.{文档标题},url_1,createdate,createtime from {房屋正文页} where  {房屋正文页}.{所属栏目}=''$name'' and {房屋正文页}.{所属城市}=''$l_city'' and {房屋正文页}.{显示类型}=''putong'' and {房屋正文页}.status_=''use'' order by createdate desc,createtime desc limit $page_size ";
}else if (2 == $level){
	$sql = "select {房屋正文页}.{文档标题},url_1,createdate,createtime from {房屋正文页} where {房屋正文页}.{所属子栏目}=''$name'' and {房屋正文页}.{所属城市}=''$l_city'' and {房屋正文页}.{显示类型}=''putong'' and {房屋正文页}.status_=''use'' order by createdate desc,createtime desc limit $page_size ";
}else {
	$sql = "";
}

$column_path2=$dbR->query_plan($sql);
if (!PEAR::isError($column_path2)) {
	$html = getProjectListHtml_putong($column_path2);
}else {
	$html = ''<li>暂时还没有此类信息</li>'';
}

return $html;' where `name_cn`='新闻列表' and status_='use' and `tbl_id` = (select id from dpps_table_def where name_cn='房屋正文页');

-- 房屋栏目页 的置顶帖子列表  算法 application::coderesult
update `dpps_field_def` set `arithmetic`='[sql]
select {栏目配置}.{级别},{栏目配置}.{栏目名称},{房屋正文页}.{文档标题},{房屋正文页}.{所属城市},{房屋正文页}.{显示类型} from {栏目配置},{房屋正文页}

[code]<?php
function getProjectListHtml_zhiding($arr, $num=5){
	$str = "";
	if (is_array($arr) && count($arr)>0) {
		//$l_arr = array_chunk($arr,$num);
		//foreach ($l_arr as $l__a){
			//$str .= ''<ul>'';
			foreach ($arr as $val){
				$str .= ''
        <li>''.substr($val["createdate"],6).'' <a href="''.$val[''url_1''].''" target="_blank">''.$val[''{文档标题}''].''</a></li>'';
			}
			//$str .= ''</ul>'';
		//}
	}
	return $str;
}

$dbR = &$a_arr[''dbR''];
$page_size = 50;

$name = ''${栏目名称}'';
$l_city = ''${所属城市}'';

$level = '''';
if (!empty($name)) {
	$dbR->table_name = ''{栏目配置}'';
	$level = $dbR->GetOne("where {栏目配置}.{栏目名称}=''$name'' order by id desc limit 1 ",''{栏目配置}.{级别}'');
	if (!PEAR::isError($level)) {
		$level = $level[''{级别}''];
	}else {
		$level = '''';
	}
}

if (1==$level){
 	$sql = "select {房屋正文页}.{文档标题},url_1,createdate,createtime from {房屋正文页} where  {房屋正文页}.{所属栏目}=''$name'' and {房屋正文页}.{所属城市}=''$l_city'' and {房屋正文页}.{显示类型}=''zhiding'' and {房屋正文页}.status_=''use'' order by createdate desc,createtime desc limit $page_size ";
}else if (2 == $level){
	$sql = "select {房屋正文页}.{文档标题},url_1,createdate,createtime from {房屋正文页} where {房屋正文页}.{所属子栏目}=''$name'' and {房屋正文页}.{所属城市}=''$l_city'' and {房屋正文页}.{显示类型}=''zhiding'' and {房屋正文页}.status_=''use'' order by createdate desc,createtime desc limit $page_size ";
}else {
	$sql = "";
}

$column_path2=$dbR->query_plan($sql);
if (!PEAR::isError($column_path2)) {
	$html = getProjectListHtml_zhiding($column_path2);
}else {
	$html = '''';
}

return $html;' where `name_eng`='zhiding_list' and `tbl_id` = (select id from dpps_table_def where name_cn='房屋正文页');

-- 房屋栏目页 的滚动帖子列表  算法 application::coderesult
update `dpps_field_def` set `arithmetic`='[sql]
select {栏目配置}.{级别},{栏目配置}.{栏目名称},{房屋正文页}.{文档标题},{房屋正文页}.{所属城市},{房屋正文页}.{显示类型} from {栏目配置},{房屋正文页}

[code]<?php
function getProjectListHtml_gundong($arr, $num=5){
	$str = "";
	if (is_array($arr) && count($arr)>0) {
		//$l_arr = array_chunk($arr,$num);
		//foreach ($l_arr as $l__a){
			//$str .= ''<ul>'';
			foreach ($arr as $val){
				$str .= ''
        <li>''.substr($val["createdate"],6).'' <a href="''.$val[''url_1''].''" target="_blank">''.$val[''{文档标题}''].''</a></li>'';
			}
			//$str .= ''</ul>'';
		//}
	}
	return $str;
}

$dbR = &$a_arr[''dbR''];
$page_size = 50;

$name = ''${栏目名称}'';
$l_city = ''${所属城市}'';

$level = '''';
if (!empty($name)) {
	$dbR->table_name = ''{栏目配置}'';
	$level = $dbR->GetOne("where {栏目配置}.{栏目名称}=''$name'' order by id desc limit 1 ",''{栏目配置}.{级别}'');
	if (!PEAR::isError($level)) {
		$level = $level[''{级别}''];
	}else {
		$level = '''';
	}
}

if (1==$level){
 	$sql = "select {房屋正文页}.{文档标题},url_1,createdate,createtime from {房屋正文页} where  {房屋正文页}.{所属栏目}=''$name'' and {房屋正文页}.{所属城市}=''$l_city'' and {房屋正文页}.{显示类型}=''gundong'' and {房屋正文页}.status_=''use'' order by createdate desc,createtime desc limit $page_size ";
}else if (2 == $level){
	$sql = "select {房屋正文页}.{文档标题},url_1,createdate,createtime from {房屋正文页} where {房屋正文页}.{所属子栏目}=''$name'' and {房屋正文页}.{所属城市}=''$l_city'' and {房屋正文页}.{显示类型}=''gundong'' and {房屋正文页}.status_=''use'' order by createdate desc,createtime desc limit $page_size ";
}else {
	$sql = "";
}

$column_path2=$dbR->query_plan($sql);
if (!PEAR::isError($column_path2)) {
	$html = getProjectListHtml_gundong($column_path2);
}else {
	$html = '''';
}

return $html;' where `name_eng`='gundong_list' and `tbl_id` = (select id from dpps_table_def where name_cn='房屋正文页');

-- 房屋正文页 "所属栏目" 的算法
update `dpps_field_def` set `arithmetic`='[query]
sql=select {栏目名称},{栏目名称} from {栏目配置} where {级别}=1 and {栏目名称}="房屋" order by {显示顺序}' where name_cn='所属栏目' and status_='use' and `tbl_id` = (select id from dpps_table_def where name_cn='房屋正文页');

-- 房屋正文页 "所属子栏目" 的算法
update `dpps_field_def` set `arithmetic`='[query]
sql=select concat({栏目名称},"-",{英文缩写}),{栏目名称} from {栏目配置} where {级别}=2 and {所属栏目}="房屋" order by {显示顺序}' where name_cn='所属子栏目' and status_='use' and `tbl_id` = (select id from dpps_table_def where name_cn='房屋正文页');

-- 房屋正文页 "相关发布-栏目页" 的算法
update `dpps_field_def` set `arithmetic`='allow=post_1,post_2
[post_1]
where={房屋栏目页}:{栏目名称}="${所属栏目}" and {所属城市}="${所属城市}"
[post_2]
where={房屋栏目页}:{栏目名称}="${所属子栏目}" and {所属城市}="${所属城市}"' where name_cn='相关发布-栏目页' and status_='use' and `tbl_id` = (select id from dpps_table_def where name_cn='房屋正文页');




-- 类似地，其他几个也都需要这样处理，具体不一一列举
-- 票务栏目页的"栏目名称"的算法
--update `dpps_field_def` set `arithmetic`='[query]\r\nsql=select concat({栏目名称},"-",{英文缩写}),{栏目名称} from {栏目配置} where {所属栏目}=''票务'' or {栏目名称}=''票务'' and status_=''use'' order by id' where name_cn='栏目名称' and status_='use' and `tbl_id` = (select id from dpps_table_def where name_cn='票务栏目页');





-- 通用修改，临时使用的，以后不必使用的语句
update `dpps_field_def` set `arithmetic`='[sql]
select {描述},{栏目名称} from {栏目配置} where {栏目名称}=''$column_name'' order by id desc limit 1
[code]<?php
$dbR = &$a_arr[''dbR''];
$l_description = '''';
$name = ''${栏目名称}'';
if (!empty($name)) {
	$dbR->table_name = ''{栏目配置}'';
	$column_description = $dbR->GetOne("where {栏目名称}=''$name'' order by id desc limit 1 ",''{描述}'');
	if (!PEAR::isError($column_description)) {
		$l_description = $column_description[''{描述}''];
	}
}
return $l_description;' where name_cn='栏目描述' and status_='use' and `t_id` = (select id from dpps_table_def where name_cn='房屋栏目页');

update `dpps_field_def` set `arithmetic`='[sql]
select {关键词},{栏目名称} from {栏目配置} where {栏目名称}=''$column_name'' order by id desc limit 1
[code]<?php
$dbR = $a_arr[''dbR''];
$html = '''';
$name = ''${栏目名称}'';
if (!empty($name)) {
	$dbR->table_name = ''{栏目配置}'';
	$html = $dbR->GetOne("where {栏目名称}=''$name'' order by id desc limit 1 ",''{关键词}'');
	if (!PEAR::isError($html)) {
		$html = $html[''{关键词}''];
	}
}
return $html;' where name_cn='栏目关键词' and status_='use';

