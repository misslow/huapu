<?php namespace Phpcmf\Controllers\Admin;

// 微信群发
class Sendall extends \Phpcmf\Table
{

    public function __construct(...$params) {
        parent::__construct(...$params);
        \Phpcmf\Service::V()->assign('menu', \Phpcmf\Service::M('auth')->_admin_menu(
            [
                '高级群发' => [APP_DIR.'/sendall/index', 'fa fa-volume-up'],
            ]
        ));
    }

    public function index() {


        if ($_GET['ac'] == 'send' && IS_AJAX_POST) {
            // 发送群发
            $data = \Phpcmf\Service::L('Input')->post('data');
            !$data['content'] && $this->_json(0, '没有选择素材内容');
            $content = \Phpcmf\Service::M()->table(weixin_wxtable('content'))->get($data['content']);
            !$content && $this->_json(0, '素材内容不存在');
            !$content['media_id'] && $this->_json(0, '素材内容未同步');
            $rt = \Phpcmf\Service::M('Weixin', 'Weixin')->sendall($data['groupid'], $content);
            !$rt['code'] && $this->_json(0, $rt['msg']);
            $this->_json(1, '发送提交成功');
            exit;
        }

        $db = \Phpcmf\Service::M()->table(weixin_wxtable('content'))->where('media_id<>""');
        if (IS_POST) {
            $param = \Phpcmf\Service::L('Input')->post('search');
            $param['tid'] && $db->where('tid', $param['tid']);
            $param['keyword'] && $db->where('`title` LIKE "%'.$param['keyword'].'%"');
        }

        $list = $db->order_by('inputtime desc')->getAll(30);

        \Phpcmf\Service::V()->assign([
            'form' => dr_form_hidden(),
            'list' => $list,
            'param' => $param,
            'group' => \Phpcmf\Service::M('User', APP_DIR)->get_group_data(),
            'ucount' => \Phpcmf\Service::M()->table(weixin_wxtable('user'))->counts(),
            'content_type' => [
                'index' => '图文',
                'image' => '图片',
                'voice' => '语音',
                'video' => '视频',
            ],
        ]);
        \Phpcmf\Service::V()->display('sendall_index.html');
    }


}
