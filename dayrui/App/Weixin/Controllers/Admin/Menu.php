<?php namespace Phpcmf\Controllers\Admin;

// 自定义菜单
class Menu extends \Phpcmf\Table
{
    private $menu_type;
    private $gid;

    public function __construct(...$params)
    {
        parent::__construct(...$params);
        // 支持附表存储
        $this->is_data = 0;
        // 表单显示名称
        $this->name = dr_lang('微信自定义菜单');
        $this->gid = intval($_GET['gid']);
        // 初始化数据表
        $this->_init([
            'table' => weixin_wxtable('menu'),
            'where_list' => 'gid='.$this->gid,
            'order_by' => 'displayorder asc,id asc',
        ]);
        $this->menu_type = [
            'view' => '网页地址',
            'login' => '登录认证',
            'click' => '小插件',
            'view_limited' => '跳转图文',
        ];
        $local = dr_dir_map(APPPATH.'Plugins', 1);
        $this->plugin = [];
        if ($local) {
            foreach ($local as $dir) {
                if (is_file(APPPATH.'Plugins/'.$dir.'/App.php')) {
                    $key = strtolower($dir);
                    $cfg = require APPPATH.'Plugins/'.$dir.'/App.php';
                    $this->plugin[$key] = [
                        'name' => $cfg['name'],
                        'value' => 'App::'.$key,
                    ];
                }
            }
        }

        $menu = [
            '自定义菜单' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/index{gid='.$this->gid.'}', 'fa fa-list'],
            '添加' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/add{gid='.$this->gid.'}', 'fa fa-plus', null, '33%'],
            '修改' => ['hide:'.APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/edit{gid='.$this->gid.'}', 'fa fa-edit'],
        ];
        if (!$this->gid) {
            $menu['从服务端获取菜单'] = ['ajax:'.APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/down_add', 'fa fa-refresh'];
        }


        \Phpcmf\Service::V()->assign([
            'gid' => $this->gid,
            'menu' => \Phpcmf\Service::M('auth')->_admin_menu($menu),
            'plugin' => $this->plugin,
            'menu_type' => $this->menu_type,
            'menu_top' => \Phpcmf\Service::M('User', APP_DIR)->top_menu(),
        ]);
    }

    public function down_add() {
        $rt = \Phpcmf\Service::M('User', APP_DIR)->down_menu();
        $rt['code'] ? $this->_json(1, '操作成功') : $this->_json(0, $rt['msg']);
    }

    public function sync_index() {
        $rt = \Phpcmf\Service::M('User', APP_DIR)->sync_menu();
        $rt['code'] ? $this->_json(1, '操作成功') : $this->_json(0, $rt['msg']);
    }

    public function displayorder_edit() {

        $id = (int)\Phpcmf\Service::L('Input')->get('id');
        $row = \Phpcmf\Service::M()->table(weixin_wxtable('menu'))->get($id);
        !$row && $this->_json(0, dr_lang('数据#%s不存在', $id));

        $value = (int)\Phpcmf\Service::L('Input')->get('value');
        $rt = \Phpcmf\Service::M()->table(weixin_wxtable('menu'))->save($id, 'displayorder', $value);
        !$rt['code'] && $this->_json(0, $rt['msg']);

        $this->_json(1, dr_lang('操作成功'));
    }


    public function index() {

        list($tpl, $data) = $this->_list([], -1);
        $list = [];
        if ($data['list']) {
            foreach ($data['list'] as $t) {
                if ($t['pid'] == 0) {
                    $list[$t['id']] = $t;
                    foreach ($data['list'] as $tt) {
                        if ($tt['pid'] == $t['id']) {
                            $list[$tt['id']] = $tt;
                        }
                    }
                }
            }

        }

        \Phpcmf\Service::V()->assign('list', $list);
        \Phpcmf\Service::V()->assign('is_ajax', 0);
        \Phpcmf\Service::V()->display($tpl);
    }

    public function add() {
        list($tpl) = $this->_Post();
        \Phpcmf\Service::V()->assign('pid', $_GET['pid']);
        $this->_get_data();
        \Phpcmf\Service::V()->display($tpl);exit;
    }

    public function edit() {

        $id = intval(\Phpcmf\Service::L('Input')->get('id'));
        list($tpl, $data) = $this->_Post($id);

        !$data && $this->_admin_msg(0, dr_lang('菜单#%s不存在', $id));
        !$data['pid'] && !$data['type'] && $tpl = 'menu_post_ajax.html';

        if ($this->gid != $data['gid']) {
            $this->_admin_msg(0, dr_lang('菜单#%s不属于当前组', $id));
        }

        $this->_get_data();

        \Phpcmf\Service::V()->display($tpl);exit;
    }

    private function _get_data() {

        \Phpcmf\Service::V()->assign([
            'view_url' => [
                [
                    'name' => '用户中心',
                    'value' => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->weixin['account']['appid'].'&redirect_uri='.urlencode(SITE_URL.'index.php?s=weixin&c=member').'&response_type=code&scope=snsapi_base&state=member#wechat_redirect',
                ],
            ],
            'twsc' => \Phpcmf\Service::M()->table(weixin_wxtable('content'))->where('tid', 'index')->where('media_id<>""')->order_by('inputtime desc')->getAll(50),
        ]);
    }

    // 格式化保存数据
    protected function _Format_Data($id, $data, $old) {

        !$data['name'] && $this->_json(0, '菜单名称必须填写', ['field' => 'name']);

        $data['name'] = dr_emoji2html($data['name']);
        $data['value'] = (string)$_POST['value_'.$data['type']];

        if (!$id) {
            $data['menuid'] = 0;
            $data['displayorder'] = 0;
        }

        $data['gid'] = $this->gid;

        return $data;
    }

    public function del() {
        $this->_Del(
            \Phpcmf\Service::L('Input')->get_post_ids(),
            null,
            null,
            \Phpcmf\Service::M()->dbprefix($this->init['table'])
        );
    }

}
