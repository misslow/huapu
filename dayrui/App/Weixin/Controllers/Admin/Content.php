<?php namespace Phpcmf\Controllers\Admin;

// 微信素材内容
class Content extends \Phpcmf\Table
{
    private $tid;

    public function __construct(...$params)
    {
        parent::__construct(...$params);
        // 支持附表存储
        $this->is_data = 0;
        // 表单显示名称
        $this->name = dr_lang('微信素材内容');
        // 字段划分
        $j = 0;
        $field = [];
        $this->tid = $_GET['tid'] ? $_GET['tid'] : str_replace('_index', '',\Phpcmf\Service::L('Router')->method);
       \Phpcmf\Service::L('Router')->method == 'index' &&\Phpcmf\Service::L('Router')->method = $this->tid.'_index';
        switch ($this->tid) {

            case 'index':
                for ($i = 1; $i<=9; $i++) {
                    $field['title_'.$i] = array(
                        'ismain' => 0,
                        'displayorder' => $j,
                        'name' => dr_lang('标题'),
                        'fieldname' => 'title_'.$i,
                        'fieldtype' => 'Text',
                        'setting' => array(
                            'option' => array(
                                'width' => '100%',
                            ),
                            'validate' => array(
                                'formattr' => ' onblur="dr_wx_show_title('.$i.');"',
                            )
                        )
                    );
                    $j++;
                    $field['author_'.$i] = array(
                        'ismain' => 0,
                        'displayorder' => $j,
                        'name' => dr_lang('作者'),
                        'fieldname' => 'author_'.$i,
                        'fieldtype' => 'Text',
                        'setting' => array(
                            'option' => array(
                                'width' => 200,
                            )
                        )
                    );
                    $j++;
                    $field['thumb_'.$i] = array(
                        'ismain' => 0,
                        'displayorder' => $j,
                        'name' => dr_lang('封面'),
                        'fieldtype' => 'File',
                        'fieldname' => 'thumb_'.$i,
                        'setting' => array(
                            'option' => array(
                                'ext' => 'jpg,png,jpeg,gif',
                                'size' => 20,
                            ),
                        )
                    );
                    $j++;
                    $field['description_'.$i] = array(
                        'ismain' => 0,
                        'displayorder' => $j,
                        'name' => dr_lang('描述'),
                        'fieldname' => 'description_'.$i,
                        'fieldtype'	=> 'Textarea',
                        'setting' => array(
                            'option' => array(
                                'width' => '90%',
                                'height' => 70,
                            ),
                            'validate' => array(
                                'xss' => 1,
                            ),
                        )
                    );
                    $j++;
                    $field['content_'.$i] = array(
                        'ismain' => 0,
                        'displayorder' => $j,
                        'name' => dr_lang('内容'),
                        'fieldtype' => 'Ueditor',
                        'fieldname' => 'content_'.$i,
                        'setting' => array(
                            'option' => array(
                                'mode' => 1,
                                'height' => 300,
                                'width' => '100%',
                                'mini' => 1,
                            ),
                            'validate' => [
                                'tips' => '内容中的图片一定不要太大，否则无法同步到微信服务器'
                            ]
                        )
                    );
                    $j++;
                    $field['url_'.$i] = array(
                        'ismain' => 0,
                        'displayorder' => $j,
                        'name' => dr_lang('原文地址'),
                        'fieldname' => 'url_'.$i,
                        'fieldtype' => 'Text',
                        'setting' => array(
                            'option' => array(
                                'width' => '100%',
                            )
                        )
                    );
                }
                break;

            case 'image';
                $field['title'] = array(
                    'ismain' => 0,
                    'displayorder' => $j,
                    'name' => dr_lang('标题'),
                    'fieldname' => 'title',
                    'fieldtype' => 'Text',
                    'setting' => array(
                        'option' => array(
                            'width' => '250',
                        )
                    )
                );
                $field['content'] = array(
                    'ismain' => 0,
                    'displayorder' => $j,
                    'name' => dr_lang('图片'),
                    'fieldtype' => 'File',
                    'fieldname' => 'content',
                    'setting' => array(
                        'option' => array(
                            'ext' => 'jpg,png,jpeg,gif',
                            'size' => 20,
                        )
                    )
                );
                break;

            case 'voice';
                $field['title'] = array(
                    'ismain' => 0,
                    'displayorder' => $j,
                    'name' => dr_lang('标题'),
                    'fieldname' => 'title',
                    'fieldtype' => 'Text',
                    'setting' => array(
                        'option' => array(
                            'width' => '250',
                        )
                    )
                );
                $field['content'] = array(
                    'ismain' => 0,
                    'displayorder' => $j,
                    'name' => dr_lang('文件'),
                    'fieldtype' => 'File',
                    'fieldname' => 'content',
                    'setting' => array(
                        'option' => array(
                            'ext' => 'mp3,wma,wav,amr',
                            'size' => 30,
                        ),
                    )
                );
                break;

            case 'video';
                $field['title'] = array(
                    'ismain' => 0,
                    'displayorder' => $j,
                    'name' => dr_lang('标题'),
                    'fieldname' => 'title',
                    'fieldtype' => 'Text',
                    'setting' => array(
                        'option' => array(
                            'width' => '250',
                        )
                    )
                );
                $field['content'] = array(
                    'ismain' => 0,
                    'displayorder' => $j,
                    'name' => dr_lang('文件'),
                    'fieldtype' => 'File',
                    'fieldname' => 'content',
                    'setting' => array(
                        'option' => array(
                            'ext' => 'mp4,flv,f4v,webm,m4v,mov,3gp,3g2,rm,rmvb,wmv,avi,asf,mpg,mpeg,mpe,ts,mp3,aac,ac3,wav,m4a,ogg,vob,dat,mkv,swf,lavf,cpk,dirac,ram,qt,fli,flc,mod',
                            'size' => 10,
                        ),
                    )
                );
                break;

        }
        // 初始化数据表
        $this->_init([
            'table' => weixin_wxtable('content'),
            'field' => $field,
            'order_by' => 'inputtime desc',
            'date_field' => 'inputtime',
        ]);
        \Phpcmf\Service::V()->assign([
            'menu' => \Phpcmf\Service::M('auth')->_admin_menu(
                [
                    '图文' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/index', 'fa fa-th-list'],
                    '图片' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/image_index', 'fa fa-file-photo-o '],
                    //'语音' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/voice_index', 'fa fa-file-sound-o'],
                    //'视频' => [APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/video_index', 'fa fa-file-video-o'],
                    '添加' => ['hide:'.APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/add', 'fa fa-plus'],
                    '修改' => ['hide:'.APP_DIR.'/'.\Phpcmf\Service::L('Router')->class.'/edit', 'fa fa-plus'],
                ]
            ),
            'tid' => $this->tid,
            'myfieldinfo' => $field,
        ]);
    }

    public function image_index() {
        $this->index();
    }

    public function voice_index() {
        $this->index();
    }

    public function video_index() {
        $this->index();
    }

    public function index() {
        $where = $_GET['keyword'] ? ' and `title` LIKE "%'.$_GET['keyword'].'%"' : '';
        \Phpcmf\Service::M()->set_where_list('`tid`=\''.$this->tid.'\''.$where);
        list($tpl) = $this->_list();
        \Phpcmf\Service::V()->display($tpl);
    }

    public function add() {
        list($tpl) = $this->_Post();
        \Phpcmf\Service::V()->display(str_replace('.html', '_'.$this->tid.'.html', $tpl));
    }

    public function edit() {
        list($tpl, $data) = $this->_Post(intval(\Phpcmf\Service::L('Input')->get('id')));
        \Phpcmf\Service::V()->display(str_replace('.html', '_'.$data['tid'].'.html', $tpl));
    }


    /**
     * 获取内容
     * */
    protected function _Data($id = 0) {

        $data = parent::_Data($id);
        if (!$data) {
            return [];
        }

        switch ($data['tid']) {

            case 'index':
                $value = dr_string2array($data['content']);
                break;

            default:
                $value = [
                    'title' => $data['title'],
                    'content' => $data['content'],
                ];
                break;


        }

        $value['tid'] = $data['tid'];

        return $value;
    }

    // 格式化保存数据
    protected function _Format_Data($id, $data, $old) {

        switch ($this->tid) {

            case 'index':

                !$data[0]['title_1'] && $this->_json(0, '第一个标题不能为空', ['field' => 'title_1']);
                !$data[0]['content_1'] && $this->_json(0, '第一个内容不能为空', ['field' => 'content_1']);

                $save = [
                    'tid' => $this->tid,
                    'title' => $data[0]['title_1'],
                    'content' => dr_array2string($data[0]),
                    'inputtime' => SYS_TIME,
                ];
                break;

            case 'image':

                !$data[0]['title'] && $this->_json(0, '标题不能为空', ['field' => 'title']);
                !$data[0]['content'] && $this->_json(0, '图片不能为空', ['field' => 'content']);

                $save = [
                    'tid' => $this->tid,
                    'title' => $data[0]['title'],
                    'content' => $data[0]['content'],
                    'inputtime' => SYS_TIME,
                ];
                break;

            case 'voice':

                !$data[0]['title'] && $this->_json(0, '标题不能为空', ['field' => 'title']);
                !$data[0]['content'] && $this->_json(0, '文件不能为空', ['field' => 'content']);

                $save = [
                    'tid' => $this->tid,
                    'title' => $data[0]['title'],
                    'inputtime' => SYS_TIME,
                ];
                break;

            case 'video':
                !$data[0]['title'] && $this->_json(0, '标题不能为空', ['field' => 'title']);
                !$data[0]['content'] && $this->_json(0, '文件不能为空', ['field' => 'content']);

                $save = [
                    'tid' => $this->tid,
                    'title' => $data[0]['title'],
                    'content' => $data[0]['content'],
                    'inputtime' => SYS_TIME,
                ];
                break;


        }

        // 每次修改媒体id重置
        $save['media_id'] = '';

        return $save;
    }

    // 保存后
    protected function _Save($id = 0, $data = [], $old = [], $before = null, $after = null) {

        return parent::_Save($id, $data, $old, $before, function($id, $data, $old) {
            if ($data['tid'] == 'index') {
                $content = dr_string2array($data['content']);
                for ($i = 1; $i <=9; $i++) {
                    !$content['url_'.$i] && $content['url_'.$i] = SITE_URL.'index.php?s=weixin&c=show&id='.$id.'&p='.$i;
                }
                \Phpcmf\Service::M()->table(weixin_wxtable('content'))->update($id, [
                    'content' => dr_array2string($content),
                ]);

            }
        });
    }

    //
    public function del() {
        $this->_Del(
            \Phpcmf\Service::L('Input')->get_post_ids(),
            null,
            null,
            \Phpcmf\Service::M()->dbprefix($this->init['table'])
        );
    }

    //
    public function sync_add() {

        $ids = \Phpcmf\Service::L('Input')->get_post_ids();
        !$ids && $this->_json(0, dr_lang('所选数据不存在'));

        $rt = weixin_get_access_token();
        !$rt['code'] && $this->_json(0, $rt['msg']);
        $access_token = $rt['msg'];

        $rows = \Phpcmf\Service::M()->init($this->init)->where_in('id', $ids)->getAll();
        !$rows && $this->_json(0, dr_lang('所选数据不存在'));

        foreach ($rows as $t) {
            if (!$t['media_id']) {
                $rt = \Phpcmf\Service::M('Weixin', 'Weixin')->sync_content($access_token, $t);
                !$rt['code'] && $this->_json(0, $rt['msg']);
            }
        }

        $this->_json(1, '同步完成');
    }

}
