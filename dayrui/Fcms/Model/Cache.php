<?php namespace Phpcmf\Model;

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


// 系统缓存
class Cache extends \Phpcmf\Model
{
    private $is_sync_cache;
    private $site_cache;
    private $module_cache;

    // 更新附件缓存
    public function update_attachment() {

        $page = intval($_GET['page']);
        if (!$page) {
            if (!SYS_CACHE) {
                exit(\Phpcmf\Service::C()->_json(0, dr_lang('系统未开启缓存功能'), 1));
            } elseif (!SYS_CACHE_ATTACH) {
                exit(\Phpcmf\Service::C()->_json(0, dr_lang('系统未设置附件缓存时间'), 1));
            }
            exit(\Phpcmf\Service::C()->_json(1, dr_lang('正在检查附件'), 1));
        }

        $total = $this->table('attachment')->counts();
        if (!$total) {
            exit(\Phpcmf\Service::C()->_json(1, dr_lang('无可用附件更新'), 0));
        }

        $psize = 50;
        $tpage = ceil($total/$psize);
        $result = $this->db->table('attachment')->orderBy('id ASC')
            ->limit($psize, $psize * ($page - 1))->get()->getResultArray();
        if ($result) {
            foreach ($result as $t) {
                \Phpcmf\Service::C()->get_attachment($t['id']);
            }
        }

        if ($page > $tpage) {
            exit(\Phpcmf\Service::C()->_json(1, dr_lang('已更新%s个附件', $total), 0));
        }

        exit(\Phpcmf\Service::C()->_json(1, dr_lang('正在更新中（%s/%s）', $page, $tpage), $page + 1));
    }

    // 同步更新缓存
    // \Phpcmf\Service::M('cache')->sync_cache();
    public function sync_cache($name = '', $namepspace = '', $is_site = 1) {


        if (!$this->is_sync_cache) {
            $this->site_cache = $this->table('site')->getAll();
            $this->module_cache = $this->table('module')->order_by('displayorder ASC,id ASC')->getAll();
            \Phpcmf\Service::M('site')->cache(0, $this->site_cache, $this->module_cache);
        }

        if (!$is_site && $name) {
            \Phpcmf\Service::M($name, $namepspace)->cache();
        }

        foreach ($this->site_cache as $t) {

            if ($this->module_cache) {
                \Phpcmf\Service::M('table')->cache($t['id'], $this->module_cache);
                \Phpcmf\Service::M('module')->cache($t['id'], $this->module_cache);
            }

            if ($is_site && $name) {
                \Phpcmf\Service::M($name, $namepspace)->cache($t['id']);
            }
        }

        \Phpcmf\Service::M('menu')->cache();

        if (!$this->is_sync_cache) {
            $this->is_sync_cache = 1;
        }

        $this->update_data_cache();
    }


    // 更新缓存
    public function update_cache() {

        $site_cache = $this->table('site')->getAll();
        $module_cache = $this->table('module')->order_by('displayorder ASC,id ASC')->getAll();

        \Phpcmf\Service::M('site')->cache(0, $site_cache, $module_cache);

        // 全局缓存
        foreach (['auth', 'email', 'urlrule', 'member', 'verify', 'attachment'] as $m) {
            \Phpcmf\Service::M($m)->cache();
        }

        // 按站点更新的缓存
        $cache = [
            'linkage' => '',
            'form' => '',
        ];

        if (is_file(MYPATH.'/Config/Cache.php')) {
            $_cache = require MYPATH.'/Config/Cache.php';
            $_cache && $cache = $cache + $_cache;
        }

        // 执行插件自己的缓存程序
        $local = dr_dir_map(dr_get_app_list(), 1);
        foreach ($local as $dir) {
            $path = dr_get_app_dir($dir);
            if (is_file($path.'install.lock')
                && is_file($path.'Config/Cache.php')) {
                $_cache = require $path.'Config/Cache.php';
                $_cache && $cache = $cache + $_cache;
            }
        }

        foreach ($site_cache as $t) {

            \Phpcmf\Service::M('table')->cache($t['id'], $module_cache);
            \Phpcmf\Service::M('module')->cache($t['id'], $module_cache);

            foreach ($cache as $m => $namespace) {
                \Phpcmf\Service::M($m, $namespace)->cache($t['id']);
            }
        }

        \Phpcmf\Service::M('menu')->cache();

        $this->update_data_cache();
    }

    // 重建索引
    public function update_search_index() {


        $site_cache = $this->table('site')->getAll();
        $module_cache = $this->table('module')->getAll();
        if (!$module_cache) {
            return;
        }

        foreach ($site_cache as $t) {
            foreach ($module_cache as $m ) {
                $table = dr_module_table_prefix($m['dirname'], $t['id']);
                // 判断是否存在表
                if (!$this->db->tableExists($table)) {
                    continue;
                }
                $this->db->table($table.'_search')->truncate();
            }
        }

    }

    // 更新数据
    public function update_data_cache() {

        // 清空系统缓存
        \Phpcmf\Service::L('cache')->init()->clean();

        // 清空文件缓存
        \Phpcmf\Service::L('cache')->init('file')->clean();

        // 递归删除文件
        dr_dir_delete(WRITEPATH.'html');
        dr_dir_delete(WRITEPATH.'temp');
        dr_dir_delete(WRITEPATH.'attach');
        dr_dir_delete(WRITEPATH.'template');

        // 重置Zend OPcache
        function_exists('opcache_reset') && opcache_reset();

    }

    // 重建子站配置文件
    public function update_site_config() {

        $site = [];
        $site_cache = $this->table('site')->getAll();
        foreach ($site_cache as $t) {
            $t['setting'] = dr_string2array($t['setting']);
            if ($t['id'] > 1 && $t['setting']['webpath']) {
                $rt = $this->update_webpath('Web', $t['setting']['webpath'], [
                    'SITE_ID' => $t['id']
                ]);
                if ($rt) {
                    $this->_error_msg('站点['.$t['domain'].']: '.$rt);
                }
            }
            $site[] = $t['id'];
        }

        $cache = $this->table('module')->getAll();
        foreach ($cache as $t) {
            if (!is_file(APPSPATH.ucfirst($t['dirname']).'/Config/App.php')) {
                continue;
            } elseif ($t['share']) {
                continue;
            }
            $t['site'] = dr_string2array($t['site']);
            foreach ($site as $siteid) {
                if ($t['site'][$siteid]['domain'] && $t['site'][$siteid] && $t['site'][$siteid]['webpath']) {
                    $rt = $this->update_webpath('Module_Domain', $t['site'][$siteid]['webpath'], [
                        'SITE_ID' => $siteid,
                        'MOD_DIR' => $t['dirname'],
                    ]);
                    if ($rt) {
                        $this->_error_msg('模块['.$t['site'][$siteid]['domain'].']: '.$rt);
                    }
                }
            }

        }

    }

    // 更新站点
    public function update_webpath($name, $path, $value) {

        if (!$path) {
            return '目录为空';
        }

        $path = dr_get_dir_path($path);
        if (!$path) {
            return '目录为空';
        }
        dr_mkdirs($path);
        if (!is_dir($path)) {
            return '目录['.$path.']不存在';
        }

        foreach ([
                     'admin.php',
                     'index.php',
                     'mobile/index.php'
                 ] as $file) {
            if (is_file(FCPATH.'Temp/'.$name.'/'.$file)) {
                $dst = $path.$file;
                dr_mkdirs(dirname($dst));
                $size = file_put_contents($dst, str_replace([
                    '{ROOTPATH}',
                    '{MOD_DIR}',
                    '{SITE_ID}'

                ], [
                    ROOTPATH,
                    $value['MOD_DIR'],
                    $value['SITE_ID']
                ], file_get_contents(FCPATH.'Temp/'.$name.'/'.$file)));
                if (!$size) {
                    return '文件['.$dst.']无法写入';
                }
            }
        }

    }

    private function _error_msg($msg) {
        echo json_encode(dr_return_data(0, $msg));exit;
    }



}