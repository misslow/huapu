<?php namespace Phpcmf\Model;

class Weixin extends \Phpcmf\Model
{

    // 获取微信表名
    public function wxtable($name) {
        return 'weixin_'.$name;
    }

    // 获取配置信息
    public function get_config($name) {

        $data = $this->db->table('weixin')->where('name', $name)->get()->getRowArray();
        if ($data) {
            $data = dr_string2array($data['value']);
            return $data;
        } else {
            $this->db->table('weixin')->insert([
                'name' => $name,
                'value' => '',
            ]);
            return [];
        }
    }

    // 存储配置信息
    public function save_config($name, $post) {

        $rt = $this->db->table('weixin')->replace([
            'name' => $name,
            'value' => dr_array2string($post),
        ]);

        if ($rt->connID->error) {
            return dr_return_data(0, $this->table.': '.$rt->connID->error);
        }

        return dr_return_data(1);
    }

    // 上传图文消息内的图片获取URL
    public function get_file_media_id($access_token, $type, $filename) {

        // 查询本地库
        $rt = $this->table(weixin_wxtable('media_id'))->where('file', md5($filename))->getRow();
        if ($rt) {
            return dr_return_data(1, 'ok', $rt);
        }
        $id = dr_get_file($filename);
        if (!$id) {
            return dr_return_data(0, '文件不存在');
        }
        $file = WRITEPATH.'temp/sync_'.md5($id).'.'.substr(strrchr($id, '.'), 1);;
        $c = dr_catcher_data($id);
        if (!$c) {
            return dr_return_data(0, '无法获取文件内容:'.$id);
        }
        $rt = file_put_contents($file, $c);
        if (!$rt) {
            return dr_return_data(0, '本地文件写入失败:'.$id);
        } elseif (!is_file($file)) {
            return dr_return_data(0, '本地文件不存在');
        }

        $rt = ihttp_request(
            'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$access_token,
            [
                'type' => $type,
                'media' => '@' . $file
            ]
        );
        if ($rt['code']) {
            // 更新到媒体表
            $rt = $this->table(weixin_wxtable('media_id'))->insert([
                'file' => md5($filename),
                'url' => $rt['data']['url'],
                'media_id' => $rt['data']['media_id'],
            ]);
        }

        return $rt;
    }

    // 同步素材到服务器
    public function sync_content($access_token, $t) {


        if ($t['tid'] == 'index') {
            // 图文素材
            $url = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token='.$access_token;
            $param = [
                'articles' => [

                ]
            ];
            $value = dr_string2array($t['content']);
            for ($i = 1; $i<=9; $i++) {
                if (!$value['thumb_'.$i] || !$value['title_'.$i]) {
                    break;
                }
                $cache = [];
                $content = htmlspecialchars_decode($value['content_'.$i]);
                if (preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|png))\\2/i", $content, $imgs)) {
                    foreach ($imgs[3] as $image) {
                        if (!isset($cache[$image])) {
                            $tmp = WRITEPATH.'temp/sync_'.md5($image).'.'.substr(strrchr($image, '.'), 1);;
                            $file = dr_catcher_data($image);
                            if ($file && file_put_contents($tmp, $file)) {
                                $cache[$image] = 1;
                                $rt = ihttp_request(
                                    'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$access_token,
                                    [
                                        'media' => '@' . $tmp
                                    ]
                                );
                                // 更新到内容
                                $rt['code'] && $content = str_replace($image, $rt['data']['url'], $content);
                            }
                        }
                    }
                }
                $rt = $this->get_file_media_id($access_token, 'thumb', $value['thumb_'.$i]);
                if (!$rt['code']) {
                    return $rt;
                }

                $thumb_id = $rt['data']['media_id'];
                $param['articles'][] = [
                    "title" => $value['title_'.$i],
                    "thumb_media_id" => $thumb_id,
                    "author" => $value['author_'.$i],
                    "digest" => $value['description_'.$i],
                    "content" => $content,
                    "show_cover_pic" => 1,
                    "content_source_url" => html_entity_decode($value['url_'.$i])
                ];
            }
            if (!count($param['articles'])) {
                return dr_return_data(0, '资源信息为空无法同步');
            }
            $rt = wx_post_https_json_data($url, $param);
            if (!$rt['code']) {
                return $rt;
            }

        } else {
            $rt = $this->get_file_media_id($access_token, $t['tid'], $t['content']);
            if (!$rt['code']) {
                return $rt;
            }
        }

        // 存储
        $this->table(weixin_wxtable('content'))->update($t['id'], [
           'media_id' => $rt['data']['media_id']
        ]);

        return dr_return_data(1, 'ok');
    }

    // 高级群发
    public function sendall($groupid, $data)
    {

        $rt = weixin_get_access_token();
        if (!$rt['code']) {
            return $rt;
        }

        switch ($data['tid']) {

            case 'index':
                $param = [
                    'mpnews' => [
                        'media_id' => $data['media_id']
                    ],
                    'msgtype' => 'mpnews'
                ];
                break;

            case 'image':
                $param = [
                    'image' => [
                        'media_id' => $data['media_id']
                    ],
                    'msgtype' => 'image'
                ];
                break;

            case 'voice':
                $param = [
                    'voice' => [
                        'media_id' => $data['media_id']
                    ],
                    'msgtype' => 'voice'
                ];
                break;

            case 'video':
                $param = [
                    'mpvideo' => [
                        'media_id' => $data['media_id']
                    ],
                    'msgtype' => 'mpvideo'
                ];
                break;

        }

        if ($groupid) {
            $param['filter'] = [
                'is_to_all' => false,
                'tag_id' => $groupid,
            ];
        } else {
            $param['filter'] = [
                'is_to_all' => true,
            ];
        }

        $rt = wx_post_https_json_data(
            'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='.$rt['msg'],
            $param
        );
        if (!$rt['code']) {
            return $rt;
        }

        return dr_return_data(1, 'ok');
    }

     // 客服群发 文本内容
    public function send_for_text($groupid, $data)
    {

        $param = [
            'msgtype' => 'text',
            'text' => [
                'content' => $data
            ],
        ];

        if ($groupid) {
            if (strlen($groupid) > 5) {
                // 指定粉丝
                $user = [
                    [
                        'openid' => $groupid,
                    ]
                ];
            } else {
                // 粉丝组
                $user = $this->table(weixin_wxtable('user'))->where('groupid', $groupid)->getAll();
            }
        } else {
            // 全部粉丝
            $user = $this->table(weixin_wxtable('user'))->getAll();
        }

        if (!$user) {
            return dr_return_data(0, '粉丝信息不存在');
        }

        return $this->_save_send_data($user, $param);
    }

    // 客服群发 内容模块
    public function send_for_module($dir, $ids) {

        $data = $this->table_site($dir)->where_in('id', $ids)->getAll(9);
        if (!$data) {
            return dr_return_data(0, '模块内容不存在');
        }

        $param = [
            'msgtype' => 'news',
            'news' => [
                'articles' => []
            ],
        ];
        foreach ($data as $t) {
            $param['news']['articles'][] = [
                "title" => $t['title'],
                "description" => $t['description'],
                "picurl" => dr_get_file($t['thumb']),
                "url" => dr_url_prefix($t['url'], $dir)
            ];
        }

        $user = $this->table(weixin_wxtable('user'))->getAll();
        if (!$user) {
            return dr_return_data(0, '粉丝信息不存在');
        }
        return $this->_save_send_data($user, $param);

    }
    // 客服群发 内容素材
    public function send_for_content($groupid, $data)
    {

        switch ($data['tid']) {

            case 'index':

                $param = [
                    'msgtype' => 'mpnews',
                    'mpnews' => [
                        'media_id' => $data['media_id']
                    ],
                ];
                break;

            case 'image':
                $param = [
                    'image' => [
                        'media_id' => $data['media_id']
                    ],
                    'msgtype' => 'image'
                ];
                break;

            case 'voice':
                $param = [
                    'music' => [
                        'media_id' => $data['media_id']
                    ],
                    'msgtype' => 'music'
                ];
                break;

            case 'video':
                $param = [
                    'video' => [
                        'media_id' => $data['media_id'],
                    ],
                    'msgtype' => 'mpvideo'
                ];
                break;

        }

        if ($groupid) {
            if (strlen($groupid) > 5) {
                // 指定粉丝
                $user = [
                    [
                        'openid' => $groupid,
                    ]
                ];
            } else {
                // 粉丝组
                $user = $this->table(weixin_wxtable('user'))->where('groupid', $groupid)->getAll();
            }
        } else {
            // 全部粉丝
            $user = $this->table(weixin_wxtable('user'))->getAll();
        }

        if (!$user) {
            return dr_return_data(0, '粉丝信息不存在');
        }

        return $this->_save_send_data($user, $param);
    }

    // 存储客服消息发送记录表
    private function _save_send_data($user, $param) {

        $id = (int)substr(SYS_TIME - rand(0, 9999), 0, 10);

        foreach ($user as $t) {
            $rt = $this->table(weixin_wxtable('send'))->insert([
                'cid' => $id,
                'openid' => $t['openid'],
                'status' => 0,
                'content' => dr_array2string($param)
            ]);
            if (!$rt['code']) {
                return $rt;
            }
        }

        return dr_return_data($id, 'ok');
    }

    // 存储客户端消息
    public function save_message($data)
    {

        $rt = $this->table(weixin_wxtable('message'))->insert([
            'tid' => $data['MsgType'],
            'openid' => $data['FromUserName'],
            'nickname' => '',
            'headimgurl' => '',
            'status' => 0,
            'content' => dr_array2string($data),
            'inputtime' => $data['CreateTime'],
        ]);
        if (!$rt['code']) {
            log_message('error', '微信消息存储失败：'.$rt['msg']);
        }

    }

    // 更新统计数据
    public function update_count_fans() {

        return $this->table(weixin_wxtable('count_fans'))->order_by('id desc')->getAll(10, 'date');

        $seven_days = array(
            date('Ymd', strtotime('-1 days')),
            date('Ymd', strtotime('-2 days')),
            date('Ymd', strtotime('-3 days')),
            date('Ymd', strtotime('-4 days')),
            date('Ymd', strtotime('-5 days')),
            date('Ymd', strtotime('-6 days')),
            date('Ymd', strtotime('-7 days')),
        );

        // 本周数据
        $week_stat_fans = $this->table(weixin_wxtable('count_fans'))->where_in('date', $seven_days)->getAll(0, 'date');
        $stat_update_yes = false;
        foreach ($seven_days as $sevens) {
            if (empty($week_stat_fans[$sevens]) || $week_stat_fans[$sevens]['cumulate'] <=0) {
                $stat_update_yes = true;
                break;
            }
        }

        if (empty($stat_update_yes)) {
            return $week_stat_fans;
        }

        $rt = weixin_get_access_token();
        if ($rt['code']) {
            $access_token = $rt['msg'];
            $url = 'https://api.weixin.qq.com/datacube/getusersummary?access_token='.$access_token;
            $rt = wx_post_https_json_data($url, [
                "begin_date" => date('Y-m-d', strtotime('-7 days')),
                "end_date" => date('Y-m-d', strtotime('-1 days')),
            ]);
            if ($rt['code']) {
                $summary = $rt['data'];
            }

            $url = 'https://api.weixin.qq.com/datacube/getusercumulate?access_token='.$access_token;
            $rt = wx_post_https_json_data($url, [
                "begin_date" => date('Y-m-d', strtotime('-7 days')),
                "end_date" => date('Y-m-d', strtotime('-1 days')),
            ]);
            if ($rt['code']) {
                $cumulate = $rt['data'];
            }

            $result = [];
            if (!empty($summary['list'])) {
                foreach ($summary['list'] as $row) {
                    $key = str_replace('-', '', $row['ref_date']);
                    $result[$key]['new'] = intval($result[$key]['new']) + $row['new_user'];
                    $result[$key]['cancel'] = intval($result[$key]['cancel']) + $row['cancel_user'];
                }
            }
            if (!empty($cumulate['list'])) {
                foreach ($cumulate['list'] as $row) {
                    $key = str_replace('-', '', $row['ref_date']);
                    $result[$key]['cumulate'] = $row['cumulate_user'];
                }
            }

            foreach ($result as $i => $t) {
                $this->table(weixin_wxtable('count_fans'))->replace([
                   'new' => (int)$t['new'],
                   'cancel' => (int)$t['cancel'],
                   'cumulate' => (int)$t['cumulate'],
                   'date' => $i,
                ]);
            }

            return $result;
        }

        return $week_stat_fans;
    }

    // 缓存
    public function cache($siteid = SITE_ID)
    {

        $data = $this->table('weixin')->getAll();
        $cache = [];
        if ($data) {
            foreach ($data as $t) {
                $value = dr_string2array($t['value']);
               $cache[$t['name']] = $value;
            }
        }

        \Phpcmf\Service::L('cache')->set_file('weixin', $cache);
        return;
    }

}