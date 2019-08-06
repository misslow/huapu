<?php namespace Phpcmf\Controllers\Admin;

// 粉丝
class User extends \Phpcmf\Table
{

    public function __construct(...$params)
    {
        parent::__construct(...$params);
        // 支持附表存储
        $this->is_data = 0;
        $this->tpl_prefix = 'user_';
        // 表单显示名称
        $this->name = dr_lang('微信用户');
        $this->my_field = array(
            'nickname' => array(
                'ismain' => 1,
                'name' => '昵称',
                'isemoji' => 1,
                'fieldname' => 'nickname',
            ),
            'username' => array(
                'ismain' => 1,
                'name' => '账号',
                'fieldname' => 'username',
            ),
        );
        // 初始化数据表
        $this->_init([
            'table' => weixin_wxtable('user'),
            'order_by' => 'subscribe_time desc,uid asc',
            'date_field' => 'subscribe_time',
            'field' => $this->my_field,
        ]);
        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_admin_menu(
                [
                    '粉丝管理' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/index', 'fa fa-user'],
                    '从服务端获取粉丝' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/down_add', 'fa fa-plus'],
                ]
            ),
            'field' => $this->my_field,
            'group' => \Phpcmf\Service::M('User', APP_DIR)->get_group_data(),
        ]);
    }

    public function down_add() {

        $rt = weixin_get_access_token();
        if (!$rt['code']) {
            $this->_admin_msg(0, $rt['msg']);
        }

        $access_token = $rt['msg'];
        $action = \Phpcmf\Service::L('Input')->get('action');
        if (!$action) {
            $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$access_token.'&next_openid='.\Phpcmf\Service::L('Input')->get('next_openid');
            $rt = wx_get_https_json_data($url);
            if (!$rt['code']) {
                $this->_admin_msg(0, $rt['msg']);
            } elseif (!isset($rt['data']['count']) || $rt['data']['count'] == 0) {
                // 拉取完毕 全部设置为0状态
                \Phpcmf\Service::M()->db->table(weixin_wxtable('follow'))->update(array(
                    'status' => 0,
                ));
                \Phpcmf\Service::M()->db->query('TRUNCATE `'.\Phpcmf\Service::M()->dbprefix(weixin_wxtable('user')).'`');
                $this->_admin_msg(1, '同步用户数据中，请勿关闭',\Phpcmf\Service::L('Router')->url('weixin/user/down_add', array('action'=>'user')), 0);
            }

            // 查询并入库
            $res = \Phpcmf\Service::M()->db->table(weixin_wxtable('follow'))->whereIn('openid', $rt['data']['data']['openid'])->get()->getResultArray();
            if (count($res) != $rt['data'] ['count']) {
                // 更新的数量不一致，可能有增加的用户openid
                $openids = array();
                if ($res) {
                    foreach ($res as $t) {
                        $openids[] = $t['openid'];
                    }
                }
                $diff = array_diff ( $rt['data'] ['data'] ['openid'], $openids );
                if (! empty ( $diff )) {
                    foreach ( $diff as $id ) {
                        $save = array();
                        $save ['openid'] = $id;
                        $save ['uid'] = 0;
                        $save ['status'] = 0;
                        \Phpcmf\Service::M()->db->table(weixin_wxtable('follow'))->insert($save);
                    }
                }
            }
            $this->_admin_msg(1, '同步用户OpenID中，请勿关闭',\Phpcmf\Service::L('Router')->url('weixin/user/down_add', array('next_openid'=>$rt['data']['next_openid'])), 0);
        } elseif ($action == 'user') {
            // 开始更新会员资料
            $list = \Phpcmf\Service::M()->db->table(weixin_wxtable('follow'))->where('status', 0)->limit(50)->get()->getResultArray();
            if (empty ($list)) {
                $this->_admin_msg(1, '同步用户完成',\Phpcmf\Service::L('Router')->url('weixin/user/index') , 0);
            }
            $param = $openids = $uids = array();
            foreach ( $list as $vo ) {
                $param['user_list'][] = array (
                    'openid' => $vo ['openid'],
                    'lang' => 'zh-CN'
                );
                $openids[] = $vo ['openid'];
                $uids [$vo ['openid']] = $vo ['uid'];
            }
            $rt = wx_post_https_json_data(
                'https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=' . $access_token,
                $param
            );
            if (!$rt['code']) {
                $this->_admin_msg(0, $rt['msg']);
            }
            $ok = 0;
            $update = array();
            foreach ($rt['data']['user_info_list'] as $u ) {
                if ($u['subscribe'] == 0) {
                    continue;
                }
                $rt = \Phpcmf\Service::M('User', APP_DIR)->insert_user($u);
                // 更新关注表状态
                if ($rt['code']) {
                    $ok++;
                    \Phpcmf\Service::M()->db->table(weixin_wxtable('follow'))->where('openid', $u['openid'])->update(array(
                        'status' => 1,
                        'uid' => $rt['code'],
                    ));
                }
                
                $update[] = $u['openid'];
            }

            // 判断没获取到的
            foreach ($list as $t) {
                if (!in_array($t['openid'], $update)) {
                    \Phpcmf\Service::M()->db->table(weixin_wxtable('follow'))->where('openid', $t['openid'])->update(array(
                        'status' => 1,
                        'uid' => 0,
                    ));
                }
            }
            $this->_admin_msg(1, '每次同步50个，本次成功'.$ok.'个，请勿关闭',\Phpcmf\Service::L('Router')->url('weixin/user/down_add', array('action'=>'user')), 0);
        }
    }

    public function index() {

        $groupid = (int)\Phpcmf\Service::L('Input')->request('groupid');
        $groupid && \Phpcmf\Service::M()->set_where_list('`groupids` LIKE "%'.str_replace('\\', '\\\\\\', json_encode($groupid)).'%"');

        list($tpl) = $this->_List(['groupid' => $groupid]);
        \Phpcmf\Service::V()->display($tpl);
    }

    // 分组设置
    public function move_edit() {

        $ids = \Phpcmf\Service::L('Input')->get_post_ids();
        !$ids && $this->_json(0, dr_lang('所选数据不存在'));

        $rows = \Phpcmf\Service::M()->init($this->init)->where_in('id', $ids)->getAll();
        !$rows && $this->_json(0, dr_lang('所选数据不存在'));

        $rt = weixin_get_access_token();
        if (!$rt['code']) {
            $this->_json(0, $rt['msg']);
        }

        $access_token = $rt['msg'];

        $groupid = (int)\Phpcmf\Service::L('Input')->post('groupid');
        if (!$groupid) {
            $this->_json(0, '未选择标签组');
        }

        $openid = [];
        foreach ($rows as $t) {
            $openid[] = $t['openid'];

            $groupids = dr_string2array($t['groupids']);
            if (!in_array($groupid, $groupids)) {
                $groupids[] = $groupid;
            }

            // 更改本地库
            \Phpcmf\Service::M()->db->table(weixin_wxtable('user'))->where('id', $t['id'])->update(array(
                'groupids' => dr_array2string($groupids),
            ));
        }

        $param = [
            'openid_list' => $openid,
            'tagid' => $groupid,
        ];
        $rt = wx_post_https_json_data(
            'https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=' . $access_token,
            $param
        );
        if (!$rt['code']) {
            $this->_json(0, $rt['msg']);
        }

        $this->_json(1, dr_lang('操作成功'));
    }

    // 分组取消
    public function move_del() {

        $ids = \Phpcmf\Service::L('Input')->get_post_ids();
        !$ids && $this->_json(0, dr_lang('所选数据不存在'));

        $rows = \Phpcmf\Service::M()->init($this->init)->where_in('id', $ids)->getAll();
        !$rows && $this->_json(0, dr_lang('所选数据不存在'));

        $rt = weixin_get_access_token();
        if (!$rt['code']) {
            $this->_json(0, $rt['msg']);
        }

        $access_token = $rt['msg'];

        $groupid = (int)\Phpcmf\Service::L('Input')->post('groupid');
        if (!$groupid) {
            $this->_json(0, '未选择标签组');
        }

        $openid = [];
        foreach ($rows as $t) {
            $openid[] = $t['openid'];

            $groupids = dr_string2array($t['groupids']);
            if ($groupids && in_array($groupid, $groupids)) {
                foreach ($groupids as $i => $v) {
                    if ($v == $groupid) {
                        unset($groupids[$i]);
                    }
                }
                // 更改本地库
                \Phpcmf\Service::M()->db->table(weixin_wxtable('user'))->where('id', $t['id'])->update(array(
                    'groupids' => dr_array2string($groupids),
                ));
            }
        }

        $param = [
            'openid_list' => $openid,
            'tagid' => $groupid,
        ];
        $rt = wx_post_https_json_data(
            'https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token=' . $access_token,
            $param
        );
        if (!$rt['code']) {
            $this->_json(0, $rt['msg']);
        }

        $this->_json(1, dr_lang('操作成功'));
    }

}
