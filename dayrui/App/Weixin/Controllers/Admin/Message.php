<?php namespace Phpcmf\Controllers\Admin;

// 消息
class Message extends \Phpcmf\Table
{

    public function __construct(...$params)
    {
        parent::__construct(...$params);
        // 支持附表存储
        $this->is_data = 0;
        $this->tpl_prefix = 'message_';
        // 表单显示名称
        $this->name = dr_lang('微信消息');
        $this->my_field = array(
            'nickname' => array(
                'ismain' => 1,
                'name' => '昵称',
                'isemoji' => 1,
                'fieldname' => 'nickname',
            ),
            'content' => array(
                'ismain' => 1,
                'name' => '内容',
                'fieldname' => 'content',
            ),
        );
        // 初始化数据表
        $this->_init([
            'table' => weixin_wxtable('message'),
            'order_by' => 'inputtime desc',
            'date_field' => 'inputtime',
            'field' => $this->my_field,
        ]);
        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_admin_menu(
                [
                    '消息管理' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/index', 'fa fa-user'],
                    '更新消息状态' => ['ajax:'.APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/update_add', 'fa fa-refresh'],
                ]
            ),
            'field' => $this->my_field,
        ]);
    }

    public function update_add() {

        $rt = weixin_get_access_token();
        if (!$rt['code']) {
            $this->_json(0, $rt['msg']);
        }

        $access_token = $rt['msg'];

        $data = \Phpcmf\Service::M()->table(weixin_wxtable('message'))->where('status', 0)->getAll();
        if ($data) {
            foreach ($data as $t) {
                $user = \Phpcmf\Service::M()->table(weixin_wxtable('user'))->where('openid', $t['openid'])->getRow();
                if (!$user) {
                    $this->_json(0, '此用户已经取消关注，消息Id：'.$t['id']);
                }
                // 需要下载资源
                $file = '';
                $value = dr_string2array($t['content']);
                if ($value['MediaId'] && in_array($t['tid'], ['image', 'voice', 'video', 'shortvideo'])) {
                    $url = 'https://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$access_token.
                       '&media_id='.$value['MediaId'];
                    $rt = \Zend\Http\ClientStatic::get($url);
                    if ($rt->isSuccess()) {
                        $head = $rt->getHeaders()->toArray();
                        $ext = substr(strrchr($head['Content-disposition'], '.'), 1, -1);
                        $file = 'weixin/'.md5($url).'.'.$ext;
                        $rt = file_put_contents(SYS_UPLOAD_PATH.$file, $rt->getContent());
                        !$rt && $this->_json(0, '图片或者视频存储失败，消息Id：'.$t['id']);
                    } else {
                        $this->_json(0, '图片或者视频下载失败，消息Id：'.$t['id']);
                    }
                }

                \Phpcmf\Service::M()->table(weixin_wxtable('message'))->update($t['id'], [
                    'status' => 1,
                    'headimgurl' => $user['headimgurl'],
                    'nickname' => $user['nickname'],
                    'userid' => $user['id'],
                    'file' => $file,
                ]);
            }
        }
        
        $this->_json(1, '更新完成');
    }

    public function index() {
        list($tpl, $data) = $this->_List();
        if ($data['list']) {
            $list = [];
            foreach ($data['list'] as $t) {
                $value = dr_string2array($t['content']);
                switch ($t['tid']) {
                    case 'text':
                        # 文本消息
                        $t['tid_name'] = '<span class="label label-success"> 文本 </span>';
                        $t['content'] = $value['Content'];
                        break;
                    case 'image':
                        # 图片消息
                        $t['tid_name'] = '<span class="label label-danger"> 图片 </span>';
                        break;
                    case 'voice':
                        # 语音消息
                        $t['tid_name'] = '<span class="label label-default"> 语音 </span>';
                        break;
                    case 'video':
                        # 视频消息
                        $t['tid_name'] = '<span class="label label-warning"> 视频 </span>';
                        break;
                    case 'shortvideo':
                        # 小视频消息
                        $t['tid_name'] = '<span class="label label-warning"> 小视频 </span>';
                        break;
                    case 'link':
                        # 链接消息
                        $t['tid_name'] = '<span class="label label-info"> 链接 </span>';
                        $t['content'] = '<a href="'.$value['Url'].'" target="_blank">'.$value['Url'].'</a>';
                        break;
                    default:
                        $t['tid_name'] = '未知';
                        break;
                }
                $t['status_name'] = $t['status'] ? '<span class="label label-success"> 已更新 </span>' : '<span class="label label-danger"> 未更新 </span>';
                $list[] = $t;
            }
        }
        \Phpcmf\Service::V()->assign('list', $list);
        \Phpcmf\Service::V()->display($tpl);
    }

    public function show() {
        \Phpcmf\Service::V()->assign('tid', \Phpcmf\Service::L('Input')->get('tid'));
        \Phpcmf\Service::V()->assign('url', SYS_UPLOAD_URL.\Phpcmf\Service::L('Input')->get('file'));
        \Phpcmf\Service::V()->display('message_show.html');exit;
    }

   
    public function del() {
        $this->_Del(
            \Phpcmf\Service::L('Input')->get_post_ids(),
            function ($rows) {
                foreach ($rows as $t) {
                    if ($t['file']) {
                        @unlink(SYS_UPLOAD_PATH.$t['file']);
                    }
                }
                return dr_return_data(1, 'ok');
            },
            null,
            \Phpcmf\Service::M()->dbprefix($this->init['table'])
        );
    }

}
