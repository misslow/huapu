<?php namespace Phpcmf\Controllers\Admin;

class Xcx extends \Phpcmf\Common
{

    public function __construct(...$params) {
        parent::__construct(...$params);
        \Phpcmf\Service::V()->assign('menu', \Phpcmf\Service::M('auth')->_admin_menu(
            [
                '小程序' => [APP_DIR.'/xcx/index', 'fa fa-cog'],
            ]
        ));
    }

    public function index() {

        $page = intval(\Phpcmf\Service::L('Input')->get('page'));
        $data = \Phpcmf\Service::M('Weixin', 'Weixin')->get_config('xcx');

        if (IS_AJAX_POST) {

            $post = \Phpcmf\Service::L('Input')->post('data');
            $rt = \Phpcmf\Service::M('Weixin', 'Weixin')->save_config('xcx', $post);
            if (!$rt['code']) {
                $this->_json(0, $rt['msg']);
            }
            $this->_json(1, dr_lang('操作成功'));
        }


        \Phpcmf\Service::V()->assign([
            'data' => $data,
            'page' => $page,
            'form' => dr_form_hidden(['page' => $page]),
        ]);
        \Phpcmf\Service::V()->display('xcx_index.html');
    }


}
