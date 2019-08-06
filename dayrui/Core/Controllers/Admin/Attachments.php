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



// 附件
class Attachments extends \Phpcmf\Table
{

    public function __construct(...$params)
    {
        parent::__construct(...$params);
        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_admin_menu(
                [
                    '已使用的附件' => [\Phpcmf\Service::L('Router')->class.'/index', 'fa fa-folder'],
                    '未使用的附件' => [\Phpcmf\Service::L('Router')->class.'/unused_index', 'fa fa-folder-o'],
                    'help' => [356],
                ]
            )
        ]);
    }

    // 已使用管理
    public function index() {

        $field = [
            'author' => [
                'ismain' => 1,
                'fieldtype' => 'Text',
                'fieldname' => 'author',
                'name' => dr_lang('账号'),
            ],
            'related' => [
                'ismain' => 1,
                'fieldtype' => 'Text',
                'fieldname' => 'related',
                'name' => dr_lang('附件归属'),
            ],
            'fileext' => [
                'ismain' => 1,
                'fieldtype' => 'Text',
                'fieldname' => 'fileext',
                'name' => dr_lang('扩展名'),
            ],
            'uid' => [
                'ismain' => 1,
                'fieldtype' => 'Text',
                'fieldname' => 'uid',
                'name' => 'uid',
            ],
        ];

        $remote = (int)$_GET['remote'];
        $where_list = '';
        $remote && $where_list = '`remote`='.$remote;

        $this->_init([
            'table' => 'attachment_data',
            'field' => $field,
            'order_by' => 'id desc',
            'where_list' => $where_list,
            'date_field' => 'inputtime',
        ]);

        $this->_List();

        \Phpcmf\Service::V()->assign([
            'field' => $field,
            'table' => 'data',
            'remote' => \Phpcmf\Service::M()->table('attachment_remote')->getAll(),
        ]);
        \Phpcmf\Service::V()->display('attachment_admin.html');
    }

    // 未使用的附件
    public function unused_index() {

        $field = [
            'author' => [
                'ismain' => 1,
                'fieldtype' => 'Text',
                'fieldname' => 'author',
                'name' => dr_lang('账号'),
            ],
            'fileext' => [
                'ismain' => 1,
                'fieldtype' => 'Text',
                'fieldname' => 'fileext',
                'name' => dr_lang('扩展名'),
            ],
            'uid' => [
                'ismain' => 1,
                'fieldtype' => 'Text',
                'fieldname' => 'uid',
                'name' => 'uid',
            ],
        ];

        $remote = (int)$_GET['remote'];
        $where_list = '';
        $remote && $where_list = '`remote`='.$remote;

        $this->_init([
            'table' => 'attachment_unused',
            'field' => $field,
            'order_by' => 'id desc',
            'where_list' => $where_list,
            'date_field' => 'inputtime',
        ]);

        $this->_List();

        \Phpcmf\Service::V()->assign([
            'field' => $field,
            'table' => 'unused',
            'remote' => \Phpcmf\Service::M()->table('attachment_remote')->getAll(),
        ]);
        \Phpcmf\Service::V()->display('attachment_admin.html');
    }

    public function del() {

        $ids = \Phpcmf\Service::L('Input')->get_post_ids();
        !$ids && $this->_json(0, dr_lang('你还没有选择呢'));

        $table = \Phpcmf\Service::L('Input')->post('table');
        $table != 'data' && $table = 'unused';

        $data = \Phpcmf\Service::M()->db->table('attachment_'.$table)->whereIn('id', $ids)->get()->getResultArray();
        !$data && $this->_json(0, dr_lang('所选附件不存在'));

        foreach ($data as $t) {

            $rt = \Phpcmf\Service::M()->table('attachment')->delete($t['id']);
            if (!$rt['code']) {
                return dr_return_data(0, $rt['msg']);
            }

            // 删除记录
            \Phpcmf\Service::M()->table('attachment_'.$table)->delete($t['id']);

            // 开始删除文件
            $storage = new \Phpcmf\Library\Storage($this);
            $storage->delete(\Phpcmf\Service::M('Attachment')->get_attach_info($t['remote']), $t['attachment']);
        }

        $this->_json(1, dr_lang('操作成功'));
    }

    // 强制归档
    public function edit() {

        $ids = \Phpcmf\Service::L('Input')->get_post_ids();
        !$ids && $this->_json(0, dr_lang('你还没有选择呢'));

        $data = \Phpcmf\Service::M()->db->table('attachment_unused')->whereIn('id', $ids)->get()->getResultArray();
        !$data && $this->_json(0, dr_lang('所选附件不存在'));

        $related = 'Save';
        foreach ($data as $t) {
            // 更新主索引表
            \Phpcmf\Service::M()->table('attachment')->update($t['id'], array(
                'related' => $related
            ));
            \Phpcmf\Service::M()->table('attachment_data')->insert(array(
                'id' => $t['id'],
                'uid' => $t['uid'],
                'remote' => $t['remote'],
                'author' => $t['author'],
                'related' => $related,
                'fileext' => $t['fileext'],
                'filesize' => $t['filesize'],
                'filename' => $t['filename'],
                'inputtime' => $t['inputtime'],
                'attachment' => $t['attachment'],
                'attachinfo' => '',
            ));
            // 删除未使用附件
            \Phpcmf\Service::M()->table('attachment_unused')->delete($t['id']);
        }

        $this->_json(1, dr_lang('操作成功'));
    }

}
