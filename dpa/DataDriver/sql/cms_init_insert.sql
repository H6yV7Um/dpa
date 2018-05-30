-- 创建cms的时候，除了基本表外，还需要insert一些最基本的数据，便于快速展开其他工作以及作为算法的参考。
-- 常见时间函数 DATE_FORMAT(NOW(),'%Y-%m-%d'),    DATE_FORMAT(NOW(),'%H:%i:%s')

-- 表的初始数据 begin
-- 文章表插入测试值，暂时不需要
INSERT INTO `aups_t003` (`id`, `aups_f070`, `aups_f078`, `aups_f071`, `aups_f072`, `aups_f073`, `aups_f074`, `aups_f075`, `aups_f076`, `aups_f077`) VALUES
(1, '新闻', '', '1', 'news', '/news', '/yule/news', 100, '', '');

INSERT INTO `aups_t004` (`aups_f079`, `aups_f080`, `aups_f081`, `aups_f082`, `aups_f083`) VALUES
('原创', 'http://www.wanhui.cn/', '本网站原创文章', 100, 'wanhui'),
('商务周刊', 'http://www.businesswatch.com.cn/', '《商务周刊》杂志是中国大陆目前唯一的国内外公开发行的新闻性商业周刊，以中国新兴的工商界人士和政府官员为主要读者对象。', 100, 'bwm'),
('掺望东方周刊', 'http://www.lwdf.cn/', '', 100, 'lwdf'),
('南方周末', 'http://www.infzm.com/', '', 100, 'nfzm');

INSERT INTO `aups_t007` (`id`, `aups_f097`) VALUES
(1, '新闻');

INSERT INTO `aups_t008` (`id`, `aups_f099`, `aups_f101`, `aups_f102`, `aups_f103`, `aups_f104`, `aups_f105`, `aups_f106`, `aups_f107`, `aups_f108`, `aups_f109`, `aups_f110`, `aups_f111`, `aups_f112`, `aups_f113`, `aups_f114`, `createdate`, `createtime`) VALUES
(1, 'yes', '2008达沃斯论坛', '2008Davos', '首页html代码...', '', '', '', '', '', '', '', '', '', '', '',DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'));

INSERT INTO `aups_t009` (`id`, `aups_f115`, `aups_f116`) VALUES
(1, '用于测试的功能代码', '$a = 33;
$b = 99;
$c = $a+$b;');

INSERT INTO `aups_t010` (`id`, `aups_f117`) VALUES
(1, '用于测试');

INSERT INTO `aups_t011` (`id`, `url_1`) VALUES
(1, '/index.shtml');
-- 表的初始数据 end

-- dpps_tmpl_design 表，理论上有多少张表就应该有多少个模板设计的初始值，即每张表发布什么样的可视化文件出来
-- 对于大多项目来说非e.ni9ni.com/xiangmu/的二级域名来说需要将 ), '/${_PROJECT_db_name}/ 替换为 ), '/
INSERT INTO `dpps_tmpl_design` (`id`, `tbl_id`, `creator`, `createdate`, `createtime`, `default_url`, `default_html`, `status_`) VALUES
(1,  (select id from dpps_table_def where name_eng='aups_t001'), "admin", DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '/${_PROJECT_db_name}/page/${YYYY}${mm}${dd}/${id}.shtml', '${内容}', 'use'),
(2,  (select id from dpps_table_def where name_eng='aups_t002'), "admin", DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '/${_PROJECT_db_name}/${栏目路径}/${YYYY}${mm}${dd}/${HH}${ii}${id}.shtml', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
.main { background:url(http://img3.hiexhibition.com/upload/d3/2e/d32e9d1051832322c966f2c562ca5037.gif) left repeat-y; width:950px; overflow:hidden; border-top:1px solid #c2d9f2; }

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
        <div class="textbox-content" id="zoomtext">${正文}</div>
      </div>
    </div>
    <div class="mainR">
      <!--#include virtual="/ads/common/1.html"-->
    </div>
  </div>
</div>
<!--#include virtual="/ssi/footer.ssi"-->
</body>
</html>', 'use'),
(3,  (select id from dpps_table_def where name_eng='aups_t003'), "admin", DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '/${_PROJECT_db_name}/${_PROJECT_TABLE_id}/${YYYY}/${mm}${dd}/${id}.shtml', '${栏目名称}', 'use'),
(4,  (select id from dpps_table_def where name_eng='aups_t004'), "admin", DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '/${_PROJECT_db_name}/${_PROJECT_TABLE_id}/${YYYY}/${mm}${dd}/${id}.shtml', '${媒体名称}', 'use'),
(5,  (select id from dpps_table_def where name_eng='aups_t005'), "admin", DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '/${_PROJECT_db_name}/css/${YYYY}/${mm}${dd}/${id}.css', '${代码}', 'use'),
(6,  (select id from dpps_table_def where name_eng='aups_t006'), "admin", DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '/${_PROJECT_db_name}/js/${YYYY}/${mm}${dd}/${id}.js', '${代码}', 'use'),
(7,  (select id from dpps_table_def where name_eng='aups_t007'), "admin", DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '/${_PROJECT_db_name}/${栏目路径}/index.shtml', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[${_PROJECT_id},${_PROJECT_TABLE_id},${id}] published at ${_SYSTEM_date} ${_SYSTEM_time} by ${_USER_id}-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="${栏目名称显示},${栏目关键词}" />
<meta name="description" content="${栏目描述}" />
<title>${栏目名称显示}_${_PROJECT_name_cn}_${_PROJECT_website_name_cn}</title>
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
.main { background:url(http://img3.hiexhibition.com/upload/d3/2e/d32e9d1051832322c966f2c562ca5037.gif) left repeat-y; width:950px; overflow:hidden; border-top:1px solid #c2d9f2; }

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

/*左侧列表*/
.list03{ width:542px; padding:0 0 0 10px; }
.list03 ul{float:left;width:542px;  padding:20px 0 8px 20px;border-bottom:1px #ddd dashed}
.list03 li{float:left; height:32px;}
.list03 li .txt01{ float:left; width:426px; font-size:14px;}
.list03 li .time01{ float:left; width:116px;font-size:12px;color:#9A9A9A;}
.box02{ width:550px; padding:16px 0; }
/*翻页*/
.next { float:right; padding:0 20px 2px 0; font-size:12px;  font-family: Arial; }
.next a { height:18px;border:#d8d8d8 1px solid; padding:2px;color: #666; background:#f2f2f2; text-align:center; text-dexoration: none; }
.next span.current {border: #8f1d22 1px solid; padding:2px 5px; font-weight: bold; color: #fff; background:#8f1d22}

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
          <div class="subMenu"> <a href="${_PROJECT_waiwang_url}/${_PROJECT_db_name}/">${_PROJECT_name_cn}</a> &gt; ${栏目名称显示}</div>
          <div class="list03">
            ${新闻列表}
			<!--<ul>
			  <li><span class="txt01"><a href="http://e.ni9ni.com/ny/qihuo/DD/SCPL/20120222/1642130104.html" target="_blank"> 豆类油脂表现分化，油强粕弱 </a></span><span class="time01">(2012-02-24 14:55)</span></li>
			  <li><span class="txt01"><a href="http://e.ni9ni.com/ny/qihuo/DD/SCPL/20120222/1642130104.html" target="_blank"> 豆类油脂表现分化，油强粕弱 </a></span><span class="time01">(2012-02-24 14:55)</span></li>
			  <li><span class="txt01"><a href="http://e.ni9ni.com/ny/qihuo/DD/SCPL/20120222/1642130104.html" target="_blank"> 豆类油脂表现分化，油强粕弱 </a></span><span class="time01">(2012-02-24 14:55)</span></li>
			</ul>
			<ul>
			  <li><span class="txt01"><a href="http://e.ni9ni.com/ny/qihuo/DD/SCPL/20120222/1642130104.html" target="_blank"> 豆类油脂表现分化，油强粕弱 </a></span><span class="time01">(2012-02-24 14:55)</span></li>
			  <li><span class="txt01"><a href="http://e.ni9ni.com/ny/qihuo/DD/SCPL/20120222/1642130104.html" target="_blank"> 豆类油脂表现分化，油强粕弱 </a></span><span class="time01">(2012-02-24 14:55)</span></li>
			  <li><span class="txt01"><a href="http://e.ni9ni.com/ny/qihuo/DD/SCPL/20120222/1642130104.html" target="_blank"> 豆类油脂表现分化，油强粕弱 </a></span><span class="time01">(2012-02-24 14:55)</span></li>
			  <li><span class="txt01"><a href="http://e.ni9ni.com/ny/qihuo/DD/SCPL/20120222/1642130104.html" target="_blank"> 豆类油脂表现分化，油强粕弱 </a></span><span class="time01">(2012-02-24 14:55)</span></li>
			</ul> -->
            <div class="clearit"></div>
			<div class="box02">
			  <div class="next">${翻页链接} <!--<a href="javascript:void(0);"> 上一页</a> <a href="javascript:void(0);">下一页</a><span class="current">1</span> --> </div>
			</div>
      </div>
      <div class="mainR">
        <!--#include virtual="/ads/common/1.html"-->
      </div>
    </div>
  </div>
</div>
<!--#include virtual="/ssi/footer.ssi"-->
</body>
</html>
', 'use'),
(8,  (select id from dpps_table_def where name_eng='aups_t008'), "admin", DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '/${_PROJECT_db_name}/topic/${专题英文名}/index.shtml', '${head区}
${顶通}
${通栏01}
${通栏02}
${通栏03}
${通栏04}
${通栏05}
${通栏06}
${通栏07}
${通栏08}
${通栏09}
${通栏10}', 'use'),
(9,  (select id from dpps_table_def where name_eng='aups_t009'), "admin", DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '/${_PROJECT_db_name}/${_PROJECT_TABLE_id}/funccode/${id}.shtml', '${内容}', 'use'),
(10, (select id from dpps_table_def where name_eng='aups_t010'), "admin", DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '/${_PROJECT_db_name}/blank/${YYYY}${mm}${dd}/${id}.shtml', '${内容}', 'use'),
(11, (select id from dpps_table_def where name_eng='aups_t011'), "admin", DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '/${_PROJECT_db_name}/index.shtml', '首页', 'use');


-- 字段定义表中的数据, 分为表自身以及表以外的算法：表以外的直接insert；表自身的则需要通过程序进行检验哪些字段的字段类型、算法、排序(list_order)、七大基本属性等
-- 第一张表 aups_t001 页面碎片字段更新id,last_modify无需更新，没有特别的算法.只需要更新如下的几个字段
update `dpps_field_def` set `f_type`='Form::TextArea' where `name_eng`='aups_f001' and `t_id` = (select id from dpps_table_def where name_eng='aups_t001');
update `dpps_field_def` set `f_type`='Form::ImageFile' where `name_eng`='aups_f002' and `t_id` = (select id from dpps_table_def where name_eng='aups_t001');
update `dpps_field_def` set `f_type`='Form::ImageFile' where `name_eng`='aups_f005' and `t_id` = (select id from dpps_table_def where name_eng='aups_t001');
update `dpps_field_def` set `f_type`='Form::ImageFile' where `name_eng`='aups_f008' and `t_id` = (select id from dpps_table_def where name_eng='aups_t001');
update `dpps_field_def` set `list_order`='1001' where `name_eng`='aups_f011' and `t_id` = (select id from dpps_table_def where name_eng='aups_t001');
update `dpps_field_def` set `list_order`='2' where `name_eng`='url_1' and `t_id` = (select id from dpps_table_def where name_eng='aups_t001');
-- 第一张表无表外字段
-- 第一张表 end

-- *注: [新增字段creator、url_1的字段类型和算法在后面统一进行更新]

-- 第二张表 aups_t002 需要更新的字段属性
update `dpps_field_def` set `list_order`='10' where `name_eng`='aups_f012' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `list_order`='20' where `name_eng`='aups_f013' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::DB_Select',`arithmetic`='[query]
sql=select CONCAT({媒体名称},"-",{英文缩写}),{媒体名称} from {媒体配置} order by {显示顺序}',`list_order`='30' where `name_eng`='aups_f014' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `list_order`='40' where `name_eng`='aups_f015' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `list_order`='50' where `name_eng`='aups_f016' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `list_order`='60' where `name_eng`='aups_f017' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `list_order`='70' where `name_eng`='aups_f018' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `list_order`='80' where `name_eng`='aups_f019' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `list_order`='90' where `name_eng`='aups_f020' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::Select',`arithmetic`=',
1,1
2,2
3,3
4,4
5,5
6,6
7,7
8,8
9,9
10,10
11,11
12,12
13,13
14,14
15,15
16,16
17,17
18,18
19,19
20,20
21,21
22,22
23,23
24,24
25,25
26,26
27,27
28,28
29,29
30,30
31,31
32,32
33,33
34,34
35,35
36,36
37,37
38,38
39,39
40,40
41,41
42,42
43,43
44,44
45,45
46,46
47,47
48,48
49,49
50,50
51,51
52,52
53,53
54,54' where `name_eng`='aups_f032' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::DB_RadioGroup',`arithmetic`='[query]
sql=select {说明},id from {功能代码}' where `name_eng`='aups_f041' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::Select',`arithmetic`='是,1
否,0',`list_order`='1100' where `name_eng`='aups_f055' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::HTMLEditor',`list_order`='1100' where `name_eng`='aups_f056' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::DB_Select',`arithmetic`='[query]
sql=select {栏目名称},{栏目名称} from {栏目配置} where {级别}=1 order by id',`list_order`='1110' where `name_eng`='aups_f057' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::DB_Select',`arithmetic`='[query]
sql=select concat({栏目名称},"-",{英文缩写}),{栏目名称} from {栏目配置} where {级别}=2 order by {显示顺序}
[add_select]
,',`list_order`='1120' where `name_eng`='aups_f058' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::DB_Select',`arithmetic`='[query]
sql=select {专题名称},{专题英文名} from {专题页} where {专题英文名}<>'''' order by {创建日期} desc

[Child]
field=所属子专题
sql=select {子栏目名称},{子栏目顺序} from {专题子栏目页} where {所属专题}=''Me.value'' order by {子栏目顺序}
[cache]
enabled=true

[add_select]
,',`list_order`='1130' where `name_eng`='aups_f059' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::Select',`arithmetic`=',
1,1
2,2
3,3
4,4
5,5
6,6
7,7
8,8
9,9
10,10
11,11
12,12
13,13
14,14
15,15
16,16
17,17
18,18
19,19
20,20
21,21
22,22
23,23
24,24
25,25
26,26
27,27
28,28
29,29
30,30
31,31
32,32
33,33
34,34
35,35
36,36
37,37
38,38
39,39
40,40',`list_order`='1140' where `name_eng`='aups_f060' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::DB_Select',`arithmetic`='[query]
sql=select {专题名称},{专题英文名} from {专题页} where {专题英文名}<>'''' order by {创建日期} desc

[Child]
field=所属子专题
sql=select {子栏目名称},{子栏目顺序} from {专题子栏目页} where {所属专题}=''Me.value'' order by {子栏目顺序}
[cache]
enabled=true

[add_select]
,',`list_order`='1150' where `name_eng`='aups_f061' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::Select',`arithmetic`=',
1,1
2,2
3,3
4,4
5,5
6,6
7,7
8,8
9,9
10,10
11,11
12,12
13,13
14,14
15,15
16,16
17,17
18,18
19,19
20,20
21,21
22,22
23,23
24,24
25,25
26,26
27,27
28,28
29,29
30,30
31,31
32,32
33,33
34,34
35,35
36,36
37,37
38,38
39,39
40,40',`list_order`='1160' where `name_eng`='aups_f062' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::File',`list_order`='1170' where `name_eng`='aups_f063' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::ImageFile',`list_order`='1180' where `name_eng`='aups_f064' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::ImageFile',`arithmetic`='[conf]
CheckRun=true
DefaultMark=0
DefaultPosition=9

[mark]
财经LOGO=/home/publish/projects/gsps/conf/finance_logo.png
财经LOGO-白底=/home/publish/projects/gsps/conf/finance_logo2.png',`list_order`='1190' where `name_eng`='aups_f065' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `list_order`='1200' where `name_eng`='aups_f066' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::TextArea',`arithmetic`='[Search]
Enabled=true
CGI=http://pub.ni9ni.com:8080/cgi-bin/search/do_rel_result_new.cgi
Param=mode{"按标题"=>"按标题","按全文"=>"按全文","按标题或全文"=>"按标题或全文"}',`list_order`='1210' where `name_eng`='aups_f067' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `list_order`='1230' where `name_eng`='aups_f068' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
update `dpps_field_def` set `f_type`='Form::Select',`arithmetic`='是,yes
否,no',`list_order`='1250' where `name_eng`='aups_f069' and `t_id` = (select id from dpps_table_def where name_eng='aups_t002');
-- 第二张表的表外字段及其属性
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f021',`name_cn`='栏目显示',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[sql]
select {栏目名称},{链接},{所属栏目},{级别} from {栏目配置}

[code]<?php
$dbR = $a_arr[''dbR''];

$column_name = ''${所属栏目}'';
$sub_column_name = ''${所属子栏目}'';

$dbR->table_name = ''{栏目配置}'';
$l_rlt = $dbR->GetOne("where {级别}=''1'' and {栏目名称}=''$column_name'' limit 1",''{栏目名称},{链接}'');
$html='''';
if (!PEAR::isError($l_rlt)){
	$name=$l_rlt["{栏目名称}"];
	$link=$l_rlt["{链接}"];
	if(''''!=$name && ''''!=$link){
		$html= "<a href=''$link'' target=''_blank''>$name</a>";
	}
	// 如果有子栏目也一起显示
	if (''''!=$sub_column_name) {
		$l_rlt = $dbR->GetOne("where {所属栏目}=''$column_name'' and {栏目名称}=''$sub_column_name'' limit 1",''{栏目名称},{链接}'');
		if (!PEAR::isError($l_rlt)){
			$name=$l_rlt["{栏目名称}"];
			$link=$l_rlt["{链接}"];
			if(''''!=$name && ''''!=$link){
				$html.= " <a href=''$link'' target=''_blank''>$name</a>";
			}
		}
	}
}

return $html;',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f022',`name_cn`='子栏目显示',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::SQLResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[sql]
select {栏目名称},{链接} from {栏目配置} where {级别}=''2'' and {栏目名称}=''${所属子栏目}'' limit 1

[code]<?php
$html='''';
$name="{栏目名称}";
$link="{链接}";
if ($name == ''2009两会'') {
	$link = "http://finance.ni9ni.com/topic/lianghui2009/index.shtml";
}
$subject=''${专题显示}'';
if($name != '''' && $link != '''' && $subject == ''''){
	$html=" <a href=''$link'' target=''_blank''>$name</a> ";
}

[html]
$html',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f023',`name_cn`='相关发布-栏目页',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::PostInPage',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='allow=post_1,post_2

[post_1]
where={栏目页}:{栏目名称}=''${所属栏目}''

[post_2]
where={栏目页}:{栏目名称}=''${所属子栏目}''',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f024',`name_cn`='相关发布-专题页',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='INT',`f_type`='Application::PostInPage',`length`='11',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='allow=post_1

[post_1]
expr=''${所属专题}'' != "" || ''${所属专题2}'' != ""
where={专题页}:{专题英文名}=''${所属专题}'' || {专题英文名}=''${所属专题2}''',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f025',`name_cn`='文档标题-编码',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[code]<?php
$title_encode = ''${文档标题}'';

[html]
$title_encode',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f026',`name_cn`='正文显示',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f027',`name_cn`='压缩图片130',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[sql]

[code]<?php

$doc_url = ''${url}'';
$img_url = ''${图片}'';
$newpic = "<a href=''$doc_url'' target=''_blank''><img src=''$img_url'' border=1></a>";


[html]
$newpic',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f028',`name_cn`='正文页图片推荐',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::PostInPage',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='allow=post_1

[post_1]
expr=''${所属子栏目}'' == ''今日看点'' && ''${推荐小图}'' != ''''
where={页面碎片}:id=5',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f029',`name_cn`='专题显示',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[sql]
select {专题名称},{专题英文名},{专题是否显示} from {专题页}

[code]<?php
$dbR = $a_arr[''dbR''];

$l_eng = ''${所属专题}'';
$l_eng2 = ''${所属专题2}'';

$dbR->table_name = ''{专题页}'';
$l_rlt = $dbR->GetOne("where ({专题英文名}=''$l_eng'' or {专题英文名}=''$l_eng2'') and {专题是否显示}=''yes''",''{专题名称},url_1'');

$html='''';
if (!PEAR::isError($l_rlt)) {
	$name=$l_rlt["{专题名称}"];
	$link=trim($l_rlt["url_1"]);
	if('''' != $name && '''' != $link ){
		if (''/''==substr($link,0,1)) {
			$link = $a_arr["p_def"]["waiwang_url"].''/''.$link;
		}
		$html="<a href=''$link''>$name</a>";
	}
}

return $html;',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f030',`name_cn`='专题子栏目显示',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[sql]

[code]<?php
$html='''';

[html]
$html',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f031',`name_cn`='相关发布-专题子栏目页',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::PostInPage',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='allow=post_1

[post_1]
expr=''${所属专题}'' != '''' || ''${所属专题2}'' != ''''
where={专题子栏目页}:({所属专题}=''${所属专题}'' && {子栏目顺序}=''${所属专题子栏目}'') || ({所属专题}=''${所属专题2}'' && {子栏目顺序}=''${所属专题子栏目2}'')',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f033',`name_cn`='相关发布-封面秀',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::PostInPage',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='allow=post_1

[post_1]
expr=''${期号}'' != ""
where={封面秀}:{媒体名称}=''${来源}'' && {期号}=''${期号}''',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f034',`name_cn`='标题显示',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[code]<?php
$title=''${文档标题}'';

[html]
$title',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f035',`name_cn`='相关发布-财经首页',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::PostInPage',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='allow=post_1

[post_1]
expr=''${权重}'' >= 80
where={财经首页}:id=1',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f036',`name_cn`='相关发布-理财首页2',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::PostInPage',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='allow=post_1,post_2,post_3,post_4,post_5,post_6

[post_1]
expr=''${权重}'' >= 80 && ''${所属栏目}'' == ''理财''
where={理财首页2}:id=1

[post_2]
expr=''${权重}'' >= 80 && ''${所属栏目}'' == ''保险''
where={理财首页2}:id=1

[post_3]
expr=''${权重}'' >= 80 && ''${所属栏目}'' == ''银行''
where={理财首页2}:id=1

[post_4]
expr=''${权重}'' >= 80 && ''${所属栏目}'' == ''基金''
where={理财首页2}:id=1

[post_5]
expr=''${权重}'' >= 80 && ''${所属栏目}'' == ''财富''
where={理财首页2}:id=1

[post_6]
expr=''${权重}'' >= 80 && ''${所属栏目}'' == ''理财课堂''
where={理财首页2}:id=1',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f037',`name_cn`='相关发布-证券首页2',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::PostInPage',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='allow=post_1,post_2

[post_1]
expr=''${权重}'' >= 80 && ''${所属栏目}'' == ''证券''
where={证券首页2}:id=1

[post_2]
expr=''${权重}'' >= 80 && ''${所属栏目}'' == ''全球股市''
where={证券首页2}:id=1',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f038',`name_cn`='相关发布-评论首页',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::PostInPage',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='allow=post_1

[post_1]
expr=''${权重}'' >= 80 && ''${所属栏目}'' == ''评论''
where={评论首页}:id=1',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f039',`name_cn`='评论显示',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[code]<?php
$html = "";
$if_display = ''${是否显示评论}'';
if ($if_display != "no")
{
 $html = "";
}


[html]
$html',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f040',`name_cn`='新版正文显示',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[code]<?php
$html = ''${正文}'';

[html]
$html',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f042',`name_cn`='股票相关显示',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[code]<?php
$content = ''${正文}'';
$title = ''${文档标题}'';
$html1 = "";
$html2 = "";
$html3 = "";

[html]
$html1',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f043',`name_cn`='更多新闻',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[code]<?php
$html= '''';

[html]
$html',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f044',`name_cn`='发往正文页小表',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CrossPublish',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[options]
disabled=no
mode=link
daemon=no
ref_polym=
trigger=yes

[target]
project=财经
template=正文页小表

[bind]
文档标题=文档标题
副标题=副标题
来源=来源
其他来源=其他来源
主题词=主题词
作者=作者
摘要=摘要
备注=备注
权重=权重
是否显示心情=是否显示心情
正文=正文
所属栏目=所属栏目
所属子栏目=所属子栏目
所属专题=所属专题
所属专题子栏目=所属专题子栏目
所属专题2=所属专题2
所属专题子栏目2=所属专题子栏目2
图片=图片
图注=图注
相关报道=相关报道
期号=期号
视频链接=视频链接
附件=附件
附注=附注',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f045',`name_cn`='关键词',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[code]<?php
$keylist = ''${主题词}'';

[html]
$keylist',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f046',`name_cn`='评论条数显示',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[code]<?php
$html = "";
$if_display = ''${是否显示评论}'';
if ($if_display != "no")
{
 $html = "<a href=''javascript:void(0);'' onclick=''viewAllComment();'' class=''ared''>已有评论<span id=''comment_count''>0</span>条</a>";
}


[html]
$html',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f047',`name_cn`='来源显示',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[code]<?php
$ms = ''${来源}'';
$rms = ''${其他来源}'';

if ($ms != '''') {
	$media = $ms;
}else{
	$media = $rms;
}

return $media;',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f048',`name_cn`='发往MSN股票正文页',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CrossPublish',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[options]
disabled=no
mode=clode
daemon=no
ref_polym=
trigger=yes

[target]
project=MSN股票
template=正文页

[bind]
文档标题=文档标题
副标题=副标题
来源=来源
其他来源=其他来源
主题词=主题词
作者=作者
摘要=摘要
备注=备注
权重=权重
是否显示心情=是否显示心情
正文=正文
所属栏目=所属栏目
所属子栏目=所属子栏目
推荐小图=推荐小图
图片=图片
图注=图注
期号=期号
视频链接=视频链接',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f049',`name_cn`='免责声明',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[code]<?php
$media = ''${来源}'';
$othermedia =''${其他来源}'';

if ($media == '''')$media = $othermedia;
if (''原创'' != $media) {
        $html = ''<p style="padding-top:5px;"><font style="font-family: 楷体_GB2312;">免责声明：本文仅代表作者个人观点，与就你网无关。其原创性以及文中陈述文字和内容未经本站证实，对本文以及其中全部或者部分内容、文字的真实性、完整性、及时性本站不作任何保证或承诺，请读者仅作参考，并请自行核实相关内容。</font></p>'';
}else{
	$html = ''<p style="padding-top:5px;"><font style="font-family: 楷体_GB2312;">版权声明：来源就你网财经频道的所有文字、图片和音视频资料，版权均属就你网所有，任何媒体、网站或个人未经本网协议授权不得转载、链接、转贴或以其他方式复制发布/发表。已经本网协议授权的媒体、网站，在下载使用时必须注明"稿件来源：就你网财经"，违者本网将依法追究责任。</font></p>'';
}

return $html;',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f051',`name_cn`='栏目路径',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[sql]
select {保存路径},{栏目名称},{级别} from {栏目配置} limit 1

[code]<?php
$dbR = $a_arr[''dbR''];

$column_path = '''';
$column_name = ''${所属栏目}'';
$sub_column_name = ''${所属子栏目}'';

$sth_column = '''';
if ('''' != $sub_column_name){
	$dbR->table_name = ''{栏目配置}'';
	$sth_column = $dbR->GetOne("where {栏目名称}=''$sub_column_name'' order by id desc limit 1 ",''{保存路径}'');
	//print_r($sth_column);
	if (!PEAR::isError($sth_column)) {
		$sth_column = $sth_column[''{保存路径}''];
	}else {
		$sth_column = '''';
	}
}


$sql = "select {保存路径} from {栏目配置} where {栏目名称}=''$column_name'' order by id desc limit 1";
$column_path2=$dbR->query_plan($sql);
if (!PEAR::isError($column_path2)) {
	$column_path2 = $column_path2[0][''{保存路径}''];
}else {
	$column_path2 = '''';
}
if ('''' != $sth_column && false===strpos($column_path2,$sth_column)) {
	$column_path = $column_path2.$sth_column;
}else if('''' != $sth_column){
	$column_path = $sth_column;
}else {
	$column_path = $column_path2;
}

if ('''' == $column_path )
{
	$column_path = "/news/";
}else {
	$column_path = str_replace(''//'',''/'',$column_path);
}

[html]
$column_path',`exec_mode`='0',`list_order`='1022',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f052',`name_cn`='文档URL-编码',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[code]<?php
$url = "http://finance.ni9ni.com${文档URL}";
$url_encode = urlencode($url);

[html]
$url_encode',`exec_mode`='0',`list_order`='1033',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f053',`name_cn`='相关发布-图片首页2',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::PostInPage',`length`='200',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='allow=post_1,post_2

[post_1]
expr=(''${所属栏目}'' == ''历史'' || ''${所属子栏目}'' == ''证券要闻'' || ''${所属子栏目}'' == ''股民纪事'' || ''${所属子栏目}'' == ''股市声音'') && ''${推荐小图}'' != ''''
where={图片首页2}:id=1

[post_2]
expr=(''${所属子栏目}'' == ''理财故事'' || ''${所属子栏目}'' == ''财子佳人'' || ''${所属子栏目}'' == ''富豪轶事'' || ''${所属子栏目}'' == ''奢侈品'' || ''${所属子栏目}'' == ''白领小资'' || ''${所属子栏目}'' == ''中产精英'' || ''${所属子栏目}'' == ''收藏'') && ''${推荐小图}'' != ''''
where={图片首页2}:id=1',`exec_mode`='0',`list_order`='1034',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t002'),`name_eng`='aups_f054',`name_cn`='相关报道显示',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='200',`attribute`='',`unit`='',`default`='[code]<?php
$html = ''${相关报道}'';

[html]
$html',`status_`='use',`arithmetic`='',`exec_mode`='0',`list_order`='1040',`source`='none',`description`='' ;
-- 第二张表 end

-- 第三张表 aups_t003
update `dpps_field_def` set `f_type`='Form::DB_Select',`arithmetic`='[query]
sql=select {栏目名称},{栏目名称} from {栏目配置} where {级别}=1 order by id

[add_select]
,',`list_order`='1002' where `name_eng`='aups_f078' and `t_id` = (select id from dpps_table_def where name_eng='aups_t003');
-- 第三张表 end

-- 第四张表 aups_t004 暂无
-- 第四张表 end

-- 第五张表 aups_t005 暂无
-- 第五张表 end

-- 第六张表 aups_t006 暂无
-- 第六张表 end

-- 第七张表 aups_t007
update `dpps_field_def` set `f_type`='Form::DB_Select',`status_`='stop',`arithmetic`='[query]
sql=select concat({栏目名称},"-",{英文缩写}),{栏目名称} from {栏目配置} order by id',`list_order`='10' where `name_eng`='aups_f090' and `t_id` = (select id from dpps_table_def where name_eng='aups_t007');
update `dpps_field_def` set `f_type`='Form::DB_Select',`arithmetic`='[query]
sql=select concat({栏目名称},"-",{英文缩写}),{栏目名称} from {栏目配置} order by id',`list_order`='1001' where `name_eng`='aups_f097' and `t_id` = (select id from dpps_table_def where name_eng='aups_t007');
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t007'),`name_eng`='aups_f091',`name_cn`='栏目路径',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='255',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[sql]
select {保存路径},{栏目名称} from {栏目配置} limit 1

[code]<?php
$dbR = $a_arr[''dbR''];

$l_path = "/column/";
$column_name = ''${栏目名称}'';
if ('''' != $column_name) {
	$dbR->table_name = ''{栏目配置}'';
	$column_path = $dbR->GetOne("where {栏目名称}=''$column_name'' order by id desc limit 1 ",''{保存路径}'');
	if (!PEAR::isError($column_path)) {
		$l_path = "/". trim($column_path[''{保存路径}'']," /") . "/";
	}
}

return $l_path;',`exec_mode`='0',`list_order`='20',`source`='none',`description`='备注一下,' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t007'),`name_eng`='aups_f092',`name_cn`='翻页链接',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='255',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[sql]
select {级别},{栏目名称} from {栏目配置} limit 1

[code]<?php
$dbR = &$a_arr[''dbR''];

$name = ''${栏目名称}'';

if (!empty($name)) {
	$dbR->table_name = ''{栏目配置}'';
	$level = $dbR->GetOne("where {栏目名称}=''$name'' order by id desc limit 1 ",''{级别}'');
	if (!PEAR::isError($level)) {
		$level = $level[''{级别}''];
	}else {
		$level = '''';
	}
}

$name_encode = urlencode($name);

$html = "";
if (1 == $level){
 	$html = "<a href=''/info/column.php?name=$name_encode&page=2''>next</a>";
}else if (2 == $level){
 	$html = "<a href=''/info/column.php?sub_name=$name_encode&page=2''>next</a>";
}

return '''';',`exec_mode`='0',`list_order`='50',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t007'),`name_eng`='aups_f093',`name_cn`='相关发布-港股动态',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::PostInPage',`length`='255',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='allow=post_1

[post_1]
expr=''${栏目名称}'' == ''港股动态''
where={专题页}:id=''55''',`exec_mode`='0',`list_order`='70',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t007'),`name_eng`='aups_f094',`name_cn`='栏目关键词',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='255',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[sql]
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
return $html;',`exec_mode`='0',`list_order`='100',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t007'),`name_eng`='aups_f095',`name_cn`='新闻列表',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='255',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[sql]
select {栏目配置}.{级别},{栏目配置}.{栏目名称},{正文页}.{文档标题} from {栏目配置},{正文页}

[code]<?php
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
	if (!PEAR::isError($level)) {
		$level = $level[''{级别}''];
	}else {
		$level = '''';
	}
}

if (1==$level){
 	$sql = "select {正文页}.{文档标题},url_1,createdate,createtime from {正文页} where {正文页}.{所属栏目}=''$name'' order by createdate desc,createtime desc limit 200";
}else if (2 == $level){
	$sql = "select {正文页}.{文档标题},url_1,createdate,createtime from {正文页} where {正文页}.{所属子栏目}=''$name'' order by createdate desc,createtime desc limit 200";
}

$column_path2=$dbR->query_plan($sql);
//print_r($column_path2);exit;
if (!PEAR::isError($column_path2)) {
	$html = getProjectListHtml2($column_path2);
}else {
	$html = '''';
}


return $html;',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t007'),`name_eng`='aups_f096',`name_cn`='栏目描述',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='255',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[sql]
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

return $l_description;',`exec_mode`='0',`list_order`='1000',`source`='none',`description`='' ;
INSERT INTO `dpps_field_def` set `t_id`=(select id from dpps_table_def where name_eng='aups_t007'),`name_eng`='aups_f098',`name_cn`='栏目名称显示',`edit_flag`='0',`is_null`='YES',`key`='',`extra`='',`type`='VARCHAR',`f_type`='Application::CodeResult',`length`='255',`attribute`='',`unit`='',`default`='',`status_`='use',`arithmetic`='[code]<?php
if (''大中华经济'' == ''${栏目名称}''){
	$column_name_display = "财经资讯";
}else {
	$column_name_display = ''${栏目名称}'';
}
return $column_name_display;',`exec_mode`='0',`list_order`='1100',`source`='none',`description`='' ;
-- 第七张表 end

-- 第八张表 aups_t008
update `dpps_field_def` set `f_type`='Form::Select',`arithmetic`='是,yes
否,no',`list_order`='20' where `name_eng`='aups_f099' and `t_id` = (select id from dpps_table_def where name_eng='aups_t008');
update `dpps_field_def` set `status_`='stop' where `name_eng`='aups_f100' and `t_id` = (select id from dpps_table_def where name_eng='aups_t008');
-- 第八张表 end

-- 第九张表 aups_t009 暂无
-- 第九张表 end

-- 1) 创建时间统一更新
update `dpps_field_def` set `list_order`='1' where `name_eng`='id';
update `dpps_field_def` set `list_order`='1051' where `name_eng`='creator';
update `dpps_field_def` set `list_order`='1052' where `name_eng`='createdate';
update `dpps_field_def` set `list_order`='1053' where `name_eng`='createtime';
update `dpps_field_def` set `list_order`='1054' where `name_eng`='mender';
update `dpps_field_def` set `list_order`='1055' where `name_eng`='menddate';
update `dpps_field_def` set `list_order`='1056' where `name_eng`='mendtime';
update `dpps_field_def` set `list_order`='1060' where `name_eng`='expireddate';
update `dpps_field_def` set `list_order`='1061' where `name_eng`='audited';
update `dpps_field_def` set `list_order`='1062' where `name_eng`='status_' and t_id not in (select id from dpps_table_def where name_eng in ('dpps_field_def','dpps_table_def','dpps_tmpl_design'));
update `dpps_field_def` set `list_order`='1063' where `name_eng`='flag';
update `dpps_field_def` set `list_order`='1064' where `name_eng`='arithmetic';
update `dpps_field_def` set `list_order`='1065' where `name_eng`='unicomment_id';
update `dpps_field_def` set `list_order`='1999' where `name_eng`='published_1';
update `dpps_field_def` set `list_order`='2000' where `name_eng`='url_1' and t_id not in (select id from dpps_table_def where name_eng in ('aups_t001'));
update `dpps_field_def` set `list_order`='2012' where `name_eng`='last_modify';
update  dpps_field_def set `creator`='admin' where creator='0';
update  dpps_field_def set `createdate`=DATE_FORMAT(NOW(),'%Y-%m-%d') where createdate='0000-00-00';
update  dpps_field_def set `createtime`=DATE_FORMAT(NOW(),'%H:%i:%s') where createtime='00:00:00';

-- 2) 非三张基本表
--   a) 更新字段creator、url_1的字段类型 和 算法 :
update  dpps_field_def set `f_type`='Form::CodeResult', `arithmetic`='[html]
$_SESSION["user"]["username"]' where name_eng='creator';

update  dpps_field_def set `f_type`='Form::CodeResult', `list_order`=2000, `arithmetic`='[code]<?php
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

return $l_url;' where name_eng='url_1';

--
--
--

