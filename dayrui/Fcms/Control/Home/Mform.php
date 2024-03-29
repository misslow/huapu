<?php namespace Phpcmf\Home;

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




// 内容模块表单操作类 基于 Ftable
class Mform extends \Phpcmf\Table
{
    public $cid; // 内容id
    public $form; // 表单信息

    public function __construct(...$params) {
        parent::__construct(...$params);
        // 初始化模块
        $this->_module_init(APP_DIR);

        // 判断表单是否操作
        $this->form = $this->module['form'][\Phpcmf\Service::L('Router')->class];
        !$this->form && exit($this->_msg(0, dr_lang('模块表单【%s】不存在',\Phpcmf\Service::L('Router')->class)));

        // 支持附表存储
        $this->is_data = 1;
        // 模板前缀(避免混淆)
        $this->tpl_name = $this->form['table'];
        $this->tpl_prefix = 'mform_';

        // 初始化数据表
        $this->_init([
            'table' => dr_module_table_prefix(APP_DIR).'_form_'.$this->form['table'],
            'field' => $this->form['field'],
            'show_field' => 'title'
        ]);

        // 写入模板
        \Phpcmf\Service::V()->assign([
            'form_name' => $this->form['name'],
            'form_table' => $this->form['table'],
        ]);
        \Phpcmf\Service::V()->module(MOD_DIR);
    }

    // ========================

    // 内容列表
    protected function _Home_List() {


        if (SYS_CACHE && SYS_CACHE_PAGE && !defined('SC_HTML_FILE')) {
            // 启用页面缓存
            $this->cachePage(SYS_CACHE_PAGE * 3600);
        }

        // 无权限访问表单
        if (!dr_member_auth(
            $this->member_authid,
            $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['form'][$this->form['table']]['show'])
        ) {
            $this->_msg(0, dr_lang('您的用户组无权限访问表单'));
            return;
        }

        // 获取父级内容
        $this->cid = intval(\Phpcmf\Service::L('Input')->get('cid'));
        $this->index = $this->_Module_Row($this->cid);
        !$this->index && exit($this->_msg(0, dr_lang('模块内容【id#%s】不存在',  $this->cid)));

        // seo
        \Phpcmf\Service::V()->assign(\Phpcmf\Service::L('Seo')->mform_list(
            $this->form,
            $this->index,
            max(1, (int)\Phpcmf\Service::L('Input')->get('page'))
        ));

        \Phpcmf\Service::V()->assign([
            'index' => $this->index,
            'catid' => intval($this->index['catid']),
            'markid' => 'module-'.MOD_DIR.'-'.intval($this->index['catid']),
            'urlrule' =>\Phpcmf\Service::L('Router')->mform_list_url($this->form['table'], $this->index['id'], MOD_DIR, '[page]'),
        ]);
        \Phpcmf\Service::V()->display($this->_tpl_filename('list'));
    }

    // 添加内容
    protected function _Home_Post() {

        // 无权限访问表单
        if (!dr_member_auth(
            $this->member_authid,
            $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['form'][$this->form['table']]['add'])
        ) {
            $this->_msg(0, dr_lang('您的用户组无发布权限'));
            return;
        }

        // 判断会员权限
        $this->member && $this->_member_option(0);

        // 是否有验证码
        $this->is_post_code = dr_member_auth(
            $this->member_authid,
            $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['form'][$this->form['table']]['code']
        );

        // 获取父级内容
        $this->cid = intval(\Phpcmf\Service::L('Input')->get('cid'));
        $this->index = $this->_Module_Row($this->cid);
        !$this->index && exit($this->_msg(0, dr_lang('模块内容【id#%s】不存在',  $this->cid)));

        list($tpl) = $this->_Post(0);

        // seo
        \Phpcmf\Service::V()->assign(\Phpcmf\Service::L('Seo')->mform_post(
            $this->form,
            $this->index
        ));

        \Phpcmf\Service::V()->assign([
            'form' => dr_form_hidden(),
            'index' => $this->index,
            'catid' => intval($this->index['catid']),
            'markid' => 'module-'.MOD_DIR.'-'.intval($this->index['catid']),
            'rt_url' => $this->form['setting']['rt_url'] ? $this->form['setting']['rt_url'] : dr_now_url(),
            'is_post_code' => $this->is_post_code,
        ]);
        \Phpcmf\Service::V()->display($tpl);
    }

    // 显示内容
    protected function _Home_Show() {


        if (SYS_CACHE && SYS_CACHE_PAGE && !defined('SC_HTML_FILE')) {
            // 启用页面缓存
            $this->cachePage(SYS_CACHE_PAGE * 3600);
        }

        // 无权限访问表单
        if (!dr_member_auth(
            $this->member_authid,
            $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['form'][$this->form['table']]['show'])
        ) {
            $this->_msg(0, dr_lang('您的用户组无权限访问表单'));
            return;
        }

        $id = intval(\Phpcmf\Service::L('Input')->get('id'));
        $name = 'module_'.MOD_DIR.'_from_'.$this->form['table'].'_show_id_'.$id;
        $cache = \Phpcmf\Service::L('cache')->init()->get($name);
        if (!$cache) {
            list($tpl, $data) = $this->_Show($id);
            !$data && exit($this->_msg(0, dr_lang('表单内容【id#%s】不存在', $id)));
            // 获取父级内容
            $this->cid = intval($data['cid']);
            $this->index = $this->_Module_Row($this->cid);
            !$this->index && exit($this->_msg(0, dr_lang('模块内容【id#%s】不存在',  $this->cid)));
            // 模块的处理
            $data = $this->_Call_Show($data);
            $cache = [
                $tpl,
                $data,
                $this->cid,
                $this->index,
            ];
            // 缓存结果
            $data['uid'] != $this->uid && \Phpcmf\Service::L('cache')->init()->save($name, $cache, SYS_CACHE_SHOW * 3600);
        } else {
            list($tpl, $data, $this->cid, $this->index) = $cache;
        }

        \Phpcmf\Service::V()->assign($data);

        // seo
        \Phpcmf\Service::V()->assign(\Phpcmf\Service::L('Seo')->mform_show(
            $this->form,
            $this->index,
            $data
        ));

        \Phpcmf\Service::V()->assign([
            'index' => $this->index,
            'catid' => intval($this->index['catid']),
            'markid' => 'module-'.MOD_DIR.'-'.intval($this->index['catid']),
            'urlrule' =>\Phpcmf\Service::L('Router')->mform_show_url($this->form['table'], $this->index['id'], MOD_DIR, '[page]'),
        ]);
        \Phpcmf\Service::V()->display($tpl);
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

        $name = 'module_mform_'.$this->form['table'].'_id_'.$id;
        $data = \Phpcmf\Service::L('cache')->init()->get($name);
        if (!$data) {
            // 处理缓存机制
            $data = $this->content_model->get_form_row($id, $this->form['table']);
            if (!$data) {
                return [];
            }
            $data['uid'] != $this->uid && \Phpcmf\Service::L('cache')->init()->save($name, $data, SYS_CACHE_SHOW * 3600);
        }


        return $data;
    }

    // 格式化保存数据 保存之前
    protected function _Format_Data($id, $data, $old) {

        // 验证父数据
        !$this->index && $this->_json(0, dr_lang('关联内容不存在'));

        // 判断日发布量
        $day_post = $this->_member_value(
            $this->member_authid,
            $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['form'][$this->form['table']]['day_post']
        );
        if ($this->uid && $day_post
            && \Phpcmf\Service::M()->db
                ->table($this->init['table'])
                ->where('uid', $this->uid)
                ->where('DATEDIFF(from_unixtime(inputtime),now())=0')
                ->countAllResults() >= $day_post) {
            $this->_json(0, dr_lang('每天发布数量不能超过%s个', $day_post));
        }

        // 判断发布总量
        $total_post = $this->_member_value(
            $this->member_authid,
            $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['form'][$this->form['table']]['total_post']
        );
        if ($this->uid && $total_post
            && \Phpcmf\Service::M()->db
                ->table($this->init['table'])
                ->where('uid', $this->uid)
                ->countAllResults() >= $total_post) {
            $this->_json(0, dr_lang('发布数量不能超过%s个', $total_post));
        }

        // 审核状态
        $is_verify = dr_member_auth(
            $this->member_authid,
            $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['form'][$this->form['table']]['verify']
        );
        $data[1]['status'] = $is_verify ? 0 : 1;

        // 默认数据
        $data[0]['uid'] = $data[1]['uid'] = (int)$this->member['uid'];
        $data[1]['author'] = $this->member['username'] ? $this->member['username'] : 'guest';
        $data[1]['cid'] = $data[0]['cid'] =  $this->cid;
        $data[1]['catid'] = $data[0]['catid'] = (int)$this->index['catid'];
        $data[1]['inputip'] = \Phpcmf\Service::L('Input')->ip_address();
        $data[1]['inputtime'] = SYS_TIME;
        $data[1]['tableid'] = 0;
        $data[1]['displayorder'] = 0;

        return $data;
    }

    /**
     * 保存内容
     * $id      内容id,新增为0
     * $data    提交内容数组,留空为自动获取
     * $func    格式化提交的数据
     * */
    protected function _Save($id = 0, $data = [], $old = [], $func = null, $func2 = null) {

        return parent::_Save($id, $data, $old, null,
            function ($id, $data, $old) {
                // 保存之后
                //审核通知
                if ($data[1]['status']) {
                    // 增减金币
                    $score = $this->_member_value($this->member_authid, $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['form'][$this->form['table']]['score']);
                    $score && \Phpcmf\Service::M('member')->add_score($this->member['uid'], $score, dr_lang('%s: %s发布', MODULE_NAME, $this->form['name']), $this->index['curl']);
                    // 增减经验
                    $exp = $this->_member_value($this->member_authid, $this->member_cache['auth_module'][SITE_ID][MOD_DIR]['form'][$this->form['table']]['exp']);
                    $exp && \Phpcmf\Service::M('member')->add_experience($this->member['uid'], $exp, dr_lang('%s: %s发布', MODULE_NAME, $this->form['name']), $this->index['curl']);
                } else {
                    \Phpcmf\Service::M('member')->admin_notice(SITE_ID, 'content', $this->member, dr_lang('%s: %s提交内容审核', MODULE_NAME, $this->form['name']), MOD_DIR.'/'.$this->form['table'].'_verify/edit:cid/'. $this->cid.'/id/'.$id, SITE_ID);
                }

                //更新total字段
                $this->content_model->update_form_total( $this->cid, $this->form['table']);
            }
        );
    }

    // 操作主内容
    protected function _Module_Row($id) {

        $data = \Phpcmf\Service::L('cache')->init()->get('module_'.MOD_DIR.'_show_id_'.$id);
        if ($data) {
            return $data;
        }

        $data = $this->content_model->get_data($id);
        if (!$data) {
            return;
        }

        // 格式化输出自定义字段
        $fields = $this->module['category'][$data['catid']]['field'] ? array_merge($this->module['field'], $this->module['category'][$data['catid']]['field']) : $this->module['field'];
        $fields['inputtime'] = ['fieldtype' => 'Date'];
        $fields['updatetime'] = ['fieldtype' => 'Date'];

        $data['url'] = dr_url_prefix($data['url'], MOD_DIR);
        return \Phpcmf\Service::L('Field')->app(MOD_DIR)->format_value($fields, $data);
    }

    /**
     * 回调处理结果
     * $data
     * */
    protected function _Call_Post($data) {

        $data[1]['status'] && $this->_json(1, dr_lang('操作成功'));
        $this->_json(1, dr_lang('操作成功，等待管理员审核'));
    }

    // 前端回调处理类
    protected function _Call_Show($data) {

        return $data;
    }
}
