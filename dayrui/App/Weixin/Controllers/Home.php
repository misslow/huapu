<?php namespace Phpcmf\Controllers;

class Home extends \Phpcmf\Common {

    private $token;
    private $access_token;

    public function index() {

        if ($this->checkSignature()) {
            
            // 判读是不是只是验证
            $echostr = \Phpcmf\Service::L('Input')->get('echostr', true);
            if ($echostr) {
                echo $echostr;exit;
            } else {
                // 实际处理用户消息
                $content = file_get_contents('php://input');
                #file_put_contents(WRITEPATH."weixin/wx.txt", var_export($content, true));
                if ($content) {
                    // 解析微信传过来的 XML 内容 转化为数组
                    $this->data = dr_object2array(simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA));
                    $this->token = $this->data['ToUserName'];
                    $rt = weixin_get_access_token();
                    $rt['code'] && $this->access_token = $rt['msg'];
                    // 初始化用户 将其加入到粉丝组里面
                    $this->_init_user();
                    // 存储和分析请求
                    switch ($this->data['MsgType']) {
                        
                        case 'text':
                            # 文本消息
                            \Phpcmf\Service::M('Weixin', 'Weixin')->save_message($this->data);
                            // 搜索自定义回复
                            $data = \Phpcmf\Service::M()->table(weixin_wxtable('reply'))->where('`keyword` LIKE "%'.$this->data['Content'].'%"')->order_by('displayorder desc')->getRow();
                            #log_message('error', ''.\Phpcmf\Service::M()->get_sql_query());
                            if ($data) {
                                // 记录
                                \Phpcmf\Service::M()->table(weixin_wxtable('reply'))->update($data['id'], [
                                    'counts' => $data['counts'] + 1,
                                    'updatetime' => SYS_TIME,
                                ]);
                                if ($data['tid']) {
                                    // 回复素材
                                    $this->_to_weixin_content($data['content']);
                                } else {
                                    // 回复文本
                                    $this->_to_weixin_text($data['content']);
                                }
                            }
                            // 搜索模块内容回复
                            if ($this->weixin['config']['ss']) {
                                // 搜索模块内容回复
                                if ($this->weixin['config']['ss_dir']) {
                                    // 搜索模块内容
                                    foreach ($this->weixin['config']['ss_dir'] as $dirname) {
                                        foreach ($this->site as $siteid) {
                                            $table = dr_module_table_prefix($dirname, $siteid);
                                            // 判断是否存在表
                                            if (!\Phpcmf\Service::M()->db->tableExists(\Phpcmf\Service::M()->dbprefix($table))) {
                                                continue;
                                            }
                                            // 查询关键词
                                            $data = \Phpcmf\Service::M()->db->table($table)->where('`title` LIKE "%'.$this->data['Content'].'%"')->orderBy('id desc')->limit(8)->get()->getResultArray();
                                            #log_message('error', ''.\Phpcmf\Service::M()->get_sql_query());
                                            if ($data) {
                                                // 查询成功，回复他们
                                                $cdata = [
                                                    'tid' => 'index',
                                                    'content' => [],
                                                ];
                                                foreach ($data as $ii => $t) {
                                                    $i = 10 + $ii;
                                                    $cdata['content'][$i] = [
                                                        'title_'.$i => $t['title'],
                                                        'author_'.$i => $t['author'],
                                                        'thumb_'.$i => dr_get_file($t['thumb']),
                                                        'description_'.$i => $t['description'],
                                                        'url_'.$i => dr_url_prefix($t['url']),
                                                    ];
                                                    if ($t['thumb'] && !isset($cdata['content'][1])) {
                                                        $cdata['content'][1] = [
                                                            'title_1' => $t['title'],
                                                            'author_1' => $t['author'],
                                                            'thumb_1' => dr_get_file($t['thumb']),
                                                            'description_1' => $t['description'],
                                                            'url_1' => dr_url_prefix($t['url']),
                                                        ];
                                                        unset($cdata['content'][$i]);
                                                    }
                                                }
                                                ksort($cdata['content']);
                                                // 回复内容
                                                $this->_to_weixin_content_data($cdata);
                                                return;
                                            }
                                        }
                                    }
                                }
                                // 没有找到回复内容
                                if ($this->weixin['config']['ss_type']) {
                                    // 回复素材
                                    $this->_to_weixin_content($this->weixin['config']['ss_value_1']);
                                } else {
                                    // 回复文本
                                    $this->_to_weixin_text($this->weixin['config']['ss_value_0']);
                                }
                            }
                            break;
                        case 'image':
                            # 图片消息
                        case 'voice':
                            # 语音消息
                        case 'video':
                            # 视频消息
                        case 'shortvideo':
                            # 小视频消息
                        case 'link':
                            # 链接消息
                            \Phpcmf\Service::M('Weixin', 'Weixin')->save_message($this->data);
                            break;
                        case 'location':
                            # 地理位置消息
                            break;
                        case 'event':
                            # 事件推送

                            switch ($this->data['Event']) {

                                case 'subscribe':
                                    // 关注时
                                    // 统计数据
                                    $fans = \Phpcmf\Service::M()->table(weixin_wxtable('count_fans'))->where('date', date('Ymd'))->getRow();
                                    if (!$fans) {
                                        // 今日新关注
                                        $usercount = \Phpcmf\Service::M()->table(weixin_wxtable('user'))->counts();
                                        $rt = \Phpcmf\Service::M()->table(weixin_wxtable('count_fans'))->insert([
                                            'new' => 1,
                                            'cancel' => 0,
                                            'cumulate' => $usercount,
                                            'date' => date('Ymd'),
                                        ]);
                                    } else {
                                        $rt = \Phpcmf\Service::M()->table(weixin_wxtable('count_fans'))->update($fans['id'], [
                                            'new' => $fans['new'] + 1,
                                            'cumulate' => $fans['cumulate'] + 1,
                                        ]);
                                    }
                                    // 回复关注信息
                                    if ($this->weixin['config']['gz']) {
                                        if ($this->weixin['config']['gz_type']) {
                                            // 回复素材
                                            $this->_to_weixin_content($this->weixin['config']['gz_value_1']);
                                        } else {
                                            // 回复文本
                                            $this->_to_weixin_text($this->weixin['config']['gz_value_0']);
                                        }
                                    }
                                    break;

                                case 'unsubscribe':
                                    // 取消关注时
                                    $fans = \Phpcmf\Service::M()->table(weixin_wxtable('count_fans'))->where('date', date('Ymd'))->getRow();
                                    if (!$fans) {
                                        // 今日新取消关注
                                        $usercount = \Phpcmf\Service::M()->table(weixin_wxtable('user'))->counts();
                                        $rt = \Phpcmf\Service::M()->table(weixin_wxtable('count_fans'))->insert([
                                            'new' => 0,
                                            'cancel' => 1,
                                            'cumulate' => $usercount,
                                            'date' => date('Ymd'),
                                        ]);
                                    } else {
                                        $rt = \Phpcmf\Service::M()->table(weixin_wxtable('count_fans'))->update($fans['id'], [
                                            'cancel' => $fans['new'] + 1,
                                            'cumulate' => max(0, $fans['cumulate'] - 1),
                                        ]);
                                    }
                                    // 删除各种记录
                                    \Phpcmf\Service::M()->db->table(weixin_wxtable('user'))->where('openid', $this->data['FromUserName'])->delete();
                                    \Phpcmf\Service::M()->db->table(weixin_wxtable('follow'))->where('openid', $this->data['FromUserName'])->delete();
                                    \Phpcmf\Service::M()->db->table(weixin_wxtable('message'))->where('openid', $this->data['FromUserName'])->delete();
                                    \Phpcmf\Service::M()->db->table('member_oauth')->where('oauth', 'wechat')->where('oid', $this->data['FromUserName'])->delete();
                                    break;

                                default:
                                    if (strpos($this->data['EventKey'], 'App::') === 0) {
                                        // 表示执行插件控制器
                                        $file = APPPATH.'Plugins/'.ucfirst(substr($this->data['EventKey'], 5)).'/Run.php';
                                        if (!is_file($file)) {
                                            log_message('error', '微信小插件运行程序不存在：'.$file);
                                        } else {
                                            require $file;
                                        }
                                    } 
                                    break;

                            }
                            
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                }
            }

        }

        exit;
    }


    // 初始化用户情况
    private function _init_user() {


        $rt = wx_get_https_json_data('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid='.$this->data['FromUserName']);
        if (!$rt['code']) {
            return;
        } elseif (!$rt['data']['nickname']) {
            return;
        }

        $user = \Phpcmf\Service::M()->table(weixin_wxtable('user'))->where('openid', $this->data['FromUserName'])->getRow();
        if (!$user) {
            // 入库粉丝表
            $user['id'] = \Phpcmf\Service::M('User', APP_DIR)->insert_user($rt['data']);
        } else {
            // 更新
            $user = \Phpcmf\Service::M('User', APP_DIR)->update_user($user, $rt['data']);;
        }

        // 判断当用户没有绑定会员的情况时
        if (!$user['uid']) {

            if ($this->weixin['config']['bang']) {
                // 提醒用户绑定会员
                $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->weixin['account']['appid'].'&redirect_uri='.urlencode(SITE_URL.'index.php?s=weixin&c=member').'&response_type=code&scope=snsapi_base&state=member#wechat_redirect';
                $txt = $this->weixin['config']['bang_text'] ? $this->weixin['config']['bang_text'] : '您还没有绑定本站账号，点我立即绑定账号';
                $this->_to_weixin_text('<a href="'.$url.'">'.$txt.'</a>');
            }
            return;
        }

        
        // 存储cookie
        $this->uid = $user['uid'];
        $this->member = \Phpcmf\Service::M('member')->get_member($this->uid);
        \Phpcmf\Service::M('member')->save_cookie($this->member);
        
        return;
    }


    private function checkSignature()
    {

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = $this->weixin['account']['token'];
        $tmpArr = [$token, $timestamp, $nonce];

        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if ($tmpStr == $signature){
            return true;
        } else{
            return false;
        }
    }

    // ================================================ 
    
    // 回复文本
    public function _to_weixin_text($text) {
        if (!$text) {
            return;
        }
echo '<xml>
<ToUserName><![CDATA['.$this->data['FromUserName'].']]></ToUserName>
<FromUserName><![CDATA['.$this->data['ToUserName'].']]></FromUserName>
<CreateTime>'.(SYS_TIME+2).'</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA['.$text.']]></Content>
</xml>';exit;
    }
    
    // 回复素材
    public function _to_weixin_content($id) {

        $id = (int)substr($id, 5);
        if (!$id) {
            return;
        }

        // 查询内容
        $data = \Phpcmf\Service::M()->table(weixin_wxtable('content'))->get($id);
        if (!$data) {
            return;
        }

        $value = dr_string2array($data['content']);
        $data['content'] = [];
        if ($value) {
            for ($i = 1; $i<=9; $i++) {
                if (!$value['thumb_'.$i] || !$value['title_'.$i]) {
                    break;
                }
                $data['content'][$i] = [
                    "title_".$i => $value['title_'.$i],
                    "thumb_".$i => dr_get_file($value['thumb_'.$i]),
                    "description_".$i => $value['description_'.$i],
                    "url_".$i => dr_url_prefix($value['url_'.$i])
                ];
            }
        }
        #file_put_contents(WRITEPATH."weixin/wx.txt", var_export($data, true));

        return $this->_to_weixin_content_data($data);
    }

    // 开始回复
    public function _to_weixin_content_data($cdata) {

        switch ($cdata['tid']) {

            case 'index':
                // 回复图文
                $count = 0;
                $text = '';
                foreach ($cdata['content'] as $i => $t) {
                    if ($t['title_'.$i]) {
                        $count ++;
                        $text.= '<item>
<Title><![CDATA['.$t['title_'.$i].']]></Title> 
<Description><![CDATA['.$t['description_'.$i].']]></Description>
<PicUrl><![CDATA['.$t['thumb_'.$i].']]></PicUrl>
<Url><![CDATA['.$t['url_'.$i].']]></Url>
</item>';
                    }
                }
                if (!$count) {
                    $this->_to_weixin_text('没有找到相关内容');
                    return;
                }
exit('
<xml>
<ToUserName><![CDATA['.$this->data['FromUserName'].']]></ToUserName>
<FromUserName><![CDATA['.$this->data['ToUserName'].']]></FromUserName>
<CreateTime>'.(SYS_TIME+2).'</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>'.$count.'</ArticleCount>
<Articles>
'.$text.'
</Articles>
</xml>
');
                break;

            case 'image':

                if (!$cdata['media_id']) {
                    $this->_to_weixin_text('没有找到相关内容');
                    return;
                }
                
                exit('<xml>
<ToUserName><![CDATA['.$this->data['FromUserName'].']]></ToUserName>
<FromUserName><![CDATA['.$this->data['ToUserName'].']]></FromUserName>
<CreateTime>'.(SYS_TIME+2).'</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
<Image>
<MediaId><![CDATA['.$cdata['media_id'].']]></MediaId>
</Image>
</xml>');
                break;

            case 'voice':

                if (!$cdata['media_id']) {
                    $this->_to_weixin_text('没有找到相关内容');
                    return;
                }
                
                exit('<xml>
<ToUserName><![CDATA['.$this->data['FromUserName'].']]></ToUserName>
<FromUserName><![CDATA['.$this->data['ToUserName'].']]></FromUserName>
<CreateTime>'.(SYS_TIME+2).'</CreateTime>
<MsgType><![CDATA[voice]]></MsgType>
<Image>
<MediaId><![CDATA['.$cdata['media_id'].']]></MediaId>
</Image>
</xml>');
                break;

            case 'video':
                if (!$cdata['media_id']) {
                    $this->_to_weixin_text('没有找到相关内容');
                    return;
                }
                
                exit('<xml>
<ToUserName><![CDATA['.$this->data['FromUserName'].']]></ToUserName>
<FromUserName><![CDATA['.$this->data['ToUserName'].']]></FromUserName>
<CreateTime>'.(SYS_TIME+2).'</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
<Image>
<MediaId><![CDATA['.$cdata['media_id'].']]></MediaId>
</Image>
</xml>');
                break;

            case 'link':

                exit('<xml>
<ToUserName><![CDATA['.$this->data['FromUserName'].']]></ToUserName>
<FromUserName><![CDATA['.$this->data['ToUserName'].']]></FromUserName>
<CreateTime>'.(SYS_TIME+2).'</CreateTime>
<MsgType><![CDATA[link]]></MsgType>
<Title><![CDATA['.$cdata['title'].']]></Title>
<Description><![CDATA['.$cdata['description'].']]></Description>
<Url><![CDATA['.$cdata['url'].']]></Url>
</xml>');
                break;


        }
        
        $this->_to_weixin_text('没有找到相关内容');

    }


    /* 组装xml数据 */
    private function _data2xml($xml, $data, $item = 'item') {
        foreach ( $data as $key => $value ) {
            is_numeric ( $key ) && ($key = $item);
            if (is_array ( $value ) || is_object ( $value )) {
                $child = $xml->addChild ( $key );
                $this->_data2xml ( $child, $value, $item );
            } else {
                if (is_numeric ( $value )) {
                    $child = $xml->addChild ( $key, $value );
                } else {
                    $child = $xml->addChild ( $key );
                    $node = dom_import_simplexml ( $child );
                    $node->appendChild ( $node->ownerDocument->createCDATASection ( $value ) );
                }
            }
        }
    }
}
