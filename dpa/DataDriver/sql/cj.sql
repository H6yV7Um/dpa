-- 先手动创建三张表，分别是：aups_t012 行情中心导航  hangye_stock_list 股票代码 zhishu_list 指数表

-- 手动创建好那三张表以后, 接着增加字段
-- aups_t012 表结构的修改 SELECT * FROM `dpps_field_def` WHERE t_id in (select id from dpps_table_def where name_cn in ('行情中心导航')) order by id
alter table `aups_t012` add `parent_id` int(10) unsigned NOT NULL COMMENT '父级ID, 节点的父级id，都在一张表中';
alter table `aups_t012` add `name_eng` varchar(100) default NULL COMMENT '英文名称';
alter table `aups_t012` add `name_cn` varchar(100) NOT NULL COMMENT '中文名称, 节点的中文名称';
alter table `aups_t012` add `nav_node` varchar(200) default NULL COMMENT '导航节点信息, 第0级，1级数组[2]项目';
alter table `aups_t012` add `son_` varchar(100) default NULL COMMENT '子节点信息, 因子节点可能指定的是一个外部文件';
alter table `aups_t012` add `other_linktype_node` varchar(255) default NULL COMMENT '链接类型节点名称等信息,第1级的[4]';
alter table `aups_t012` add `jibie` smallint(3) unsigned NOT NULL COMMENT '级别, 用于数据库查询条件';
-- alter table `aups_t012` drop index `parent_id_name_cn`;
alter table `aups_t012` add UNIQUE `parent_id_name_cn` (`parent_id`,`name_cn`);
-- add_2_field_def
INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES
((select id from `dpps_table_def` where name_eng='aups_t012'), 'parent_id', '父级ID', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'NO', '', '', 'INT', 'Form::TextField', '10', 'UNSIGNED', NULL, NULL, 'use', NULL, '0', 10, 'none', '父级ID, 节点的父级id，都在一张表中'),
((select id from `dpps_table_def` where name_eng='aups_t012'), 'name_eng', '英文名称', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '100', '', NULL, NULL, 'use', NULL, '0', 20, 'none', '英文名称,第0级的[3]项'),
((select id from `dpps_table_def` where name_eng='aups_t012'), 'name_cn', '中文名称', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'NO', '', '', 'VARCHAR', 'Form::TextField', '100', '', NULL, NULL, 'use', NULL, '0', 30, 'none', '中文名称, 节点的中文名称所有数组[0]项'),
((select id from `dpps_table_def` where name_eng='aups_t012'), 'nav_node', '导航节点', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '200', '', NULL, NULL, 'use', NULL, '0', 40, 'none', '导航节点信息, 第0级，1级数组[2]项目'),
((select id from `dpps_table_def` where name_eng='aups_t012'), 'son_', '子节点信息', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '100', '', NULL, NULL, 'use', NULL, '0', 15, 'none', '子节点信息, 可能是一个外部文件, 即第一级数组[1]项'),
((select id from `dpps_table_def` where name_eng='aups_t012'), 'other_linktype_node', '链接类型节点名称等信息', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '255', '', NULL, NULL, 'use', NULL, '0', 50, 'none', '链接类型节点名称等信息,第1级的[4]'),
((select id from `dpps_table_def` where name_eng='aups_t012'), 'jibie', '级别', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'NO', '', '', 'SMALLINT', 'Form::TextField', '3', 'UNSIGNED', NULL, NULL, 'use', NULL, '0', 35, 'none', '级别, 用于数据库查询条件');


-- hangye_stock_list SELECT * FROM `dpps_field_def` WHERE t_id in (select id from dpps_table_def where name_eng in ('hangye_stock_list')) order by id
alter table `hangye_stock_list` add `symbol` varchar(10) NOT NULL COMMENT '股票代码全称, 例如sh000001等';
alter table `hangye_stock_list` add `code` char(6) NOT NULL COMMENT '股票代码, 6位的代码';
alter table `hangye_stock_list` add `code_int` int(10) unsigned NOT NULL COMMENT '股票代码整数, 即变为整数，只要代码不是字母的，那么存储整型可能更好，此条用于测试';
alter table `hangye_stock_list` add `name_cn` varchar(100) NOT NULL COMMENT '股票代码中文名, 例如浦发银行';
alter table `hangye_stock_list` add `zhengjianhui_suoshu_fenlei1` varchar(60) default NULL COMMENT '所属证监会行业分类大类, 还有小分类, 就两个';
alter table `hangye_stock_list` add `zhengjianhui_suoshu_fenlei2` varchar(60) default NULL COMMENT '所属证监会行业分类小类';
alter table `hangye_stock_list` add `fenlei_suoshu_fenlei1` varchar(60) default NULL COMMENT '所属分类大类';
alter table `hangye_stock_list` add `fenlei_suoshu_fenlei2` varchar(60) default NULL COMMENT '所属分类小类';
alter table `hangye_stock_list` add `gn_fenlei1` varchar(60) default NULL COMMENT '所属概念板块大类';
alter table `hangye_stock_list` add `gn_fenlei2` varchar(60) default NULL COMMENT '所属概念板块小类';
alter table `hangye_stock_list` add `diyu_fenlei1` varchar(60) default NULL COMMENT '所属地域大类';
alter table `hangye_stock_list` add `diyu_fenlei2` varchar(60) default NULL COMMENT '所属地域小类';
alter table `hangye_stock_list` add `zhishu_fenlei1` varchar(60) default NULL COMMENT '所属指数成分大类';
alter table `hangye_stock_list` add `zhishu_fenlei2` varchar(60) default NULL COMMENT '所属指数成分小类';
alter table `hangye_stock_list` add `jsvar` text COMMENT '个股总股本等数据,包括流通A股、个股状态等信息 http://finance.sina.com.cn/realstock/company/sh600005/jsvar.js 的数据';
alter table `hangye_stock_list` add `url_2` varchar(255) DEFAULT NULL COMMENT '文档发布的第2个url';

-- 
alter table `hangye_stock_list` add UNIQUE `symbol` (`symbol`);
INSERT INTO `dpps_field_def` (`t_id`, `creator`, `createdate`, `createtime`, `name_eng`, `name_cn`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'symbol', '股票代码全称', '0', 'NO', '', '', 'VARCHAR', 'Form::TextField', '10', '', NULL, NULL, 'use', NULL, '0', 10, 'none', '股票代码全称, 例如sh000001等'),
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'code', '股票代码', '0', 'NO', '', '', 'CHAR', 'Form::TextField', '6', '', NULL, NULL, 'use', NULL, '0', 20, 'none', '股票代码, 6位的代码'),
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'code_int', '股票代码整数', '0', 'NO', '', '', 'INT', 'Form::TextField', '10', 'UNSIGNED', NULL, NULL, 'use', NULL, '0', 30, 'none', '股票代码整数, 即变为整数，只要代码不是字母的，那么存储整型可能更好，此条用于测试'),
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'name_cn', '股票代码中文名', '0', 'NO', '', '', 'VARCHAR', 'Form::TextField', '100', '', NULL, NULL, 'use', NULL, '0', 60, 'none', '股票代码中文名, 例如浦发银行'),
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'zhengjianhui_suoshu_fenlei1', '所属证监会行业分类大类', '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '60', '', NULL, NULL, 'use', NULL, '0', 80, 'none', '所属证监会行业分类大类, 还有小分类, 就两个'),
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'zhengjianhui_suoshu_fenlei2', '所属证监会行业分类小类', '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '60', '', NULL, NULL, 'use', NULL, '0', 90, 'none', '所属证监会行业分类小类'),
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'fenlei_suoshu_fenlei1', '所属分类大类', '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '60', '', NULL, NULL, 'use', NULL, '0', 100, 'none', '所属分类大类'),
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'fenlei_suoshu_fenlei2', '所属分类小类', '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '60', '', NULL, NULL, 'use', NULL, '0', 110, 'none', '所属分类小类'),
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'gn_fenlei1', '所属概念板块大类', '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '60', '', NULL, NULL, 'use', NULL, '0', 120, 'none', '所属概念板块大类'),
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'gn_fenlei2', '所属概念板块小类', '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '60', '', NULL, NULL, 'use', NULL, '0', 130, 'none', '所属概念板块小类'),
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'diyu_fenlei1', '所属地域大类', '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '60', '', NULL, NULL, 'use', NULL, '0', 140, 'none', '所属地域大类'),
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'diyu_fenlei2', '所属地域小类', '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '60', '', NULL, NULL, 'use', NULL, '0', 150, 'none', '所属地域小类'),
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'zhishu_fenlei1', '所属指数成分大类', '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '60', '', NULL, NULL, 'use', NULL, '0', 160, 'none', '所属指数成分大类'),
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'zhishu_fenlei2', '所属指数成分小类', '0', 'YES', '', '', 'VARCHAR', 'Form::TextField', '60', '', NULL, NULL, 'use', NULL, '0', 170, 'none', '所属指数成分小类'),
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'jsvar', '个股总股本等数据', '0', 'YES', '', '', 'TEXT', 'Form::TextArea', '255', '', NULL, NULL, 'use', NULL, '0', 200, 'none', '个股总股本等数据,包括流通A股、个股状态等信息 http://finance.sina.com.cn/realstock/company/sh600005/jsvar.js 的数据'),
((select id from `dpps_table_def` where name_eng='hangye_stock_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'url_2', '文档发布的第2个url', '0', 'YES', '', '', 'VARCHAR', 'Form::CodeResult', '255', '', NULL, NULL, 'use', '[code]<?php\r\n// 该字段的名称为 $a_key;\r\n$l_tmpl_design_arr = array();\r\nif (isset($a_arr["t_def"]["tmpl_design"][0]["default_field"])) {\r\n	$l_tmpl_design_arr = cArray::Index2KeyArr($a_arr["t_def"]["tmpl_design"],array("key"=>"default_field","value"=>array()));\r\n}\r\n\r\nif (isset($form[$a_key])){\r\n    $l_url=$form[$a_key];\r\n}else if (""!=trim($a_arr["f_data"][$a_key])){\r\n    $l_url=$a_arr["f_data"][$a_key];\r\n}else if (""!=isset($l_tmpl_design_arr[$a_key]["default_url"])){\r\n    $l_url=$l_tmpl_design_arr[$a_key]["default_url"];\r\n}else if (""!=trim($a_arr["t_def"]["waiwang_url"])){\r\n    $l_url=$a_arr["t_def"]["waiwang_url"];\r\n}else if (""!=trim($a_arr["p_def"]["waiwang_url"])){\r\n    $l_url=$a_arr["p_def"]["waiwang_url"];\r\n}else {\r\n    $l_url="";\r\n}\r\n\r\nreturn $l_url;', '0', 2012, 'none', '文档发布的第2个url');

-- zhishu_list SELECT * FROM `dpps_field_def` WHERE t_id in (select id from dpps_table_def where name_eng in ('zhishu_list')) order by id
alter table `zhishu_list` add `symbol` varchar(10) NOT NULL COMMENT '股票代码全称, 例如sh000001等';
alter table `zhishu_list` add `code` char(6) NOT NULL COMMENT '股票代码, 6位的代码';
alter table `zhishu_list` add `code_int` int(10) unsigned NOT NULL COMMENT '股票代码整数, 即变为整数，只要代码不是字母的，那么存储整型可能更好，此条用于测试';
alter table `zhishu_list` add `name_cn` varchar(100) NOT NULL COMMENT '股票代码中文名, 例如浦发银行';
-- 
alter table `zhishu_list` add UNIQUE `symbol` (`symbol`);
-- reg , '\d+-\d+-\d+ \d+:\d+:\d+'\)     , 'admin', '\d+-\d+-\d+', '\d+:\d+:\d+', '0', '0000-00-00', '00:00:00'
INSERT INTO `dpps_field_def` (`t_id`, `creator`, `createdate`, `createtime`, `name_eng`, `name_cn`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES
((select id from `dpps_table_def` where name_eng='zhishu_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'symbol', '股票代码全称', '0', 'NO', '', '', 'VARCHAR', 'Form::TextField', '10', '', '', '', 'use', '', '0', 10, 'none', '股票代码全称, 例如sh000001等'),
((select id from `dpps_table_def` where name_eng='zhishu_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'code', '股票代码', '0', 'NO', '', '', 'CHAR', 'Form::TextField', '6', '', '', '', 'use', '', '0', 20, 'none', '股票代码, 6位的代码'),
((select id from `dpps_table_def` where name_eng='zhishu_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'code_int', '股票代码整数', '0', 'NO', '', '', 'INT', 'Form::TextField', '10', 'UNSIGNED', '', '', 'use', '', '0', 30, 'none', '股票代码整数, 即变为整数，只要代码不是字母的，那么存储整型可能更好，此条用于测试'),
((select id from `dpps_table_def` where name_eng='zhishu_list'), 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 'name_cn', '股票代码中文名', '0', 'NO', '', '', 'VARCHAR', 'Form::TextField', '100', '', '', '', 'use', '', '0', 60, 'none', '股票代码中文名, 例如浦发银行');
