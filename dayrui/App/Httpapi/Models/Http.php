<?php namespace Phpcmf\Model\Httpapi;

// api
class Http extends \Phpcmf\Model
{


    /**
     * 解析接口数据
     */
    public function get_api_data($data) {

        if (!$data) {
            return dr_return_data(0, dr_lang('接口数据不存在'));
        }

        switch ($data['type']) {

            case 0:
                return dr_return_data(1, 'ok', $data['data']);
                break;

            case 1:
                $rt = dr_string2array($data['data']);
                if (!$rt) {
                    return dr_return_data(0, dr_lang('接口数据内容格式必须是数组'));
                }

                return dr_return_data(1, 'ok', $rt);
                break;

            case 2:
                $return = null;
                if (is_file(dr_get_app_dir('httpapi').'Api/'.$data['file'])) {
                    require dr_get_app_dir('httpapi').'Api/'.$data['file'];
                } else {
                    return dr_return_data(0, dr_lang('接口程序文件【Api/'.$data['file'].'】不存在'));
                }
                return dr_return_data(1, 'ok', $return);
                break;

            case 3:
                $return = null;
                $param = trim(trim($data['list'], '{'), '}');
                $rt = \Phpcmf\Service::V()->list_tag($param);
                if (!$rt['return']) {
                    return dr_return_data(0, $rt['debug']);
                }

                $call = $data['call'];
                if ($call) {
                    // 回调函数
                    if (method_exists(\Phpcmf\Service::L('http'), $call)) {
                        $rt['return'] = \Phpcmf\Service::L('http')->$call($rt['return']);
                    } else {
                        $this->_json(0, '回调方法【'.$data['call'].'】未定义');
                    }
                }
                return dr_return_data(1, 'ok', $rt['return']);
                break;

            case 4:
                $return = null;
                if (!$data['sql']) {
                    return dr_return_data(0, 'SQL内容不存在');
                }

                $return = \Phpcmf\Service::M()->db->query($data['sql'])->getResultArray();


                $call = $data['call'];
                if ($call) {
                    // 回调函数
                    if (method_exists(\Phpcmf\Service::L('http'), $call)) {
                        $rt['return'] = \Phpcmf\Service::L('http')->$call($return);
                    } else {
                        $this->_json(0, '回调方法【'.$data['call'].'】未定义');
                    }
                }
                return dr_return_data(1, 'ok', $return);
                break;
        }

        return dr_return_data(0, dr_lang('未知接口类型'));
    }

    // 缓存
    public function cache() {

        // api 授权码
        $data = $this->table('api_auth')->where('disabled', 0)->getAll();
        $cache = [];
        if ($data) {
            foreach ($data as $t) {
                $cache[$t['id']] = $t['secret'];
            }
        }

        \Phpcmf\Service::L('cache')->set_file('api_auth', $cache);

        // api 返回数据
        $data = $this->table('api_http')->where('disabled', 0)->getAll();
        $cache = [];
        if ($data) {
            foreach ($data as $t) {
                $cache[$t['id']] = dr_string2array($t['content']);
            }
        }

        \Phpcmf\Service::L('cache')->set_file('api_http', $cache);

    }

}