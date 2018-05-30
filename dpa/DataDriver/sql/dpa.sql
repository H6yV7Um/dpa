
--
-- 数据库: `dpa`
--

-- --------------------------------------------------------

--
-- 表的结构 `dpps_host_backend_reg`
--

CREATE TABLE IF NOT EXISTS `dpps_host_backend_reg` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `host_label` varchar(50) NOT NULL default '' COMMENT '主机卷标, 具有唯一性',
  `host_name` varchar(200) default NULL COMMENT '主机名称, 唯一性',
  `host_os` varchar(200) NOT NULL default '' COMMENT '主机的操作系统',
  `host_ip` varchar(100) NOT NULL default '' COMMENT '主机的ip,主要是外网ip',
  `creator` varchar(100) default NULL COMMENT '创建者',
  `createdate` date default '0000-00-00' COMMENT '创建日期',
  `createtime` time default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default '0000-00-00' COMMENT '修改日期',
  `mendtime` time default '00:00:00' COMMENT '修改时间',
  `description` text COMMENT '描述',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `host_label` (`host_label`),
  UNIQUE KEY `host_name` (`host_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='后台主机信息';

--
-- 导出表中的数据 `dpps_host_backend_reg`
--

-- --------------------------------------------------------

--
-- 表的结构 `dpps_host_reg`
--

CREATE TABLE IF NOT EXISTS `dpps_host_reg` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `host_label` varchar(50) NOT NULL default '' COMMENT '主机卷标',
  `host_name` varchar(200) NOT NULL default '' COMMENT '主机名称',
  `host_os` varchar(200) NOT NULL default '' COMMENT '主机的操作系统',
  `host_ip` varchar(100) NOT NULL default '' COMMENT '主机的ip',
  `host_domain` varchar(100) NOT NULL default '' COMMENT '主机域名',
  `host_port` varchar(100) NOT NULL default '' COMMENT '主机端口',
  `host_root` varchar(200) NOT NULL default '' COMMENT '主机的root',
  `creator` varchar(100) default NULL COMMENT '创建者',
  `createdate` date default '0000-00-00' COMMENT '创建日期',
  `createtime` time default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default '0000-00-00' COMMENT '修改日期',
  `mendtime` time default '00:00:00' COMMENT '修改时间',
  `description` text COMMENT '描述',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `host_label` (`host_label`),
  UNIQUE KEY `host_name` (`host_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='主机信息';

--
-- 导出表中的数据 `dpps_host_reg`
--

-- --------------------------------------------------------

--
-- 表的结构 `dpps_loginlog`
--

CREATE TABLE IF NOT EXISTS `dpps_loginlog` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `username` varchar(100) NOT NULL COMMENT '用户名',
  `nickname` varchar(100) default NULL COMMENT '昵称',
  `logindate` timestamp NOT NULL COMMENT '登录时间',
  `clientip` varchar(15) default NULL COMMENT '客户端IP',
  `serverip` varchar(15) default NULL COMMENT '服务器IP',
  `succ_or_not` enum('y','n') NOT NULL default 'n' COMMENT '登录成功如否',
  `description` varchar(200) default NULL COMMENT '描述',
  PRIMARY KEY  (`id`),
  KEY `idx_logindate` (`logindate`),
  KEY `idx_login` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='登录日志';

--
-- 导出表中的数据 `dpps_loginlog`
--

-- --------------------------------------------------------

--
-- 表的结构 `dpps_project`
--

CREATE TABLE IF NOT EXISTS `dpps_project` (
  `id` int(11) NOT NULL auto_increment COMMENT '自增ID',
  `name_cn` varchar(200) NOT NULL COMMENT '中文名称',
  `type` enum('SYSTEM','CMS','PHP_PROJECT','NORMAL','PUB','RES','GRAB') NOT NULL default 'CMS' COMMENT '项目类型',
  `parent_id` int(11) NOT NULL default '0' COMMENT '所属父级ID',
  `table_field_xiangmu_id` int(11) NOT NULL default '0' COMMENT '字段定义表所属项目ID, 0:表示所属项目,当前大多这样，只有外来的项目可能不是0',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `db_host` varchar(50) NOT NULL default 'localhost' COMMENT '数据库主机',
  `db_name` varchar(50) NOT NULL default '' COMMENT '数据库名称, 英文名称',
  `db_port` int(11) NOT NULL default '3306' COMMENT '数据库端口',
  `db_user` varchar(20) NOT NULL default 'root' COMMENT '数据库用户名',
  `db_pwd` varchar(20) NOT NULL default '' COMMENT '数据库密码',
  `db_timeout` int(11) default '0' COMMENT '数据库过期时间',
  `db_sock` varchar(100) default NULL COMMENT '数据库socket位置',
  `if_use_slave` char(1) NOT NULL default 'n' COMMENT '是否使用从库',
  `slave_db_host` varchar(20) default NULL COMMENT '从库主机名',
  `slave_db_name` varchar(50) default NULL COMMENT '从库数据库名',
  `slave_db_port` int(11) default NULL COMMENT '从库端口',
  `slave_db_user` varchar(20) default NULL COMMENT '从库用户名',
  `slave_db_pwd` varchar(20) default NULL COMMENT '从库密码',
  `slave_db_timeout` int(11) default NULL COMMENT '从库过期时间',
  `slave_db_sock` varchar(100) default NULL COMMENT '从库socket',
  `if_use_slave2` char(1) NOT NULL default 'n' COMMENT '是否使用从库2',
  `slave2_db_host` varchar(20) default NULL COMMENT '从库2主机名',
  `slave2_db_name` varchar(50) default NULL COMMENT '从库2数据库名',
  `slave2_db_port` int(11) default NULL COMMENT '从库2端口',
  `slave2_db_user` varchar(20) default NULL COMMENT '从库2用户名',
  `slave2_db_pwd` varchar(20) default NULL COMMENT '从库2密码',
  `slave2_db_timeout` int(11) default NULL COMMENT '从库2过期时间',
  `slave2_db_sock` varchar(100) default NULL COMMENT '从库2socket',
  `if_daemon_pub` enum('yes','no') NOT NULL default 'no' COMMENT '是否后台发布',
  `daemon_pub_cgi` varchar(200) default NULL COMMENT '后台发布CGI',
  `status_` enum('use','stop','test','del','scrap','OPEN','PAUSE','CLOSE') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `search_order` int(11) NOT NULL default '0' COMMENT '搜索顺序',
  `list_order` int(11) NOT NULL default '0' COMMENT '显示顺序',
  `if_hide` enum('yes','no') NOT NULL default 'no' COMMENT '是否隐藏',
  `description` text COMMENT '描述',
  `host_id` int(10) unsigned NOT NULL default '0' COMMENT '主机id',
  `res_pub_map` int(11) NOT NULL default '0',
  `website_name_cn` varchar(200) NOT NULL default '就你网' COMMENT '网站中文名称, 该项目的',
  `waiwang_url` varchar(255) default 'http://e.ni9ni.com' COMMENT '外网URL',
  `bendi_uri` varchar(255) default 'D:/www/ni9ni/htdocs/e.ni9ni.com' COMMENT '本地URI,/data0/htdocs/e.ni9ni.com',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_cn` (`name_cn`),
  KEY `type` (`type`),
  KEY `search_order` (`search_order`),
  KEY `list_order` (`list_order`),
  KEY `pg_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='全部项目';

-- --------------------------------------------------------

--
-- 表的结构 `dpps_schedule`
--

CREATE TABLE IF NOT EXISTS `dpps_schedule` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `name` varchar(255) NOT NULL default '' COMMENT '名称',
  `host` varchar(255) NOT NULL default '' COMMENT '主机',
  `fashion` enum('0','1','2') NOT NULL default '0' COMMENT '样式',
  `month` varchar(255) NOT NULL default '*' COMMENT '执行月份',
  `day` varchar(255) NOT NULL default '*' COMMENT '执行天',
  `week` varchar(255) NOT NULL default '*' COMMENT '执行周',
  `hour` varchar(255) default NULL COMMENT '执行小时',
  `minute` varchar(255) default NULL COMMENT '执行分钟',
  `mode` enum('0','1','2') default NULL COMMENT '模式',
  `shell_command` varchar(600) default NULL COMMENT 'shell命令',
  `suoshuxiangmu_id` int(10) unsigned default NULL COMMENT '所属项目id',
  `suoshubiao_id` int(10) unsigned default NULL COMMENT '所属表id',
  `condition` enum('0','1') NOT NULL default '0' COMMENT '条件',
  `doc_list` varchar(255) default NULL COMMENT '文档列表',
  `jit` enum('yes','no') NOT NULL default 'no' COMMENT 'jit',
  `status_` enum('0','1') NOT NULL default '0' COMMENT '状态, 使用、停用等',
  `creator` varchar(100) NOT NULL COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date NOT NULL default '0000-00-00' COMMENT '修改日期',
  `mendtime` time NOT NULL default '00:00:00' COMMENT '修改时间',
  `belong_user` varchar(100) NOT NULL default 'finance' COMMENT '所属用户',
  `forbidden_date` varchar(200) default NULL COMMENT '静止执行的日期',
  `forbidden_timezone` tinyint(1) NOT NULL default '-12' COMMENT '静止执行的时区',
  `server_timezone` tinyint(1) NOT NULL default '8' COMMENT '服务器时区',
  `description` text COMMENT '描述',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='计划任务';

--
-- 导出表中的数据 `dpps_schedule`
--

-- --------------------------------------------------------

--
-- 表的结构 `dpps_user`
--

CREATE TABLE IF NOT EXISTS `dpps_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `g_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属组ID',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属父级ID',
  `username` varchar(100) NOT NULL COMMENT '用户名',
  `pwd` varchar(100) NOT NULL DEFAULT '' COMMENT '密码',
  `nickname` varchar(200) NOT NULL COMMENT '昵称',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '移动电话',
  `telephone` varchar(20) NOT NULL DEFAULT '' COMMENT '电话',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '电子邮箱EMAIL',
  `fixed` enum('T','F') NOT NULL DEFAULT 'F' COMMENT '传真',
  `locked` enum('T','F') NOT NULL DEFAULT 'F' COMMENT '是否锁定',
  `stat_priv` enum('00','01','02') NOT NULL DEFAULT '00' COMMENT '统计权限',
  `admin` enum('T','F') NOT NULL DEFAULT 'F' COMMENT '是否管理员',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expired` datetime DEFAULT NULL COMMENT '过期时间',
  `description` text COMMENT '描述',
  `badPwdStr` varchar(100) DEFAULT NULL COMMENT '错误密码',
  `lastPwdChange` varchar(100) DEFAULT NULL COMMENT '最后一次修改的密码',
  `isIPLimit` enum('T','F') NOT NULL DEFAULT 'T' COMMENT '是否IP限制',
  `if_super` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否超级用户, 1：是；0：不是',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`username`),
  UNIQUE KEY `username` (`nickname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户表';

-- --------------------------------------------------------

--
-- 表的结构 `dpps_user_doc_privileges`
--

CREATE TABLE IF NOT EXISTS `dpps_user_doc_privileges` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `u_id` int(10) unsigned NOT NULL default '0' COMMENT '用户ID',
  `suoshuxiangmu_id` int(10) unsigned NOT NULL default '0' COMMENT '所属项目ID',
  `suoshubiao_id` int(10) unsigned NOT NULL default '0' COMMENT '所属表ID',
  `all_priv` enum('T','F') NOT NULL default 'F' COMMENT '是否拥有全部权限',
  `list_priv` enum('T','F') NOT NULL default 'T' COMMENT 'list权限',
  `insert_priv` enum('T','F') NOT NULL default 'T' COMMENT 'insert权限',
  `update_priv` enum('T','F') NOT NULL default 'T' COMMENT 'update权限',
  `delete_priv` enum('T','F') NOT NULL default 'F' COMMENT 'delete权限',
  `publish_priv` enum('T','F') NOT NULL default 'T' COMMENT '发布权限',
  `list_self_doc_priv` enum('T','F') NOT NULL default 'F' COMMENT '自身文档的list权限',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_db_tbl` (`u_id`,`suoshuxiangmu_id`,`suoshubiao_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户的文档权限';

--
-- 导出表中的数据 `dpps_user_doc_privileges`
--

-- --------------------------------------------------------

--
-- 表的结构 `dpps_user_proj_privileges`
--

CREATE TABLE IF NOT EXISTS `dpps_user_proj_privileges` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `u_id` int(10) unsigned NOT NULL default '0' COMMENT '用户ID',
  `suoshuxiangmu_id` int(10) unsigned NOT NULL default '0' COMMENT '所属项目ID',
  `all_priv` enum('T','F') NOT NULL default 'F' COMMENT '是否拥有全部权限',
  `tmpl_priv` enum('T','F') NOT NULL default 'F' COMMENT '模板权限',
  `polym_priv` enum('T','F') NOT NULL default 'F' COMMENT '样式权限',
  `rsync_priv` enum('T','F') NOT NULL default 'F' COMMENT 'rsync权限',
  `subject_priv` enum('T','F') NOT NULL default 'F' COMMENT '主题权限',
  `column_priv` enum('T','F') NOT NULL default 'F' COMMENT '栏目权限',
  `page_priv` enum('T','F') NOT NULL default 'F' COMMENT '分页权限',
  `audit_priv` enum('T','F') NOT NULL default 'F' COMMENT '审核权限',
  `keyword_priv` enum('T','F') NOT NULL default 'F' COMMENT '关键词权限',
  `alertkeyword_priv` enum('T','F') NOT NULL default 'F' COMMENT '告警关键词权限',
  `global_priv` enum('T','F') NOT NULL default 'F' COMMENT '全局权限',
  `list_all_temp_priv` enum('T','F') NOT NULL default 'T' COMMENT '显示全部表权限',
  `add_temp_priv` enum('T','F') NOT NULL default 'F' COMMENT '添加表的权限',
  `list_self_doc_priv` enum('T','F') NOT NULL default 'F' COMMENT '显示自身文档的权限',
  `change_pub_time_priv` enum('T','F') NOT NULL default 'F' COMMENT '修改发布时间的权限',
  `role` enum('ENGINEER','PRODUCTOR','EDITOR') NOT NULL default 'EDITOR' COMMENT '角色',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_project` (`u_id`,`suoshuxiangmu_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户的项目权限';

--
-- 导出表中的数据 `dpps_user_proj_privileges`
--

-- --------------------------------------------------------

--
-- 表的结构 `dpps_user_tempdef_privileges`
--

CREATE TABLE IF NOT EXISTS `dpps_user_tempdef_privileges` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `u_id` int(10) unsigned NOT NULL default '0' COMMENT '用户ID',
  `suoshuxiangmu_id` int(10) unsigned NOT NULL default '0' COMMENT '所属项目ID',
  `suoshubiao_id` int(10) unsigned NOT NULL default '0' COMMENT '所属表ID',
  `all_priv` enum('T','F') NOT NULL default 'F' COMMENT '所有权限',
  `list_priv` enum('T','F') NOT NULL default 'T' COMMENT '列表权限',
  `select_priv` enum('T','F') NOT NULL default 'T' COMMENT 'select权限',
  `insert_priv` enum('T','F') NOT NULL default 'T' COMMENT '添加权限',
  `update_priv` enum('T','F') NOT NULL default 'F' COMMENT '更新权限',
  `delete_priv` enum('T','F') NOT NULL default 'F' COMMENT '删除权限',
  `tempfield_priv` enum('T','F') NOT NULL default 'F' COMMENT '模板字段修改',
  `publish_priv` enum('T','F') NOT NULL default 'T' COMMENT '发布权限',
  `list_self_doc_priv` enum('T','F') NOT NULL default 'F' COMMENT '显示自身文档的权限',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_db_tbl` (`u_id`,`suoshuxiangmu_id`,`suoshubiao_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户的模板权限';

--
-- 导出表中的数据 `dpps_user_tempdef_privileges`
--
