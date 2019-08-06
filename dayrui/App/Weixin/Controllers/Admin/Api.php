<?php namespace Phpcmf\Controllers\Admin;

class Api extends \Phpcmf\Common
{


	/**
	 * 生成安全码
	 */
	public function token() {
		echo 'PHP7CMS'.strtoupper(substr((md5(SYS_TIME)), rand(0, 10), 13));exit;
	}

	/**
	 * 生成来路随机字符
	 */
	public function asckey() {
		$s = strtoupper(base64_encode(md5(SYS_TIME).md5(rand(0, 2015).md5(rand(0, 2015)))).md5(rand(0, 2009)));
		echo substr('PHP7CMS'.str_replace('=', '', $s), 0, 43);exit;
	}


	// 选择素材
	public function content() {

        // 强制将模板设置为后台
        \Phpcmf\Service::V()->admin();

        // 登陆判断
        !$this->uid && $this->_json(0, dr_lang('会话超时，请重新登录'));


        $builder = \Phpcmf\Service::M()->db->table(weixin_wxtable('content'));

        // 搜索结果显示条数
        $limit = (int)$_GET['limit'];
        $limit = $limit ? $limit : 20;

        $data = $_GET;

        if ($data['search'] && isset($data['keyword']) && $data['keyword']) {
            $builder->like('title', urldecode($data['keyword']));
        }

        $list = $builder->limit($limit)->orderBy('inputtime DESC')->get()->getResultArray();

        \Phpcmf\Service::V()->assign(array(
            'list' => $list,
            'param' => $data,
            'search' => dr_form_search_hidden(['search' => 1, 'limit' => $limit]),
            'content_type' => [
                'index' => '图文',
                'image' => '图片',
                'voice' => '语音',
                'video' => '视频',
            ],
        ));
        \Phpcmf\Service::V()->display('api_content.html');exit;
    }
}
