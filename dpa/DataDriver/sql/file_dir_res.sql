
CREATE TABLE IF NOT EXISTS `dir` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(200) NOT NULL default '',
  `creator` varchar(100) NOT NULL default '0',
  `createdate` date NOT NULL default '0000-00-00',
  `createtime` time NOT NULL default '00:00:00',
  `mender` varchar(100) default '0',
  `menddate` date NOT NULL default '0000-00-00',
  `mendtime` time NOT NULL default '00:00:00',
  `u_id` int(11) NOT NULL default '0',
  `g_id` int(11) NOT NULL default '0',
  `u_readable` enum('TRUE','FALSE') default 'TRUE',
  `u_writable` enum('TRUE','FALSE') default 'TRUE',
  `u_execable` enum('TRUE','FALSE') default 'TRUE',
  `g_readable` enum('TRUE','FALSE') default 'TRUE',
  `g_writable` enum('TRUE','FALSE') default 'FALSE',
  `g_execable` enum('TRUE','FALSE') default 'TRUE',
  `o_readable` enum('TRUE','FALSE') default 'TRUE',
  `o_writable` enum('TRUE','FALSE') default 'FALSE',
  `o_execable` enum('TRUE','FALSE') default 'TRUE',
  `description` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `file` (
  `id` int(10) unsigned NOT NULL default '0',
  `dir_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(200) NOT NULL default '',
  `size` bigint(20) unsigned NOT NULL default '0',
  `creator` varchar(100) unsigned NOT NULL default '0',
  `createdate` date NOT NULL default '0000-00-00',
  `createtime` time NOT NULL default '00:00:00',
  `mender` varchar(100) default '0',
  `menddate` date NOT NULL default '0000-00-00',
  `mendtime` time NOT NULL default '00:00:00',
  `u_id` int(11) NOT NULL default '0',
  `g_id` int(11) NOT NULL default '0',
  `u_readable` enum('TRUE','FALSE') default 'TRUE',
  `u_writable` enum('TRUE','FALSE') default 'TRUE',
  `u_execable` enum('TRUE','FALSE') default 'TRUE',
  `g_readable` enum('TRUE','FALSE') default 'TRUE',
  `g_writable` enum('TRUE','FALSE') default 'TRUE',
  `g_execable` enum('TRUE','FALSE') default 'TRUE',
  `o_readable` enum('TRUE','FALSE') default 'TRUE',
  `o_writable` enum('TRUE','FALSE') default 'FALSE',
  `o_execable` enum('TRUE','FALSE') default 'TRUE',
  `description` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `file_db` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `content` longblob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `grp` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `grp` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `grp_user` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `g_id` int(10) unsigned NOT NULL default '0',
  `u_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `g_u` (`g_id`,`u_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `res_sync` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `rs_title` varchar(255) NOT NULL default '',
  `if_use` enum('TRUE','FALSE') NOT NULL default 'TRUE',
  `method` enum('Rsync','FTP','Copy') NOT NULL default 'Rsync',
  `rsync_host` varchar(255) default NULL,
  `rsync_port` int(11) default '0',
  `rsync_module` varchar(255) default NULL,
  `rsync_if_need_auth` enum('TRUE','FALSE') NOT NULL default 'FALSE',
  `rsync_user` varchar(255) default NULL,
  `rsync_pwd` varchar(255) default '',
  `rsync_timeout` int(11) default '0',
  `rsync_params` varchar(255) default '',
  `ftp_host` varchar(255) default NULL,
  `ftp_port` int(10) unsigned default '21',
  `ftp_user` varchar(255) default NULL,
  `ftp_pwd` varchar(255) default NULL,
  `ftp_subdir` varchar(255) default NULL,
  `ftp_server_type` char(2) NOT NULL default '00',
  `ftp_if_use_passive_mode` enum('TRUE','FALSE') NOT NULL default 'FALSE',
  `ftp_transfer_type` enum('ascii','binary') NOT NULL default 'ascii',
  `ftp_use_firewall` char(2) NOT NULL default '00',
  `ftp_proxy_host` varchar(100) default NULL,
  `ftp_proxy_port` int(11) default '21',
  `ftp_proxy_user` varchar(100) default NULL,
  `ftp_proxy_pwd` varchar(100) default NULL,
  `local_copy_path` varchar(255) default NULL,
  `delivery_call_protocol` char(2) NOT NULL default '00',
  `if_delivery_call` char(1) NOT NULL default 'n',
  `delivery_call_cgi` varchar(255) default NULL,
  `web_server_url_prefix` varchar(255) NOT NULL default '',
  `description` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `user_priv` (
  `id` int(10) unsigned NOT NULL default '0' COMMENT '用户id，非自增',
  `u_read_default` enum('TRUE','FALSE') NOT NULL default 'TRUE',
  `u_write_default` enum('TRUE','FALSE') NOT NULL default 'TRUE',
  `u_exec_default` enum('TRUE','FALSE') NOT NULL default 'TRUE',
  `g_read_default` enum('TRUE','FALSE') NOT NULL default 'TRUE',
  `g_write_default` enum('TRUE','FALSE') NOT NULL default 'TRUE',
  `g_exec_default` enum('TRUE','FALSE') NOT NULL default 'TRUE',
  `o_read_default` enum('TRUE','FALSE') NOT NULL default 'TRUE',
  `o_write_default` enum('TRUE','FALSE') NOT NULL default 'TRUE',
  `o_exec_default` enum('TRUE','FALSE') NOT NULL default 'TRUE',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
