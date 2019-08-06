<?php namespace Phpcmf\Controllers\Admin;

// 微信自动回复内容
class Reply extends \Phpcmf\Table
{

    public function __construct(...$params)
    {
        parent::__construct(...$params);
        // 支持附表存储
        $this->is_data = 0;
        // 表单显示名称
        $this->name = dr_lang('微信回复内容');
        // 初始化数据表
        $this->_init([
            'table' => weixin_wxtable('reply'),
            'order_by' => '`displayorder` desc,`id` desc',
            'date_field' => 'updatetime',
        ]);
        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_admin_menu(
                [
                    '自动回复' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/index', 'fa fa-comments'],
                    '添加' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/add', 'fa fa-plus'],
                    '修改' => ['hide:'.APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/edit', 'fa fa-plus'],
                ]
            ),
        ]);
    }

    public function index() {
        $where = $_GET['keyword'] ? '`keyword` LIKE "%'.$_GET['keyword'].'%"' : '';
        $where && \Phpcmf\Service::M()->set_where_list($where);
        list($tpl) = $this->_list();
        \Phpcmf\Service::V()->display($tpl);
    }

    public function add() {
        list($tpl) = $this->_Post();
        \Phpcmf\Service::V()->display($tpl);
    }

    public function edit() {
        list($tpl) = $this->_Post(intval(\Phpcmf\Service::L('Input')->get('id')));
        \Phpcmf\Service::V()->display($tpl);
    }

    public function displayorder_edit() {

        $id = (int)\Phpcmf\Service::L('Input')->get('id');
        $row = \Phpcmf\Service::M()->table(weixin_wxtable('reply'))->get($id);
        !$row && $this->_json(0, dr_lang('数据#%s不存在', $id));

        $value = (int)\Phpcmf\Service::L('Input')->get('value');
        $rt = \Phpcmf\Service::M()->table(weixin_wxtable('reply'))->save($id, 'displayorder', $value);
        !$rt['code'] && $this->_json(0, $rt['msg']);

        $this->_json(1, dr_lang('操作成功'));
    }


    /**
     * 获取内容
     * */
    protected function _Data($id = 0) {

        $data = parent::_Data($id);
        if (!$data) {
            return [];
        }


        return $data;
    }

    // 格式化保存数据
    protected function _Format_Data($id, $data, $old) {

        $data['content'] = $_POST['value'][$data['tid']];
        $data['displayorder'] = (int)$data['displayorder'];

        if (!$id) {
            $data['counts'] = 0;
            $data['updatetime'] = 0;
        }

        return $data;
    }

    //
    public function del() {
        $this->_Del(\Phpcmf\Service::L('Input')->get_post_ids());
    }

}
