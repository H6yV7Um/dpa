
-- --------------------------------------------------------

--
-- 表的结构 `aups_t001`
--

CREATE TABLE IF NOT EXISTS `aups_t001` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核, 0:无需审核,能直接显示;1:需审核,不能直接发布,需要审核通过才能发布',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  `aups_f001` text COMMENT '内容',
  `aups_f002` varchar(255) default NULL COMMENT '图片1',
  `aups_f003` varchar(255) default NULL COMMENT '标题1',
  `aups_f004` varchar(255) default NULL COMMENT '链接1',
  `aups_f005` varchar(255) default NULL COMMENT '图片2',
  `aups_f006` varchar(255) default NULL COMMENT '标题2',
  `aups_f007` varchar(255) default NULL COMMENT '链接2',
  `aups_f008` varchar(255) default NULL COMMENT '图片3',
  `aups_f009` varchar(255) default NULL COMMENT '标题3',
  `aups_f010` varchar(255) default NULL COMMENT '链接3',
  `aups_f011` varchar(200) default NULL COMMENT '说明',
  PRIMARY KEY  (`id`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='页面碎片';

-- --------------------------------------------------------

--
-- 表的结构 `aups_t002`
--

CREATE TABLE IF NOT EXISTS `aups_t002` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核, 0:无需审核,能直接显示;1:需审核,不能直接发布,需要审核通过才能发布',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  `aups_f012` varchar(200) default NULL COMMENT '文档标题',
  `aups_f013` varchar(200) default NULL COMMENT '副标题',
  `aups_f014` varchar(200) default NULL COMMENT '来源',
  `aups_f015` varchar(200) default NULL COMMENT '其他来源',
  `aups_f016` varchar(200) default NULL COMMENT '主题词',
  `aups_f017` varchar(200) default NULL COMMENT '作者',
  `aups_f018` text COMMENT '摘要',
  `aups_f019` varchar(200) default NULL COMMENT '备注',
  `aups_f020` int(11) default '70' COMMENT '权重',
  `aups_f032` varchar(200) default NULL COMMENT '期号',
  `aups_f041` varchar(200) default NULL COMMENT '功能代码',
  `aups_f050` varchar(200) default NULL COMMENT '机构名称',
  `aups_f055` varchar(200) default NULL COMMENT '是否显示心情',
  `aups_f056` mediumtext COMMENT '正文',
  `s_shu_chengshi` varchar(60) default NULL COMMENT '所属城市, 可能是地级市，也可能是直辖市。地级市以上包括直辖市, 如可能是孝感，武汉，北京等能作为首页的',
  `aups_f057` varchar(200) default NULL COMMENT '所属栏目',
  `aups_f058` varchar(200) default NULL COMMENT '所属子栏目',
  `aups_f059` varchar(200) default NULL COMMENT '所属专题',
  `aups_f060` varchar(200) default NULL COMMENT '所属专题子栏目',
  `aups_f061` varchar(200) default NULL COMMENT '所属专题2',
  `aups_f062` varchar(200) default NULL COMMENT '所属专题子栏目2',
  `aups_f063` varchar(200) default NULL COMMENT '附件',
  `aups_f064` varchar(200) default NULL COMMENT '推荐小图',
  `aups_f065` varchar(200) default NULL COMMENT '图片',
  `aups_f066` varchar(200) default NULL COMMENT '图注',
  `aups_f067` text COMMENT '相关报道',
  `aups_f068` varchar(200) default NULL COMMENT '视频链接',
  `aups_f069` varchar(200) default NULL COMMENT '是否显示评论',
  PRIMARY KEY  (`id`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='正文页';

-- --------------------------------------------------------

--
-- 表的结构 `aups_t003`
--

CREATE TABLE IF NOT EXISTS `aups_t003` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核, 0:无需审核,能直接显示;1:需审核,不能直接发布,需要审核通过才能发布',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  `aups_f070` varchar(200) default NULL COMMENT '栏目名称',
  `aups_f071` smallint(3) unsigned default NULL COMMENT '级别',
  `aups_f072` varchar(50) default NULL COMMENT '英文缩写',
  `aups_f073` varchar(200) default NULL COMMENT '保存路径',
  `aups_f074` varchar(200) default NULL COMMENT '链接',
  `aups_f075` float default NULL COMMENT '显示顺序',
  `aups_f076` varchar(200) default NULL COMMENT '关键词',
  `aups_f077` text COMMENT '描述',
  `aups_f078` varchar(200) default NULL COMMENT '所属栏目',
  PRIMARY KEY  (`id`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`),
  KEY `name_cn_jibie` (`aups_f071`,`aups_f070`,`aups_f072`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='栏目配置';

--
-- 导出表中的数据 `aups_t003`
--

-- --------------------------------------------------------

--
-- 表的结构 `aups_t004`
--

CREATE TABLE IF NOT EXISTS `aups_t004` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核, 0:无需审核,能直接显示;1:需审核,不能直接发布,需要审核通过才能发布',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  `aups_f079` varchar(200) default NULL COMMENT '媒体名称',
  `aups_f080` varchar(200) default NULL COMMENT '链接',
  `aups_f081` text COMMENT '备注',
  `aups_f082` float default '100' COMMENT '显示顺序',
  `aups_f083` varchar(200) default NULL COMMENT '英文缩写',
  PRIMARY KEY  (`id`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='媒体配置';

--
-- 导出表中的数据 `aups_t004`
--

-- --------------------------------------------------------

--
-- 表的结构 `aups_t005`
--

CREATE TABLE IF NOT EXISTS `aups_t005` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核, 0:无需审核,能直接显示;1:需审核,不能直接发布,需要审核通过才能发布',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  `aups_f084` varchar(255) default NULL COMMENT '说明',
  `aups_f085` mediumtext COMMENT '代码',
  `aups_f086` mediumtext COMMENT '备份',
  PRIMARY KEY  (`id`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='CSS模板';

-- --------------------------------------------------------

--
-- 表的结构 `aups_t006`
--

CREATE TABLE IF NOT EXISTS `aups_t006` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核, 0:无需审核,能直接显示;1:需审核,不能直接发布,需要审核通过才能发布',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  `aups_f087` varchar(255) default NULL COMMENT '说明',
  `aups_f088` mediumtext COMMENT '代码',
  `aups_f089` mediumtext COMMENT '备份',
  PRIMARY KEY  (`id`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='JS模板';

-- --------------------------------------------------------

--
-- 表的结构 `aups_t007`
--

CREATE TABLE IF NOT EXISTS `aups_t007` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核, 0:无需审核,能直接显示;1:需审核,不能直接发布,需要审核通过才能发布',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括字段的模板代码html,js等和发布文档列表算法, [f_tmpl_design_default_html]<html>也可以是字段的模板html代码，例如栏目页发布的时候可能需要生成栏目页，栏目页的模板不一定在模板设计表中的，优先使用最近的模板设计html模板</html>[publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  `aups_f090` varchar(255) default NULL COMMENT '栏目名称',
  `aups_f097` varchar(255) default NULL COMMENT '栏目名称',
  PRIMARY KEY  (`id`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='栏目页';

--
-- 导出表中的数据 `aups_t007`
--

-- --------------------------------------------------------

--
-- 表的结构 `aups_t008`
--

CREATE TABLE IF NOT EXISTS `aups_t008` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核, 0:无需审核,能直接显示;1:需审核,不能直接发布,需要审核通过才能发布',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  `aups_f099` varchar(255) NOT NULL default 'yes' COMMENT '专题是否显示',
  `aups_f100` varchar(255) default '/zt/1.shtml' COMMENT '专题链接url',
  `aups_f101` varchar(255) default NULL COMMENT '专题名称',
  `aups_f102` varchar(255) default NULL COMMENT '专题英文名',
  `aups_f103` mediumtext COMMENT 'head区',
  `aups_f104` mediumtext COMMENT '顶通',
  `aups_f105` mediumtext COMMENT '通栏01',
  `aups_f106` mediumtext COMMENT '通栏02',
  `aups_f107` varchar(255) default NULL COMMENT '通栏03',
  `aups_f108` varchar(255) default NULL COMMENT '通栏04',
  `aups_f109` varchar(255) default NULL COMMENT '通栏05',
  `aups_f110` varchar(255) default NULL COMMENT '通栏06',
  `aups_f111` varchar(255) default NULL COMMENT '通栏07',
  `aups_f112` varchar(255) default NULL COMMENT '通栏08',
  `aups_f113` varchar(255) default NULL COMMENT '通栏09',
  `aups_f114` varchar(255) default NULL COMMENT '通栏10',
  PRIMARY KEY  (`id`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='专题页';

--
-- 导出表中的数据 `aups_t008`
--

-- --------------------------------------------------------

--
-- 表的结构 `aups_t009`
--

CREATE TABLE IF NOT EXISTS `aups_t009` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核, 0:无需审核,能直接显示;1:需审核,不能直接发布,需要审核通过才能发布',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  `aups_f115` varchar(255) default NULL COMMENT '说明',
  `aups_f116` mediumtext COMMENT '内容',
  PRIMARY KEY  (`id`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='功能代码';

--
-- 导出表中的数据 `aups_t009`
--

-- --------------------------------------------------------

--
-- 表的结构 `aups_t010`
--

CREATE TABLE IF NOT EXISTS `aups_t010` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核, 0:无需审核,能直接显示;1:需审核,不能直接发布,需要审核通过才能发布',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  `aups_f117` mediumtext COMMENT '内容',
  `aups_f118` mediumtext COMMENT '备份',
  PRIMARY KEY  (`id`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='空白模板';

--
-- 导出表中的数据 `aups_t010`
--

-- --------------------------------------------------------

--
-- 表的结构 `aups_t011`
--

CREATE TABLE IF NOT EXISTS `aups_t011` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',
  `creator` varchar(100) NOT NULL default '0' COMMENT '创建者',
  `createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',
  `createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',
  `mender` varchar(100) default NULL COMMENT '修改者',
  `menddate` date default NULL COMMENT '修改日期',
  `mendtime` time default NULL COMMENT '修改时间',
  `expireddate` date NOT NULL default '0000-00-00' COMMENT '过期日期',
  `audited` enum('0','1') NOT NULL default '0' COMMENT '是否审核, 0:无需审核,能直接显示;1:需审核,不能直接发布,需要审核通过才能发布',
  `status_` enum('use','stop','test','del','scrap') NOT NULL default 'use' COMMENT '状态, 使用、停用等',
  `flag` int(11) NOT NULL default '0' COMMENT '标示, 预留',
  `arithmetic` text COMMENT '文档算法, 包括发布文档列表算法, [publish_docs]1:28:1,1:28:2,,,,',
  `unicomment_id` varchar(30) default NULL COMMENT '评论唯一ID, 1-2-36963:项目id-表id-评论id',
  `published_1` enum('0','1') NOT NULL default '0' COMMENT '是否发布, 0:不发布;1:发布,通常都是发布的',
  `url_1` varchar(255) default NULL COMMENT '文档发布成html的外网url,通常是省略了域名的相对地址',
  `last_modify` timestamp NOT NULL COMMENT '最近修改时间',
  PRIMARY KEY  (`id`),
  KEY `createdate` (`createdate`,`createtime`),
  KEY `menddate` (`menddate`,`mendtime`),
  KEY `expireddate` (`expireddate`),
  KEY `audited` (`audited`),
  KEY `status_` (`status_`),
  KEY `published_1` (`published_1`),
  KEY `url_1` (`url_1`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='频道首页';

--
-- 导出表中的数据 `aups_t011`
--
