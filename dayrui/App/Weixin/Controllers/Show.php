<?php namespace Phpcmf\Controllers;

// 素材内容显示
class Show extends \Phpcmf\Common {

    public function index() {

        $id = (int)\Phpcmf\Service::L('Input')->get('id');
        $p = (int)\Phpcmf\Service::L('Input')->get('p');

        $data = \Phpcmf\Service::M()->table(weixin_wxtable('content'))->get($id);
        if (!$data) {
            $this->_msg(0, '微信素材内容不存在');
        } elseif ($data['tid'] != 'index') {
            $this->_msg(0, '素材内容不正确');
        }

        $content = dr_string2array($data['content']);
        if (!$content['title_'.$p] || !$content['content_'.$p]) {
            $this->_msg(0, '素材内容不完整');
        }

        $share = new \Phpcmf\Library\Wxshare($this);
        \Phpcmf\Service::V()->assign([
            'title' => $content['title_'.$p],
            'thumb' => $content['thumb_'.$p],
            'author' => $content['author_'.$p],
            'content' => htmlspecialchars_decode($content['content_'.$p]),
            'inputtime' => dr_date($data['inputtime']),
            '_inputtime' => $data['inputtime'],
            'description' => $content['description_'.$p],
            'signPackage' => $share->GetSignPackage(),
        ]);
        \Phpcmf\Service::V()->display('show.html');exit;
    }

}
