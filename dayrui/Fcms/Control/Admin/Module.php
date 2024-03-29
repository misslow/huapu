<?php namespace Phpcmf\Admin;

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


// 内容模块操作类 基于 Ftable
class Module extends \Phpcmf\Table
{
    protected $post_time; // 定时发布时间
    protected $module_menu; // 是否显示模块菜单

    public function __construct(...$params) {
        parent::__construct(...$params);
        // 初始化模块
        $this->_module_init(APP_DIR);
        // 支持附表存储
        $this->is_data = 1;
        // 是否支持模块索引表
        $this->is_module_index = 1;
        // 是否支持
        $this->is_category_data_field = (int)$this->module['category_data_field'];
        // 模板前缀(避免混淆)
        $this->tpl_prefix = 'share_';
        // 单独模板命名
        $this->tpl_name = APP_DIR;
        // 模块显示名称
        $this->name = dr_lang('内容模块[%s]（%s）', APP_DIR, $this->module['cname']);
        $this->where_list_sql = dr_is_app('cqx') ? \Phpcmf\Service::M('content', 'cqx')->get_list_where() : '';
        // 初始化数据表
        $this->_init([
            'table' => SITE_ID.'_'.APP_DIR,
            'field' => $this->module['field'],
            'sys_field' => ['inputtime', 'updatetime', 'inputip', 'displayorder', 'hits', 'author'],
            'date_field' => 'updatetime',
            'show_field' => 'title',
            'where_list' => $this->where_list_sql,
            'order_by' => $this->module['setting']['order'],
            'list_field' => $this->module['setting']['list_field'],
        ]);
        $this->content_model->init($this->init); // 初始化内容模型
        // 写入模板
        \Phpcmf\Service::V()->assign([
            'field' => $this->init['field'],
            'weibo' => $this->get_cache('site', SITE_ID, 'weibo', 'module', MOD_DIR),
            'module' => $this->module,
            'post_url' =>\Phpcmf\Service::L('Router')->url(APP_DIR.'/home/add', ['catid' => intval($_GET['catid'])]),
            'is_hcategory' => $this->is_hcategory,
            'is_category_show' => $this->is_hcategory ? 0 : (dr_count($this->module['category']) == 1 ? 0 : 1),
        ]);
    }

    // ========================

    // 后台查看列表
    protected function _Admin_List() {

        list($tpl, $data) = $this->_List([]);
        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_module_menu(
                $this->module,
                ' <i class="'.dr_icon($this->module['icon']).'"></i>  '.dr_lang('%s管理', $this->module['cname']),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/home/index'),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/home/add', ['catid' => intval($_GET['catid'])])
            ),
            'clink' => $this->_app_clink(),
            'cbottom' => $this->_app_cbottom(),
            'move_select' => \Phpcmf\Service::L('Tree')->select_category(
                $this->module['category'],
                $data['param']['catid'],
                'name="catid"',
                '--',
                1, 1
            ),
            'category_select' => \Phpcmf\Service::L('Tree')->select_category(
                $this->module['category'],
                $data['param']['catid'],
                'name="catid"',
                '--'
            ),
        ]);
        \Phpcmf\Service::V()->display($tpl);
    }

    // 后台添加内容
    protected function _Admin_Add() {

        $id = 0;
        $did = intval(\Phpcmf\Service::L('Input')->get('did'));
        $catid = intval(\Phpcmf\Service::L('Input')->get('catid'));

        $did && $this->auto_save = 0; // 草稿数据时不加载
        $draft = $did ? $this->content_model->get_draft($did) : [];

        $catid = $draft['catid'] ? $draft['catid'] : $catid;

        // 栏目id不存在时就去第一个可用栏目为catid
        if (!$catid) {
            list($select, $catid) = \Phpcmf\Service::L('Tree')->select_category(
                $this->module['category'],
                $catid,
                'id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"',
                '', 1, 1, 1
            );
        } else {
            $select = \Phpcmf\Service::L('Tree')->select_category(
                $this->module['category'],
                $catid,
                'id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"',
                '', 1, 1
            );
        }

        $this->is_get_catid = $catid;
        $draft && $draft['catid'] = $catid;

        list($tpl) = $this->_Post($id, $draft);

        \Phpcmf\Service::V()->assign([
            'did' => $did,
            'form' =>  dr_form_hidden(['is_draft' => 0, 'module' => MOD_DIR, 'id' => $id]),
            'select' => $select,
            'draft_url' =>\Phpcmf\Service::L('Router')->url(APP_DIR.'/home/add'),
            'draft_list' => $this->content_model->get_draft_list('cid='.$id),
            'menu' => \Phpcmf\Service::M('auth')->_module_menu(
                $this->module,
                ' <i class="'.dr_icon($this->module['icon']).'"></i>  '.dr_lang('%s管理', $this->module['cname']),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/home/index'),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/home/add', ['catid' => $catid])
            ),
            'category_field_url' => $this->module['category_data_field'] ?\Phpcmf\Service::L('Router')->url(APP_DIR.'/home/add') : ''
        ]);
        \Phpcmf\Service::V()->display($tpl);
    }

    // 后台修改内容
    protected function _Admin_Edit() {

        $id = intval(\Phpcmf\Service::L('Input')->get('id'));
        $did = intval(\Phpcmf\Service::L('Input')->get('did'));
        $did && $this->auto_save = 0; // 草稿数据时不加载
        $draft = $did ? $this->content_model->get_draft($did) : [];

        list($tpl, $data) = $this->_Post($id, $draft);
        if (!$data) {
            $this->_admin_msg(0, dr_lang('数据#%s不存在', $id));
        } elseif ($this->where_list_sql && \Phpcmf\Service::M('content', 'cqx')->is_edit($data['catid'], $data['uid'])) {
            $this->_admin_msg(0, dr_lang('当前角色无权限管理此栏目'));
        }

        $select = \Phpcmf\Service::L('Tree')->select_category(
            $this->module['category'],
            $data['catid'],
            'id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"',
            '', 1, 1
        );

        \Phpcmf\Service::V()->assign([
            'did' => $did,
            'form' =>  dr_form_hidden(['is_draft' => 0, 'module' => MOD_DIR, 'id' => $id]),
            'select' => $select,
            'draft_url' =>\Phpcmf\Service::L('Router')->url(APP_DIR.'/home/edit', ['id' => $id]),
            'draft_list' => $this->content_model->get_draft_list('cid='.$id),
            'menu' => \Phpcmf\Service::M('auth')->_module_menu(
                $this->module,
                ' <i class="'.dr_icon($this->module['icon']).'"></i>  '.dr_lang('%s管理', $this->module['cname']),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/home/index'),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/home/add', ['catid' => $data['catid']])
            ),
            'category_field_url' => $this->module['category_data_field'] ?\Phpcmf\Service::L('Router')->url(APP_DIR.'/home/edit', ['id' => $id]) : ''
        ]);
        \Phpcmf\Service::V()->display($tpl);
    }

    // 后台删除内容
    protected function _Admin_Del() {

        if (IS_POST) {

            $ids = \Phpcmf\Service::L('Input')->get_post_ids();
            !$ids && $this->_json(0, dr_lang('参数不存在'));

            $rt = $this->content_model->delete_to_recycle($ids, \Phpcmf\Service::L('Input')->post('note'));

            // 写入日志
            $rt['code'] && \Phpcmf\Service::L('Input')->system_log(dr_lang('内容模块[%s]', APP_DIR).'：放入回收站('.implode(', ', $ids).')');

            $rt['code'] ? $this->_json(1, dr_lang('所选内容已被放入回收站中')) : $this->_json(0, $rt['msg']);
        } else {
            // 选择选项
            $ids = $_GET['ids'];
            \Phpcmf\Service::V()->assign([
                'ids' => $ids,
                'delete_msg' => $this->module['setting']['delete_msg'] ? @explode(PHP_EOL, $this->module['setting']['delete_msg']) : [],
            ]);
            \Phpcmf\Service::V()->display('share_delete.html');
            exit;
        }
    }

    // 后台批量保存排序值
    protected function _Admin_Order() {
        $this->_Display_Order(
            intval(\Phpcmf\Service::L('Input')->get('id')),
            intval(\Phpcmf\Service::L('Input')->get('value'))
        );
    }

    // 批量移动栏目
    protected function _Admin_Move() {

        $ids = \Phpcmf\Service::L('Input')->get_post_ids();
        $catid = (int)\Phpcmf\Service::L('Input')->post('catid');
        if (!$ids) {
            $this->_json(0, dr_lang('选择内容不存在'));
        } elseif (!$catid) {
            $this->_json(0, dr_lang('目标栏目未选择'));
        } elseif (!$this->content_model->admin_category_auth($catid, 'edit')) {
            $this->_json(0, dr_lang('无权限操作此栏目'));
        } elseif ($this->where_list_sql && \Phpcmf\Service::M('content', 'cqx')->is_edit($catid)) {
            $this->_json(0, dr_lang('当前角色无权限管理此栏目'));
        }

        $rt = $this->content_model->move_category($ids, $catid);

        // 写入日志
        $rt['code'] && \Phpcmf\Service::L('Input')->system_log(dr_lang('内容模块[%s]', APP_DIR).'：批量修改栏目('.implode(', ', $ids).')');

        $rt['code'] ? $this->_json(1, dr_lang('操作成功')) : $this->_json(0, $rt['msg']);
    }

    // 同步栏目选择器
    protected function _Admin_Syncat() {

        if (IS_AJAX_POST) {

            $catid = \Phpcmf\Service::L('Input')->post('catid');
            !$catid && $this->_json(0, dr_lang('你没有选择同步的栏目'));

            $syncat = [];
            foreach ($catid as $i) {
                if ($this->where_list_sql && \Phpcmf\Service::M('content', 'cqx')->is_edit($i)) {
                    $this->_json(0, dr_lang('当前角色无权限管理此栏目'));
                }
                if (!$this->module['category'][$i]) {
                    continue;
                } elseif ($this->module['category'][$i]['tid'] != 1) {
                    continue;
                } elseif ($this->module['category'][$i]['child'] != 0) {
                    continue;
                } else {
                    $syncat[] = $i;
                }
            }

            !$syncat && $this->_json(0, dr_lang('所选栏目无效'));

            $this->_json(1, dr_count($syncat), implode(',', $syncat));
        }

        \Phpcmf\Service::V()->assign([
            'form' =>  dr_form_hidden(),
            'select' => \Phpcmf\Service::L('Tree')->select_category(
                $this->module['category'],
                0,
                'id=\'dr_catid\' name=\'catid[]\' multiple="multiple" style="height:200px"',
                '', 1, 1
            ),
        ]);
        \Phpcmf\Service::V()->display('share_syncat.html');exit;
    }

    // 批量推送
    protected function _Admin_Send() {

        $ids = \Phpcmf\Service::L('Input')->get('ids');
        !$ids && $this->_json(0, dr_lang('所选数据不存在'));

        $page = \Phpcmf\Service::L('Input')->get('page');

        if (IS_AJAX_POST) {

            $in = [];
            foreach ($ids as $i) {
                $i && $in[] = intval($i);
            }

            !$in && $this->_json(0, dr_lang('所选数据不存在'));

            switch ($page) {

                case 1: // 推送到其他栏目

                    $catids = \Phpcmf\Service::L('Input')->post('catid');
                    !$catids && $this->_json(0, dr_lang('你还没有选择同步的栏目'));

                    $data = \Phpcmf\Service::M()->db->table($this->init['table'])->whereIn('id', $in)->where('link_id<=0')->get()->getResultArray();
                    !$data && $this->_json(0, dr_lang('没有可用数据'));

                    $c = 0;
                    foreach ($data as $t) {
                        $u = 0;
                        foreach ($catids as $catid) {
                            if ($catid && $catid != $t['catid']) {
                                if ($this->where_list_sql && \Phpcmf\Service::M('content', 'cqx')->is_edit($catid)) {
                                    $this->_json(0, dr_lang('当前角色无权限管理此栏目'));
                                }
                                // 插入到同步栏目中
                                $new[1] = $t;
                                $new[1]['catid'] = $catid;
                                $new[1]['link_id'] = $t['id'];
                                $new[1]['tableid'] = 0;
                                $new[1]['id'] = $this->content_model->index(0, $new);
                                if ($new[1]['id']) {
                                    $rt = $this->content_model->table($this->init['table'])->replace($new[1]);
                                    $c ++;
                                    $u = 1;
                                }
                            }
                        }
                        $u && $this->content_model->table($this->init['table'])->update($t['id'], ['link_id' => -1]);
                    }

                    $this->_json(1, dr_lang('批量执行%s条', $c));

                    break;

                case 0: // 推荐位

                    $flag = \Phpcmf\Service::L('Input')->post('flag');
                    $clear = \Phpcmf\Service::L('Input')->post('clear');
                    !$clear && !$flag && $this->_json(0, dr_lang('你还没有选择推荐位'));

                    \Phpcmf\Service::M()->db->table($this->init['table'].'_flag')->whereIn('id', $in)->delete();
                    $clear && $this->_json(1, dr_lang('推荐位清除成功'));

                    $data = \Phpcmf\Service::M()->db->table($this->init['table'].'_index')->whereIn('id', $in)->get()->getResultArray();
                    !$data && $this->_json(0, dr_lang('所选数据不存在'));

                    $c = 0;
                    foreach ($data as $t) {
                        foreach ($flag as $fid) {
                            $this->content_model->insert_flag((int)$fid, (int)$t['id'], (int)$t['uid'], (int)$t['catid']);
                            $c ++;
                        }
                    }

                    $this->_json(1, dr_lang('批量执行%s条', $c));
                    break;

            }

            exit;
        } else if ($page == 3) {
            !$this->get_cache('site', SITE_ID, 'weibo', 'module', MOD_DIR, 'use') && $this->_json(0, dr_lang('当前模块没有启用微博分享'));
            foreach ($ids as $id) {
                $data = $this->content_model->get_data($id);
                !$data && $this->_json(0, dr_lang('内容#%s不存在', $id));
                $rt = $this->content_model->sync_weibo($data);
                !$rt['code'] && $this->_json(0, $rt['msg']);
            }
            $this->_json(1, dr_lang('任务添加成功'));
        } else if ($page == 2) {
            dr_count($ids) > 9 && $this->_json(0, dr_lang('微信推送不能超过9条数据'));
            !dr_is_app('weixin') && $this->_json(0, '没有安装[微信]插件');
            \Phpcmf\Service::C()->init_file('weixin');
            $rt = \Phpcmf\Service::M('Weixin', 'Weixin')->send_for_module(APP_DIR, $ids);
            !$rt['code'] && $this->_json(0, $rt['msg']);
            dr_redirect(\Phpcmf\Service::L('Router')->url('weixin/send/add', ['id' => $rt['code']]));
            exit;
        } else if ($page == 4) {
            $this->content_model->update_time($ids);
            $this->_json(1, dr_lang('操作成功'));
            exit;
        }

        \Phpcmf\Service::V()->assign([
            'page' => $page,
            'form' => dr_form_hidden(),
            'select' => \Phpcmf\Service::L('Tree')->select_category(
                $this->module['category'],
                0,
                'id=\'dr_catid\' name=\'catid[]\' multiple="multiple" style="height:200px"',
                '', 1, 1
            ),
        ]);
        \Phpcmf\Service::V()->display('share_send.html');exit;
    }

    // ===========================

    // 后台查看草稿列表
    protected function _Admin_Draft_List() {

        $this->_init([
            'table' => SITE_ID.'_'.APP_DIR.'_draft',
            'date_field' => 'inputtime',
            'order_by' => 'inputtime desc',
            'where_list' => 'uid='.$this->uid,
        ]);

        $this->_List();

        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_module_menu(
                $this->module,
                ' <i class="'.dr_icon('fa fa-pencil').'"></i>  '.dr_lang('草稿箱管理'),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/draft/index'),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/home/add')
            ),
        ]);
        \Phpcmf\Service::V()->display('share_list_draft.html');
    }

    // 后台删除草稿内容
    protected function _Admin_Draft_Del() {

        // 支持附表存储
        $this->is_data = 0;
        $this->name = dr_lang('内容模块[%s]（%s）', APP_DIR, dr_lang('草稿'));
        $this->_init([
            'table' => SITE_ID.'_'.APP_DIR.'_draft',
        ]);

        $this->_Del(\Phpcmf\Service::L('Input')->get_post_ids());
    }

    // ===========================

    // 后台查看审核列表
    protected function _Admin_Verify_List() {

        $this->_init([
            'db' => SITE_ID,
            'table' => SITE_ID.'_'.APP_DIR.'_verify',
            'date_field' => 'inputtime',
            'order_by' => 'inputtime desc',
            'where_list' => 'status > 0'.($this->where_list_sql ? ' AND '.$this->where_list_sql : ''),
        ]);

        $this->_List();

        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_admin_menu(
                [
                    '审核管理' => [MOD_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/index', 'fa fa-edit'],
                ]
            ),
        ]);
        \Phpcmf\Service::V()->display('share_list_verify.html');
    }

    // 后台修改审核内容
    protected function _Admin_Verify_Edit() {

        // 说明来自审核页面
        define('IS_MODULE_VERIFY', 1);

        $id = intval(\Phpcmf\Service::L('Input')->get('id'));
        list($tpl, $data) = $this->_Post($id);
        if (!$data) {
            $this->_admin_msg(0, dr_lang('内容不存在'));
        } elseif ($this->where_list_sql && \Phpcmf\Service::M('content', 'cqx')->is_edit($data['catid'], $data['uid'])) {
            $this->_admin_msg(0, dr_lang('当前角色无权限管理此栏目'));
        }

        $select = \Phpcmf\Service::L('Tree')->select_category(
            $this->module['category'],
            $data['catid'],
            'id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"',
            '', 1, 1
        );

        $step = $this->_get_verify($data['uid'], $data['catid']);
        $step[9] = [
            'name' => dr_lang('完成'),
        ];

        $verify_msg = [
          dr_lang('词文不对'),
        ];
        if ($this->module['setting']['verify_msg']) {
            $msg = @explode(PHP_EOL, $this->module['setting']['verify_msg']);
            $msg && $verify_msg = $msg;
        }

        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_admin_menu(
                [
                    '审核管理' => [MOD_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/index', 'fa fa-edit'],
                    '审核处理' => ['hide:'.MOD_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/edit', 'fa fa-edit'],
                ]
            ),
            'form' =>  dr_form_hidden(['is_draft' => 0, 'module' => MOD_DIR, 'id' => $id]),
            'select' => $select,
            'is_verify' => 1,
            'verify_msg' => $verify_msg,
            'verify_step' => $step,
            'verify_next' => dr_count($step) - 1 <= $data['status'] ? 9 : $data['status'] + 1,
        ]);
        \Phpcmf\Service::V()->display('share_post.html');
    }

    // 后台删除审核内容
    protected function _Admin_Verify_Del() {

        // 支持附表存储
        $this->is_data = 0;
        $this->name = dr_lang('内容模块[%s]（%s）', APP_DIR, dr_lang('审核'));
        $this->_init([
            'table' => SITE_ID.'_'.APP_DIR.'_verify',
        ]);
        $this->_Del(\Phpcmf\Service::L('Input')->get_post_ids(), function ($rows) {
            foreach ($rows as $t) {
                if ($this->where_list_sql && \Phpcmf\Service::M('content', 'cqx')->is_edit($t['catid'], $t['uid'])) {
                    return dr_return_data(0, dr_lang('当前角色无权限管理此栏目'));
                }
            }
            return dr_return_data(1, 'ok');
        }, function($rows) {
            foreach ($rows as $t) {
                // 删除索引
                $t['isnew'] && \Phpcmf\Service::M()->table(SITE_ID.'_'.APP_DIR.'_index')->delete($t['id']);
                // 删除审核提醒
                \Phpcmf\Service::M('member')->delete_admin_notice(APP_DIR.'/verify/edit:id/'.$t['id'], SITE_ID);
            }
            return dr_return_data(1, 'ok');
        });
    }


    // ===========================

    // 后台定时发布列表
    protected function _Admin_Time_List() {

        $this->_init([
            'table' => SITE_ID.'_'.APP_DIR.'_time',
            'date_field' => 'inputtime',
            'order_by' => 'inputtime desc',
            'where_list' => $this->admin['adminid'] == 1 ? '' : 'uid='.$this->uid,
        ]);

        $this->_List();

        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_module_menu(
                $this->module,
                ' <i class="'.dr_icon('fa fa-clock-o').'"></i>  '.dr_lang('待发布管理'),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/time/index'),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/home/add')
            ),
        ]);
        \Phpcmf\Service::V()->display('share_list_time.html');
    }

    // 后台定时发布
    protected function _Admin_Time_Add() {

        $at = \Phpcmf\Service::L('Input')->get('at');
        if ($at == 'post') {
            // 批量发布
            $ids = \Phpcmf\Service::L('Input')->get_post_ids();
            !$ids && $this->_json(0, dr_lang('还没有选择呢'));
            $html = [];
            foreach ($ids as $id) {
                $rt = $this->content_model->post_time(\Phpcmf\Service::M()->table(SITE_ID.'_'.MOD_DIR.'_time')->get($id));
                $rt['data'] && $html[] = $rt['data'];
                !$rt['code'] && $this->_json(0, $rt['msg'], ['htmlfile' => $html]);
            }
            $this->_json(1, dr_lang('操作成功'), ['htmlfile' => $html]);
            exit;
        }

        // 说明来自定时页面
        define('IS_MODULE_TIME', 1);

        $this->_Post();
        \Phpcmf\Service::V()->display('share_time.html');exit;
    }

    // 后台修改定时内容
    protected function _Admin_Time_Edit() {

        // 说明来自定时页面
        define('IS_MODULE_TIME', 1);
        $id = intval(\Phpcmf\Service::L('Input')->get('id'));
        list($tpl, $data) = $this->_Post($id);
        !$data && $this->_admin_msg(0, dr_lang('内容不存在'));

        $select = \Phpcmf\Service::L('Tree')->select_category(
            $this->module['category'],
            $data['catid'],
            'id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"',
            '', 1, 1
        );

        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_admin_menu(
                [
                    '定时发布' => [MOD_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/index', 'fa fa-clock-o"'],
                    '修改' => ['hide:'.MOD_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/edit', 'fa fa-edit'],
                ]
            ),
            'form' =>  dr_form_hidden(['is_draft' => 0, 'module' => MOD_DIR, 'id' => $id]),
            'select' => $select,
            'is_post_time' => 1,
        ]);
        \Phpcmf\Service::V()->display('share_post.html');
    }

    // 后台删除定时内容
    protected function _Admin_Time_Del() {

        // 支持附表存储
        $this->is_data = 0;
        $this->name = dr_lang('内容模块[%s]（%s）', APP_DIR, dr_lang('定时'));
        $this->_init([
            'table' => SITE_ID.'_'.APP_DIR.'_time',
        ]);

        $this->_Del(\Phpcmf\Service::L('Input')->get_post_ids());
    }

    // ===========================

    // 后台查看草稿列表
    protected function _Admin_Recycle_List() {

        $this->_init([
            'table' => SITE_ID.'_'.APP_DIR.'_recycle',
            'date_field' => 'inputtime',
            'order_by' => 'inputtime desc',
            'where_list' => $this->admin['adminid'] == 1 ? '' : 'uid='.$this->uid,
        ]);

        $this->_List();

        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_module_menu(
                $this->module,
                ' <i class="'.dr_icon('fa fa-trash-o').'"></i>  '.dr_lang('回收站管理'),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/recycle/index'),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/home/add')
            ),
        ]);
        \Phpcmf\Service::V()->display('share_list_recycle.html');
    }

    // 后台删除内容
    protected function _Admin_Recycle_Del() {

        $ids = \Phpcmf\Service::L('Input')->get_post_ids();
        !$ids && $this->_json(0, dr_lang('参数不存在'));

        $rt = $this->content_model->delete_for_recycle($ids);

        // 删除附件
        SYS_ATTACHMENT_DB && \Phpcmf\Service::M('Attachment')->id_delete(
            $this->member['id'],
            $ids,
            \Phpcmf\Service::M()->dbprefix($this->init['table'])
        );

        // 写入日志
        \Phpcmf\Service::L('Input')->system_log(dr_lang('内容模块[%s]', APP_DIR).'：删除('.implode(', ', $ids).')');

        $rt['code'] ? $this->_json(1, dr_lang('操作成功')) : $this->_json(0, $rt['msg']);
    }

    // 后台恢复查看
    protected function _Admin_Recycle_Show() {

        // 说明来自页面
        define('IS_MODULE_RECYCLE', 1);

        $id = intval(\Phpcmf\Service::L('Input')->get('id'));
        list($tpl, $data) = $this->_Show($id);
        !$data && $this->_admin_msg(0, dr_lang('内容不存在'));

        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_module_menu(
                $this->module,
                ' <i class="'.dr_icon('fa fa-trash-o').'"></i>  '.dr_lang('回收站管理'),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/recycle/index'),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/home/add')
            ),
            'catid' => $data['catid'],
            'select' => '<label style="padding-top: 9px">'.$this->module['category'][$data['catid']]['name'].'</label>',
        ]);
        \Phpcmf\Service::V()->display('share_post.html');

    }

    // 后台恢复内容
    protected function _Admin_Recovery() {

        $ids = \Phpcmf\Service::L('Input')->get_post_ids();
        !$ids && $this->_json(0, dr_lang('参数不存在'));

        $rt = $this->content_model->recovery($ids);

        // 写入日志
        \Phpcmf\Service::L('Input')->system_log(dr_lang('内容模块[%s]', APP_DIR).'：恢复('.implode(', ', $ids).')');

        $rt['code'] ? $this->_json(1, dr_lang('操作成功')) : $this->_json(0, $rt['msg']);
    }


    // ===========================

    // 推荐位管理
    protected function _Admin_Flag_List() {

        $flag = intval(\Phpcmf\Service::L('Input')->get('flag'));
        !$this->module['setting']['flag'][$flag] && $this->_admin_msg(0, dr_lang('推荐位不存在: '.$flag));

        $this->_init([
            'table' => SITE_ID.'_'.APP_DIR,
            'date_field' => 'inputtime',
            'order_by' => 'inputtime desc',
            'show_field' => 'title',
            'list_field' => $this->module['setting']['list_field'],
            'where_list' => 'id IN (select id from `'.\Phpcmf\Service::M()->dbprefix(SITE_ID.'_'.APP_DIR.'_flag').'` where flag='.$flag.')'.($this->where_list_sql ? ' AND '.$this->where_list_sql : ''),
        ]);

        list($tpl, $data) = $this->_List();

        \Phpcmf\Service::V()->assign([
            'p' => ['flag' => $flag],
            'category_select' => \Phpcmf\Service::L('Tree')->select_category(
                $this->module['category'],
                $data['param']['catid'],
                'name="catid"', '--'
            ),
            'move_select' => \Phpcmf\Service::L('Tree')->select_category(
                $this->module['category'],
                $data['param']['catid'],
                'name="catid"', '--', 1, 1
            ),
            'menu' => \Phpcmf\Service::M('auth')->_module_menu(
                $this->module,
                ' <i class="'.dr_icon($this->module['setting']['flag'][$flag]['icon']).'"></i>  '.dr_lang($this->module['setting']['flag'][$flag]['name']),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/flag/index', ['flag' => $flag]),
                \Phpcmf\Service::L('Router')->url(APP_DIR.'/home/add')
            ),
        ]);
        \Phpcmf\Service::V()->display('share_list.html');

    }



    // ===========================

    /**
     * 获取内容
     * $id      内容id,新增为0
     * */
    protected function _Data($id = 0) {

        if (!$id) {
            return [];
        }

        $catid = intval(\Phpcmf\Service::L('Input')->get('catid'));

        if (defined('IS_MODULE_VERIFY')) {
            // 判断是否来至审核
            $row = \Phpcmf\Service::M()->table(SITE_ID.'_'.MOD_DIR.'_verify')->get($id);
            $data = dr_string2array($row['content']);
            $data['verify'] = [
                'uid' => $row['backuid'],
                'isnew' => $row['isnew'],
                'backinfo' => $row['backinfo'],
            ];
            $this->is_get_catid = $catid ? $catid : $data['catid'];
            return $data;
        } elseif (defined('IS_MODULE_TIME')) {
            // 判断是否来至定时发布
            $row = \Phpcmf\Service::M()->table(SITE_ID.'_'.MOD_DIR.'_time')->get($id);
            $data = dr_string2array($row['content']);
            $data['myflag'] = $data['flag'];
            $data['posttime'] = $row['posttime'];
            $this->is_get_catid = $catid ? $catid : $data['catid'];
            return $data;
        } elseif (defined('IS_MODULE_RECYCLE')) {
            // 判断是否来至回收站
            $row = \Phpcmf\Service::M()->table(SITE_ID.'_'.MOD_DIR.'_recycle')->get($id);
            $c = dr_string2array($row['content']);
            $data = [];
            if ($c) {
                foreach ($c as $t) {
                    $t && $data = dr_array22array($data, $t);
                }
            }
            $this->is_get_catid = $data['catid'];
            return $data;
        }

        $row = $this->content_model->get_data($id);
        if (!$row) {
            return [];
        }

        $this->is_get_catid = $catid ? $catid : $row['catid'];

        // 判断是同步栏目数据
        if ($row['link_id'] > 0) {
            $row = $this->content_model->get_data($row['link_id']);
            if (!$row) {
                return [];
            }
            $this->replace_id = $id = $row['id'];
        }

        // 推荐位
        $row['myflag'] = $id ? $this->content_model->get_flag($id) : [];

        // 更新时间
        $row['updatetime'] = SYS_TIME;

        return $row;
    }

    // 格式化保存数据
    protected function _Format_Data($id, $data, $old) {

        // 验证栏目
        $catid = (int)\Phpcmf\Service::L('Input')->post('catid');
        if (!$this->module['category'][$catid] && !$this->is_hcategory) {
            $this->_json(0, dr_lang('栏目[%s]不存在', $catid));
        }

        // 验证后台权限
        $data[1]['catid'] = $data[0]['catid'] = $catid;

        // 验证状态
        $data[1]['status'] = 9;
        $data[1]['uid'] = (int)$data[1]['uid'];

        // 默认数据
        $data = $this->content_model->format_data($data);

        // 不更新时间
        if (!$old['updatetime']) {
            $data[1]['updatetime'] = SYS_TIME;
        } elseif ($id && isset($_POST['no_time'])
            && $_POST['no_time']) {
            unset($data[1]['updatetime']);
        }

        // 不验证账号
        if ($id && $_POST['no_author']) {
            $data[1]['uid'] = (int)$old['uid'];
        }

        return $data;
    }

    /**
     * 保存内容
     * $id      内容id,新增为0
     * $data    提交内容数组,留空为自动获取
     * $func    格式化提交的数据
     * */
    protected function _Save($id = 0, $data = [], $old = [], $func = null, $func2 = null) {

        $did = intval(\Phpcmf\Service::L('Input')->get('did'));
        $is_draft = intval(\Phpcmf\Service::L('Input')->post('is_draft'));

        // 判断定时发布时间
        if (defined('IS_MODULE_TIME')) {
            $this->post_time = (int)strtotime(\Phpcmf\Service::L('Input')->get('posttime'));
            if (SYS_TIME > $this->post_time) {
                return dr_return_data(0, dr_lang('定时发布时间不正确'), $data);
            }
            // 保存定时发布数据
            $this->init['table'] = SITE_ID.'_'.APP_DIR.'_time';
            return $this->content_model->save_post_time($id, $data, $this->post_time);
        } elseif ($is_draft) {
            // 草稿箱存储
            $data[1]['id'] = $id;
            $this->init['table'] = SITE_ID.'_'.APP_DIR.'_draft';
            return $this->content_model->insert_draft($did, $data);
        } else {
            // 删除草稿
            $did && $this->content_model->delete_draft($did);
            // 正常存储
            return parent::_Save($id, $data, $old,
                function ($id, $data, $old) {
                    // 发布之前判断是否来自审核
                    if ($old && defined('IS_MODULE_VERIFY')) {
                        if ($_POST['verify']['status']) {
                            // 通过
                            $step = $this->_get_verify($data[1]['uid'], $data[1]['catid']);
                            $status = intval($old['status']);
                            $data[1]['status'] = dr_count($step) <= $status ? 9 : $status + 1;
                            // 任务执行成功
                            \Phpcmf\Service::M('member')->todo_admin_notice( MOD_DIR.'/verify/edit:id/'.$id, SITE_ID);
                        } else {
                            // 拒绝
                            $data[1]['status'] = 0;
                            // 通知
                            $old['note'] = $_POST['verify']['msg'];
                            \Phpcmf\Service::L('Notice')->send_notice('module_content_verify_0', $old);
                        }
                    }
                    // 禁止修改栏目
                    if ($old['catid'] && $this->module['category'][$old['catid']]['setting']['notedit']) {
                        $data[1]['catid'] = $old['catid'];
                    }

                    return dr_return_data(1, 'ok', $data);
                },
                function ($id, $data, $old) {
                    // 审核跳过
                    if (defined('IS_MODULE_VERIFY')) {
                        return $data;
                    }
                    // 处理推荐位
                    $myflag = $old['myflag'];
                    $update = \Phpcmf\Service::L('Input')->post('flag');
                    if ($update !== $myflag) {
                        // 删除旧的
                        $id && $myflag && $this->content_model->delete_flag($id, $myflag);
                        // 增加新的
                        if ($update) {
                            foreach ($update as $i) {
                                $this->content_model->insert_flag((int)$i, $id, $data[1]['uid'], $data[1]['catid']);
                            }
                        }
                    }
                    $data[1]['id'] = $id;
                    // 同步发送到其他栏目
                    !$old && \Phpcmf\Service::L('Input')->post('sync_cat') && $this->content_model->sync_cat(\Phpcmf\Service::L('Input')->post('sync_cat'), $data);
                    // 同步微博
                    !$old && \Phpcmf\Service::L('Input')->post('sync_weibo') && $this->content_model->sync_weibo($data);
                    return $data;
                }
            );
        }
    }

    /**
     * 回调处理结果
     * $data
     * */
    protected function _Call_Post($data) {

        if ($data[1]['status'] == 9) {
            $html = '';
            if ($this->module['category'][$data[1]['catid']]['setting']['html']) {
                // 生成权限文件
                !dr_html_auth(1) && $this->_json(0, dr_lang('/cache/html/ 无法写入文件'));
                $html = '/index.php?s='.MOD_DIR.'&c=html&m=showfile&id='.$data[1]['id'];
                $list = '/index.php?s='.MOD_DIR.'&c=html&m=categoryfile&id='.$data[1]['catid'];
            }
            $this->_json(1, dr_lang('操作成功'), ['htmlfile' => $html, 'listfile' => $list]);
        } else {
            $this->_json(1, dr_lang('操作成功'));
        }
    }

    // 获取当前栏目的时候流程
    private function _get_verify($uid, $catid) {

        $authid = \Phpcmf\Service::M('member')->authid($uid);
        $auth = $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['category'][$catid]['verify'];
        $cache = $this->get_cache('verify');
        if ($cache && $auth) {
            $verify = [];
            foreach ($authid as $aid) {
                if (isset($auth[$aid]) && $auth[$aid] && isset($cache[$auth[$aid]])) {
                    $verify = $cache[$auth[$aid]];
                    break; // 找到最近的审核机制就ok了
                }
            }
            $rt = [];
            if ($verify['value']['role']) {
                $role = $this->get_cache('auth');
                foreach ($verify['value']['role'] as $id => $rid) {
                    $rt[$id] = [
                        'rid' => $rid,
                        'name' => dr_lang($role[$rid]['name'] ? $role[$rid]['name'] : '管理员'),
                    ];
                }
            }

            return $rt;
        }

        return [];
    }

}
