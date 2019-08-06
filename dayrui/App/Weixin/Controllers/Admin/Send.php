<?php namespace Phpcmf\Controllers\Admin;

// 微信客服群发
class Send extends \Phpcmf\Common
{

    public function __construct(...$params) {
        parent::__construct(...$params);
        \Phpcmf\Service::V()->assign('menu', \Phpcmf\Service::M('auth')->_admin_menu(
            [
                '客服群发' => [APP_DIR.'/send/index', 'fa fa-envelope-o'],
            ]
        ));
    }

    public function add() {

        $id = (int)\Phpcmf\Service::L('Input')->get('id');
        $send_table = weixin_wxtable('send');
        $data = \Phpcmf\Service::M()->table($send_table)->where('cid', $id)->where('status', 0)->getAll(20);
        if (!$data) {
            $msg = '发送结果：成功'. \Phpcmf\Service::M()->table($send_table)->where('cid', $id)->where('status', 1)->counts().'个，失败'. \Phpcmf\Service::M()->table($send_table)->where('cid', $id)->where('status', 2)->counts().'个';
            \Phpcmf\Service::M()->db->table($send_table)->where('cid', $id)->delete();
            $this->_admin_msg(1, $msg);
        }

        $rt = weixin_get_access_token();
        if (!$rt['code']) {
            $this->_admin_msg(0, $rt['msg']);
        }
        $access_token = $rt['msg'];

        foreach ($data as $t) {
            $param = dr_string2array($t['content']);
            $param['touser'] = $t['openid'];
            $rt = wx_post_https_json_data(
                'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token,
                $param
            );
            if (!$rt['code']) {
                \Phpcmf\Service::M()->table($send_table)->update($t['id'], [
                    'status' => 2,
                ]);
            } else {
                // 发送成功
                \Phpcmf\Service::M()->table($send_table)->update($t['id'], [
                    'status' => 1,
                ]);
            }
        }

        $this->_admin_msg(2, '正在发送中...', FC_NOW_URL, 0);
    }

    public function index() {


        if ($_GET['ac'] == 'send' && IS_AJAX_POST) {
            // 发送群发
            $data = \Phpcmf\Service::L('Input')->post('data');
            !$data['content'][$data['type']] && $this->_json(0, '没有选择内容');
            if ($data['type']) {
                $content = \Phpcmf\Service::M()->table(weixin_wxtable('content'))->get($data['content'][$data['type']]);
                !$content && $this->_json(0, '素材内容不存在');
                !$content['media_id'] && $this->_json(0, '素材内容未同步');
                $rt = \Phpcmf\Service::M('Weixin', 'Weixin')->send_for_content($data['groupid'], $content);
            } else {
                $rt = \Phpcmf\Service::M('Weixin', 'Weixin')->send_for_text($data['groupid'], $data['content'][0]);
            }
            !$rt['code'] && $this->_json(0, $rt['msg']);
            $this->_json(1, '发送命令提交成功，即将自动发布', ['url' =>\Phpcmf\Service::L('Router')->url('weixin/send/add', ['id' => $rt['code']])]);
            exit;
        }

        $db = \Phpcmf\Service::M()->table(weixin_wxtable('content'))->where('media_id<>""');
        if (IS_POST) {
            $param = \Phpcmf\Service::L('Input')->post('search');
            $param['tid'] && $db->where('tid', $param['tid']);
            $param['keyword'] && $db->where('`title` LIKE "%'.$param['keyword'].'%"');
        }

        $list = $db->order_by('inputtime desc')->getAll(30);

        $uuid = 0;
        $group = \Phpcmf\Service::M('User', APP_DIR)->get_group_data();
        if ($_GET['uid']) {
            $user = \Phpcmf\Service::M()->table(weixin_wxtable('user'))->get($_GET['uid']);
            $uuid = $user['openid'];
            if ($user && $uuid) {
                $group[$uuid] = [
                    'id' => $uuid,
                    'tag' => $uuid,
                    'name' => '指定粉丝',
                    'count' => dr_html2emoji($user['nickname']),
                ];
            }
        }

        \Phpcmf\Service::V()->assign([
            'form' => dr_form_hidden(),
            'list' => $list,
            'uuid' => $uuid,
            'param' => $param,
            'group' => $group,
            'ucount' => \Phpcmf\Service::M()->table(weixin_wxtable('user'))->counts(),
            'content_type' => [
                'index' => '图文',
                'image' => '图片',
                'voice' => '语音',
                'video' => '视频',
            ],
        ]);
        \Phpcmf\Service::V()->display('send_index.html');
    }


}
