<?php namespace Phpcmf\Controllers\Admin;

class Config extends \Phpcmf\Common
{

    public function __construct(...$params) {
        parent::__construct(...$params);
        \Phpcmf\Service::V()->assign('menu', \Phpcmf\Service::M('auth')->_admin_menu(
            [
                '系统配置' => [APP_DIR.'/config/index', 'fa fa-cog'],
            ]
        ));
    }

    public function index() {

        $page = intval(\Phpcmf\Service::L('Input')->get('page'));
        $data = \Phpcmf\Service::M('Weixin', 'Weixin')->get_config('config');

        if (IS_AJAX_POST) {

            $post = \Phpcmf\Service::L('Input')->post('data');
            $rt = \Phpcmf\Service::M('Weixin', 'Weixin')->save_config('config', $post);
            if (!$rt['code']) {
                $this->_json(0, $rt['msg']);
            }
            $this->_json(1, dr_lang('操作成功'));
        }

        $module = [];
        $local = dr_dir_map(APPSPATH, 1); // 搜索本地模块
        foreach ($local as $dir) {
            if (is_file(APPSPATH.$dir.'/Config/App.php')) {
                $key = strtolower($dir);
                $cfg = require APPSPATH.$dir.'/Config/App.php';
                if ($cfg['type'] == 'module') {
                    $cfg['dirname'] = $key;
                    $module[$key] = $cfg;
                }
            }
        }

        \Phpcmf\Service::V()->assign([
            'data' => $data,
            'page' => $page,
            'form' => dr_form_hidden(['page' => $page]),
            'module' => $module,
        ]);
        \Phpcmf\Service::V()->display('config_index.html');
    }


}
