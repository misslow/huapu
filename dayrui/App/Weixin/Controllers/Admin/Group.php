<?php namespace Phpcmf\Controllers\Admin;

// 粉丝组
class Group extends \Phpcmf\Table
{

    public function __construct(...$params)
    {
        parent::__construct(...$params);
        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_admin_menu(
                [
                    '用户标签' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/index', 'fa fa-users'],
                    '添加' => ['add:'.APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/add', 'fa fa-plus', null, '33%'],
                    '从服务端获取分组' => ['ajax:'.APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/down_add', 'fa fa-refresh'],
                ]
            ),
        ]);
        // 支持附表存储
        $this->is_data = 0;
        // 表单显示名称
        $this->name = dr_lang('微信用户标签');
        // 初始化数据表
        $this->_init([
            'table' => weixin_wxtable('group'),
            'order_by' => 'id asc',
        ]);
    }

    public function down_add() {
        $rt = \Phpcmf\Service::M('User', 'weixin')->down_group();
        $rt['code'] ? $this->_json(1, '操作成功') : $this->_json(0, $rt['msg']);
    }

    public function index() {
        $this->_list([], -1);
        \Phpcmf\Service::V()->display('group_index.html');
    }

    public function add() {
        $this->_Post();
        \Phpcmf\Service::V()->display('group_add.html');exit;
    }

    public function edit() {
        $this->_Post(intval(\Phpcmf\Service::L('Input')->get('id')));
        \Phpcmf\Service::V()->display('group_add.html');exit;
    }

    // 删除个性菜单
    public function menu_del() {

        $gid = intval($_GET['gid']);
        !$gid && $this->_json(0, '参数未定义');

        $menu = \Phpcmf\Service::M()->table(weixin_wxtable('menu'))->where('gid', $gid)->getRow();
        !$menu && $this->_json(0, '菜单不存在');

        $at = weixin_get_access_token();
        if (!$at['code']) {
            $this->_json(0, $at['msg']);
        }

        $data = array('menuid'=> $menu['menuid']);
        $rt = wx_post_https_json_data('https://api.weixin.qq.com/cgi-bin/menu/delconditional?access_token='.$at['msg'], $data);
        if (!$rt['code']) {
            $this->_json(0, $rt['msg']);
        }

        \Phpcmf\Service::M()->db->table(weixin_wxtable('menu'))->where('gid', $gid)->delete();

        $this->_json(1, '操作成功');
    }

    // 格式化保存数据
    protected function _Format_Data($id, $data, $old) {

        $data['count'] = intval($old['count']);
        !$data['name'] && $this->_json(0, '用户标签名称必须填写', ['field' => 'name']);

        if ($id) {
            // 修改标签
            if ($data['name'] != $old['name']) {
                $rt = \Phpcmf\Service::M('User', 'weixin')->edit_group($id, $data['name']);
                !$rt['code'] && $this->_json(0, $rt['msg']);
            }
        } else {
            // 创建标签
            $rt = \Phpcmf\Service::M('User', 'weixin')->add_group($data['name']);
            !$rt['code'] && $this->_json(0, $rt['msg']);
        }

        $data['groupid'] = intval($data['groupid']);
        unset($data['tag']);


        return $data;
    }

    // 后台删除表单内容
    public function del() {
        $this->_Del(
            \Phpcmf\Service::L('Input')->get_post_ids(),
            function ($rows) {
                foreach ($rows as $t) {
                    $rt = \Phpcmf\Service::M('User', 'weixin')->delete_group($t['id']);
                    !$rt['code'] && $this->_json(0, $rt['msg']);
                }
                return dr_return_data(1, 'ok');
            },
            null,
            \Phpcmf\Service::M()->dbprefix($this->init['table'])
        );
    }

}
