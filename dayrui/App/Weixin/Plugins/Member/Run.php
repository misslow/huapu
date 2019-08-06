<?php
/**
 * 前端运行控制器
 */

$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->weixin['account']['appid'].'&redirect_uri='.urlencode(SITE_URL.'index.php?s=weixin&c=member').'&response_type=code&scope=snsapi_base&state=member#wechat_redirect';
$txt = '小插件提醒：单击进入用户中心';
$this->_to_weixin_text('<a href="'.$url.'">'.$txt.'</a>');