<?php namespace Phpcmf\Controllers\Admin;

// API接口数据
class Http extends \Phpcmf\Table
{
    private $sjlx;

    public function __construct(...$params)
    {
        parent::__construct(...$params);
        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_admin_menu(
                [
                    '接口数据' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/index', 'fa fa-plug'],
                    '添加' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/add', 'fa fa-plus'],
                    '修改' => ['hide:'.APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/edit', 'fa fa-edit'],
                    '更新缓存' => ['ajax:api/cache_update', 'fa fa-refresh'],
                    'help' => [399],
                ]
            ),
        ]);
        // 支持附表存储
        $this->is_data = 0;
        $this->my_field = array(
            'name' => array(
                'ismain' => 1,
                'name' => dr_lang('名称'),
                'fieldname' => 'name',
                'fieldtype' => 'Text',
                'setting' => array(
                    'option' => array(
                        'width' => 200,
                    ),
                    'validate' => array(
                        'required' => 1,
                    )
                )
            ),
        );
        // 表单显示名称
        $this->name = dr_lang('API接口数据');
        // 初始化数据表
        $this->_init([
            'table' => 'api_http',
            'field' => $this->my_field,
            'order_by' => 'id desc',
        ]);
        $this->sjlx = [
            0 => dr_lang('直接输出'),
            1 => dr_lang('JSON数组'),
            2 => dr_lang('PHP执行代码'),
            3 => dr_lang('模板循环标签'),
            4 => dr_lang('自定义SQL语句'),
        ];
        \Phpcmf\Service::V()->assign([
            'sjlx' => $this->sjlx,
            'field' => $this->my_field,
        ]);
    }

    // 后台查看表单列表
    public function index() {
        list($tpl) = $this->_List();
        \Phpcmf\Service::V()->display($tpl);
    }

    // 后台添加表单内容
    public function add() {
        list($tpl) = $this->_Post(0);
        \Phpcmf\Service::V()->assign('disabled', 0);
        \Phpcmf\Service::V()->display($tpl);
    }

    // 后台修改表单内容
    public function edit() {
        list($tpl, $data) = $this->_Post(intval(\Phpcmf\Service::L('Input')->get('id')));
        \Phpcmf\Service::V()->assign('content', dr_string2array($data['content']));
        \Phpcmf\Service::V()->display($tpl);
    }

    public function test_index() {

        $id = intval(\Phpcmf\Service::L('Input')->get('id'));
        $data = $this->_Data($id);
        if (!$data) {
            exit(dr_lang('接口数据不存在'));
        }
        $content = dr_string2array($data['content']);
        if (!$content) {
            exit(dr_lang('接口数据定义内容不存在'));
        }

        $rt = \Phpcmf\Service::M('http', APP_DIR)->get_api_data($content);
        echo SITE_URL.'index.php?s=httpapi&id='.$id.'&appid=需要填写值&appsecret=需要填写值';
        echo '<hr>';
        echo '<pre>';
        print_r($rt);
        exit;
    }

    public function call_index() {

        $call = dr_safe_replace(\Phpcmf\Service::L('Input')->get('name'));
        if (!$call) {
            $this->_json(0, dr_lang('没有填写回调方法'));
        }
        // 回调函数
        if (method_exists(\Phpcmf\Service::L('http'), $call)) {
            $this->_json(1, dr_lang('定义成功'));
        } else {
            $this->_json(0, '回调方法【'.$call.'】未定义');
        }
    }

    // 不用或者启用
    public function hidden_edit() {

        $id = (int)\Phpcmf\Service::L('Input')->get('id');
        $row = \Phpcmf\Service::M()->table($this->init['table'])->get($id);
        !$row && $this->_json(0, dr_lang('数据#%s不存在', $id));

        $v = $row['disabled'] ? 0 : 1;
        \Phpcmf\Service::M()->table($this->init['table'])->update($id, ['disabled' => $v]);
        \Phpcmf\Service::M('http', APP_DIR)->cache();

        exit($this->_json(1, dr_lang($this->name.($v ? '已被禁用' : '已被启用')), ['value' => $v]));
    }

    // 保存
    protected function _Save($id = 0, $data = [], $old = [], $func = null, $func2 = null) {
        return parent::_Save($id, $data, $old, function($id, $data, $old){

            $post = \Phpcmf\Service::L('Input')->post('data');
            if (!$id) {
                $data[1]['inputtime'] = SYS_TIME;
            }
            $data[1]['content'] = dr_array2string($post['content']);
            $data[1]['disabled'] = $post['disabled'];
            return dr_return_data(1, null, $data);
        }, function ($id, $data, $old) {
            \Phpcmf\Service::M('http', APP_DIR)->cache();
        });
    }


    // 后台删除表单内容
    public function del() {
        $this->_Del(
            \Phpcmf\Service::L('Input')->get_post_ids(),
            null,
            function ($r) {

                \Phpcmf\Service::M('http', APP_DIR)->cache();
            },
            \Phpcmf\Service::M()->dbprefix($this->init['table'])
        );
    }

}
