<?php namespace Phpcmf\Controllers;

// 绑定账号
class Member extends \Phpcmf\Common {

    // 从微信菜单中进入授权
    public function index() {

        $code = \Phpcmf\Service::L('input')->get('code');
        $state = \Phpcmf\Service::L('input')->get('state');

        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->weixin['account']['appid'].'&secret='.$this->weixin['account']['appsecret'].'&code='.$code.'&state='.$state.'&grant_type=authorization_code';
        $rt = wx_get_https_json_data($url);
        if (!$rt['code']) {
            $this->_msg(0, $rt['msg']);
        }

        $user = \Phpcmf\Service::M()->table(weixin_wxtable('user'))->where('openid', $rt['data']['openid'])->getRow();
        if (!$user) {
            $rts = weixin_get_access_token();
            $rts['code'] && $this->access_token = $rts['msg'];
            $rts = wx_get_https_json_data('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid='.$rt['data']['openid']);
            if (!$rts['code']) {
                $this->_msg(0, $rts['msg']);
            } elseif (!$rts['data']['nickname']) {
                $this->_msg(0, '未获取到微信用户昵称');
            }
            $user = $rts['data'];
            $user['id'] = \Phpcmf\Service::M('User', APP_DIR)->insert_user($rts['data']);
        }

        $oid = $rt['data']['openid'];
        $rt = \Phpcmf\Service::M('member')->insert_oauth(0, 'login', [
            'oid' => $oid,
            'oauth' => 'wechat',
            'avatar' => $user['headimgurl'],
            'nickname' => dr_emoji2html($user['nickname']),
            'expire_at' => SYS_TIME,
            'access_token' => 0,
            'refresh_token' => 0,
        ], $state);
        if (!$rt['code']) {
            $this->_msg(0, $rt['msg']);
            exit;
        } else {
            dr_redirect($rt['msg']);
        }
        exit;
    }

    // 小程序登录
    public function xcx() {

        if (IS_POST) {

            $json = json_decode($_POST['json'], true);
            if (!$json) {
                $this->_json(0, 'POST数据解析失败');
            }


            $url = "https://api.weixin.qq.com/sns/jscode2session?appid='.$this->weixin['xcx']['appid'].'&secret='.$this->weixin['xcx']['appsecret'].'&js_code=".$_POST['js_code']."&grant_type=authorization_code";
            $token = json_decode(dr_catcher_data($url), true);

            $rt = \Phpcmf\Service::M('member')->insert_oauth(0, 'login', [
                'oid' => $token['openid'],
                'oauth' => 'wxxcx',
                'avatar' => $json['avatarUrl'],
                'nickname' => dr_emoji2html($json['nickName']),
                'expire_at' => SYS_TIME,
                'access_token' => $token['session_key'],
                'refresh_token' => '',
            ]);
            if (!$rt['code']) {
                $this->_json(0, $rt['msg']);
            }


            $oauth = \Phpcmf\Service::M()->table('member_oauth')->get($rt['code']);
            if (!$oauth) {
                $this->_json(0, '服务端储存用户失败');
            } elseif ($oauth['uid']) {
                $rt = \Phpcmf\Service::M('member')->login_uid($oauth, $oauth['uid']);
                if (!$rt['code']) {
                    $this->_json(0, $rt['msg']);
                }
                $this->_json(1, 'login', $rt['data']);
            }

            if (0) {
                // 直接登录
                $rt = \Phpcmf\Service::M('member')->register_oauth(0, $oauth);
                if ($rt['code']) {
                    // 登录成功
                    $this->_json(1, 'login', $rt['data']);
                } else {
                    $this->_json(0, $rt['msg']);
                }
            } else {
                // 提示注册
                $oauth['nickname'] = dr_html2emoji($oauth['nickname']);
                $this->_json(1, 'register', $oauth);
            }

        } else {
            $this->_json(0, '非POST提交');
        }

        exit;
    }

    // 小程序绑定账号
    public function xcx_bang() {

        if (IS_POST) {

            $oid = (int)\Phpcmf\Service::L('Input')->post('oid');
            $post = \Phpcmf\Service::L('Input')->post('data', true);
            if (!$post) {
                $this->_json(0, 'POST数据解析失败');
            } elseif (!$oid) {
                $this->_json(0, '小程序OpenId为空');
            }

            $oauth = \Phpcmf\Service::M()->table('member_oauth')->get($oid);
            if (!$oauth) {
                $this->_json(0, '服务端用户不存在');
            } elseif ($oauth['uid']) {
                $rt = \Phpcmf\Service::M('member')->login_uid($oauth, $oauth['uid']);
                if (!$rt['code']) {
                    $this->_json(0, $rt['msg']);
                }
                $this->_json(1, 'ok', $rt['data']);
            }

            // 登录绑定
            if (empty($post['username']) || empty($post['password'])) {
                $this->_json(0, dr_lang('账号或密码必须填写'));
            } else {
                $rt = \Phpcmf\Service::M('member')->login($post['username'], $post['password']);
                if ($rt['code']) {
                    // 登录成功
                    \Phpcmf\Service::M()->db->table('member_oauth')->where('id', $oid)->update(['uid' => $rt['data']['member']['id']]);
                    $this->_json(1, 'ok', $rt['data']);
                } else {
                    $this->_json(0, $rt['msg']);
                }
            }



        } else {
            $this->_json(0, '非POST提交');
        }
    }


    // 小程序注册账号
    public function xcx_reg() {

        if (IS_POST) {

            $oid = (int)\Phpcmf\Service::L('Input')->post('oid');
            $post = \Phpcmf\Service::L('Input')->post('data', true);
            if (!$post) {
                $this->_json(0, 'POST数据解析失败');
            } elseif (!$oid) {
                $this->_json(0, '小程序OpenId为空');
            }

            $oauth = \Phpcmf\Service::M()->table('member_oauth')->get($oid);
            if (!$oauth) {
                $this->_json(0, '服务端用户不存在');
            } elseif ($oauth['uid']) {
                $rt = \Phpcmf\Service::M('member')->login_uid($oauth, $oauth['uid']);
                if (!$rt['code']) {
                    $this->_json(0, $rt['msg']);
                }
                $this->_json(1, 'ok', $rt['data']);
            }

            // 注册


            if (in_array('username', $this->member_cache['register']['field'])
                && !\Phpcmf\Service::L('Form')->check_username($post['username'])) {
                $this->_json(0, dr_lang('账号格式不正确'), ['field' => 'username']);
            } elseif (in_array('email', $this->member_cache['register']['field'])
                && !\Phpcmf\Service::L('Form')->check_email($post['email'])) {
                $this->_json(0, dr_lang('邮箱格式不正确'), ['field' => 'email']);
            } elseif (in_array('phone', $this->member_cache['register']['field'])
                && !\Phpcmf\Service::L('Form')->check_phone($post['phone'])) {
                $this->_json(0, dr_lang('手机号码格式不正确'), ['field' => 'phone']);
            } elseif (empty($post['password'])) {
                $this->_json(0, dr_lang('密码必须填写'), ['field' => 'password']);
            } elseif ($post['password'] != $post['password2']) {
                $this->_json(0, dr_lang('确认密码不正确'), ['field' => 'password2']);
            } else {
                $rt = \Phpcmf\Service::M('member')->register_oauth_bang($oauth, 0, [
                    'username' => (string)$post['username'],
                    'phone' => (string)$post['phone'],
                    'email' => (string)$post['email'],
                    'password' => dr_safe_password($post['password']),
                ]);
                if ($rt['code']) {
                    // 注册绑定成功
                    $this->_json(1, 'ok', $rt['data']);
                } else {
                    $this->_json(0, $rt['msg'], ['field' => $rt['data']['field']]);
                }
            }




        } else {
            $this->_json(0, '非POST提交');
        }
    }


    // 公众号浏览url弹出登录
    public function login() {

        $code = \Phpcmf\Service::L('input')->get('code');
        $state = \Phpcmf\Service::L('input')->get('state');

        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->weixin['account']['appid'].'&secret='.$this->weixin['account']['appsecret'].'&code='.$code.'&state='.$state.'&grant_type=authorization_code';
        $rt = wx_get_https_json_data($url);
        if (!$rt['code']) {
            $this->_msg(0, $rt['msg']);
        }

        $user = \Phpcmf\Service::M()->table(weixin_wxtable('user'))->where('openid', $rt['data']['openid'])->getRow();
        if (!$user) {
            // 刷新
            $rs = wx_get_https_json_data('https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$this->weixin['account']['appid'].'&grant_type=refresh_token&refresh_token='.$rt['data']['refresh_token']);
            if (!$rs['code']) {
                $this->_msg(0, $rs['msg']);
            }

            $rts = wx_get_https_json_data('https://api.weixin.qq.com/sns/userinfo?access_token='.$rs['data']['access_token'].'&openid='.$rt['data']['openid'].'&lang=zh_CN');
            if (!$rts['code']) {
                $this->_msg(0, $rts['msg']);
            } elseif (!$rts['data']['nickname']) {
                $this->_msg(0, '未获取到微信用户昵称');
            }
            $user = $rts['data'];
            $user['id'] = \Phpcmf\Service::M('User', APP_DIR)->insert_user($rts['data']);
        }

        $oid = $rt['data']['openid'];
        $rt = \Phpcmf\Service::M('member')->insert_oauth(0, 'login', [
            'oid' => $oid,
            'oauth' => 'wechat',
            'avatar' => $user['headimgurl'],
            'nickname' => dr_emoji2html($user['nickname']),
            'expire_at' => SYS_TIME,
            'access_token' => 0,
            'refresh_token' => 0,
        ], $state);
        if (!$rt['code']) {
            $this->_msg(0, $rt['msg']);
            exit;
        } else {
            dr_redirect($rt['msg']);
        }

    }

    // 公众号浏览url弹出登录 url
    public function login_url() {
        $url = SITE_URL.'index.php?s=weixin&c=member&m=login';
        if ($_GET['back']) {
            $burl = $_GET['back'];
        } else {
            $burl = SITE_URL.'index.php?s=member';
        }
        dr_redirect('https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->weixin['account']['appid'].'&redirect_uri='.urlencode($url).'&response_type=code&scope=snsapi_userinfo&state='.urlencode($burl).'#wechat_redirect');
    }




}
