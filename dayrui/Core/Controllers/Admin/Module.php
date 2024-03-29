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



class Module extends \Phpcmf\Common
{

    private $dir;
    private $form;
    private $jname = ['case', 'class', 'extends', 'new', 'var'];

    public function __construct(...$params) {
        parent::__construct(...$params);

        $this->dir = dr_safe_replace(\Phpcmf\Service::L('Input')->get('dir'));

        $menu = [
            '内容模块' => [\Phpcmf\Service::L('Router')->class.'/index', 'fa fa-cogs'],
            '模块配置' => ['hide:'.\Phpcmf\Service::L('Router')->class.'/edit', 'fa fa-cog'],
            '推荐位配置' => ['hide:'.\Phpcmf\Service::L('Router')->class.'/flag_edit', 'fa fa-flag'],
            '重建表单' => ['ajax:module/form_init_index', 'fa fa-refresh'],
            'help' => [57]
        ];

        if (strpos(\Phpcmf\Service::L('Router')->method, 'form') !== false) {
            unset($menu['help']);
            $menu['模块'.$this->dir.'表单'] = [\Phpcmf\Service::L('Router')->class.'/form_index{dir='.$this->dir.'}', 'fa fa-list'];
            $menu['添加表单'] = ['add:module/form_add{dir='.$this->dir.'}', 'fa fa-plus', '500px', '310px'];
            $menu['表单配置'] = ['hide:'.\Phpcmf\Service::L('Router')->class.'/form_edit', 'fa fa-cog'];
            $menu['help'] = [98];
        }

        \Phpcmf\Service::V()->assign('menu', \Phpcmf\Service::M('auth')->_admin_menu($menu));

        // 表单验证配置
        $this->form = [
            'name' => [
                'name' => '表单名称',
                'rule' => [
                    'empty' => dr_lang('表单名称不能为空')
                ],
                'filter' => [],
                'length' => '200'
            ],
            'table' => [
                'name' => '表单别名',
                'rule' => [
                    'empty' => dr_lang('表单别名不能为空'),
                    'table' => dr_lang('表单别名格式不正确'),
                ],
                'filter' => [],
                'length' => '200'
            ],
        ];
    }


    // 安装模块
    public function install() {

        $dir = dr_safe_replace(\Phpcmf\Service::L('Input')->get('dir'));
        $type = (int)\Phpcmf\Service::L('Input')->get('type');

        !preg_match('/^[a-z]+$/U', $dir) && $this->_json(0, dr_lang('模块目录[%s]格式不正确', $dir));
        in_array($dir, $this->jname) && $this->_json(0, dr_lang('模块目录[%s]名称是系统保留名称，请重命名', $dir));

        $path = dr_get_app_dir($dir);
        !is_dir($path) && $this->_json(0, dr_lang('模块目录[%s]不存在', $path));

        // 对当前模块属性判断
        $cfg = require $path.'Config/App.php';
        !$cfg && $this->_json(0, dr_lang('文件[%s]不存在', 'App/'.ucfirst($dir).'/Config/App.php'));

        $cfg['share'] = $type ? 0 : 1;

        $rt = \Phpcmf\Service::M('Module')->install($dir, $cfg);
        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
        $this->_json($rt['code'], $rt['msg']);
    }

    // 卸载模块
    public function uninstall() {

        $dir = dr_safe_replace(\Phpcmf\Service::L('Input')->get('dir'));
        !preg_match('/^[a-z]+$/U', $dir) && $this->_json(0, dr_lang('模块目录[%s]格式不正确', $dir));

        $path = dr_get_app_dir($dir);
        !is_dir($path) && $this->_json(0, dr_lang('模块目录[%s]不存在', $path));

        $cfg = require $path.'Config/App.php';
        !$cfg && $this->_json(0, dr_lang('文件[%s]不存在', 'App/'.ucfirst($dir).'/Config/App.php'));

        $rt = \Phpcmf\Service::M('Module')->uninstall($dir, $cfg);
        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
        $this->_json($rt['code'], $rt['msg']);

    }

    // 模块管理
    public function index() {

        $list = [];
        $local = dr_dir_map(dr_get_app_list(), 1); // 搜索本地模块
        foreach ($local as $dir) {
            if (is_file(dr_get_app_dir($dir).'Config/App.php')) {
                $key = strtolower($dir);
                $cfg = require dr_get_app_dir($dir).'Config/App.php';
                if ($cfg['type'] == 'module' || $cfg['ftype'] == 'module') {
                    if (isset($cfg['hlist']) && $cfg['hlist']) {
                        // 不在列表显示
                        continue;
                    }
                    $cfg['dirname'] = $key;
                    $list[$key] = $cfg;
                }
            }
        }

        $my = [];
        $module = \Phpcmf\Service::M('Module')->All(); // 库中已安装模块
        if ($module) {
            foreach ($module as $t) {
                $dir = $t['dirname'];
                if ($list[$dir]) {
                    $t['key'] = $t['key'];
                    $t['name'] = $list[$dir]['name'];
                    $t['mtype'] = $list[$dir]['mtype'];
                    $t['system'] = $list[$dir]['system'];
                    $t['version'] = $list[$dir]['version'];
                    $site = dr_string2array($t['site']);
                    $t['install'] = isset($site[SITE_ID]) && $site[SITE_ID] ? 1 : 0;
                    $t['site'] = dr_count($site);
                    $my[$dir] = $t;
                    unset($list[$dir]);
                }
            }
        }

        \Phpcmf\Service::V()->assign([
            'my' => $my,
            'list' => $list,
        ]);
        \Phpcmf\Service::V()->display('module_list.html');
    }

    // 排序
    public function displayorder_edit() {

        // 查询数据
        $id = (int)\Phpcmf\Service::L('Input')->get('id');
        $row = \Phpcmf\Service::M('Module')->table('module')->get($id);
        !$row && $this->_json(0, dr_lang('数据#%s不存在', $id));

        $value = (int)\Phpcmf\Service::L('Input')->get('value');
        $rt = \Phpcmf\Service::M('Module')->table('module')->save($id, 'displayorder', $value);
        !$rt['code'] && $this->_json(0, $rt['msg']);

        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
        \Phpcmf\Service::L('Input')->system_log('修改模块('.$row['dirname'].')的排序值为'.$value);
        $this->_json(1, dr_lang('操作成功'));
    }

    // 隐藏或者启用
    public function hidden_edit() {

        $id = (int)\Phpcmf\Service::L('Input')->get('id');
        $row = \Phpcmf\Service::M('Module')->table('module')->get($id);
        !$row && $this->_json(0, dr_lang('数据#%s不存在', $id));

        $v = $row['disabled'] ? 0 : 1;
        \Phpcmf\Service::M('Module')->table('module')->update($id, ['disabled' => $v]);

        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
        exit($this->_json(1, dr_lang($v ? '模块已被禁用' : '模块已被启用'), ['value' => $v]));
    }

    // 模块配置
    public function edit() {

        $id = (int)\Phpcmf\Service::L('Input')->get('id');
        $data = \Phpcmf\Service::M()->table('module')->get($id);
        !$data && $this->_admin_msg(0, dr_lang('数据#%s不存在', $id));

        // 格式转换
        $data['site'] = dr_string2array($data['site']);
        $data['setting'] = dr_string2array($data['setting']);

        // 判断站点
        !$data['site'][SITE_ID] && $this->_admin_msg(0, dr_lang('当前站点尚未安装'));

        // 默认显示字段
        !$data['setting']['list_field'] && $data['setting']['list_field'] = [
            'title' => [
                'use' => 1,
                'name' => dr_lang('主题'),
                'func' => 'title',
                'width' => 0,
                'order' => 1,
            ],
            'catid' => [
                'use' => 1,
                'name' => dr_lang('栏目'),
                'func' => 'catid',
                'width' => 120,
                'order' => 2,
            ],
            'author' => [
                'use' => 1,
                'name' => dr_lang('作者'),
                'func' => 'author',
                'width' => 100,
                'order' => 3,
            ],
            'updatetime' => [
                'use' => 1,
                'name' => dr_lang('更新时间'),
                'func' => 'datetime',
                'width' => 160,
                'order' => 4,
            ],
        ];
        // 默认显示字段
        !$data['setting']['comment_list_field'] && $data['setting']['comment_list_field'] = [
            'content' => [
                'use' => 1,
                'name' => dr_lang('评论'),
                'func' => 'comment',
                'width' => 0,
                'order' => 1,
            ],
            'author' => [
                'use' => 1,
                'name' => dr_lang('作者'),
                'func' => 'author',
                'width' => 100,
                'order' => 3,
            ],
            'inputtime' => [
                'use' => 1,
                'name' => dr_lang('评论时间'),
                'func' => 'datetime',
                'width' => 160,
                'order' => 4,
            ],
        ];


        if (IS_AJAX_POST) {
            $post = \Phpcmf\Service::L('Input')->post('data', true);
            $rt = \Phpcmf\Service::M('Module')->config($data, $post);
            if ($rt['code']) {
                \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
                $this->_json(1, '操作成功');
            } else {
                $this->_json(0, $rt['msg']);
            }
        }

        // 主表字段
        $field = \Phpcmf\Service::M()->db->table('field')
            ->where('disabled', 0)
            ->where('ismain', 1)
            ->where('relatedname', 'module')
            ->where('relatedid', $id)
            ->orderBy('displayorder ASC,id ASC')
            ->get()->getResultArray();
        $sys_field = \Phpcmf\Service::L('Field')->sys_field(['catid', 'author', 'inputtime', 'updatetime']);
        sort($sys_field);
        $field = dr_array2array($sys_field, $field);

        // 评论字段
        $comment_field = \Phpcmf\Service::M()->db->table('field')
            ->where('disabled', 0)
            ->where('ismain', 1)
            ->where('relatedname', 'comment-module-'.$data['dirname'])
            ->orderBy('displayorder ASC,id ASC')
            ->get()->getResultArray();
        $sys_field = \Phpcmf\Service::L('Field')->sys_field(['content', 'author', 'inputtime']);
        $comment_field = dr_array2array($sys_field, $comment_field);


        $page = intval(\Phpcmf\Service::L('Input')->get('page'));
        $config = require dr_get_app_dir($data['dirname']).'Config/App.php';

        if (!$data['site'][SITE_ID]['title']) {
            $data['site'][SITE_ID]['title'] = $config['name'];
        }

        \Phpcmf\Service::V()->assign([
            'page' => $page,
            'data' => $data,
            'site' => $data['site'][SITE_ID],
            'form' => dr_form_hidden(['page' => $page]),
            'field' => $field,
            'is_hcategory' => isset($config['hcategory']) && $config['hcategory'],
            'comment_field' => $comment_field,
        ]);
        \Phpcmf\Service::V()->display('module_edit.html');
    }

    // 推荐位
    public function flag_edit() {

        $id = (int)\Phpcmf\Service::L('Input')->get('id');
        $data = \Phpcmf\Service::M('Module')->table('module')->get($id);
        !$data && $this->_admin_msg(0, dr_lang('数据#%s不存在', $id));

        // 格式转换
        $data['setting'] = dr_string2array($data['setting']);

        if (IS_AJAX_POST) {
            $post = \Phpcmf\Service::L('Input')->post('flag', true);
            $rt = \Phpcmf\Service::M('Module')->config($data, null, [
                'flag' => $post,
            ]);
            if ($rt['code']) {
                \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
                $this->_json(1, '操作成功');
            } else {
                $this->_json(0, $rt['msg']);
            }
        }

        \Phpcmf\Service::V()->assign([
            'flag' => $data['setting']['flag'],
            'form' => dr_form_hidden(),
        ]);
        \Phpcmf\Service::V()->display('module_flag.html');
    }

    // 模块表单
    public function form_index() {

        \Phpcmf\Service::V()->assign([
            'list' => \Phpcmf\Service::M()->table('module_form')->where('module', $this->dir)->getAll(),
        ]);
        \Phpcmf\Service::V()->display('module_form.html');
    }

    // 创建模块表单
    public function form_add() {

        if (IS_AJAX_POST) {
            $data = \Phpcmf\Service::L('Input')->post('data', true);
            $this->_validation(0, $data);
            \Phpcmf\Service::L('Input')->system_log('创建模块['.$this->dir.']表单('.$data['name'].')');
            $rt = \Phpcmf\Service::M('Module')->create_form($this->dir, $data);
            if ($rt['code']) {
                \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
                $this->_json(1, dr_lang('操作成功，请刷新后台页面'));
            } else {
                $this->_json(0, $rt['msg']);
            }
        }

        \Phpcmf\Service::V()->assign([
            'form' => dr_form_hidden()
        ]);
        \Phpcmf\Service::V()->display('module_form_add.html');
        exit;
    }

    // 修改模块表单
    public function form_edit() {

        $id = intval(\Phpcmf\Service::L('Input')->get('id'));
        $data = \Phpcmf\Service::M()->table('module_form')->get($id);
        !$data && $this->_admin_msg(0, dr_lang('模块表单（%s）不存在', $id));

        $data['setting'] = dr_string2array($data['setting']);
        !$data['setting']['list_field'] && $data['setting']['list_field'] = [
            'title' => [
                'use' => 1,
                'name' => dr_lang('主题'),
                'func' => 'title',
                'width' => 0,
                'order' => 1,
            ],
            'author' => [
                'use' => 1,
                'name' => dr_lang('作者'),
                'func' => 'author',
                'width' => 100,
                'order' => 2,
            ],
            'inputtime' => [
                'use' => 1,
                'name' => dr_lang('录入时间'),
                'func' => 'datetime',
                'width' => 160,
                'order' => 3,
            ],
        ];

        if (IS_AJAX_POST) {
            $data = \Phpcmf\Service::L('Input')->post('data', true);
            \Phpcmf\Service::M('Module')->table('module_form')->update($id,
                [
                    'name' => $data['name'],
                    'setting' => dr_array2string($data['setting'])
                ]
            );

            \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
            \Phpcmf\Service::L('Input')->system_log('修改模块['.$this->dir.']表单('.$data['name'].')配置');
            exit($this->_json(1, dr_lang('操作成功')));
        }

        // 主表字段
        $field = \Phpcmf\Service::M()->db->table('field')
            ->where('disabled', 0)
            ->where('ismain', 1)
            ->where('relatedname', 'mform-'.$this->dir)
            ->where('relatedid', $id)
            ->orderBy('displayorder ASC,id ASC')
            ->get()->getResultArray();
        $sys_field = \Phpcmf\Service::L('Field')->sys_field(['author', 'inputtime']);
        sort($sys_field);

        $page = intval(\Phpcmf\Service::L('Input')->get('page'));

        \Phpcmf\Service::V()->assign([
            'data' => $data,
            'page' => $page,
            'form' => dr_form_hidden(['page' => $page]),
            'field' => dr_array2array($sys_field, $field),
        ]);
        \Phpcmf\Service::V()->display('module_form_edit.html');
    }

    // 删除表单
    public function form_del() {

        $ids = \Phpcmf\Service::L('Input')->get_post_ids();
        !$ids && exit($this->_json(0, dr_lang('你还没有选择呢')));

        $rt = \Phpcmf\Service::M('Module')->delete_form($ids);
        !$rt['code'] && exit($this->_json(0, $rt['msg']));

        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
        \Phpcmf\Service::L('Input')->system_log('批量删除模块表单: '. @implode(',', $ids));

        exit($this->_json(1, dr_lang('操作成功'), ['ids' => $ids]));
    }

    // 验证数据
    private function _validation($id, $data) {

        list($data, $return) = \Phpcmf\Service::L('Form')->validation($data, $this->form);
        $return && exit($this->_json(0, $return['error'], ['field' => $return['name']]));
        \Phpcmf\Service::M()->table('module_form')->where('module', $this->dir)->is_exists($id, 'table', $data['table']) && exit($this->_json(0, dr_lang('数据表名称已经存在'), ['field' => 'table']));
    }


    // 表单初始化
    public function form_init_index() {

        $data = \Phpcmf\Service::M()->table('module_form')->getAll();
        !$data && $this->_json(0, dr_lang('没有任何可用表单'));

        $ct = $file = 0;
        foreach ($data as $t) {
            $par = \Phpcmf\Service::M()->dbprefix(dr_module_table_prefix($t['module'], SITE_ID)); // 父级表
            if (!\Phpcmf\Service::M()->is_table_exists($par)) {
                continue; // 当前站点没有安装
            }
            $rt = \Phpcmf\Service::M('Module')->create_form_file($t['module'], $t['table'], 1);
            !$rt['code'] && $this->_json(0, $rt['msg']);
            $file+= (int)$rt['msg'];
            $ct++;
            // 创建统计字段
            $fname = $t['table']."_total";
            if (!\Phpcmf\Service::M()->db->fieldExists($fname, $par)) {
                \Phpcmf\Service::M()->db->simpleQuery("ALTER TABLE `{$par}` ADD `{$fname}` INT(10) UNSIGNED NULL DEFAULT '0' COMMENT '表单".$t['name']."统计' , ADD INDEX (`".$fname."`) ;");
            }
        }

        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
        $this->_json(1, dr_lang('本站点共（%s）个表单，重建（%s）个文件', $ct, $file));
    }


}
