
CREATE TABLE IF NOT EXISTS `dpps_table_def` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `p_id` int(10) unsigned NOT NULL default '0' COMMENT '所属项目ID',
  `name_eng` varchar(200) NOT NULL default 'aups_t001' COMMENT '英文名称',
  `name_cn` varchar(200) NOT NULL COMMENT '中文名称',
  `tbl_type` enum('00','01','02') NOT NULL default '00' COMMENT '表类型',
  `field_def_table` varchar(200) NOT NULL default 'dpps_field_def' COMMENT '字段定义表, 暂未支持',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者ID',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) NOT NULL default '0' COMMENT '修改者ID',
  `menddate` date NOT NULL default '0000-00-00' COMMENT '修改日期',
  `mendtime` time NOT NULL default '00:00:00' COMMENT '修改时间',
  `list_order` tinyint(2) NOT NULL default '100' COMMENT '显示顺序',
  `description` text COMMENT '描述',
  `doc_list_order` text COMMENT '文档显示顺序',
  `source` enum('db','grab','none') NOT NULL default 'none' COMMENT '来源',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `arithmetic` text COMMENT '表定义表的算法, 通常是CodeResult类型',
  `waiwang_url` varchar(255) default NULL COMMENT '外网URL, 没有提供则继承自project的',
  `bendi_uri` varchar(255) default NULL COMMENT '本地URI, 没有提供则继承自project的',
  `js_verify_add_edit` enum('TRUE','FALSE') NOT NULL default 'FALSE' COMMENT '是否js验证, 某张表中添加、修改记录的时候',
  `js_code_add_edit` mediumtext COMMENT 'js验证代码, 某张表中添加、修改记录的时候',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_eng` (`p_id`,`name_eng`),
  KEY `idx_cdt` (`createdate`,`createtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='表定义表';


CREATE TABLE IF NOT EXISTS `dpps_field_def` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `t_id` int(10) unsigned NOT NULL default '0' COMMENT '表ID',
  `name_eng` varchar(100) NOT NULL default 'aups_f001' COMMENT '英文名称',
  `name_cn` varchar(200) NOT NULL COMMENT '中文名称',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者ID',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default '0' COMMENT '修改者ID',
  `menddate` date NOT NULL default '0000-00-00' COMMENT '修改日期',
  `mendtime` time NOT NULL default '00:00:00' COMMENT '修改时间',
  `edit_flag` enum('0','1','2','3') NOT NULL default '0' COMMENT '编辑标记',
  `is_null` enum('YES','NO') NOT NULL default 'YES' COMMENT '是否为空. YES:为空；NO:非空',
  `key` enum('','PRI','MUL','UNI','fulltext') NOT NULL default '' COMMENT '键类型. PRI：主键；‘MUL’索引；UNI唯一，fulltext：全文搜索',
  `extra` enum('','AUTO_INCREMENT') NOT NULL default '' COMMENT '额外属性',
  `type` enum('VARCHAR','TINYINT','TEXT','DATE','SMALLINT','MEDIUMINT','INT','BIGINT','FLOAT','DOUBLE','DECIMAL','DATETIME','TIMESTAMP','TIME','YEAR','CHAR','TINYBLOB','TINYTEXT','BLOB','MEDIUMBLOB','MEDIUMTEXT','LONGBLOB','LONGTEXT','ENUM','SET','BIT','BOOL','BINARY','VARBINARY') NOT NULL default 'VARCHAR' COMMENT '字段数据类型',
  `f_type` enum('Form::CodeResult','Form::TextField','Form::Date','Form::DateTime','Form::Password','Form::TextArea','Form::HTMLEditor','Form::Select','Form::DB_Select','Form::DB_RadioGroup','Form::ImageFile','Form::File','Application::SQLResult','Application::PostInPage','Application::CrossPublish','Application::CodeResult') NOT NULL default 'Form::TextField' COMMENT '字段的算法类型',
  `length` varchar(600) default '255' COMMENT '长度',
  `attribute` enum('','BINARY','UNSIGNED','UNSIGNED ZEROFILL','ON UPDATE CURRENT_TIMESTAMP') NOT NULL default '' COMMENT '无符号等属性',
  `unit` varchar(20) default NULL COMMENT '单位',
  `default` text COMMENT '默认值',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `arithmetic` text COMMENT '字段算法',
  `exec_mode` enum('0','1','2','3') NOT NULL default '0' COMMENT '执行模式',
  `list_order` smallint(5) NOT NULL default '1000' COMMENT '显示顺序',
  `source` enum('db','grab','none') NOT NULL default 'none' COMMENT '来源',
  `description` varchar(255) default NULL COMMENT '描述',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ind_2` (`t_id`,`name_eng`),
  KEY `t_id` (`t_id`,`list_order`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='字段定义表';


CREATE TABLE IF NOT EXISTS `dpps_tmpl_design` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `tbl_id` int(10) unsigned NOT NULL COMMENT '所属表ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) NOT NULL default '0' COMMENT '修改者',
  `menddate` date NOT NULL default '0000-00-00' COMMENT '修改日期',
  `mendtime` time NOT NULL default '00:00:00' COMMENT '修改时间',
  `if_publish` enum('TRUE','FALSE') NOT NULL default 'TRUE' COMMENT '是否发布',
  `content_type` enum('Text','HTML','XML','WML','JSON') NOT NULL default 'HTML' COMMENT '内容类型, html、json、xml',
  `default_field` varchar(60) NOT NULL default 'url_1' COMMENT '发布地址存放字段, 发布成功以后得到的地址存放到哪个字段中',
  `default_url` varchar(255) default NULL COMMENT '默认URL',
  `default_html` longtext COMMENT '默认静态模板代码',
  `tmpl_expr` text COMMENT '执行条件, 只有满足此表达式条件才执行发布, 必须是PHP表达式(如:${是否发往首页) == "yes" && ${栏目名称} == "国内")',
  `description` text COMMENT '描述',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  PRIMARY KEY  (`id`),
  KEY `idx_cdt` (`createdate`,`createtime`),
  KEY `idx_mdt` (`menddate`,`mendtime`),
  KEY `tbl_id` (`tbl_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='模板设计表';


--ALTER TABLE `dpps_field_def` CHANGE `type` `type` ENUM( 'VARCHAR', 'TINYINT', 'TEXT', 'DATE', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT', 'FLOAT', 'DOUBLE', 'DECIMAL', 'DATETIME', 'TIMESTAMP', 'TIME', 'YEAR', 'CHAR', 'TINYBLOB', 'TINYTEXT', 'BLOB', 'MEDIUMBLOB', 'MEDIUMTEXT', 'LONGBLOB', 'LONGTEXT', 'ENUM', 'SET', 'BIT', 'BOOL', 'BINARY', 'VARBINARY' ) CHARACTER SET utf8 NOT NULL DEFAULT 'VARCHAR' COMMENT '字段数据类型',
--CHANGE `f_type` `f_type` ENUM( 'Form::CodeResult', 'Form::TextField', 'Form::Password', 'Form::TextArea', 'Form::HTMLEditor', 'Form::Select', 'Form::DB_Select', 'Form::DB_RadioGroup', 'Form::ImageFile', 'Form::File', 'Application::SQLResult', 'Application::PostInPage', 'Application::CrossPublish', 'Application::CodeResult' ) CHARACTER SET utf8 NOT NULL DEFAULT 'Form::TextField' COMMENT '字段的算法类型',
--CHANGE `length` `length` VARCHAR( 280 ) CHARACTER SET utf8 NULL DEFAULT '255' COMMENT '长度';

--update `dpps_field_def` set `length`='''VARCHAR'',''TINYINT'',''TEXT'',''DATE'',''SMALLINT'',''MEDIUMINT'',''INT'',''BIGINT'',''FLOAT'',''DOUBLE'',''DECIMAL'',''DATETIME'',''TIMESTAMP'',''TIME'',''YEAR'',''CHAR'',''TINYBLOB'',''TINYTEXT'',''BLOB'',''MEDIUMBLOB'',''MEDIUMTEXT'',''LONGBLOB'',''LONGTEXT'',''ENUM'',''SET'',''BIT'',''BOOL'',''BINARY'',''VARBINARY''' where `name_eng`='type' and `t_id` = (select id from dpps_table_def where name_eng='dpps_field_def');
--update `dpps_field_def` set `length`='''Form::CodeResult'',''Form::TextField'',''Form::Password'',''Form::TextArea'',''Form::HTMLEditor'',''Form::Select'',''Form::DB_Select'',''Form::DB_RadioGroup'',''Form::ImageFile'',''Form::File'',''Application::SQLResult'',''Application::PostInPage'',''Application::CrossPublish'',''Application::CodeResult''' where `name_eng`='f_type' and `t_id` = (select id from dpps_table_def where name_eng='dpps_field_def');
--update `dpps_field_def` set `length`='280' where `name_eng`='length' and `t_id` = (select id from dpps_table_def where name_eng='dpps_field_def');


-- INSERT INTO `dpps_field_def` (`t_id`, `name_eng`, `name_cn`, `creator`, `createdate`, `createtime`, `edit_flag`, `is_null`, `key`, `extra`, `type`, `f_type`, `length`, `attribute`, `unit`, `default`, `status_`, `arithmetic`, `exec_mode`, `list_order`, `source`, `description`) VALUES (13, 'arithmetic', '表定义表的算法', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), '0', 'YES', '', '', 'TEXT', 'Form::TextArea', '', '', NULL, 'NULL', 'use', NULL, '0', 1000, 'db', '表定义表的算法, 通常是CodeResult类型');
