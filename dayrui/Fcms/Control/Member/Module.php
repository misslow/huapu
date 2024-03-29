<?php namespace Phpcmf\Member;

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
    
    public function __construct(...$params) {
        parent::__construct(...$params);
        // 初始化模块
        $this->_module_init(APP_DIR);
        // 支持附表存储
        $this->is_data = 1;
        // 是否支持模块表
        $this->is_module_index = 1;
        // 是否支持模型字段
        $this->is_category_data_field = (int)$this->module['category_data_field'];
        // 模板前缀(避免混淆)
        $this->tpl_prefix = 'module_';
        // 单独模板命名
        $this->tpl_name = 'content';
        // 模块显示名称
        $this->name = dr_lang('内容模块[%s]（%s）', $this->module['dirname'], $this->module['title']);
        // 初始化数据表
        $this->_init([
            'table' => SITE_ID.'_'.$this->module['dirname'],
            'field' => $this->module['field'],
            'date_field' => 'updatetime',
            'show_field' => 'title',
            'where_list' => 'uid='.$this->uid,
            'order_by' => 'updatetime desc',
        ]);
        // 写入模板
        \Phpcmf\Service::V()->assign([
            'module' => $this->module,
            'module_memu' => $this->content_model->module_menu(),
        ]);
    }

    // ========================

    // 查看列表
    protected function _Member_List() {

        list($tpl) = $this->_List();

        \Phpcmf\Service::V()->assign([
            'mcid' => 'list',
        ]);
        \Phpcmf\Service::V()->display($tpl);
    }

    // 添加内容
    protected function _Member_Add() {

        $id = 0;
        $did = intval(\Phpcmf\Service::L('Input')->get('did'));
        $catid = intval(\Phpcmf\Service::L('Input')->get('catid'));

        $did && $this->auto_save = 0; // 草稿数据时不加载
        $draft = $did ? $this->content_model->get_draft($did) : [];
        $catid = $draft['catid'] ? $draft['catid'] : $catid;

        if (!$this->is_hcategory) {
            // 走栏目权限
            $category = $this->_module_member_category($this->module['category'], $this->module['dirname'], 'add');
            // 栏目id不存在时就去第一个可用栏目为catid
            if (!$catid) {
                list($select, $catid) = \Phpcmf\Service::L('Tree')->select_category(
                    $category,
                    $catid,
                    'id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"',
                    '', 1, 1, 1
                );
            } else {
                $select = \Phpcmf\Service::L('Tree')->select_category(
                    $category,
                    $catid,
                    'id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"',
                    '', 1, 1
                );
            }
            !$category[$catid] && exit($this->_msg(0, dr_lang('当前栏目(%s)没有发布权限', (int)$catid)));
            $this->is_post_code = dr_member_auth($this->member_authid, $this->member_cache['auth_module'][SITE_ID][$this->module['dirname']]['category'][$catid]['code']);
        } else {
            // 不走栏目权限，走自定义权限
            $this->content_model->_hcategory_member_add_auth();
            $this->is_post_code = $this->content_model->_hcategory_member_post_code();
        }

        $this->is_get_catid = $catid;
        $draft && $draft['catid'] = $catid;

        list($tpl) = $this->_Post($id, $draft);

        \Phpcmf\Service::V()->assign([
            'did' => $did,
            'mcid' => 'add',
            'form' =>  dr_form_hidden(['is_draft' => 0, 'module' => $this->module['dirname'], 'id' => $id]),
            'select' => $select,
            'draft_url' =>\Phpcmf\Service::L('Router')->member_url($this->module['dirname'].'/home/add'),
            'draft_list' => $this->content_model->get_draft_list('cid='.$id),
            'is_post_code' =>  $this->is_post_code,
            'category_field_url' =>  $this->is_post_code || $this->module['category_data_field'] ? \Phpcmf\Service::L('Router')->member_url($this->module['dirname'].'/home/add') : ''
        ]);
        \Phpcmf\Service::V()->display($tpl);
    }

    // 修改内容
    protected function _Member_Edit() {

        $id = intval(\Phpcmf\Service::L('Input')->get('id'));
        $this->is_get_catid = intval(\Phpcmf\Service::L('Input')->get('catid'));
        if (defined('IS_MODULE_VERIFY')) {
            $draft = [];
        } else {
            $did = intval(\Phpcmf\Service::L('Input')->get('did'));
            $did && $this->auto_save = 0; // 草稿数据时不加载
            $draft = $did ? $this->content_model->get_draft($did) : [];
        }

        list($tpl, $data) = $this->_Post($id, $draft);
        if (!$data) {
            $this->_msg(0, dr_lang('内容不存在'));
            exit;
        } elseif ($this->uid != $data['uid']) {
            $this->_msg(0, dr_lang('无权限操作'));
            exit;
        } elseif (defined('IS_MODULE_VERIFY') && $data['status'] != 0) {
            // 判断是否来至审核
            $this->_msg(2, dr_lang('正在审核之中'));
            exit;
        }


        if (!$this->is_hcategory) {
            // 可编辑的栏目
            $category = $this->_module_member_category($this->module['category'], $this->module['dirname'], 'edit');
            !$category[$data['catid']] && exit($this->_msg(0, dr_lang('当前栏目[%s]没有修改权限', "#" . $data['catid'])));
        } else {
            $this->content_model->_hcategory_member_edit_auth();
        }

        \Phpcmf\Service::V()->assign([
            'did' => $did,
            'form' =>  dr_form_hidden(['is_draft' => 0, 'module' => $this->module['dirname'], 'id' => $id]),
            'select' => \Phpcmf\Service::L('Tree')->select_category(
                $category,
                $data['catid'],
                'id=\'dr_catid\' name=\'catid\' onChange="show_category_field(this.value)"',
                '', 1, 1
            ),
            'is_verify' => defined('IS_MODULE_VERIFY') ? 1 : 0,
            'draft_url' =>\Phpcmf\Service::L('Router')->member_url($this->module['dirname'].'/home/edit', ['id' => $id]),
            'draft_list' => $this->content_model->get_draft_list('cid='.$id),
            'is_post_code' => $this->is_hcategory ? $this->content_model->_hcategory_member_post_code() : $this->is_post_code,
            'category_field_url' => $this->is_post_code || $this->module['category_data_field'] ? \Phpcmf\Service::L('Router')->member_url($this->module['dirname'].'/home/edit', ['id' => $id]) : ''
        ]);
        \Phpcmf\Service::V()->display($tpl);
    }

    // 删除内容
    protected function _Member_Del() {

        $id = (int)\Phpcmf\Service::L('Input')->get('id');
        !$id && $this->_json(0, dr_lang('id不存在'));

        $row = \Phpcmf\Service::M()->table(SITE_ID.'_'.$this->module['dirname'].'_index')->get($id);
        !$row && $this->_json(0, dr_lang('内容不存在'));
        $row['uid'] != $this->uid && $this->_json(0, dr_lang('不能删除别人的内容'));

        if (!$this->is_hcategory) {
            $cat = $this->_module_member_category($this->module['category'], $this->module['dirname'], 'del');
            !isset($cat[$row['catid']]) && $this->_json(0, dr_lang('当前栏目没有删除权限'));
        } else {
            $this->content_model->_hcategory_member_del_auth();
        }

        $rt = $this->content_model->delete_to_recycle([$id]);
        $rt['code'] ? $this->_json(1, dr_lang('删除成功')) : $this->_json(0, $rt['msg']);
    }


    
    // ===========================

    // 查看审核列表
    protected function _Member_Verify_List() {

        $this->_init([
            'table' => SITE_ID.'_'.$this->module['dirname'].'_verify',
            'date_field' => 'inputtime',
            'order_by' => 'inputtime desc',
            'where_list' => 'uid='.$this->uid,
        ]);

        $this->_List();

        \Phpcmf\Service::V()->assign([
            'mcid' => 'verify'
        ]);
        \Phpcmf\Service::V()->display('module_verify.html');
    }

    // 修改审核内容
    protected function _Member_Verify_Edit() {

        // 说明来自审核页面
        define('IS_MODULE_VERIFY', 1);
        $this->_Member_Edit();
    }

    // 删除审核内容
    protected function _Member_Verify_Del() {

        // 支持附表存储
        $this->is_data = 0;
        $this->_init([
            'table' => SITE_ID.'_'.$this->module['dirname'].'_verify',
        ]);

        // 删除条件
        $this->delete_where = 'uid='.$this->uid;

        // 执行删除
        $this->_Del(\Phpcmf\Service::L('Input')->get_post_ids(), null, function($rows) {
            foreach ($rows as $t) {
                // 删除索引
                $t['isnew'] && \Phpcmf\Service::M()->table(SITE_ID.'_'.$this->module['dirname'].'_index')->delete($t['id']);
            }
            return dr_return_data(1, 'ok');
        });
    }
    
    // ===========================


    // 查看草稿列表
    protected function _Member_Draft_List() {

        $this->_init([
            'db' => SITE_ID,
            'table' => SITE_ID.'_'.$this->module['dirname'].'_draft',
            'date_field' => 'inputtime',
            'order_by' => 'inputtime desc',
            'where_list' => 'uid='.$this->uid,
        ]);

        $this->_List();

        \Phpcmf\Service::V()->assign([
            'mcid' => 'draft'
        ]);
        \Phpcmf\Service::V()->display('module_draft.html');
    }

    // 删除草稿内容
    protected function _Member_Draft_Del() {

        // 支持附表存储
        $this->is_data = 0;
        $this->_init([
            'table' => SITE_ID.'_'.$this->module['dirname'].'_draft',
        ]);

        // 删除条件
        $this->delete_where = 'uid='.$this->uid;

        // 执行删除
        $this->_Del(\Phpcmf\Service::L('Input')->get_post_ids());
    }


    // ===========================

    /**
     * 获取内容
     * $id      内容id,新增为0
     * */
    protected function _Data($id = 0) {

        // 判断是否来至审核
        if (defined('IS_MODULE_VERIFY')) {
            $row = \Phpcmf\Service::M()->table(SITE_ID.'_'.$this->module['dirname'].'_verify')->get($id);
            if (!$row) {
                return [];
            } elseif ($this->uid != $row['uid']) {
                return [];
            }
            $data = dr_string2array($row['content']);
            $data['backinfo'] = dr_string2array($row['backinfo']);
            !$this->is_get_catid && $this->is_get_catid = (int)$row['catid'];
            // 栏目验证码
            $this->is_post_code = $this->is_hcategory ? $this->content_model->_hcategory_member_post_code() : dr_member_auth($this->member_authid, $this->member_cache['auth_module'][SITE_ID][$this->module['dirname']]['category'][$this->is_get_catid]['code']);
            return $data;
        }

        $row = $this->content_model->get_data($id);
        if (!$row) {
            return [];
        } elseif ($this->uid != $row['uid']) {
            return [];
        }

        // 判断是同步栏目数据
        if ($row['link_id'] > 0) {
            $row = $this->content_model->get_data($row['link_id']);
            if (!$row) {
                return [];
            }
            $this->replace_id = $id = $row['id'];
        }

        !$this->is_get_catid && $this->is_get_catid = (int)$row['catid'];

        // 栏目验证码
        $this->is_post_code = $this->is_hcategory ? $this->content_model->_hcategory_member_post_code() : dr_member_auth($this->member_authid, $this->member_cache['auth_module'][SITE_ID][$this->module['dirname']]['category'][$this->is_get_catid]['code']);

        // 更新时间
        $row['updatetime'] = SYS_TIME;

        return $row;
    }

    // 格式化保存数据
    protected function _Format_Data($id, $data, $old) {

        // 验证栏目   验证码
        $catid = (int)\Phpcmf\Service::L('Input')->post('catid');
        if (!$this->module['category'][$catid] && !$this->is_hcategory) {
            $this->_json(0, dr_lang('栏目[%s]不存在', $catid), ['field' => 'catid']);
        }

        // 默认数据
        $data[1]['uid'] = $data[0]['uid'] = $this->uid;
        $data[1]['author'] = $this->member['username'];
        $data[1]['catid'] = $data[0]['catid'] = $catid;
        $data[1]['updatetime'] = SYS_TIME;

        // 第一次保存初始化信息
        if (!$id) {
            $data[1]['inputip'] = \Phpcmf\Service::L('Input')->ip_address();
            $data[1]['inputtime'] = SYS_TIME;
            $data[1]['displayorder'] = 0;
        }

        // 判断是否来至审核
        if (defined('IS_MODULE_VERIFY')) {
            $data[1]['status'] = 1;
        } else {
            // 判断权限

            // 验证状态
            $data[1]['status'] = $this->is_hcategory ? $this->content_model->_hcategory_member_post_status($this->member_authid) : $this->content_model->get_verify_status(
                $id,
                $this->member_authid,
                $this->member_cache['auth_module'][SITE_ID][$this->module['dirname']]['category'][$catid]['verify']
            );
        }

        // 默认数据
        $data = $this->content_model->format_data($data);

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
        if (!defined('IS_MODULE_VERIFY') && $is_draft) {
            // 草稿箱存储
            $data[1]['id'] = $id;
            return $this->content_model->insert_draft($did, $data);
        } else {
            // 删除草稿
            $did && $this->content_model->delete_draft($did);
            // 正常存储
            return parent::_Save($id, $data, $old,
                // 保存之前
                function ($id, $data, $old) {
                    // 判断是否来至审核
                    if (defined('IS_MODULE_VERIFY')) {
                        if ($old['status'] != 0) {
                            return dr_return_data(0, dr_lang('内容正在审核之中，无法再次修改'));
                        }
                    }
                    if ($this->is_hcategory) {
                        // 没有开通栏目时
                        $data = $this->content_model->_hcategory_member_save_before($data);
                        return dr_return_data(1, '', $data);
                    }
                    if ($old) {
                        // 表示修改
                        $cat = $this->_module_member_category($this->module['category'], $this->module['dirname'], 'edit');
                        if (!$cat[$old['catid']]) {
                            return dr_return_data(0, dr_lang('当前栏目[%s]没有修改权限', "#".$old['catid']));
                        }
                        // 禁止修改栏目
                        if ($this->module['category'][$old['catid']]['setting']['notedit']) {
                            $data[1]['catid'] = $old['catid'];
                        }
                    } else {
                        // 表示新增
                        $cat = $this->_module_member_category($this->module['category'], $this->module['dirname'], 'add');
                        if (!$cat[$data[1]['catid']]) {
                            return dr_return_data(0, dr_lang('当前栏目(%s)没有发布权限', (int)$data[1]['catid']));
                        }
                        // 判断日发布量
                        $day_post = $this->_module_member_value($data[1]['catid'], $this->module['dirname'], 'day_post', $this->member['authid']);
                        if ($this->uid && $day_post
                            && \Phpcmf\Service::M()->db
                                    ->table(SITE_ID.'_'.$this->module['dirname'].'_index')
                                    ->where('uid', $this->uid)
                                    ->where('DATEDIFF(from_unixtime(inputtime),now())=0')
                                    ->where('catid', $data[1]['catid'])
                                    ->countAllResults() >= $day_post) {
                            return dr_return_data(0, dr_lang('当前栏目每天发布数量不能超过%s个', $day_post));
                        }
                        // 判断发布总量
                        $total_post = $this->_module_member_value($data[1]['catid'], $this->module['dirname'], 'total_post', $this->member['authid']);
                        if ($this->uid && $total_post
                            && \Phpcmf\Service::M()->db
                                    ->table(SITE_ID.'_'.$this->module['dirname'].'_index')
                                    ->where('uid', $this->uid)
                                    ->where('catid', $data[1]['catid'])
                                    ->countAllResults() >= $day_post) {
                            return dr_return_data(0, dr_lang('当前栏目的发布数量不能超过%s个', $total_post));
                        }
                        // 金币验证
                        $score = $this->_module_member_value($data[1]['catid'], $this->module['dirname'], 'score', $this->member['authid']);
                        if ($this->uid && $score + $this->member['score'] < 0) {
                            return dr_return_data(0, dr_lang(SITE_SCORE.'不足，本次需要%s'.SITE_SCORE, abs($score)));
                        }
                    }
                    return dr_return_data(1, '', $data);
                },
                // 保存之后
                function ($id, $data, $old) {
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
                $html = '/index.php?s='.$this->module['dirname'].'&c=html&m=showfile&id='.$data[1]['id'];
                $list = '/index.php?s='.$this->module['dirname'].'&c=html&m=categoryfile&id='.$data[1]['catid'];
            }
            $this->_json(1, dr_lang('操作成功'), ['id' => $data[1]['id'], 'catid' => $data[1]['catid'], 'htmlfile' => $html, 'listfile' => $list]);
        } else {
            \Phpcmf\Service::L('Input')->post('is_draft') ? $this->_json(1, dr_lang('操作成功，已存储到草稿箱')) : $this->_json(1, dr_lang('操作成功，等待管理员审核'), ['id' => $data[1]['id'], 'catid' => $data[1]['catid']]);
        }
    }
}
