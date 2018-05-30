-- 网站ssi，网站介绍等初始数据入库
INSERT INTO `aups_t001` (`id`, `creator`, `createdate`, `createtime`, `mender`, `menddate`, `mendtime`, `expireddate`, `audited`, `status_`, `flag`, `arithmetic`, `unicomment_id`, `published_1`, `url_1`, `last_modify`, `aups_f001`, `aups_f002`, `aups_f003`, `aups_f004`, `aups_f005`, `aups_f006`, `aups_f007`, `aups_f008`, `aups_f009`, `aups_f010`, `aups_f011`) VALUES
(1, 'admin', '2012-03-26', '08:52:19', NULL, NULL, NULL, '0000-00-00', '0', 'use', 0, NULL, NULL, '0', '/ssi/header.ssi', '2012-03-26 10:56:51', '<div id="header">
  <div id="gog">
    <div id="gbar"><nobr><a class="gb1" href="http://www.ni9ni.com/">首页</a> <a class="gb1" href="http://shenghuo.ni9ni.com/">生活</a> <a class="gb1" href="http://t.ni9ni.com/yule/">娱乐</a> <a class="gb1" href="http://t.ni9ni.com/cj/">财经</a> <a class="gb1" href="http://t.ni9ni.com/it/">科学技术</a> <a class="gb1" href="http://e.ni9ni.com/ny/">农业</a> <a class="gb1" href="http://t.ni9ni.com/book/1/">读书</a> </nobr></div>
    <div width="100%" id="guser"><nobr><span class="gbi" id="gbn"></span><span class="gbf" id="gbf"></span> <a class="gb4" href="http://www.ni9ni.com/home/lyb.shtml" target="_blank">意见反馈</a> | <a class="gb4" href="http://uibi.ni9ni.com/login/?bak=http://www.ni9ni.com/" target="_blank">登录</a></nobr></div>
    <div style="left: 0pt;" class="gbh"></div>
    <div style="right: 0pt;" class="gbh"></div>
  </div>
</div>', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'admin', '2012-03-26', '08:53:21', NULL, NULL, NULL, '0000-00-00', '0', 'use', 0, NULL, NULL, '0', '/ssi/footer.ssi', '2012-03-26 10:57:36', '<!-- footer begin -->
<div id="footer">
  <div style="height:15px;"></div>
  <div class="footer"> <a href="http://www.ni9ni.com/home/about.shtml" target="_blank">关于我们</a> | <a href="http://www.ni9ni.com/home/contactus.shtml" target="_blank">联系我们</a> | <a href="http://www.ni9ni.com/home/lyb.shtml" target="_blank">留言版</a><br />
    Copyright &copy; 2010-2011 ni9ni.com, All Rights Reserved<br />就你网　<a href="http://www.ni9ni.com/home/copyright.shtml" target="_blank">版权所有</a>　<a href="http://www.miibeian.gov.cn" target="_blank">京ICP证11041866号</a></div>
</div>
<p>&nbsp;</p>
<!-- footer end -->', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'admin', '2012-03-26', '08:59:35', NULL, NULL, NULL, '0000-00-00', '0', 'use', 0, NULL, NULL, '0', '/home/about.shtml', '2012-03-26 09:21:33', '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
由某社会公益团体出资创建，旨在通过大家的技术分享，提升您的技术水平，为您创造更多的财富，如何做到？敬请期待', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'admin', '2012-03-26', '09:26:52', NULL, NULL, NULL, '0000-00-00', '0', 'use', 0, NULL, NULL, '0', '/home/contactus.shtml', '2012-03-26 09:26:52', '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
联系我们，请发邮件到 <img src="http://img3.ni9ni.com/home/deco/2010/1201/biosocial_mail.jpg" width="127" height="18" alt="邮箱地址" /> 或 北京手机号: 13401166610', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'admin', '2012-03-26', '09:27:59', NULL, NULL, NULL, '0000-00-00', '0', 'use', 0, NULL, NULL, '0', '/home/copyright.shtml', '2012-03-26 09:27:59', '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'admin', '2012-03-26', '09:28:44', NULL, NULL, NULL, '0000-00-00', '0', 'use', 0, NULL, NULL, '0', '/home/lyb.shtml', '2012-03-26 09:28:44', '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
建设中，请稍候......', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- 首页模板代码替换
update `dpps_tmpl_design` set `default_html`='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--[${_PROJECT_id},${_PROJECT_TABLE_id},${id}] published at ${_SYSTEM_date} ${_SYSTEM_time} by ${_USER_id}-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>首页_就你网</title>
<meta name="description" content="技术、技能、知识经验共享！提高效率，创造财富，成就非凡的你,提供IT技术服务，PHP技术兼职人员、java人才等, 方便的网络管理系统，是您日常生活的好管家 " />
<meta name="keywords" content="提高您的能力,创造社会财富,就算真有2012，只要有你，就能重造整个人类文明，你，就是你，我们的目标：让生命永远存在，让智慧发扬光大" />
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

#wrap{margin:0 auto;}

/* header center footer */
#header ,#centers ,#footer{ margin:0 auto; clear:both;} 



#logo {float:left; margin: 5px 0px 5px 0px; padding-top:4px;}

#denglu {PADDING-RIGHT: 10px; PADDING-LEFT: 30px; RIGHT: 10px; FLOAT: right; PADDING-BOTTOM: 0px; PADDING-TOP: 4px; TOP: 4px}

#navbox {MARGIN-LEFT:60px; margin-top:44px; TEXT-ALIGN: left; float:left;}
#navbox li{FLOAT: left; MARGIN-LEFT: 10px; line-height:36px;}

#centers .c_left{ float:left; width:230px; border:1px solid #00CC66; background:#F7F7F7; margin-right:5px; } 
#centers .c_right{ float:left; width:500px;border:1px solid #00CC66; background:#F7F7F7}
#centers li{ line-height:20px;}

.left{width:220px; float:left; margin-left:10px; margin-top:10px; border:#ccc solid 1px;background:url(http://img3.ni9ni.com/home/deco/2010/1201/sort_bg1.jpg) repeat-x left top;  padding-bottom:10px; }
.left li{margin-left:24px;}
.left li span{ font-size:8px;}
.left h3{margin-left:20px;margin-top:0px; margin-bottom:10px; line-height:29px;}



.nav_a a:link {background: url(http://img3.ni9ni.com/home/deco/2010/1201/nav_0.jpg) repeat-x left top; height:32px; width:140px;  FLOAT: left; COLOR: #000; text-align:center; TEXT-DECORATION: none}
.nav_a a:visited {background: url(http://img3.ni9ni.com/home/deco/2010/1201/nav_0.jpg) repeat-x left top; height:32px; width:140px;  FLOAT: left; COLOR: #000; text-align:center; TEXT-DECORATION: none}
.nav_a a:hover {background:  url(http://img3.ni9ni.com/home/deco/2010/1201/nav_1.jpg) repeat-x left top; height:32px; width:140px; FLOAT: left; COLOR: #fff; text-align:center; TEXT-DECORATION: none}
.nav_a a:active {background:  url(http://img3.ni9ni.com/home/deco/2010/1201/nav_0.jpg) repeat-x left top; height:32px; width:140px;  FLOAT: left; COLOR: #000; text-align:center; TEXT-DECORATION: none}


/* footer */
.footer{margin: 20px 0;text-align: center;line-height: 24px;color:#333;}
.footer a{color:#333;}


-->
</style>
</head>
<body>
<!--#include file="/ssi/header.ssi"-->
<div id="wrap">
  <div id="centers">
    <div class="left" style="height:auto">
      <h3><a href="http://t.ni9ni.com/yule/">娱乐</a> - <span style="font-size:12px; font-weight:normal">笑一笑十年少</span></h3>
      <!--#include file="/ssi/yule.ssi"-->
	  <span style="float:right; padding-right:16px;"><a href="http://t.ni9ni.com/yule/">更多&gt;&gt;</a></span>
    </div>
	<div class="left" style="height:auto">
      <h3><a href="http://t.ni9ni.com/it/">IT技术</a> - <span style="font-size:12px; font-weight:normal">科技是第一生产力</span></h3>
      <!--#include file="/ssi/it.ssi"-->
	  <span style="float:right; padding-right:16px;"><a href="http://t.ni9ni.com/it/">更多&gt;&gt;</a></span>
    </div>
	<div class="left">
      <h3><a href="http://t.ni9ni.com/book/1/">读书</a> - <span style=" font-size:12px; font-weight:normal">人类进步的阶梯</span></h3>
      <ul>
        <li><a href="http://t.ni9ni.com/book/2/" target="_blank">世界从十亿光年到0.1飞米(转)</a></li>
		<li><a href="http://t.ni9ni.com/book/1/" target="_blank">生命简史</a></li>
		<li>&nbsp;</li>
		<li>&nbsp;</li>
		<li>&nbsp;</li>
		<li>&nbsp;</li><br /><br /></ul>
	  <span style="float:right; padding-right:16px;"><a href="http://t.ni9ni.com/book/1/">更多&gt;&gt;</a></span>
    </div>
	<div class="left">
      <h3>财经 - <span style=" font-size:12px; font-weight:normal">用数字说明世界</span></h3>
      <ul>
        <li><a href="http://t.ni9ni.com/cj/quanqiu.shtml" target="_blank">全球指数</a></li>
		<!--<li><a href="http://e.ni9ni.com/cj/zq/" target="_blank">内地市场</a></li> -->
		<li>&nbsp;</li>
		<li>&nbsp;</li>
		<li>&nbsp;</li>
		<li>&nbsp;</li>
		<li>&nbsp;</li><br /><br /></ul>
	  <span style="float:right; padding-right:16px;">更多&gt;&gt;</span>
    </div>
    <div class="left">
      <h3><a href="http://e.ni9ni.com/ny/">农业</a> - <span style="font-size:12px; font-weight:normal">国之根本</span></h3>
      <ul>
        <li><a href="http://e.ni9ni.com/ny/qihuo" target="_blank">农产品期货</a></li>
        <!--期货行情-->
        <li>&nbsp;</li>
        <li>&nbsp;</li>
		<li>&nbsp;</li>
		<li>&nbsp;</li>
        <li>...期货行情即将登场...</li><br /><br /></ul>
	  <span style="float:right; padding-right:16px;"><a href="http://e.ni9ni.com/ny/">更多&gt;&gt;</a></span>
    </div>
	<!--<div class="left">
      <h3>彩票 - <span style=" font-size:12px">时来运转</span></h3>
      <ul>
        <li><a href="http://e.ni9ni.com/ny/qihuo" target="_blank">全球指数</a></li>
		<li><a href="http://e.ni9ni.com/ny/qihuo" target="_blank">内地市场</a></li>
		<br /><br />
		<li>...稍后陆续放出其他板块...</li>
      </ul>
    </div> -->
    <!--
    <div class="left" style="height:auto">
      <h3>数据更新频率</h3>
      <ul>
        <li><a href="https://admin.money.finance.sina.com.cn/jl/login.htm"> 大于1秒小于1分钟 </a>
          <ul>
            <li><a href="https://admin.money.finance.sina.com.cn/jl/login.htm"> 行情 </a>
              <ul>
                <li><a href="http://live.video.sina.com.cn/modules/wap/live_finance.php">A股行情</a></li>
                <li><a href="http://live.video.sina.com.cn/modules/wap/live_finance.php">港股行情</a></li>
                <li><a href="http://live.video.sina.com.cn/modules/wap/live_finance.php">美股行情</a></li>
                <li><a href="http://live.video.sina.com.cn/modules/wap/live_finance.php">台股行情</a></li>
                <li><a href="http://live.video.sina.com.cn/modules/wap/live_finance.php">环球行情</a></li>
              </ul>
            </li>
          </ul>
        </li>
        <li><a href="https://admin.money.finance.sina.com.cn/jl/login.htm">1分钟</a>
          <ul>
            <li><a href="http://live.video.sina.com.cn/modules/wap/live_finance.php">A股分时数据</a></li>
            <li><a href="http://live.video.sina.com.cn/modules/wap/live_finance.php">港股分时数据</a></li>
          </ul>
        </li>
        <li><a href="https://admin.money.finance.sina.com.cn/jl/login.htm">1分钟以上</a>
          <ul>
            <li><a href="http://live.video.sina.com.cn/modules/wap/live_finance.php">A股</a></li>
            <li><a href="http://live.video.sina.com.cn/modules/wap/live_finance.php">港股</a></li>
          </ul>
        </li>
        <li><a href="https://admin.money.finance.sina.com.cn/jl/login.htm">1天</a>
          <ul>
            <li><a href="http://live.video.sina.com.cn/modules/wap/live_finance.php">A股</a></li>
            <li><a href="http://live.video.sina.com.cn/modules/wap/live_finance.php">港股</a></li>
          </ul>
        </li>
        <li><a href="https://admin.money.finance.sina.com.cn/jl/login.htm">1年</a>
          <ul>
            <li><a href="http://live.video.sina.com.cn/modules/wap/live_finance.php">A股</a></li>
            <li><a href="http://live.video.sina.com.cn/modules/wap/live_finance.php">港股</a></li>
          </ul>
        </li>
      </ul>
    </div> -->
  </div>
</div>

<!--#include virtual="/ssi/footer.ssi"-->
</body>
</html>' where `tbl_id`= (select id from dpps_table_def where name_cn='频道首页');

