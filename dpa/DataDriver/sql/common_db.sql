-- 共用数据库，存放ip数据库、地名数据库等等

CREATE TABLE IF NOT EXISTS `region_planets` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '最近修改时间',
  `name_cn` varchar(100) default NULL COMMENT '星球名称',
  `code_eng` varchar(255) default NULL COMMENT '星球编号',
  PRIMARY KEY  (`id`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='星球表';

CREATE TABLE IF NOT EXISTS `region_area` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '最近修改时间',
  `guojiquhao` varbinary(10) NOT NULL COMMENT '国际区号，例如大陆（86），香港（852），澳门（853）和台湾（886）',
  `name_cn` varchar(200) NOT NULL COMMENT '例如，中国大陆、香港、澳门、台湾，或美国，法国等',
  `jibie` smallint(3) NOT NULL default '1' COMMENT '默认1。级别2表示从属于1级别的。例如港澳台有国际区号，但又是中国的一部分，所以他们的级别为2',
  `pingyin_shouzimu` char(1) NOT NULL COMMENT '拼音首字母',
  `s_shu_xingqiu_id` int(10) default NULL COMMENT '所属星球ID',
  `guojiquhao_int` smallint(6) default NULL COMMENT '国际区号, 整型数据',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `quhao_name_cn` (`guojiquhao`,`name_cn`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='国家或地区代码';

CREATE TABLE IF NOT EXISTS `region_sheng` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '最近修改时间',
  `s_shu_area_id` int(10) NOT NULL COMMENT '所属国家或地区ID',
  `xingzheng_quhua_daima` int(10) NOT NULL COMMENT '行政区划代码为六位,同http://www.stats.gov.cn/tjbz/xzqhdm/t20120105_402777427.htm保持一致',
  `code_sheng` smallint(3) NOT NULL COMMENT '同行政区号代码，但是只有前两位',
  `name_cn` varchar(255) NOT NULL COMMENT '中文名称',
  `name_eng` varchar(255) default NULL COMMENT '英文名称',
  `pingyin_shouzimu` char(1) default NULL COMMENT '拼音首字母',
  `s_shu_xingqiu_id` int(10) NOT NULL COMMENT '所属星球ID',
  `code_city` smallint(3) default NULL COMMENT '市级行政代码, 两位',
  `code_quxian` smallint(3) NOT NULL COMMENT '区县级行政代码,两位',
  `name_eng_58` varchar(255) default NULL COMMENT '58同城的拼音',
  `name_eng_ganji` varchar(255) default NULL COMMENT '赶集网的拼音',
  `xingzheng_type` tinyint(3) NOT NULL default '0' COMMENT '行政区划类型, 0:普通;1:直辖县级;2:自治县;10:直辖市;11:港澳台',
  `name_eng_zhantai` varchar(255) default NULL COMMENT '站台网的拼音',
  PRIMARY KEY  (`id`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='省份';


CREATE TABLE IF NOT EXISTS `ip_data` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '最近修改时间',
  `ip_min` int(10) unsigned NOT NULL COMMENT 'ip段的小值',
  `ip_min_dot` varchar(15) NOT NULL COMMENT 'ip的dot',
  `ip_max` int(10) unsigned NOT NULL COMMENT 'ip段的大值',
  `ip_max_dot` varchar(15) NOT NULL COMMENT 'ip的dot',
  `country_city` varchar(200) default NULL COMMENT '地理位置信息1',
  `description` varchar(255) default NULL COMMENT '地理位置信息2或其他信息',
  PRIMARY KEY  (`id`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`),
  KEY `ip_min_max` (`ip_min`,`ip_max`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='IP地址段归属地';

-- 首先创建前面的表, 然后在管理后台进行创建项目

-- 初始数据
INSERT INTO `region_planets` (`creator`, `createdate`, `createtime`, `status_`, `flag`, `arithmetic`, `unicomment_id`, `published_1`, `url_1`, `name_cn`, `code_eng`) VALUES
('admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'use', 0, NULL, NULL, '0', 'http://shenghuo.ni9ni.com', '地球', 'planet01'),
('admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'use', 0, NULL, NULL, '0', 'http://shenghuo.ni9ni.com', '火星', 'planet02');


update `dpps_table_def` set `arithmetic`='[hidden_field]\r\n{国家或地区代码}:{所属星球ID}', `js_verify_add_edit`=TRUE, `js_code_add_edit`='<script type="text/javascript" src="http://img3.ni9ni.com/js/jquery.min.js"></script>\r\n<script type="text/javascript">\r\nfunction browserDetect(){\r\n		var sUA=navigator.userAgent.toLowerCase();\r\n		var sIE=sUA.indexOf("msie");\r\n		var sOpera=sUA.indexOf("opera");\r\n		var sMoz=sUA.indexOf("gecko");\r\n		if(sOpera!=-1)return "opera";\r\n		if(sIE!=-1){\r\n			nIeVer=parseFloat(sUA.substr(sIE+5));\r\n			if(nIeVer>=6)return "ie6";\r\n			else if(nIeVer>=5.5)return "ie55";\r\n			else if(nIeVer>=5)return "ie5";\r\n		}\r\n		if(sMoz!=-1)return "moz";\r\n		return "other";\r\n}\r\n\r\n\r\nfunction jsRemoveItemFromSelect(objSelect) {                  \r\n	var ie_or = browserDetect();\r\n	var ie_or2 = ie_or.substring(0,2);\r\n	if (ie_or2=="ie") { \r\n		objSelect.options.length = 0;\r\n    }else{\r\n		objSelect.innerHTML = "";\r\n	}\r\n	return true;\r\n}\r\n\r\nfunction jsAddItemToSelect(objSelect, objItemText, objItemValue) {           \r\n    //判断是否存在           \r\n    if (jsSelectIsExitItem(objSelect, objItemValue)) {           \r\n        //alert("该Item的Value值已经存在");           \r\n    } else {           \r\n        var varItem = new Option(objItemText, objItemValue);         \r\n        objSelect.options.add(varItem);\r\n    }\r\n}\r\n\r\nfunction jsSelectIsExitItem(objSelect, objItemValue) {           \r\n    var isExit = false;           \r\n    for (var i = 0; i < objSelect.options.length; i++) {           \r\n        if (objSelect.options[i].value == objItemValue) {           \r\n            isExit = true;           \r\n            break;           \r\n        }           \r\n    }\r\n    return isExit;           \r\n}\r\n\r\n$(function(){\r\n\r\n$("#s_shu_xingqiu_id").change(function (){\r\n	var a_p_id=$("input[name=''p_id'']").val();\r\n	var a_tid=''region_area'';\r\n	var a_id=$("#s_shu_xingqiu_id").val();\r\n	\r\n	// 如果数据已经存在，则无需请求，如果不存在，则需要请求一次\r\n 	// 拼装请求url， \r\n	var l_url = "/dpa/main.php";\r\n	var l_ = Math.round((Math.random()) * 100000000);\r\n	var var_flag = "json_project";\r\n	\r\n    $.ajax({\r\n	   	url: l_url,\r\n	   	//cache:false,\r\n		data:"_r=" + l_ + "&do=GetTemplateListJS&cont_type=json&var_flag=" + var_flag + "&p_id=" + a_p_id + "&t_id=" + a_tid + "&_ziduan=s_shu_xingqiu_id&id=" + a_id + "&_r="+l_,\r\n	   	scriptCharset:"utf-8",\r\n		\r\n		complete:function () {\r\n	   		eval("var l_data = " + var_flag + ";");\r\n			\r\n			if(a_id>=0){\r\n				// 先清空\r\n				if(jsRemoveItemFromSelect(document.getElementById("s_shu_area_id"))){\r\n					// 然后再赋值\r\n					if(0==a_id){\r\n						jsAddItemToSelect(document.getElementById("s_shu_area_id"), "-请选择-", 0);\r\n					}else {\r\n						for(var s_id in l_data[a_id]){\r\n							jsAddItemToSelect(document.getElementById("s_shu_area_id"), l_data[a_id][s_id],s_id);\r\n						}\r\n					}\r\n				}\r\n			}\r\n			//$("#content").text( l_data.title ).css({"color":"red","font-size":"12px"});\r\n		},\r\n	   	//success:function(){alert("success");},\r\n		\r\n		dataType: "script", //script能自己删除节点\r\n	   	type: "GET"\r\n	});\r\n	\r\n});\r\n})\r\n</script>' where `name_cn`='省份';


-- select * from dpps_field_def where t_id = (select id from `dpps_table_def` where name_eng='region_planets');
update `dpps_field_def` set `list_order`=10, `arithmetic`='[query]\r\nsql=select {星球名称},id from {星球表}\r\n\r\n[add_select]\r\n,0' where `name_eng`='s_shu_xingqiu_id' and t_id = (select id from `dpps_table_def` where name_eng='region_area');
update `dpps_field_def` set `list_order`=20 where `name_eng`='guojiquhao' and t_id = (select id from `dpps_table_def` where name_eng='region_area');
update `dpps_field_def` set `list_order`=30 where `name_eng`='name_cn' and t_id = (select id from `dpps_table_def` where name_eng='region_area');
update `dpps_field_def` set `list_order`=40 where `name_eng`='jibie' and t_id = (select id from `dpps_table_def` where name_eng='region_area');
update `dpps_field_def` set `list_order`=60 where `name_eng`='pingyin_shouzimu' and t_id = (select id from `dpps_table_def` where name_eng='region_area');
update `dpps_field_def` set `list_order`=100 where `name_eng`='guojiquhao_int' and t_id = (select id from `dpps_table_def` where name_eng='region_area');

update `dpps_field_def` set `list_order`=2, `f_type`='Form::DB_Select', `arithmetic`='[query]\r\nsql=select {星球名称},id from {星球表}\r\n\r\n[add_select]\r\n,0' where `name_eng`='s_shu_xingqiu_id' and t_id = (select id from `dpps_table_def` where name_eng='region_sheng');
update `dpps_field_def` set `list_order`=20, `f_type`='Form::DB_Select', `arithmetic`='[query]\r\nsql=select concat({拼音首字母},"-",{国家或地区名称},"-",id),id from {国家或地区代码} where {级别}=1\r\n\r\n[add_select]\r\n-请选择-,0' where `name_eng`='s_shu_area_id' and t_id = (select id from `dpps_table_def` where name_eng='region_sheng');

update `dpps_field_def` set `list_order`=10 where `name_eng`='ip_min' and t_id = (select id from `dpps_table_def` where name_eng='ip_data');
update `dpps_field_def` set `list_order`=20 where `name_eng`='ip_min_dot' and t_id = (select id from `dpps_table_def` where name_eng='ip_data');
update `dpps_field_def` set `list_order`=30 where `name_eng`='ip_max' and t_id = (select id from `dpps_table_def` where name_eng='ip_data');
update `dpps_field_def` set `list_order`=40 where `name_eng`='ip_max_dot' and t_id = (select id from `dpps_table_def` where name_eng='ip_data');
update `dpps_field_def` set `list_order`=50 where `name_eng`='country_city' and t_id = (select id from `dpps_table_def` where name_eng='ip_data');
update `dpps_field_def` set `list_order`=70 where `name_eng`='description' and t_id = (select id from `dpps_table_def` where name_eng='ip_data');

