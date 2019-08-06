<?php namespace Phpcmf\Model;

class User extends \Phpcmf\Model
{
    public $openid;

    // 获取微信表名
    public function wxtable($name) {
        return weixin_wxtable($name);
    }

    // 获取本地用户组信息
    public function get_group_data() {

        $data = $this->db->table(weixin_wxtable('group'))->orderBy('id asc')->get()->getResultArray();
        if (!$data) {
            return [];
        }

        $rt = [];
        foreach ($data as $t) {
            $rt[$t['tag']] = $t;
        }

        return $rt;
    }

    // 服务器获取用户组
    public function down_group() {

        $rt = weixin_get_access_token();
        if (!$rt['code']) {
            return $rt;
        }

        $rt = wx_get_https_json_data('https://api.weixin.qq.com/cgi-bin/tags/get?access_token='.$rt['msg']);
        if (!$rt['code']) {
            return $rt;
        }

        if ($rt['data']['tags']) {
            $this->db->query('TRUNCATE `'.$this->dbprefix(weixin_wxtable('group')).'`');
            foreach ($rt['data']['tags'] as $t) {
                $this->db->table(weixin_wxtable('group'))->insert([
                    'tag' => $t['id'],
                    'name' => $t['name'],
                    'count' => $t['count'],
                    'groupid' => 0,
                ]);
            }
            return dr_return_data(1, 'ok');
        } else {
            return dr_return_data(0, '公众号上没有任何粉丝组');
        }
    }

    // 获取同步规则值
    private function _get_menu_data($t) {

        $data = array(
            'type' => $t['type'],
            'name' => dr_html2emoji($t['name']),
        );

        $root_url = \Phpcmf\Service::C()->site_info[1]['SITE_MURL'] ? \Phpcmf\Service::C()->site_info[1]['SITE_MURL'] : ROOT_URL;

        switch ($t['type']) {

            case 'view':
                $data['url'] = $t['value'];
                break;

            case 'login':
                $data['url'] = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.\Phpcmf\Service::C()->weixin['account']['appid'].'&redirect_uri='.urlencode($root_url.'index.php?s=weixin&c=member').'&response_type=code&scope=snsapi_base&state='.urlencode($t['value']).'#wechat_redirect';
                $data['type'] = 'view';
                break;

            case 'view_limited':
                $data['media_id'] = $t['value'];
                break;

            default:
                $data['key'] = $t['value'];
                break;
        }

        return $data;
    }

    // 服务器同步更新菜单
    public function sync_menu() {

        $at = weixin_get_access_token();
        if (!$at['code']) {
            return $at;
        }

        // 全局菜单
        $data = $this->db->table(weixin_wxtable('menu'))->where('gid', 0)->where('pid', 0)->orderBy('displayorder asc')->get()->getResultArray();
        if ($data) {
            $json = array();
            foreach ($data as $i => $t) {
                $list = $this->db->table(weixin_wxtable('menu'))->where('pid', (int)$t['id'])->orderBy('displayorder asc')->get()->getResultArray();
                if ($list) {
                    $val = array();
                    foreach ($list as $c) {
                        $val[] = $this->_get_menu_data($c);
                    }
                    $json[] = array(
                        'name' => dr_html2emoji($t['name']),
                        'sub_button' => $val
                    );
                } else {
                    $json[] = $this->_get_menu_data($t);
                }
            }
            $data = array('button'=>$json);
            $rt = wx_post_https_json_data('https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$at['msg'], $data);
            if (!$rt['code']) {
                $rt['msg'] = '全局菜单：'.$rt['msg'];
                return $rt;
            }
        } else {
            return dr_return_data(0, '你还没有创建菜单');
        }

        // 个性菜单
        $group = $this->db->table(weixin_wxtable('group'))->orderBy('id asc')->get()->getResultArray();
        if ($group) {
           foreach ($group as $g) {
               $gid = $g['tag'];
               $data = $this->db->table(weixin_wxtable('menu'))->where('gid', $gid)->where('pid', 0)->orderBy('displayorder asc')->get()->getResultArray();
               if ($data) {
                   $json = array();
                   foreach ($data as $i => $t) {
                       $list = $this->db->table(weixin_wxtable('menu'))->where('pid', (int)$t['id'])->orderBy('displayorder asc')->get()->getResultArray();
                       if ($list) {
                           $val = array();
                           foreach ($list as $c) {
                               $val[] = $this->_get_menu_data($c);
                           }
                           $json[] = array(
                               'name' => dr_html2emoji($t['name']),
                               'sub_button' => $val
                           );
                       } else {
                           $json[] = $this->_get_menu_data($t);
                       }
                   }
                   $data = array(
                       'button'=>$json,
                       'matchrule' => [
                           'tag_id' => $gid,
                       ]
                   );
                   $rt = wx_post_https_json_data('https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token='.$at['msg'], $data);
                   if (!$rt['code']) {
                       $rt['msg'] = '个性菜单['.$g['name'].']：'.$rt['msg'];
                       return $rt;
                   }
                   $this->db->table(weixin_wxtable('menu'))->where('gid', $gid)->update(['menuid' => (int)$rt['data']['menuid']]);
               }
           }
        }

        return dr_return_data(1, 'ok');

        //https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token=ACCESS_TOKEN
    }

    // 服务器获取菜单
    public function down_menu() {

        $rt = weixin_get_access_token();
        if (!$rt['code']) {
            return $rt;
        }

        $rt = wx_get_https_json_data('https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token='.$rt['msg']);
        if (!$rt['code']) {
            return $rt;
        } elseif (!$rt['data']['is_menu_open']) {
            return dr_return_data(0, '公众号上菜单没有开启');
        } elseif ($rt['data']['selfmenu_info']) {
            $this->db->query('TRUNCATE `'.$this->dbprefix(weixin_wxtable('menu')).'`');
            foreach ($rt['data']['selfmenu_info']['button'] as $top) {
                if ($top['sub_button']) {
                    $this->db->table(weixin_wxtable('menu'))->insert([
                        'gid' => 0,
                        'pid' => 0,
                        'name' => dr_emoji2html($top['name']),
                        'type' => '',
                        'value' => '',
                        'displayorder' => 0,
                    ]);
                    $pid = $this->db->insertID();
                    // 子菜单
                    foreach ($top['sub_button']['list'] as $menu) {
                        $this->db->table(weixin_wxtable('menu'))->insert([
                            'gid' => 0,
                            'pid' => $pid,
                            'name' => dr_emoji2html($menu['name']),
                            'type' => $menu['type'],
                            'value' => (string)($menu['url'] ? $menu['url'] : $menu['key']),
                            'displayorder' => 0,
                        ]);
                    }
                } else {
                    // 本身菜单
                    $this->db->table(weixin_wxtable('menu'))->insert([
                        'gid' => 0,
                        'pid' => 0,
                        'name' => dr_emoji2html($top['name']),
                        'type' => $top['type'],
                        'value' => (string)($top['url'] ? $top['url'] : $top['key']),
                        'displayorder' => 0,
                    ]);
                }
            }
        }

        return dr_return_data(1, 'ok');
    }

    // 顶级菜单
    public function top_menu() {
        return $this->db->table(weixin_wxtable('menu'))->where('pid', 0)->orderBy('displayorder asc')->get()->getResultArray();
    }

    // 修改标签
    public function edit_group($id, $name) {

        $rt = weixin_get_access_token();
        if (!$rt['code']) {
            return $rt;
        }

        $rt = wx_post_https_json_data(
            'https://api.weixin.qq.com/cgi-bin/tags/update?access_token='.$rt['msg'],
            [
                'tag' => [
                    'id' => $id,
                    'name' => $name,
                ]
            ]
        );
        if (!$rt['code']) {
            return $rt;
        }

        return dr_return_data(1, 'ok');
    }

    // 添加标签
    public function add_group($name) {

        $rt = weixin_get_access_token();
        if (!$rt['code']) {
            return $rt;
        }

        $rt = wx_post_https_json_data(
            'https://api.weixin.qq.com/cgi-bin/tags/create?access_token='.$rt['msg'],
            [
                'tag' => [
                    'name' => $name,
                ]
            ]
        );
        if (!$rt['code']) {
            return $rt;
        }

        return dr_return_data(1, 'ok');
    }

    // 删除标签
    public function delete_group($id) {

        $rt = weixin_get_access_token();
        if (!$rt['code']) {
            return $rt;
        }

        $rt = wx_post_https_json_data(
            'https://api.weixin.qq.com/cgi-bin/tags/delete?access_token='.$rt['msg'],
            [
                'tag' => [
                    'id' => $id,
                ]
            ]
        );
        if (!$rt['code']) {
            return $rt;
        }

        return dr_return_data(1, 'ok');
    }

    // 更新本地账户表
    public function insert_user($data) {

        $save = [
            'uid' => 0,
            'username' => '',
            'openid' => $data['openid'],
            'nickname' => dr_emoji2html($data['nickname']),
            'sex' => $data['sex'],
            'city' => $data['city'],
            'province' => $data['province'],
            'country' => $data['country'],
            'headimgurl' => (string)$data['headimgurl'],
            'subscribe' => (int)$data['subscribe'],
            'subscribe_time' => (int)$data['subscribe_time'],
            'unionid' => (string)$data['unionid'],
            'remark' => dr_emoji2html($data['remark']),
            'groupids' => dr_array2string($data['tagid_list']),
            'content' => dr_array2string($data),
        ];

        $oauth = $this->db->table('member_oauth')->where('oid', $data['openid'])->get()->getRowArray();
        if ($oauth && $oauth['uid']) {
            $save['uid'] = $oauth['uid'];
            $save['username'] = \Phpcmf\Service::M('member')->username($save['uid']);
        }

        $rt = $this->table(weixin_wxtable('user'))->insert($save);


        return $rt;
    }


    // 更新本地账户表
    public function update_user($user, $data) {

        $save = [
            'openid' => $data['openid'],
            'nickname' => dr_emoji2html($data['nickname']),
            'sex' => $data['sex'],
            'city' => $data['city'],
            'province' => $data['province'],
            'country' => $data['country'],
            'headimgurl' => $data['headimgurl'],
            'subscribe' => (int)$data['subscribe'],
            'subscribe_time' => (int)$data['subscribe_time'],
            'unionid' => (string)$data['unionid'],
            'remark' => dr_emoji2html($data['remark']),
            'groupids' => dr_array2string($data['tagid_list']),
            'content' => dr_array2string($data),
        ];

        $this->table(weixin_wxtable('user'))->update($user['id'], $save);

        return dr_array22array($user, $save);
    }

    // 更新组
    public function add_member_group($uid, $groupid) {

        $group = $this->db->table(weixin_wxtable('group'))->where('groupid', $groupid)->get()->getRowArray();
        if (!$group) {
            return;
        }

        $gid = $group['tag'];
        $user = $this->db->table(weixin_wxtable('user'))->where('uid', $uid)->get()->getRowArray();
        if ($user && $gid) {
            $tags = dr_string2array($user['groupids']);
            if (!in_array($gid, $tags)) {
                $tags[] = $gid;
                $this->table(weixin_wxtable('user'))->update($user['id'], [
                    'groupids' => dr_array2string($tags),
                ]);
                // 增加标签
                $rt = weixin_get_access_token();
                if ($rt['code']) {
                    $access_token = $rt['msg'];

                    $param = [
                        'openid_list' => [$user['openid']],
                        'tagid' => $gid,
                    ];
                    $rt = wx_post_https_json_data(
                        'https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=' . $access_token,
                        $param
                    );
                    if (!$rt['code']) {
                        log_message('error', '联动变更微信组标签：'.$rt['msg']);
                    }
                } else {
                    log_message('error', '联动变更微信组标签：'.$rt['msg']);
                }

            }
        }
    }

    // 删除组
    public function delete_member_group($uid, $groupid) {

        $group = $this->db->table(weixin_wxtable('group'))->where('groupid', $groupid)->get()->getRowArray();
        if (!$group) {
            return;
        }

        $gid = $group['tag'];
        $user = $this->db->table(weixin_wxtable('user'))->where('uid', $uid)->get()->getRowArray();
        if ($user && $gid) {
            $tags = dr_string2array($user['groupids']);
            if (in_array($gid, $tags)) {
                $tags[] = $gid;

                foreach ($tags as $i => $v) {
                    if ($v == $gid) {
                        unset($tags[$i]);
                    }
                }

                $this->table(weixin_wxtable('user'))->update($user['id'], [
                    'groupids' => dr_array2string($tags),
                ]);
                // 删除标签
                $rt = weixin_get_access_token();
                if ($rt['code']) {
                    $access_token = $rt['msg'];

                    $param = [
                        'openid_list' => [$user['openid']],
                        'tagid' => $gid,
                    ];
                    $rt = wx_post_https_json_data(
                        'https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token=' . $access_token,
                        $param
                    );
                    if (!$rt['code']) {
                        log_message('error', '联动变更微信组标签：'.$rt['msg']);
                    }
                } else {
                    log_message('error', '联动变更微信组标签：'.$rt['msg']);
                }

            }
        }
    }

}