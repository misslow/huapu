<?php namespace Phpcmf\Controllers\Member;

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



// 用户api
class Api extends \Phpcmf\Common
{

    /**
     * 头像更新
     */
    public function avatar() {
        \Phpcmf\Service::L('Thread')->cron(['action' => 'oauth_down_avatar', 'id' => intval(\Phpcmf\Service::L('Input')->get('id')) ]);
        exit;
    }

    /**
     * 审核
     */
    public function verify() {

        !$this->member && $this->_msg(0, dr_lang('账号未登录'));

        // 获取返回页面
        $url = $_SERVER['HTTP_REFERER'];
        (strpos($url, 'verify') !== false || !$url) && $url = MEMBER_URL;

        if ($this->member['is_verify']) {
            IS_API_HTTP && $this->_json(0, dr_lang('此用户已经通过审核了'));
            dr_redirect($url);
            exit;
        } elseif (!$this->member['is_verify'] && !$this->member_cache['register']['verify']) {
            // 如果系统已经关闭了审核机制就自动通过
            \Phpcmf\Service::M('member')->verify_member($this->member['uid']);
            IS_API_HTTP && $this->_json(0, dr_lang('系统已经关闭了审核机制'));
            dr_redirect($url);
            exit;
        }
        
        $this->member_cache['register']['verify'] == 'admin' && $this->_msg(0, dr_lang('请等待管理员的审核'));

        \Phpcmf\Service::V()->assign([
            'post_url' => dr_member_url('api/verify_code', ['back' => urlencode($url)]),
            'meta_title' => dr_lang('账号审核'),
            'verify_url' => dr_member_url('api/'.$this->member_cache['register']['verify'].'_verify_code'),
            'verify_name' => $this->member[$this->member_cache['register']['verify']],
            'verify_type' => $this->member_cache['register']['verify'],
        ]);
        \Phpcmf\Service::V()->display('verify.html');
    }

    /**
     * 审核时变更邮箱或者手机
     */
    public function change() {

        !$this->member && $this->_msg(0, dr_lang('账号未登录'));

        if ($this->member['is_verify']) {
            IS_API_HTTP && $this->_json(0, dr_lang('此用户已经通过审核了'));
            dr_redirect(MEMBER_URL);
            exit;
        } elseif (!$this->member['is_verify'] && !$this->member_cache['register']['verify']) {
            // 如果系统已经关闭了审核机制就自动通过
            \Phpcmf\Service::M('member')->verify_member($this->member['uid']);
            IS_API_HTTP && $this->_json(0, dr_lang('系统已经关闭了审核机制'));
            dr_redirect(MEMBER_URL);
            exit;
        }

        $this->member_cache['register']['verify'] == 'admin' && $this->_msg(0, dr_lang('请等待管理员的审核'));

        if (IS_POST) {
            $value = dr_safe_replace(\Phpcmf\Service::L('Input')->post('value'));
            if (!\Phpcmf\Service::L('Form')->check_captcha('code')) {
                $this->_json(0, dr_lang('验证码不正确'), ['field' => 'code']);
            } elseif (empty($value)) {
                $this->_json(0, dr_lang('新邮箱或者手机号码必须填写'));
            } elseif ($this->member_cache['register']['verify'] == 'email' && !\Phpcmf\Service::L('Form')->check_email($value)) {
                $this->_json(0, dr_lang('邮箱格式不正确'), ['field' => 'value']);
            } elseif ($this->member_cache['register']['verify'] == 'phone' && !\Phpcmf\Service::L('Form')->check_phone($value)) {
                $this->_json(0, dr_lang('手机号码格式不正确'), ['field' => 'value']);
            } elseif ($this->member_cache['register']['verify'] == 'email' && \Phpcmf\Service::M()->db->table('member')->where('email', $value)->countAllResults()) {
                $this->_json(0, dr_lang('邮箱%s已经注册',$value), ['field' => 'value']);
            } elseif ($this->member_cache['register']['verify'] == 'phone' && \Phpcmf\Service::M()->db->table('member')->where('phone', $value)->countAllResults()) {
                $this->_json(0, dr_lang('手机号码%s已经注册', $value), ['field' => 'value']);
            }

            if (defined('UCSSO_API')) {
                if ($this->member_cache['register']['verify'] == 'phone') {
                    $rt = ucsso_edit_phone($this->uid, $value);
                    if (!$rt['code']) {
                        return dr_return_data(0, dr_lang('通信失败：%s', $rt['msg']));
                    }
                } else {
                    $rt = ucsso_edit_email($this->uid, $value);
                    if (!$rt['code']) {
                        return dr_return_data(0, dr_lang('通信失败：%s', $rt['msg']));
                    }
                }
            }

            $this->member['randcode'] = rand(100000, 999999);
            \Phpcmf\Service::M()->db->table('member')->where('id', $this->uid)->update([
                'randcode' => $this->member['randcode'],
                $this->member_cache['register']['verify'] => $value
            ]);

            switch ($this->member_cache['register']['verify']) {

                case 'phone':
                    \Phpcmf\Service::M('member')->sendsms_code($value, $this->member['randcode']);
                    break;

                case 'email':
                    \Phpcmf\Service::M('member')->sendmail($value, dr_lang('注册邮件验证'), 'member_verify.html', $this->member);
                    break;
            }

            $this->_json(1, dr_lang('信息已经变更'));
        }

        \Phpcmf\Service::V()->assign([
            'post_url' => dr_member_url('api/verify_code'),
            'meta_title' => dr_lang('账号审核信息变更'),
            'verify_name' => $this->member[$this->member_cache['register']['verify']],
            'verify_type' => $this->member_cache['register']['verify'],
        ]);
        \Phpcmf\Service::V()->display('change.html');
    }

    /**
     * 邮件审核验证码
     */
    public function email_verify_code() {

        !$this->member && $this->_json(0, dr_lang('账号未登录'));
        $this->member['is_verify'] && $this->_msg(0, dr_lang('此用户已经通过审核了'));
        $this->member_cache['register']['verify'] != 'email' && $this->_msg(0, dr_lang('审核方式不正确'));

        // 验证操作间隔
        $name = 'member-verify-phone-'.$this->uid;
        $this->session()->get($name) && $this->_json(0, dr_lang('已经发送稍后再试'));

        $this->member['randcode'] = rand(100000, 999999);
        \Phpcmf\Service::M()->db->table('member')->where('id', $this->member['id'])->update(['randcode' => $this->member['randcode']]);
        $rt = \Phpcmf\Service::M('member')->sendmail($this->member['email'], dr_lang('注册邮件验证'), 'member_verify.html', $this->member);
        !$rt['code'] && $this->_json(0, dr_lang('邮件发送失败'));

        $this->session()->setTempdata($name, 1, 60);
        $this->_json(1, dr_lang('验证码发送成功'));
    }

    /**
     * 短信审核验证码
     */
    public function phone_verify_code() {

        !$this->member && $this->_json(0, dr_lang('账号未登录'));
        $this->member['is_verify'] && $this->_msg(0, dr_lang('此用户已经通过审核了'));
        $this->member_cache['register']['verify'] != 'phone' && $this->_msg(0, dr_lang('审核方式不正确'));

        // 验证操作间隔
        $name = 'member-verify-phone-'.$this->uid;
        $this->session()->get($name) && $this->_json(0, dr_lang('已经发送稍后再试'));

        $this->member['randcode'] = rand(100000, 999999);
        \Phpcmf\Service::M()->db->table('member')->where('id', $this->member['id'])->update(['randcode' => $this->member['randcode']]);
        $rt = \Phpcmf\Service::M('member')->sendsms_code($this->member['phone'], $this->member['randcode']);
        !$rt && $this->_json(0, dr_lang('发送失败'));

        $this->session()->setTempdata($name, 1, 60);
        $this->_json(1, dr_lang('验证码发送成功'));
    }


    /**
     * 验证审核验证码
     */
    public function verify_code() {

        // 获取返回页面
        $url = $_GET['back'] ? urldecode($_GET['back']) : $_SERVER['HTTP_REFERER'];
        strpos($url, 'login') !== false && $url = MEMBER_URL;

        !$this->member && $this->_json(0, dr_lang('账号未登录'));
        !$this->member['randcode'] && $this->_json(0, dr_lang('验证码已过期'));
        \Phpcmf\Service::L('Input')->get('code') != $this->member['randcode'] && $this->_json(0, dr_lang('验证码不正确'));
        \Phpcmf\Service::M()->db->table('member')->where('id', $this->member['id'])->update(['randcode' => 0]);

        \Phpcmf\Service::M('member')->verify_member($this->member['id']);
        $this->member_cache['register']['verify'] == 'phone' 
        && \Phpcmf\Service::M()->db->table('member_data')->where('id', $this->member['uid'])->update(['is_mobile' => 1]);

        $this->_json(1, dr_lang('验证成功'), ['url' => $url ? $url : MEMBER_URL]);
    }

    /**
     * 找回密码验证码
     */
    public function find_code() {

        $value = dr_safe_replace(\Phpcmf\Service::L('Input')->get('value'));
        !$value && $this->_json(0, dr_lang('账号凭证不能为空'));

        // 验证操作间隔
        $name = 'member-find-password';
        $this->session()->get($name) && $this->_json(0, dr_lang('已经发送稍后再试'));

        if (strpos($value, '@') !== false) {
            // 邮箱模式
            $data = \Phpcmf\Service::M()->db->table('member')->where('email', $value)->get()->getRowArray();
            !$data && $this->_json(0, dr_lang('账号凭证不存在'));
            $data['randcode'] = $rand = rand(100000, 999999);
            \Phpcmf\Service::M()->db->table('member')->where('id', $data['id'])->update(['randcode' => $rand]);
            $rt = \Phpcmf\Service::M('member')->sendmail($value, dr_lang('找回密码'), 'member_find.html', $data);
            !$rt['code'] && $this->_json(0, dr_lang('邮件发送失败'));
        } else if (is_numeric($value) && strlen($value) == 11) {
            // 手机
            $data = \Phpcmf\Service::M()->db->table('member')->where('phone', $value)->get()->getRowArray();
            !$data && $this->_json(0, dr_lang('账号凭证不存在'));
            $rand = rand(100000, 999999);
            \Phpcmf\Service::M()->db->table('member')->where('id', $data['id'])->update(['randcode' => $rand]);
            $rt = \Phpcmf\Service::M('member')->sendsms_code($value, $rand);
            !$rt['code'] && $this->_json(0, dr_lang('发送失败'));
        } else {
            $this->_json(0, dr_lang('账号凭证格式不正确'));
        }

        $this->session()->setTempdata($name, 1, 60);
        $this->_json(1, dr_lang('验证码发送成功'));
    }

    /**
     * 注册验证码
     */
    public function register_code() {

        $phone = dr_safe_replace(\Phpcmf\Service::L('Input')->get('id'));
        if (!\Phpcmf\Service::L('Form')->check_phone($phone)) {
            $this->_json(0, dr_lang('手机号码格式不正确'), ['field' => 'phone']);
        } elseif (\Phpcmf\Service::M()->db->table('member')->where('phone', $phone)->countAllResults()) {
            $this->_json(0, dr_lang('手机号码已经注册'), ['field' => 'phone']);
        }

        // 验证操作间隔
        $name = 'member-register-phone-'.$phone;
        $this->session()->get($name) && $this->_json(0, dr_lang('已经发送稍后再试'));

        $code = rand(100000, 999999);
        $rt = \Phpcmf\Service::M('member')->sendsms_code($phone, $code);
        !$rt['code'] && $this->_json(0, dr_lang('发送失败'));

        $this->session()->setTempdata($name, $code, 60);
        $this->_json(1, dr_lang('验证码发送成功'));
    }

    /**
     * 登录验证码
     */
    public function login_code() {

        $phone = dr_safe_replace(\Phpcmf\Service::L('Input')->get('id'));
        if (!\Phpcmf\Service::L('Form')->check_phone($phone)) {
            $this->_json(0, dr_lang('手机号码格式不正确'), ['field' => 'phone']);
        } elseif (!\Phpcmf\Service::M()->db->table('member')->where('phone', $phone)->countAllResults()) {
            $this->_json(0, dr_lang('手机号码未注册'), ['field' => 'phone']);
        }

        // 验证操作间隔
        $name = 'member-login-phone-'.$phone;
        $this->session()->get($name) && $this->_json(0, dr_lang('已经发送稍后再试'));

        $code = rand(100000, 999999);
        $rt = \Phpcmf\Service::M('member')->sendsms_code($phone, $code);
        !$rt['code'] && $this->_json(0, dr_lang('发送失败'));

        $this->session()->setTempdata($name, $code, 60);
        $this->_json(1, dr_lang('验证码发送成功'));
    }


    /**
     * 注册协议
     */
    public function protocol() {

        \Phpcmf\Service::V()->display('protocol.html');
    }

}
