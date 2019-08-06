<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 系统配置文件
 */

return [

	'SYS_DEBUG'                     => 0, //调试器开关
	'SYS_EMAIL'                     => '751586159@qq.com', //系统收件邮箱，用于接收系统信息
	'SYS_ADMIN_CODE'                => 0, //后台登录验证码开关
	'SYS_ADMIN_LOG'                 => 0, //后台操作日志开关
	'SYS_AUTO_FORM'                 => 0, //自动存储表单数据
	'SYS_ADMIN_PAGESIZE'            => 10, //后台数据分页显示数量
	'SYS_CAT_RNAME'                 => 0, //栏目目录允许重复
	'SYS_PAGE_RNAME'                => 0, //自定义页面目录允许重复
	'SYS_KEY'                       => 'PHPCMFde0fb612742a6d780cf308c0f04bb38e', //安全密匙
	'SYS_HTTPS'                     => 0, //https模式
	'SYS_ATTACHMENT_DB'             => '', //附件归属开启模式
	'SYS_ATTACHMENT_PATH'           => '', //附件上传路径
	'SYS_ATTACHMENT_URL'            => '', //附件访问地址
	'SYS_BDMAP_API'                 => '', //百度地图API
	'SYS_THEME_ROOT'                => '', //风格目录引用作用域
	'SYS_FIELD_THUMB_ATTACH'        => '', //缩略图字段存储策略
	'SYS_FIELD_CONTENT_ATTACH'      => '', //内容字段存储策略

];