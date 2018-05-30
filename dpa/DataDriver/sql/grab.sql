
CREATE TABLE IF NOT EXISTS `dpps_grab_article_list` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `parent_id` int(11) unsigned NOT NULL COMMENT '所属父级ID, 位于请求表中的ID',
  `tbl_name_eng` varchar(77) NOT NULL default 'dpps_grab_request' COMMENT '所属表名, 属于哪张表, 不同的请求来自不同的表',
  `url` varchar(255) NOT NULL COMMENT 'URL',
  `title` varchar(255) default NULL COMMENT '标题',
  `author` varchar(100) default NULL COMMENT '文章作者',
  `tags` varchar(255) default NULL COMMENT 'tag标签',
  `atype` varchar(100) default NULL COMMENT '类型',
  `status_` enum('in','doing','complete','empty','del','scrap','arti_inter_fail') NOT NULL default 'in' COMMENT '状态, 使用、停用等',
  `short_text` text COMMENT '内容摘要',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `url` (`parent_id`,`tbl_name_eng`,`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章列表数据';

CREATE TABLE IF NOT EXISTS `dpps_grab_artitext` (
  `id` int(11) NOT NULL COMMENT '自增ID',
  `content` text COMMENT '自增ID',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章内容表';

CREATE TABLE IF NOT EXISTS `dpps_grab_request` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `name_cn` varchar(200) NOT NULL COMMENT '中文名称',
  `url` varchar(255) NOT NULL COMMENT 'URL',
  `arithmetic` text COMMENT '每条url的算法, 有算法的通常返回很多数据',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `parent_id` int(11) unsigned NOT NULL COMMENT '所属父级请求ID, 依然属于此表范围, 默认0即没有父级',
  `p_id_to` int(11) unsigned NOT NULL default '0' COMMENT '抓取后的目的项目ID, 暂时只支持对一个项目一个张表。post到url的暂时不支持',
  `t_id_to` int(11) unsigned NOT NULL default '0' COMMENT '抓取后的目的表ID, 以后可以项目ID(表ID,表ID),如:3(2,2)@@',
  `levelnum` int(11) unsigned NOT NULL default '1' COMMENT '级别, 请求可能会被分解成子级别',
  `son_request_tbl` varchar(77) DEFAULT NULL COMMENT '算法计算的子请求所在的表, 英文表名, 空表示没有子请求,或者子请求也在此表中',
  `domain` varchar(50) default NULL COMMENT '域名',
  `startdate` date NOT NULL default '0000-00-00' COMMENT '开始处理的日期',
  `starttime` time NOT NULL default '00:00:00' COMMENT '开始处理的时间',
  `status_` enum('in','doing','complete','empty','del','scrap','failure') NOT NULL default 'in' COMMENT '状态, 使用、停用等',
  `if_article` enum('0','1') NOT NULL default '1' COMMENT '是否抓取文章',
  `arti_total` int(10) default NULL COMMENT '文章总数',
  `arti_hidden` int(10) default NULL COMMENT '隐藏的文章数',
  `if_album` enum('0','1') NOT NULL default '0' COMMENT '是否抓取相册',
  `album_toal` int(10) default NULL COMMENT '相册总数',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `request` (`p_id_to`,`t_id_to`,`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='抓取请求表';

