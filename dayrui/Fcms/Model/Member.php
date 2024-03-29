<?php namespace Phpcmf\Model;

/* *
 *
 * Copyright [2019] [李睿]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * http://www.tianruixinxi.com
 *
 * 本文件是框架系统文件，二次开发时不建议修改本文件
 *
 * */


class Member extends \Phpcmf\Model
{

    private $sso_url;
    
    /**
     * 由用户名获取uid
     */
    function uid($name) {
        if ($name == $this->member['username']) {
            return $this->member['uid'];
        }
        $data = $this->db->table('member')->select('id')->where('username', dr_safe_replace($name))->get()->getRowArray();
        return intval($data['id']);
    }
    
    /**
     * 由uid获取用户名
     */
    function username($uid) {
        if ($uid == $this->member['uid']) {
            return $this->member['username'];
        }
        $data = $this->db->table('member')->select('username')->where('id', intval($uid))->get()->getRowArray();
        return $data['username'];
    }

    /**
     * 由uid获取电话
     */
    function phone($uid) {
        if ($uid == $this->member['uid']) {
            return $this->member['phone'];
        }
        $data = $this->db->table('member')->select('phone')->where('id', intval($uid))->get()->getRowArray();
        return $data['phone'];
    }

    // 用户基本信息
    function member_info($uid) {

        if ($uid == $this->member['uid']) {
            return $this->member;
        }

        $data = $this->db->table('member')->where('id', intval($uid))->get()->getRowArray();
        $data['uid'] = $data['id'];

        return $data;
    }

    /**
     * 登录记录
     *
     * @param	intval	$data		会员
     * @param	string	$OAuth		登录方式
     */
    private function _login_log($data, $type = '') {

        $ip = \Phpcmf\Service::L('Input')->ip_address();
        if (!$ip || !$data['id']) {
            return;
        }

        $agent = \Config\Services::request(null, true)->getUserAgent();
        if (strlen($agent) <= 5) {
            return;
        }

        $log = array(
            'uid' => $data['id'],
            'type' => $type,
            'loginip' => $ip,
            'logintime' => SYS_TIME,
            'useragent' => substr($agent, 0, 255),
        );

        // 同一天Ip一致时只更新一次更新时间
        if ($row = $this->db
                        ->table('member_login')
                        ->where('uid', $data['id'])
                        ->where('loginip', $ip)
                        ->where('DATEDIFF(from_unixtime(logintime),now())=0')
                        ->get()
                        ->getRowArray()) {
                        $this->db->table('member_login')->where('id', $row['id'])->update($log);
        } else {
            $this->db->table('member_login')->insert($log);
        }

        // 会员部分只保留20条登录记录
        $row = $this->db->table('member_login')->where('uid', $data['id'])->orderBy('logintime asc')->get()->getResultArray();
        if (dr_count($row) > 20) {
            $del = [];
            foreach ($row as $i => $t) {
                $i > 19 && $del[] = (int)$t['id'];
            }
            $del && $this->db->table('member_login')->where('uid', $data['id'])->whereIn('id', $del)->delete();
        }

        $time = \Phpcmf\Service::L('Input')->get_cookie('member_login');
        if (date('Ymd') != date('Ymd', $time)) {
            // 登录后的通知
            \Phpcmf\Service::L('Notice')->send_notice('member_login', $data);
            // 登录后的钩子
            \Phpcmf\Hooks::trigger('member_login_after', $data);
            \Phpcmf\Service::L('Input')->set_cookie('member_login', SYS_TIME, 3600*12);
        }

    }

    /**
     * 取会员COOKIE
     */
    public function member_uid() {

        // 获取本地cookie
        $uid = (int)\Phpcmf\Service::L('Input')->get_cookie('member_uid');
        if (!$uid) {
            return 0;
        }

        return $uid;
    }


    /**
     * 初始化处理
     */
    public function init_member($member) {

        if (!$member) {
            return;
        }

        // 明天凌晨时间戳
        $time = strtotime(date('Y-m-d', strtotime('+1 day')));

        // 每日登录积分处理
        $value = \Phpcmf\Service::C()->_member_auth_value($member['authid'], 'login_exp');
        if ($value && !\Phpcmf\Service::L('Input')->get_cookie('login_experience_'.$member['id'])) {
            $this->add_experience($member['id'], $value, dr_lang('每日登陆'), '', 'login_score_'.date('Ymd', SYS_TIME), 1);
            \Phpcmf\Service::L('Input')->set_cookie('login_experience_'.$member['id'], 1, $time - SYS_TIME);
        }

        // 每日登录金币处理
        $value = \Phpcmf\Service::C()->_member_auth_value($member['authid'], 'login_score');
        if ($value && !\Phpcmf\Service::L('Input')->get_cookie('login_score_'.$member['id'])) {
            $this->add_score($member['id'], $value, dr_lang('每日登陆'), '', 'login_score_'.date('Ymd', SYS_TIME), 1);
            \Phpcmf\Service::L('Input')->set_cookie('login_score_'.$member['id'], 1, $time - SYS_TIME);
        }
    }

    /**
     * 验证会员有效性 1表示通过 0表示不通过
     */
    public function check_member_cookie($member) {

        // 获取本地认证cookie
        $cookie = \Phpcmf\Service::L('Input')->get_cookie('member_cookie');

        // 授权登陆时不验证
        if ($member['id'] && \Phpcmf\Service::C()->session()->get('member_auth_uid') == $member['id']) {
            return 1;
        } elseif (!$cookie) {
            return 0;
        } elseif (substr(md5(SYS_KEY.$member['password']), 5, 20) !== $cookie) {
            if (defined('UCSSO_API')) {
                $rt = ucsso_get_password($member['id']);
                if ($rt['code']) {
                    // 变更本地库
                    $this->db->table('member')->where('id', $member['id'])->update(array(
                        'salt' => $rt['data']['salt'],
                        'password' => $rt['data']['password'],
                    ));
                    return 1;
                }
            }
            return 0;
        }

        return 1;
    }

    /**
     * 授权登录信息
     */
    public function oauth($uid) {

        $data = $this->db->table('member_oauth')->where('uid', $uid)->get()->getResultArray();
        if (!$data) {
            return [];
        }

        $rt = [];
        foreach ($data as $t) {
            $rt[$t['oauth']] = $t;
        }

        return $rt;
    }

    /**
     * 会员信息
     */
    public function get_member($uid = 0, $name = '') {

        $uid = intval($uid);

        if ($uid) {
            $data = $this->db->table('member')->where('id', $uid)->get()->getRowArray();
        } elseif ($name) {
            $data = $this->db->table('member')->where('username', $name)->get()->getRowArray();
            $uid = (int)$data['id'];
        }
        if (!$data) {
            return null;
        }

        // 附表字段
        $data2 = $this->db->table('member_data')->where('id', $uid)->get()->getRowArray();
        $data2 && $data = $data + $data2;
        
        $data['uid'] = $data['id'];
        $data['authid'] = [];
        $data['avatar'] = dr_avatar($data['id']);
        $data['adminid'] = (int)$data['is_admin'];
        $data['tableid'] = (int)substr((string)$data['id'], -1, 1);

        // 会员组信息
        $data2 = $this->update_group($data, $this->db->table('member_group_index')->where('uid', $uid)->get()->getResultArray());
        if ($data2) {
            foreach ($data2 as $t) {
                $t['group_name'] = \Phpcmf\Service::C()->member_cache['group'][$t['gid']]['name'];
                $t['group_level'] = \Phpcmf\Service::C()->member_cache['group'][$t['gid']]['level'][$t['lid']]['name'];
                $data['group'][$t['gid']] = $t;
                $data['groupid'][$t['gid']] = $t['gid'];
                $data['levelid'][$t['gid']] = $t['lid'];
                $data['authid'][] = $t['lid'] ? $t['gid'].'-'.$t['lid'] : $t['gid'];
            }
            //
        } else {
            $data['groupid'] = [];
        }

        return $data;
    }

    // 获取authid
    public function authid($uid) {

        if (!$uid) {
            return [0];
        } elseif ($uid == $this->uid) {
            return \Phpcmf\Service::C()->member['authid'];
        }

        $rt = [];
        $data2 = $this->db->table('member_group_index')->where('uid', $uid)->get()->getResultArray();
        if ($data2) {
            foreach ($data2 as $t) {
                $rt[] = $t['lid'] ? $t['gid'].'-'.$t['lid'] : $t['gid'];
            }
        }

        return $rt;
    }
    
    // 更新用户组
    // member 用户信息
    // groups 拥有的用户组
    public function update_group($member, $groups) {

        $g = [];
        if (!$member || !$groups) {
            return $g;
        }

        $uid = (int)$member['id'];
        foreach ($groups as $group) {
            // 判断是否可用
            if (!\Phpcmf\Service::C()->member_cache['group'][$group['gid']]) {
                continue;
            }
            $group['gid'] = (int)$group['gid'];
            // 判断等级是否有效
            $levels = isset(\Phpcmf\Service::C()->member_cache['group'][$group['gid']]['level']) ? \Phpcmf\Service::C()->member_cache['group'][$group['gid']]['level'] : [];
            if ($levels) {
                // bu存在
                if ($group['lid'] && (!isset($levels[$group['lid']]) || !$levels[$group['lid']])) {
                    $this->db->table('member_group_index')->where('uid', $uid)->where('gid', $group['gid'])->update(['lid' => 0]);
                    $group['lid'] = 0;
                }
            } elseif ($group['lid']) {
                // 还原升级
                $this->db->table('member_group_index')->where('uid', $uid)->where('gid', $group['gid'])->update(['lid' => 0]);
                $group['lid'] = 0;
            }
            $group_info = \Phpcmf\Service::C()->member_cache['group'][$group['gid']];
            // 判断过期
            $price = floatval($group_info['price']);
            if ($group['etime'] && $group['etime'] - SYS_TIME < 0) {
                // 过期了
                if ($group_info['setting']['timeout']) {
                    // 过期自动续费price
                    $name = $group_info['unit'] ? 'score' : 'money';
                    if ($name == 'money') {
                        // 余额
                        if ($price > 0) {
                            // 收费组情况下
                            if ($member[$name] - $price < 0) {
                                // 余额不足 删除
                                $this->db->table('member_group_index')->where('uid', $uid)->where('gid', $group['gid'])->delete();
                                // 提醒通知
                                $this->notice($uid, 2, dr_lang('您的用户组（%s）已过期，自动续费失败，账户%s不足', $group_info['name'], dr_lang('余额')));
                                continue;
                            }
                            $group['etime'] = dr_member_group_etime($group_info['days']);
                            // 自动续费
                            $rt = $this->add_money($uid, -$price);
                            if (!$rt['code']) {
                                continue;
                            }
                            // 增加到交易流水
                            $rt = \Phpcmf\Service::M('Pay')->add_paylog([
                                'uid' => $member['id'],
                                'username' => $member['username'],
                                'touid' => 0,
                                'tousername' => '',
                                'mid' => 'system',
                                'title' => dr_lang('用户组（%s）续费', $group_info['name']),
                                'value' => -$price,
                                'type' => 'finecms',
                                'status' => 1,
                                'result' => dr_lang('有效期至%s', $group['etime'] ? dr_date($group['etime']) : dr_lang('永久')),
                                'paytime' => SYS_TIME,
                                'inputtime' => SYS_TIME,
                            ]);
                            // 提醒通知
                            $this->notice(
                                $uid,
                                2,
                                dr_lang('您的用户组（%s）已过期，自动续费成功', $group_info['name']),
                                \Phpcmf\Service::L('router')->member_url('paylog/show', ['id'=>$rt['code']])
                            );
                        } else {
                            // 免费组自己续费
                            // 提醒通知
                            $this->notice(
                                $uid,
                                2,
                                dr_lang('您的用户组（%s）已过期，自动免费续期成功', $group_info['name'])
                            );
                        }
                        // 更新时间
                        $this->db->table('member_group_index')->where('uid', $uid)->where('gid', $group['gid'])->update(['etime' => $group['etime']]);
                    } else {
                        // 金币
                        $price = (int)$price;

                        if ($price > 0) {
                            // 收费组情况下
                            if ($member[$name] - $price < 0) {
                                // 金币不足 删除
                                $this->db->table('member_group_index')->where('uid', $uid)->where('gid', $group['gid'])->delete();
                                // 提醒通知
                                $this->notice($uid, 2, dr_lang('您的用户组（%s）已过期，自动续费失败，账户%s不足', $group_info['name'], SITE_SCORE));
                                continue;
                            }
                            // 自动续费
                            $group['etime'] = dr_member_group_etime($group_info['days']);
                            // 自动续费
                            $rt = $this->add_score($uid, -$price, dr_lang('您的用户组（%s）自动续费', $group_info['name']));
                            if (!$rt['code']) {
                                continue;
                            }
                            // 提醒通知
                            $this->notice(
                                $uid,
                                2,
                                dr_lang('您的用户组（%s）已过期，自动续费成功', $group_info['name'])
                            );
                        } else {
                            // 免费组自己续费
                            // 提醒通知
                            $this->notice(
                                $uid,
                                2,
                                dr_lang('您的用户组（%s）已过期，自动免费续期成功', $group_info['name'])
                            );
                        }
                        // 更新时间
                        $this->db->table('member_group_index')->where('uid', $uid)->where('gid', $group['gid'])->update(['etime' => $group['etime']]);
                    }
                } else {
                    // 未开通自动续费直接删除
                    $this->db->table('member_group_index')->where('uid', $uid)->where('gid', $group['gid'])->delete();
                    // 提醒通知
                    $this->notice($uid, 2, dr_lang('您的用户组（%s）已过期，系统权限已关闭', $group_info['name']));
                }

            }
            // 开启自动升级时需要判断等级
            if ($levels
                && \Phpcmf\Service::C()->member_cache['group'][$group['gid']]['setting']['level']['auto']) {
                $value = \Phpcmf\Service::C()->member_cache['group'][$group['gid']]['setting']['level']['unit'] ? $member['spend'] : $member['experience'];
                $level = array_reverse($levels); // 倒序判断
                foreach ($level as $t) {
                    if ($value >= $t['value']) {
                        if ($group['lid'] != $t['id']) {
                            // 开始变更等级
                            $this->db->table('member_group_index')->where('uid', $uid)->where('gid', $group['gid'])->update(array('lid' => $t['id']));
                            /* 等级升级 */
                            $this->notice($uid, 2, dr_lang('您的用户组（%s）等级自动升级为（%s）', \Phpcmf\Service::C()->member_cache['group'][$group['gid']]['name'], $t['name']));
                            $group['lid'] = $t['id'];
                        }
                        break;
                    }
                }
            }
            $g[$group['gid']] = $group;
        }

        return $g;
    }

    // 删除用户组
    public function delete_group($uid, $gid) {

        $this->db->table('member_group_index')->where('gid', $gid)->where('uid', $uid)->delete();

        // 提醒
        $this->notice($uid, 2, dr_lang('您的用户组（%s）被取消', \Phpcmf\Service::C()->member_cache['group'][$gid]['name']));

        // 判断微信标记组
        if (dr_is_app('weixin')) {
            \Phpcmf\Service::C()->init_file('weixin');
            \Phpcmf\Service::M('user', 'weixin')->delete_member_group($uid, $gid);
        }
    }

    // 新增用户组
    public function insert_group($uid, $gid, $is_notice = 1) {
        $data = [
            'uid' => $uid,
            'gid' => $gid,
            'lid' => 0,
            'stime' => SYS_TIME,
            'etime' => dr_member_group_etime(\Phpcmf\Service::C()->member_cache['group'][$gid]['days']),
        ];
        $this->table('member_group_index')->insert($data);
        // 挂钩点 用户组变更之后
        $call = $this->member_info($uid);
        $call['group_gid'] = $call['gid'] = $gid;
        $call['group_name'] = \Phpcmf\Service::C()->member_cache['group'][$gid]['name'];
        $is_notice && \Phpcmf\Service::L('Notice')->send_notice('member_edit_group', $call);
        \Phpcmf\Hooks::trigger('member_edit_group_after', $call);
        // 判断微信标记组
        if (dr_is_app('weixin')) {
            \Phpcmf\Service::C()->init_file('weixin');
            \Phpcmf\Service::M('user', 'weixin')->add_member_group($uid, $gid);
        }
    }
    
    // 手动变更等级
    public function update_level($uid, $gid, $lid) {
        $old = $data = $this->db->table('member_group_index')->where('uid', $uid)->where('gid', $gid)->get()->getRowArray();
        $data['gid'] = $gid;
        $data['lid'] = $lid;
        $this->db->table('member_group_index')->where('uid', $uid)->where('gid', $gid)->update(['lid' => $lid]);
        // 挂钩点 用户组变更之后
        $call = $this->member_info($uid);
        $call['group_name'] = \Phpcmf\Service::C()->member_cache['group'][$gid]['name'];
        $call['group_level'] = \Phpcmf\Service::C()->member_cache['group'][$gid]['level'][$lid]['name'];
        \Phpcmf\Service::L('Notice')->send_notice('member_edit_level', $call);
        \Phpcmf\Hooks::trigger('member_edit_level_after', $data, $old);
    }

    /**
     * 添加一条通知
     *
     * @param	string	$uid
     * @param	string	$note
     * @return	null
     */
    public function notice($uid, $type, $note, $url = '') {

        if (!$uid || !$note) {
            return '';
        }

        $uids = is_array($uid) ? $uid : explode(',', $uid);
        foreach ($uids as $uid) {
            $this->db->table('member_notice')->insert([
                'uid' => $uid,
                'type' => max(1, (int)$type),
                'isnew' => 1,
                'content' => $note,
                'url' => (string)$url,
                'inputtime' => SYS_TIME,
            ]);
        }

        return '';
    }

    /**
     * 系统提醒
     *
     * @param	site    站点id,公共部分0
     * @param	type    system系统  content内容相关  member会员相关 app应用相关 pay 交易相关
     * @param	msg     提醒内容
     * @param	uri     后台对应的链接
     * @param	to      通知对象 留空表示全部对象
     * array(
     *      to_uid 指定人
     *      to_rid 指定角色组
     * )
     */
    public function admin_notice($site, $type, $member, $msg, $uri, $to = []) {

        $this->db->table('admin_notice')->insert([
            'site' => (int)$site,
            'type' => $type,
            'msg' => dr_strcut(dr_clearhtml($msg), 100),
            'uri' => $uri,
            'to_rid' => intval($to['to_rid']),
            'to_uid' => intval($to['to_uid']),
            'status' => 0,
            'uid' => (int)$member['id'],
            'username' => $member['username'] ? $member['username'] : '',
            'op_uid' => 0,
            'op_username' => '',
            'updatetime' => 0,
            'inputtime' => SYS_TIME,
        ]);
    }
    
    // 执行提醒
    public function todo_admin_notice($uri, $site = 0) {
        $this->db->table('admin_notice')->where('site', (int)$site)->where('uri', $uri)->update([
            'status' => 3,
            'updatetime' => SYS_TIME,
        ]);
    }

    // 执行删除提醒
    public function delete_admin_notice($uri, $site = 0) {
        $this->db->table('admin_notice')->where('site', (int)$site)->where('uri', $uri)->delete();
    }
    

    // 审核用户
    public function verify_member($uid) {

        $this->db->table('member_data')->where('id', $uid)->update(['is_verify' => 1]);
        // 后台提醒
        $this->todo_admin_notice('member_verify/index:field/id/keyword/'.$uid, 0);
    }

    /**
     * 存储cookie
     */
    public function save_cookie($data, $remember = 0) {

        // 存储cookie
        $expire = $remember ? 8640000 : SITE_LOGIN_TIME;
        \Phpcmf\Service::L('Input')->set_cookie('member_uid', $data['id'], $expire);
        \Phpcmf\Service::L('Input')->set_cookie('member_cookie', substr(md5(SYS_KEY.$data['password']), 5, 20), $expire);

    }

    // 获取本站通讯地址
    public function get_sso_url() {

        if ($this->sso_url) {
            return $this->sso_url;
        }

        $this->sso_url = [
            '/'
        ];

        if (is_file(WRITEPATH.'config/domain_sso.php')) {
            $sso = require WRITEPATH.'config/domain_sso.php';
            foreach ($sso as $u) {
                $this->sso_url[] = dr_http_prefix($u).'/';
            }
        }
        
        return $this->sso_url;
    }

    /**
     * sso 登录url
     */
    public function sso($data, $remember = 0) {

        $sso = [];
        // 同步登录地址
        if (defined('UCSSO_API')) {
            $rt = ucsso_synlogin($data['id']);
            if ($rt && preg_match_all('/<script type=\"text\/javascript\" src=\"(.+)\"/iU', $rt, $mt)) {
                $sso = $mt[1];
            }
        }
        $url = $this->get_sso_url();
        foreach ($url as $u) {
            $code = dr_authcode($data['id'].'-'.$data['salt'], 'ENCODE');
            $sso[]= $u.'index.php?s=api&c=sso&action=login&remember='.$remember.'&code='.$code;
        }

        return $sso;
    }

    /**
     * 前端会员退出登录
     */
    public function logout() {

        \Phpcmf\Service::L('Input')->set_cookie('member_uid', 0, -100000000);
        \Phpcmf\Service::L('Input')->set_cookie('member_cookie', '', -100000000);
        \Phpcmf\Service::L('Input')->set_cookie('admin_login_member', '', -100000000);

        $sso = [];
        // 同步退出地址
        if (defined('UCSSO_API')) {
            $rt = ucsso_synlogout();
            if ($rt && preg_match_all('/<script type=\"text\/javascript\" src=\"(.+)\"/iU', $rt, $mt)) {
                $sso = $mt[1];
            }
        }
        $url = $this->get_sso_url();
        foreach ($url as $u) {
            $sso[]= $u.'index.php?s=api&c=sso&action=logout';
        }

        return $sso;
    }

    // 查询会员信息
    private function _find_member_info($username) {

        $data = $this->db->table('member')->where('username', $username)->get()->getRowArray();
        if (!$data && \Phpcmf\Service::C()->member_cache['login']['field']) {
            if (in_array('email', \Phpcmf\Service::C()->member_cache['login']['field'])
                && \Phpcmf\Service::L('Form')->check_email($username)) {
                $data = $this->db->table('member')->where('email', $username)->get()->getRowArray();
            } elseif (in_array('phone', \Phpcmf\Service::C()->member_cache['login']['field'])
                && \Phpcmf\Service::L('Form')->check_phone($username)) {
                $data = $this->db->table('member')->where('phone', $username)->get()->getRowArray();
                if (!$data) {
                    return [];
                }
            }
        }

        $data['uid'] = $data['id'];

        return $data;
    }

    // 验证管理员登录权限
    private function _is_admin_login_member($uid) {

        if (!$uid) {
            return dr_return_data(1, 'ok');
        }

        if (!\Phpcmf\Service::C()->member_cache['login']['admin']
            && $this->db->table('admin')->where('uid', $uid)->countAllResults()) {
            return dr_return_data(0, dr_lang('管理员账号不允许前台登录'));
        }

        return dr_return_data(1, 'ok');
    }

    /**
     * 验证登录
     *
     * @param	string	$username	用户名
     * @param	string	$password	明文密码
     * @param	intval	$remember	是否记住密码
     */
    public function login($username, $password, $remember = 0) {

        // 同步登录
        if (defined('UCSSO_API')) {
            /*
                    1:表示用户登录成功
                   -1:用户名不合法
                   -2:密码不合法
                   -3:用户名不存在
                   -4:密码不正确
               */
            $rt = ucsso_login($username, $password);
            if ($rt['code'] <= 0) {
                if ($rt['code'] == -3) {
                    // 当ucsso用户不存在时，在验证本地库
                    $data = $this->_find_member_info($username);
                    if ($data) {
                        //如果本地库有，我们就同步到服务器去
                        $rt = ucsso_register($username, $password, $data['email'], $data['phone']);
                        /*
                            大于 0:返回用户 ID，表示用户注册成功
                             0:失败
                            -1:用户名不合法
                            -2:用户名已经存在
                            -3:Email 格式有误
                            -4:该 Email 已经被注册
                            -5:该 手机号码格式有误
                            -6:该 手机号码已经被注册
                        */
                        if ($rt['code'] > 0) {
                            // 注册成功了
                            $rt2 = ucsso_syncuid($rt['code'], $data['uid']); // 上报uid
                            if (!$rt2['code']) {
                                return dr_return_data(0, dr_lang('通信失败：%s', $rt2['msg']));
                            }
                        } else {
                            return dr_return_data(0, dr_lang('通信失败：%s', $rt['msg']));
                        }
                    }
                } else {
                    return dr_return_data(0, dr_lang('通信失败：%s', $rt['msg']));
                }
            } else {
                // 登录成功
                if (!$rt['data']['uid']) {
                    // 表示ucsso存在这个账号，但没有注册uid
                    $data = $this->_find_member_info($username);
                    $ucsso_id = $rt['data']['ucsso_id'];
                    if (!$data) {
                        // 本地有会员不存在时就重新注册
                        $rt = $this->register(0, array(
                            'username' => $username,
                            'password' => $password,
                            'email' => $rt['data']['email'],
                            'phone' => $rt['data']['phone'],
                        ), [], 0);
                        if (!$rt['code']) {
                            return dr_return_data(0, $rt['msg']);
                        }
                        $data = $rt['data'];
                    }
                    // 上报uid
                    $rt2 = ucsso_syncuid($ucsso_id, $data['id']);
                    if (!$rt2['code']) {
                        return dr_return_data(0, dr_lang('通信失败：%s', $rt2['msg']));
                    }
                }
            }
            // 同步操作之后再查找用户
            !$data && $data = $this->_find_member_info($username);
            if (!$data) {
                return dr_return_data(0, dr_lang('用户不存在'));
            }
        } else {
            $data = $this->_find_member_info($username);
            if (!$data) {
                return dr_return_data(0, dr_lang('用户不存在'));
            }
            // 密码验证
            $password = dr_safe_password($password);
            if (md5(md5($password).$data['salt'].md5($password)) != $data['password']) {
                return dr_return_data(0, dr_lang('密码不正确'));
            }
        }

        // 验证管理员登录
        /*
        $rt = $this->_is_admin_login_member($data['id']);
        if (!$rt['code']) {
            return $rt;
        }*/

        // 保存本地会话
        $this->save_cookie($data, $remember);

        // 记录日志
        $this->_login_log($data);


        return dr_return_data(1, 'ok', [
            'auth'=> md5($data['passowrd'].$data['salt']), // API认证字符串,
            'member' => $this->get_member($data['id']),
            'sso' => $this->sso($data, $remember)]
        );
    }

    // 短信登录
    public function login_sms($phone, $remember) {

        $data = $this->db->table('member')->where('phone', $phone)->get()->getRowArray();
        if (!$data) {
            return dr_return_data(0, dr_lang('手机号码未注册'));
        }

        $data['uid'] = $data['id'];

        // 保存本地会话
        $this->save_cookie($data, $remember);

        // 记录日志
        $this->_login_log($data);

        return dr_return_data(1, 'ok', [
                'auth'=> md5($data['passowrd'].$data['salt']), // API认证字符串,
                'member' => $this->get_member($data['id']),
                'sso' => $this->sso($data, $remember)]
        );
    }
    
    // 授权登录
    public function login_oauth($name, $data) {
        
        // 保存本地会话
        $this->save_cookie($data);
        
        // 记录日志
        $this->_login_log($data, $name);

        return $this->sso($data);
    }
    
    // 绑定注册模式 授权注册绑定
    public function register_oauth_bang($oauth, $groupid, $data) {

        if (!$oauth) {
            return dr_return_data(0, dr_lang('OAuth数据不存在，请重试'));
        }

        $rt = $this->register($groupid, $data);
        if (!$rt['code']) {
            return dr_return_data(0, $rt['msg']);
        }

        $data = $rt['data'];

        // 保存本地会话
        $this->save_cookie($data);

        // 记录日志
        $this->_login_log($data, $oauth['oauth']);

        // 更改状态
        $this->db->table('member_oauth')->where('id', $oauth['id'])->update(['uid' => $data['id']]);
        dr_is_app('weixin') && $oauth['oauth'] == 'wechat' && $this->db->table('weixin_user')->where('openid', $oauth['oid'])->update([
            'uid' => $data['id'],
            'username' => $data['username'],
        ]);

        // 下载头像和同步登录
        $sso = $this->sso($data);

        // 下载头像
        \Phpcmf\Service::L('thread')->cron(['action' => 'oauth_down_avatar', 'id' => $oauth['id'] ]);

        return dr_return_data(1, 'ok', [
            'auth'=> md5($data['passowrd'].$data['salt']), // API认证字符串,
            'member' => $data,
            'sso' => $sso
        ]);
    }

    // api直接按uid登录
    public function login_uid($oauth, $uid) {

        $member = $this->get_member($uid);
        if (!$member) {
            return dr_return_data(0, dr_lang('用户不存在'));
        }

        // 保存本地会话
        $this->save_cookie($member);

        // 记录日志
        $this->_login_log($member, $oauth['oauth']);

        return dr_return_data(1, 'ok', [
            'auth'=> md5($member['passowrd'].$member['salt']), // API认证字符串,
            'member' => $member,
        ]);
    }
    
    // 直接登录模式 授权注册
    public function register_oauth($groupid, $oauth) {

        $name = dr_clear_emoji($oauth['nickname']);
        $member = [
            'username' => $name,
            'name' => $name,
            'email' => '',
            'phone' => '',
        ];

        $count = $this->db->table('member')->where('username', $member['username'])->countAllResults();
        $count && $member['username'].= '_'.$oauth['oauth'].'_'.rand(0, 9999);

        $rt = $this->register($groupid, $member);
        if (!$rt['code']) {
            return dr_return_data(0, $rt['msg']);
        }

        $data = $rt['data'];

         // 保存本地会话
        $this->save_cookie($data);

        // 记录日志
        $this->_login_log($data, $oauth['oauth']);

        // 更改状态
        $this->db->table('member_oauth')->where('id', $oauth['id'])->update(['uid' => $data['id']]);
        dr_is_app('weixin') && $oauth['oauth'] == 'wechat' && $this->db->table('weixin_user')->where('openid', $oauth['oid'])->update([
            'uid' => $data['id'],
            'username' => $data['username'],
        ]);

        // 下载头像和同步登录
        $sso = $this->sso($data);
        $sso[] = \Phpcmf\Service::L('router')->member_url('api/avatar', ['id'=>$oauth['id']]);

        return dr_return_data(1, 'ok', [
            'auth'=> md5($data['passowrd'].$data['salt']), // API认证字符串,
            'member' => $data,
            'sso' => $sso
        ]);
    }

    /**
     * 用户注册
     *
     * @param   用户组
     * @param	注册账户信息
     * @param	自定义字段信息
     * @param	是否注册到服务器
     */
    public function register($groupid, $member, $data = [], $sync = 1) {
        
        $member['email'] && $member['email'] = strtolower($member['email']);
        $member['username'] && $member['username'] = strtolower($member['username']);

        // 默认注册组
        !$groupid && $groupid = (int)\Phpcmf\Service::C()->member_cache['register']['groupid'];

        // 验证唯一性
        if ($member['username'] && $this->db->table('member')->where('username', $member['username'])->countAllResults()) {
            return dr_return_data(0, dr_lang('账号%s已经注册', $member['username']), ['field' => 'username']);
        } elseif ($member['email'] && $this->db->table('member')->where('email', $member['email'])->countAllResults()) {
            return dr_return_data(0, dr_lang('邮箱%s已经注册', $member['email']), ['field' => 'email']);
        } elseif ($member['phone'] && $this->db->table('member')->where('phone', $member['phone'])->countAllResults()) {
            return dr_return_data(0, dr_lang('手机号码%s已经注册', $member['phone']), ['field' => 'phone']);
        }

        if ($member['username'] == 'guest') {
            return dr_return_data(0, dr_lang('此名称guest系统不允许注册'), ['field' => 'username']);
        } elseif (!IS_ADMIN && \Phpcmf\Service::C()->member_cache['register']['notallow']) {
            foreach (\Phpcmf\Service::C()->member_cache['register']['notallow'] as $mt) {
                if ($mt && stripos($member['username'], $mt) !== false) {
                    return dr_return_data(0, dr_lang('账号%s禁止包含关键字[%s]', $member['username'], $mt), ['field' => 'username']);
                }
            }
        }

        $ucsso_id = 0;
        if ($sync && defined('UCSSO_API')) {
            /*
                大于 0:返回用户 ID，表示用户注册成功
                 0:失败
                -1:用户名不合法
                -2:用户名已经存在
                -3:Email 格式有误
                -4:该 Email 已经被注册
                -5:该 手机号码 格式有误
                -6:该 手机号码 已经被注册
            */
            $rt = ucsso_register($member['username'], $member['password'], $member['email'], $member['phone']);
            if ($rt['code'] > 0) {
                // 注册成功
                $ucsso_id = (int)$rt['code'];
            } else {
                return dr_return_data(0, dr_lang('通信失败：%s', $rt['msg']));
            }
        }

        !$member['name'] && $member['name'] = '';
        $member['salt'] = substr(md5(rand(0, 999)), 0, 10); // 随机10位密码加密码
        $member['password'] = $member['password'] ? md5(md5($member['password']).$member['salt'].md5($member['password'])) : '';
        $member['money'] = 0;
        $member['freeze'] = 0;
        $member['spend'] = 0;
        $member['score'] = 0;
        $member['experience'] = 0;
        $member['regip'] = (string)\Phpcmf\Service::L('Input')->ip_address();
        $member['regtime'] = SYS_TIME;
        $member['randcode'] = rand(100000, 999999);
        !$member['username'] && $member['username'] = '';
        $rt = $this->table('member')->insert($member);
        if (!$rt['code']) {
            return dr_return_data(0, $rt['msg']);
        }
        // 没有账号，随机一个默认登录账号
        if (!$member['username']) {
            $member['username'] = strtolower(trim(\Phpcmf\Service::C()->member_cache['register']['unprefix'].$rt['code']));
            $this->table('member')->update($rt['code'], [
                'username' => $member['username']
            ]);
        }

        // 附表信息
        $data['id'] = $member['uid'] = $uid = $rt['code'];
        $data['is_lock'] = 0;
        $data['is_auth'] = 0;
        $data['is_admin'] = 0;
        $data['is_avatar'] = 0;
        // 审核状态值
        $data['is_verify'] = IS_ADMIN ? 1 : (\Phpcmf\Service::C()->member_cache['register']['verify'] ? 0 : 1);
        $data['is_mobile'] = 0;
        $data['is_complete'] = 0;
        $rt = $this->table('member_data')->insert($data);
        if (!$rt['code']) {
            // 删除主表
            $this->table('member')->delete($uid);
            return dr_return_data(0, $rt['msg']);
        }

        // uid 同步
        if ($ucsso_id && defined('UCSSO_API')) {
            $rt = ucsso_syncuid($ucsso_id, $uid);
            if (!$rt['code']) {
                // 同步失败
                log_message('error', 'UCSSO同步uid失败：'.$rt['msg']);
            }
        }

        // 归属用户组
        $this->insert_group($uid, $groupid, 0);

        $data = $member + $data;

        // 审核判断
        if (!$data['is_verify']) {
            switch (\Phpcmf\Service::C()->member_cache['register']['verify']) {

                case 'phone':
                    $this->sendsms_code($member['phone'], $member['randcode']);
                    break;

                case 'email':
                    $this->sendmail($member['email'], dr_lang('注册邮件验证'), 'member_verify.html', $data);
                    break;
            }
            // 发送审核提醒
            $this->admin_notice(0, 'member', $member, dr_lang('新会员【%s】注册审核', $member['username']), 'member_verify/index:field/id/keyword/'.$uid);
        }

        // 注册后的通知
        \Phpcmf\Service::L('Notice')->send_notice('member_register', $data);

        // 注册后的钩子
        \Phpcmf\Hooks::trigger('member_register_after', $data);

        // 邀请注册绑定
        if (dr_is_app('yaoqing')) {
            $puid = \Phpcmf\Service::C()->session()->get('app_yaoqing_uid');
            if ($puid) {
                \Phpcmf\Service::M('yq', 'yaoqing')->register($puid, $data);
            }
        }

        return dr_return_data(1, 'ok', $data);
    }

    /**
     * 存储授权信息
     */
    public function insert_oauth($uid, $type, $data, $state = '') {

        $row = $this->db->table('member_oauth')->where('oid', $data['oid'])->where('oauth', $data['oauth'])->get()->getRowArray();
        if (!$row) {
            // 插入授权信息
            $data['uid'] = (int)$uid;
            $rt = $this->table('member_oauth')->insert($data);
            if (!$rt['code']) {
                return dr_return_data(0, $rt['msg']);
            }
            $id = $rt['code'];
        } else {
            // 更新授权信息
            $uid && $data['uid'] = $uid;
            $this->db->table('member_oauth')->where('id', $row['id'])->update($data);
            $id = $row['id'];
        }

        // 存储session
        \Phpcmf\Service::C()->session()->set('member_auth_'.$type.'_'.$data['oauth'], $id);

        return dr_return_data($id, $type == 'login' ? \Phpcmf\Service::L('router')->member_url('login/oauth', ['name' => $data['oauth'], 'state' => $state]) : \Phpcmf\Service::L('router')->member_url('account/oauth', ['name' => $data['oauth']]));
    }

    // 修改密码 
    public function edit_password($member, $password) {

        $id = (int)$member['id'];
        $password = dr_safe_password($password);
        if (!$id || !$password) {
            return false;
        }

        if (defined('UCSSO_API')) {
            $rt = ucsso_edit_password($id, $password);
            // 修改失败
            if (!$rt['code']) {
                return false;
            }
        }

        $update['salt'] = substr(md5(rand(0, 999)), 0, 10); // 随机10位密码加密码
        $update['randcode'] = 0;
        $update['password'] = md5(md5($password).$update['salt'].md5($password));
        $this->db->table('member')->where('id', $id)->update($update);

        $member['uid'] = $id;

        // 通知
        \Phpcmf\Service::L('Notice')->send_notice('member_edit_password', $member);
        // 钩子
        \Phpcmf\Hooks::trigger('member_edit_password_after', $member);

        return true;
    }

    /**
     * 邮件发送
     */
    public function sendmail($tomail, $subject, $msg, $data = []) {

        if (!$tomail || !$subject || !$msg) {
            return dr_return_data(0, dr_lang('参数不规范'));
        }

        $cache = \Phpcmf\Service::L('cache')->get('email');
        if (!$cache) {
            return dr_return_data(0, dr_lang('无邮件smtp配置'));
        }

        $content = $msg;
        if (trim(strtolower(strrchr($msg, '.')), '.') == 'html') {
            $my = WEBPATH.'config/notice/email/'.$msg;
            $default = ROOTPATH.'config/notice/email/'.$msg;
            $content = is_file($my) ? file_get_contents($my) : file_get_contents($default);
            if (!$content) {
                log_message('error', '邮件模板不存在：'.$msg);
                return dr_return_data(0, dr_lang('邮件模板#%s不存在', $msg));
            }
            ob_start();
            extract($data, EXTR_PREFIX_SAME, 'data');
            $file = \Phpcmf\Service::V()->code2php($content);
            require $file;
            $content = ob_get_clean();
        }

        $dmail = \Phpcmf\Service::L('email');
        foreach ($cache as $data) {
            $dmail->set(array(
                'host' => $data['host'],
                'user' => $data['user'],
                'pass' => $data['pass'],
                'port' => $data['port'],
                'from' => $data['user'],
            ));
            if ($dmail->send($tomail, $subject, $content)) {
                return dr_return_data(1, 'ok');
            }
        }

        return dr_return_data(0, 'Error：'.$dmail->error);
    }

    /**
     * 短信发送验证码
     */
    public function sendsms_code($mobile, $code) {

        return $this->sendsms_text($mobile, $code, 'code');
    }

    /**
     * 短信发送文本
     */
    public function sendsms_text($mobile, $content, $type = 'text') {

        if (!$mobile || !$content) {
            return dr_return_data(0, dr_lang('手机号码或内容不能为空'));
        }

        $file = WRITEPATH.'config/sms.php';
        if (!is_file($file)) {
            log_message('error', '短信接口配置文件不存在');
            return dr_return_data(0, dr_lang('接口配置文件不存在'));
        }

        $config = require_once $file;
        if ($config['third']) {
            if (is_file(WEBPATH.'config/mysms.php')) {
                require_once WEBPATH.'config/mysms.php';
            }
            $method = 'my_sendsms_'.$type;
            if (function_exists($method)) {
                return call_user_func_array($method, [
                    $mobile,
                    $content,
                    $config['third'],
                ]);
            } else {
                $error = dr_lang('你没有定义第三方短信接口: '. $method);
                @file_put_contents(WRITEPATH.'sms_log.php', date('Y-m-d H:i:s').' ['.$mobile.'] ['.$error.'] （'.str_replace(array(chr(13), chr(10)), '', $content).'）'.PHP_EOL, FILE_APPEND);
                return dr_return_data(0, $error);
            }
        } else {
            $content = $type == 'code' ? dr_lang('您的本次验证码是: %s', $content) : $content;
            $url = 'http://sms.dayrui.com/index.php?uid='.$config['uid'].'&key='.$config['key'].'&mobile='.$mobile.'&content='.$content.'【'.$config['note'].'】&domain='.trim(str_replace('http://', '', SITE_URL), '/').'&sitename='.SITE_NAME;
            $result = dr_catcher_data($url);
            if (!$result) {
                log_message('error', '访问天睿云短信服务器失败');
                return dr_return_data(0, dr_lang('访问天睿云短信服务器失败'));
            }
            $result = json_decode($result, true);
        }

        @file_put_contents(WRITEPATH.'sms_log.php', date('Y-m-d H:i:s').' ['.$mobile.'] ['.$result['msg'].'] （'.str_replace(array(chr(13), chr(10)), '', $content).'）'.PHP_EOL, FILE_APPEND);

        return $result;
    }

    /**
     * 获取微信 access token
     */
    public function _weixin_template_access_token() {

        $cache_data = \Phpcmf\Service::L('cache')->init('file')->get('access_token');
        if (isset($cache_data['time']) && $cache_data['access_token']) {
            $access_token = $cache_data['access_token'];
        } else {
            $weixin = \Phpcmf\Service::C()->get_cache('weixin');
            $result = wx_get_https_json_data('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$weixin['account']['appid'].'&secret='.$weixin['account']['appsecret']);
            if (!$result['code']) {
                return dr_return_data(0, 'AccessToken：'.$result['msg']);
            }
            $rt = \Phpcmf\Service::L('cache')->init('file')->save('access_token', $result['data'], 3600);
            if (!$rt) {
                return dr_return_data(0, 'AccessToken：/cache/temp/目录不可写，文件写入失败');
            }
            $access_token = $result['data']['access_token'];
        }

        return dr_return_data(1, 'ok', ['access_token' => $access_token]);
    }

    /**
     * 发送微信通知模板
     *
     * $uid	会员id
     * $id 	微信模板id
     * $data	通知内容
     * $url	详细地址
     * $color	top颜色
     */
    public function wexin_template($uid, $id, $data, $url = '', $color = '') {

        $oauth = $this->db->table('member_oauth')->where('uid', $uid)->where('oauth', 'wechat')->get()->getRowArray();
        if (!$oauth) {
            return dr_return_data(0, 'uid（'.$uid.'）未绑定账号');
        }

        $rt = $this->_weixin_template_access_token($id);
        if (!$rt['code']) {
            return dr_return_data(0, $rt['msg']);
        }

        $p = [
            'touser' => $oauth['oid'],
            'template_id' => $id,
            'url' => $url,
            'topcolor' => $color,
            'data' => $data,
        ];
        $rt = wx_post_https_json_data('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$rt['data']['access_token'], $p);

        return $rt;
    }

    /**
     * 增加经验
     *
     * @param	intval	$uid	会员id
     * @param	intval	$value	分数变动值
     * @param	string	$mark	标记
     * @param	string	$note	备注
     * @param	intval	$count	统计次数
     * @return	intval
     */
    public function add_experience($uid, $val, $note = '', $url = '', $mark = '', $count = 0) {

        if (!$uid || !$val) {
            return dr_return_data(0, dr_lang('参数失败'));
        }

        $user = $this->member_info($uid);
        if (!$user) {
            return dr_return_data(0, dr_lang('用户不存在'));
        }
        
        // 判断次数
        if ($mark && $count && $this->db->table('member_explog')->where('uid', $uid)->where('mark', $mark)->countAllResults() >= $count) {
            return dr_return_data(0, dr_lang('本次不能累计增减金币'));
        }

        $val = abs($val); // 经验值只能加
        $value = max(0, (int)$user['experience'] + $val); // 不允许小于0
        $this->db->table('member')->where('id', (int)$uid)->update(['experience' => $value]);

        // 交易记录
        return $this->table('member_explog')->insert([
            'uid' => $uid,
            'username' => $user['username'],
            'url' => $url ? $url : '',
            'mark' => $mark ? $url : '',
            'note' => $note,
            'value' => $val,
            'inputtime' => SYS_TIME,
        ]);
    }

    /**
     * 增加金币
     *
     * @param	intval	$uid	会员id
     * @param	intval	$value	分数变动值
     * @param	string	$mark	标记
     * @param	string	$note	备注
     * @param	intval	$count	统计次数
     * @return	intval
     */
    public function add_score($uid, $val, $note = '', $url = '', $mark = '', $count = 0) {

        if (!$uid || !$val) {
            return dr_return_data(0, dr_lang('参数错误'));
        }

        $user = $this->member_info($uid);
        if (!$user) {
            return dr_return_data(0, dr_lang('用户不存在'));
        }

        // 判断次数
        if ($mark && $count && $this->db->table('member_scorelog')->where('uid', $uid)->where('mark', $mark)->countAllResults() >= $count) {
            return dr_return_data(0, dr_lang('本次不能累计增减金币'));
        }

        $value = max(0, (int)$user['score'] + $val); // 不允许小于0
        $this->db->table('member')->where('id', (int)$uid)->update(['score' => $value]);

        // 交易记录
        return $this->table('member_scorelog')->insert([
            'uid' => $uid,
            'username' => $user['username'],
            'url' => $url ? $url : '',
            'mark' => $mark ? $mark : '',
            'note' => $note,
            'value' => $val,
            'inputtime' => SYS_TIME,
        ]);
    }

    // 增加money
    public function add_money($uid, $value) {

        $value = floatval($value);
        if (!$value) {
            return dr_return_data(0, dr_lang('金额不正确'));
        }

        $member = $this->member_info($uid);
        if (!$member) {
            return dr_return_data(0, dr_lang('用户不存在'));
        }

        $update = [
            'money' => max(0, $member['money'] + $value),
        ];
        $value < 0 && $update['spend'] = max(0, $member['spend'] + abs($value));

        $rt = $this->table('member')->update($uid, $update);

        return $rt;
    }

    // 冻结资金
    public function add_freeze($uid, $value) {

        $value = floatval(abs($value));
        if (!$uid || !$value) {
            return dr_return_data(0, dr_lang('参数不正确'));
        }

        $rt = $this->query('update `'.$this->dbprefix('member').'` set `money`=`money`-'.$value.',`freeze`=`freeze`+'.$value.' where id='.$uid);
        return $rt;
    }

    // 取消冻结资金
    public function cancel_freeze($uid, $value) {

        $value = floatval(abs($value));
        if (!$uid || !$value) {
            return dr_return_data(0, dr_lang('参数不正确'));
        }

        $rt = $this->query('update `'.$this->dbprefix('member').'` set `money`=`money`+'.$value.',`freeze`=`freeze`-'.$value.' where id='.$uid);
        return $rt;
    }

    // 使用冻结资金
    public function use_freeze($uid, $value) {

        $value = floatval(abs($value));
        if (!$uid || !$value) {
            return dr_return_data(0, dr_lang('参数不正确'));
        }

        $rt = $this->query('update `'.$this->dbprefix('member').'` set `spend`=`spend`+'.$value.',`freeze`=`freeze`-'.$value.' where id='.$uid);

        return $rt;
    }

    // 删除会员后执行
    public function member_delete($id) {

        // 删除会员的相关表
        $this->db->table('member_data')->where('id', $id)->delete();
        $this->db->table('member_group_index')->where('uid', $id)->delete();
        $this->db->table('member_login')->where('uid', $id)->delete();
        $this->db->table('member_oauth')->where('uid', $id)->delete();
        $this->db->table('admin')->where('uid', $id)->delete();
        $this->db->table('admin_login')->where('uid', $id)->delete();
        $this->db->table('admin_role_index')->where('uid', $id)->delete();
        $this->db->table('member_notice')->where('uid', $id)->delete();
        $this->db->table('member_cashlog')->where('uid', $id)->delete();
        $this->db->table('member_explog')->where('uid', $id)->delete();
        $this->db->table('member_group_verify')->where('uid', $id)->delete();
        $this->db->table('member_oauth')->where('uid', $id)->delete();
        $this->db->table('member_paylog')->where('uid', $id)->delete();
        $this->db->table('member_scorelog')->where('uid', $id)->delete();
        SYS_ATTACHMENT_DB && \Phpcmf\Service::M('Attachment')->uid_delete($id);
        $this->delete_admin_notice('member_verify/index:field/id/keyword/'.$id, 0);

        // 按站点数据删除
        foreach ($this->site as $siteid) {
            // 表单
            $form = $this->init(['table' => $siteid.'_form'])->getAll();
            if ($form) {
                foreach ($form as $t) {
                    $table = $siteid.'_form_'.$t['table'];
                    $this->db->table($table)->where('uid', $id)->delete();
                    for ($i = 0; $i < 200; $i ++) {
                        if (!$this->db->query("SHOW TABLES LIKE '".$this->dbprefix($table).'_data_'.$i."'")->getRowArray()) {
                            break;
                        }
                        $this->db->table($table.'_data_'.$i)->where('uid', $id)->delete();
                    }
                }
            }
            // 模块
            $module = $this->table('module')->getAll();
            if ($module) {
                foreach ($module as $m) {
                    $mdir = $m['dirname'];
                    $table = $siteid.'_'.$mdir;
                    // 模块内容
                    if (!$this->db->query("SHOW TABLES LIKE '".$this->dbprefix($table)."'")->getRowArray()) {
                        break;
                    }
                    $mdb = \Phpcmf\Service::M('Content', $mdir);
                    $mdb->_init($mdir, $siteid);
                    // 查询删除内容
                    $index = $this->table($table.'_index')->where('uid', $id)->getAll();
                    if ($index) {
                        foreach ($index as $t) {
                            $mdb->delete_content($t['id']);
                        }
                    }
                }
            }

        }

        // 同步删除动作
        \Phpcmf\Service::M('Sync')->delete_member($id);

    }

    // 按用户uid查询表id集合
    private function _get_data_ids($uid, $table) {

    }

    // 用户系统缓存
    public function cache($site = SITE_ID) {

        $cache = [];

        // 获取会员全部配置信息
        $result = $this->db->table('member_setting')->get()->getResultArray();
        if ($result) {
            foreach ($result as $t) {
                $cache[$t['name']] = dr_string2array($t['value']);
            }
        }

        // 字段归属
        $cache['myfield'] = $cache['field'];

        // 自定义字段
        $register_field = $group_field = $cache['field'] = [];
        $field = $this->db->table('field')->where('disabled', 0)->where('relatedname', 'member')->orderBy('displayorder ASC,id ASC')->get()->getResultArray();
        if ($field) {
            foreach ($field as $f) {
                $f['setting'] = dr_string2array($f['setting']);
                $cache['field'][$f['fieldname']] = $f;
                // 归类用户组字段
                if ($cache['myfield'][$f['id']]) {
                    foreach ($cache['myfield'][$f['id']] as $gid) {
                        $group_field[$gid][] = $f['fieldname'];
                    }
                }
                // 归类可用注册的字段
                if ($cache['register_field'][$f['id']]) {
                    $register_field[] = $f['fieldname'];
                }
            }
        }


        // 支付接口
        if ($cache['payapi']) {
            foreach ($cache['payapi'] as $i => $t) {
                if (!$t['use']) {
                    unset($cache['payapi'][$i]);
                }
            }
        }

        // 注册配置
        $cache['register']['notallow'] = explode(',', $cache['register']['notallow']);

        // 用户组权限id
        $cache['authid'] = [ 0 ];

        // 用户组
        $cache['register']['group'] = $cache['group'] = [];
        $group = $this->db->table('member_group')->orderBy('displayorder ASC,id ASC')->get()->getResultArray();
        if ($group) {
            foreach ($group as $t) {
                $level = $this->db->table('member_level')->where('gid', $t['id'])->orderBy('value ASC,id ASC')->get()->getResultArray();
                if ($level) {
                    foreach ($level as $lv) {
                        $cache['authid'][] = $t['id'].'-'.$lv['id'];
                        $t['level'][$lv['id']] = $lv;
                    }
                } else {
                    $cache['authid'][] = $t['id'];
                }
                $t['setting'] = dr_string2array($t['setting']);
                // 用户组的可用字段
                $t['field'] = $group_field[$t['id']];
                // 当前用户组开启了注册时, 查询它可注册的字段
                $t['register'] && $t['field'] && $t['register_field'] = $register_field ? array_intersect($t['field'], $register_field) : [];
                // 是否允许注册
                $t['register'] && $cache['register']['group'][] = $t['id'];
                $cache['group'][$t['id']] = $t;
            }
        }

        \Phpcmf\Service::L('cache')->set_file('member', $cache);
    }
}