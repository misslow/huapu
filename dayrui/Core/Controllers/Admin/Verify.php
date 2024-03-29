<?php namespace Phpcmf\Controllers\Admin;

/* *
 *
 * Copyright [2019] [李睿]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * http://www.tianruixinxi.com
 *
 * 本文件是框架系统文件，二次开发时不建议修改本文件
 *
 * */



// 审核流程
class Verify extends \Phpcmf\Table
{
    public $role;
    public $type;

    public function __construct(...$params)
    {
        parent::__construct(...$params);
        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_admin_menu(
                [
                    '审核流程' => [\Phpcmf\Service::L('Router')->class.'/index', 'fa fa-sort-numeric-asc'],
                    '添加' => [\Phpcmf\Service::L('Router')->class.'/add', 'fa fa-plus'],
                    '修改' => ['hide:'.\Phpcmf\Service::L('Router')->class.'/edit', 'fa fa-edit'],
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
        // url显示名称
        $this->name = dr_lang('审核流程');
        // 初始化数据表
        $this->_init([
            'table' => 'admin_verify',
            'field' => $this->my_field,
            'order_by' => 'id desc',
        ]);
        $this->role = \Phpcmf\Service::M('Auth')->get_role_all();
    }

    // 后台查看url列表
    public function index() {
        
        $this->_List([], -1);
        \Phpcmf\Service::V()->display('verify_index.html');
    }

    // 后台添加url内容
    public function add() {
        $this->_Post(0);
        \Phpcmf\Service::V()->display('verify_add.html');
    }

    // 后台修改url内容
    public function edit() {
        $this->_Post(intval(\Phpcmf\Service::L('Input')->get('id')));
        \Phpcmf\Service::V()->display('verify_add.html');
    }

    // 复制
    public function copy_edit() {

        $id = intval(\Phpcmf\Service::L('Input')->get('id'));
        $data = \Phpcmf\Service::M()->db->table('admin_verify')->where('id', $id)->get()->getRowArray();
        !$data && $this->_josn(0, dr_lang('数据#%s不存在', $id));

        unset($data['id']);
        $data['name'].= '_copy';

        $rt = \Phpcmf\Service::M()->table('admin_verify')->insert($data);

        !$rt['code'] && $this->_json(0, dr_lang($rt['msg']));
        \Phpcmf\Service::M('cache')->sync_cache('verify');
        $this->_json(1, dr_lang('复制成功'));
    }

    // 查看流程
    public function show_index() {

        $id = intval(\Phpcmf\Service::L('Input')->get('id'));
        $data = \Phpcmf\Service::M()->db->table('admin_verify')->where('id', $id)->get()->getRowArray();
        !$data && $this->_josn(0, dr_lang('数据#%s不存在', $id));

        \Phpcmf\Service::V()->assign([
            'value' => dr_string2array($data['verify']),
        ]);
        \Phpcmf\Service::V()->display('verify_show.html');exit;
    }


    // 保存
    protected function _Save($id = 0, $data = [], $old = [], $func = null, $func2 = null) {
        return parent::_Save($id, $data, $old, function($id, $data){
            // 保存前的格式化
            $value = \Phpcmf\Service::L('Input')->post('value');
            if ($value['role']) {
                foreach ($value['role'] as $i => $ids) {
                    if (!$ids) {
                        unset($value['role'][$i]);
                    }
                }
            }
            $data[1]['verify'] = dr_array2string($value);

            return dr_return_data(1, 'ok', $data);
        }, function ($id, $data, $old) {

            \Phpcmf\Service::M('cache')->sync_cache('verify');
        });
    }

    /**
     * 获取内容
     * $id      内容id,新增为0
     * */
    protected function _Data($id = 0) {

        $data = parent::_Data($id);
        $data['value'] = dr_string2array($data['verify']);
        return $data;
    }

    // 后台删除url内容
    public function del() {
        $this->_Del(
            \Phpcmf\Service::L('Input')->get_post_ids(),
            null,
            function ($r) {
                \Phpcmf\Service::M('cache')->sync_cache('verify');
            }
        );
    }

}
