DROP TABLE IF EXISTS `{dbprefix}weixin`;
CREATE TABLE IF NOT EXISTS `{dbprefix}weixin` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `name` varchar(100) NOT NULL COMMENT '表名称',
  `value` text CHARACTER SET utf8 NOT NULL COMMENT '字段配置项目',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信配置';

DROP TABLE IF EXISTS `{dbprefix}weixin_follow`;
CREATE TABLE `{dbprefix}weixin_follow` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `openid` varchar(255) NOT NULL COMMENT '唯一id',
  `status` tinyint(1) NOT NULL,
  `uid` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`uid`),
  KEY (`status`),
  KEY (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信粉丝同步表';

DROP TABLE IF EXISTS `{dbprefix}weixin_group`;
CREATE TABLE `{dbprefix}weixin_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `tag` int(10) NOT NULL COMMENT '微信TagId',
  `name` varchar(100) NOT NULL COMMENT '标签名称',
  `count` int(10) NOT NULL COMMENT '粉丝人数',
  `groupid` int(10) NOT NULL COMMENT '用户组id',
  PRIMARY KEY (`id`),
   KEY (`tag`),
   KEY `groupid` (`groupid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信粉丝分组表';

DROP TABLE IF EXISTS `{dbprefix}weixin_user`;
CREATE TABLE `{dbprefix}weixin_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` int(10) unsigned DEFAULT NULL COMMENT '会员id',
  `username` varchar(100) NOT NULL COMMENT '会员名',
  `groupids` varchar(100) NOT NULL COMMENT '标签组id值',
  `openid` varchar(50) NOT NULL COMMENT '唯一id',
  `nickname` varchar(255) NOT NULL COMMENT '微信昵称',
  `sex` tinyint(1) unsigned DEFAULT NULL COMMENT '性别',
  `city` varchar(30) DEFAULT NULL COMMENT '城市',
  `country` varchar(30) DEFAULT NULL COMMENT '国家',
  `province` varchar(30) DEFAULT NULL COMMENT '省',
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '头像地址',
  `unionid` varchar(255) DEFAULT NULL COMMENT '绑定账号',
  `remark` varchar(255) DEFAULT NULL COMMENT '对粉丝的备注',
  `subscribe` int(10) unsigned NOT NULL COMMENT '关注',
  `subscribe_time` int(10) unsigned NOT NULL COMMENT '关注时间',
  `content` text NOT NULL COMMENT '字段',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`openid`),
  KEY `subscribe` (`subscribe`),
  KEY `subscribe_time` (`subscribe_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信粉丝表';

DROP TABLE IF EXISTS `{dbprefix}weixin_menu`;
CREATE TABLE `{dbprefix}weixin_menu` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `pid` int(10) NOT NULL COMMENT '父id',
  `name` varchar(255) NOT NULL COMMENT '菜单名称',
  `type` varchar(20) NOT NULL COMMENT '菜单类型',
  `value` text NOT NULL COMMENT '执行值',
  `displayorder` int(10) NOT NULL COMMENT '排序值',
  `gid` int(10) NOT NULL COMMENT '标签组id',
  `menuid` int(10) NOT NULL COMMENT '个性菜单id',
  PRIMARY KEY (`id`),
  KEY (`pid`),
  KEY (`displayorder`),
  KEY (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信自定义菜单表';


DROP TABLE IF EXISTS `{dbprefix}weixin_content`;
CREATE TABLE `{dbprefix}weixin_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `tid` varchar(20) NOT NULL COMMENT '素材类型',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容值数组',
  `media_id` varchar(255) NOT NULL COMMENT '微信media_id',
  `inputtime` int(10) NOT NULL COMMENT '录入时间',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信素材内容表';

DROP TABLE IF EXISTS `{dbprefix}weixin_media_id`;
CREATE TABLE `{dbprefix}weixin_media_id` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `file` varchar(255) NOT NULL COMMENT '文件编码',
  `media_id` varchar(255) NOT NULL COMMENT '微信media_id',
  `url` varchar(255) NOT NULL COMMENT '对应地址',
  PRIMARY KEY (`id`),
  KEY `file` (`file`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信素材多媒体id表';

DROP TABLE IF EXISTS `{dbprefix}weixin_send`;
CREATE TABLE `{dbprefix}weixin_send` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `cid` int(10) NOT NULL COMMENT 'cid',
  `openid` varchar(255) NOT NULL COMMENT 'openid',
  `content` text NOT NULL COMMENT '内容值数组',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '状态1成功，2失败，0未发送',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信客服消息发布表';

DROP TABLE IF EXISTS `{dbprefix}weixin_message`;
CREATE TABLE `{dbprefix}weixin_message` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `tid` varchar(20) NOT NULL COMMENT '消息类型',
  `userid` int(10) NOT NULL COMMENT '用户id',
  `openid` varchar(255) NOT NULL COMMENT 'openid',
  `nickname` varchar(255) NOT NULL COMMENT '昵称',
  `headimgurl` varchar(255) NOT NULL COMMENT '头像',
  `content` text NOT NULL COMMENT '消息内容',
  `file` text NOT NULL COMMENT 'xiazia',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '状态1更新，0未更新',
  `inputtime` int(10) NOT NULL COMMENT '录入时间',
  PRIMARY KEY (`id`),
  KEY `openid` (`openid`),
  KEY `userid` (`userid`),
  KEY `nickname` (`nickname`),
  KEY `inputtime` (`inputtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信消息记录表';

DROP TABLE IF EXISTS `{dbprefix}weixin_count_fans`;
CREATE TABLE `{dbprefix}weixin_count_fans` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `new` int(10) NOT NULL COMMENT '新关注',
  `cancel` int(10) NOT NULL COMMENT '取消关注',
  `cumulate` int(10) NOT NULL COMMENT '统计关注',
  `date` int(10) NOT NULL COMMENT '时间段',
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信粉丝统计';


DROP TABLE IF EXISTS `{dbprefix}weixin_reply`;
CREATE TABLE `{dbprefix}weixin_reply` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `tid` varchar(20) NOT NULL COMMENT '素材类型',
  `title` varchar(255) NOT NULL COMMENT '规则名称',
  `keyword` varchar(255) NOT NULL COMMENT '关键字',
  `content` text NOT NULL COMMENT '内容值数组',
  `counts` int(10) NOT NULL COMMENT '命中次数',
  `displayorder` int(10) NOT NULL COMMENT '权重值',
  `updatetime` int(10) NOT NULL COMMENT '最近命中时间',
  PRIMARY KEY (`id`),
  KEY `keyword` (`keyword`),
  KEY `displayorder` (`displayorder`),
  KEY `updatetime` (`updatetime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信素材自动回复表';
