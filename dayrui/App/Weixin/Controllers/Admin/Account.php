<?php namespace Phpcmf\Controllers\Admin;

class Account extends \Phpcmf\Common
{

    public function __construct(...$params) {
        parent::__construct(...$params);
        \Phpcmf\Service::V()->assign('menu', \Phpcmf\Service::M('auth')->_admin_menu(
            [
                '账号接入' => [APP_DIR.'/account/index', 'fa fa-weixin'],
            ]
        ));
    }

    public function index() {


        $page = intval(\Phpcmf\Service::L('Input')->get('page'));
        $data = \Phpcmf\Service::M('Weixin', 'Weixin')->get_config('account');

        if (IS_AJAX_POST) {

            $post = \Phpcmf\Service::L('Input')->post('data');
            $rt = \Phpcmf\Service::M('Weixin', 'Weixin')->save_config('account', $post);
            if (!$rt['code']) {
                $this->_json(0, $rt['msg']);
            }
            $rt = weixin_get_access_token($post);
            if (!$rt['code']) {
                $this->_json(0, $rt['msg']);
            }
            $this->_json(1, dr_lang('操作成功'));
        }

        $rt = weixin_get_access_token();
        $access_tocken = $rt['code'] ? '': $rt['msg'];

        \Phpcmf\Service::V()->assign([
            'data' => $data,
            'page' => $page,
            'form' => dr_form_hidden(['page' => $page]),
            'access_tocken' => $access_tocken,
        ]);
        \Phpcmf\Service::V()->display('account_index.html');
    }


}
