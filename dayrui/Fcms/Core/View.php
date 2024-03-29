<?php namespace Phpcmf;

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




/**
 * CMS模板标签解析
 */
class View {

    private $_is_admin; // 是否后台模板
    private $_disp_dir; // display传人的目录参数

    private $_dir; // 模板目录
    private $_tname; // 判断是否是手机端目录

    private $_cache; // 模板缓存目录

    private $_root; // 默认前端项目模板目录
    private $_mroot; // 默认会员项目模板目录

    private $_root_array; // 默认前端项目模板目录,pc+移动
    private $_mroot_array; // 默认会员项目模板目录,pc+移动
    private $_module_root_array; // 默认模块前端模板目录,pc+移动

    private $_aroot; // 默认后台项目模板目录

    private $_options; // 模板变量
    private $_filename; // 主模板名称
    private $_include_file; // 引用计数

    private $pos_order; // 是否包含有地图定位的排序

    private $action; // 指定action

    public $_is_mobile; // 是否是移动端模板

    private $performanceData = []; // 用于调试栏数据
    private $loadjs = []; // 加载的js

    private $_page_config = []; // 分页参数


    /**
     * 初始化环境
     */
    public function init($name = 'pc') {

        $this->_is_mobile = $name == 'mobile';

        // 模板缓存目录
        $this->_cache = WRITEPATH.'template/';
        $this->_tname = $this->_is_mobile ? 'mobile' : $name;
        // 当前项目模板目录
        if (IS_ADMIN) {
            // 后台
            $this->admin();
        } elseif (IS_MEMBER) {
            // 会员
            //$this->_is_mobile && !is_dir(TPLPATH.'mobile/'.SITE_TEMPLATE.'/member/') && $this->_tname = 'pc';
            $this->_root = $this->get_client_home_path($this->_tname);
            $this->_dir = $this->_mroot = $this->get_client_member_path($this->_tname);
            // 当用户中心没有这个模板目录时我们就调用default的用户中心模板
            !is_dir($this->_mroot) && $this->_mroot = str_replace('/'.SITE_TEMPLATE.'/', '/default/', $this->_mroot);
            // 项目的会员中心
            APP_DIR != 'member' && $this->_dir = $this->get_client_member_path($this->_tname, APP_DIR);
        } elseif (IS_API || (APP_DIR && dr_is_app_dir(APP_DIR))) {
            // 插件模块或者api
            $dir = IS_API ? 'api' : APP_DIR;
            //$this->_is_mobile && !is_dir(TPLPATH.'mobile/'.SITE_TEMPLATE.'/home/') && $this->_tname = 'pc';
            $this->_root = $this->get_client_home_path($this->_tname);
            $this->_dir = $this->get_client_home_path($this->_tname, $dir);
        } else {
            // 首页前端页面
            //$this->_is_mobile && !is_dir(TPLPATH.'mobile/'.SITE_TEMPLATE.'/home/') && $this->_tname = 'pc';
            $this->_dir = $this->_root = $this->get_client_home_path($this->_tname);
        }

        // 可用action
        $this->action = [
            'category', 'module', 'content', 'related', 'share', 'table', 'form', 'mform', 'member', 'page',
            'tag', 'hits', 'search', 'category_search_field', 'linkage', 'sql',
            'cache', 'navigator'
        ];

    }

    // 终端路径
    private function get_client_home_path($name, $mid = '') {
        return dr_get_app_tpl($mid).$name.'/'.SITE_TEMPLATE.'/home/'.($mid ? $mid.'/' : '');
    }

    // 终端路径
    private function get_client_member_path($name, $mid = '') {
        return dr_get_app_tpl($mid).$name.'/'.SITE_TEMPLATE.'/member/'.($mid ? $mid.'/' : '');
    }

    /**
     * 强制设置模块模板目录
     *
     * @param	string	$dir	模板名称
     * @param	string  $isweb  强制前台
     */
    public function module($module, $isweb = 0) {

        if ($isweb == 0 && (IS_ADMIN || IS_MEMBER)) {
            return;
        }

        // 默认模板目录
        $this->_root = $this->get_client_home_path($this->_tname);
        $this->_dir = $this->get_client_home_path($this->_tname, $module);
    }

    /**
     * 强制设置为当前默认的模板目录(一般用于api外部接入)
     */
    public function dir($dir) {
        $this->_dir = $this->_mdir = TPLPATH.$dir;
    }


    /**
     * 强制设置为后台模板目录
     */
    public function admin() {
        $this->_dir = APPPATH.'Views/';
        $this->_aroot = COREPATH.'Views/';
        $this->_is_admin = 1;
    }

    /**
     * 输出模板
     *
     * @param	string	$_name		模板文件名称（含扩展名）
     * @param	string	$_dir		模块名称
     * @return  void
     */
    public function display($_name, $_dir = '') {

        $start = microtime(true);

        if (!$this->_options['my_web_url']) {
            $this->_options['my_web_url'] = $this->_options['fix_html_now_url'] ? $this->_options['fix_html_now_url'] : dr_now_url();
        }

        if (IS_API_HTTP) {
            // 如果是来自api就不解析模板，直接输出变量
            $call = \Phpcmf\Service::L('Input')->request('api_call_function');
            if ($call) {
                $call = dr_safe_replace($call);
                if (method_exists(\Phpcmf\Service::L('http'), $call)) {
                    \Phpcmf\Service::C()->_json(1, 'view', \Phpcmf\Service::L('http')->$call($this->_options));
                }
                \Phpcmf\Service::C()->_json(0, '回调方法('.$call.')未定义');
            }
            \Phpcmf\Service::C()->_json(1, 'view', $this->_options);
        }

        extract($this->_options, EXTR_PREFIX_SAME, 'data');

        $this->_filename = str_replace('..', '[removed]', $_name);


        // 加载编译后的缓存文件
        $this->_disp_dir = $_dir;
        $_view_file = $this->get_file_name($this->_filename, $_dir);
        $_view_name = str_replace([TPLPATH, FCPATH, APPSPATH], ['TPLPATH/', 'FCPATH/', 'APPSPATH/'], $_view_file);

        if (IS_DEV) {
            echo "<!--当前页面的模板文件是：$_view_name （本代码只在开发者模式下显示）-->".PHP_EOL;
        } else {
            $this->_options = null;
        }

        \Config\Services::timer()->start($_view_name);
        include $this->load_view_file($_view_file);
        \Config\Services::timer()->stop($_view_name);
        // 记录性能日志
        $this->logPerformance($start, microtime(true), $_view_name);

        // 消毁变量
        $this->_include_file = null;
        $this->loadjs = null;
    }

    // 动态加载js
    public function load_js($js) {
        if (isset($this->loadjs[$js])) {
            return '';
        } else {
            $this->loadjs[$js] = 1;
            return '<script type=\'text/javascript\' src=\''.$js.'\'></script>';
        }
    }

    /**
     * 设置模块/应用的模板目录
     *
     * @param	string	$file		文件名
     * @param	string	$dir		模块/应用名称
     * @param	string	$include	是否使用的是include标签
     */
    public function get_file_name($file, $dir = null, $include = FALSE) {

        $dir = $dir ? $dir : $this->_disp_dir;

        if (IS_ADMIN || $dir == 'admin' || $this->_is_admin) {
            // 后台操作时，不需要加载风格目录，如果文件不存在可以尝试调用主项目模板
            $adir = APPPATH.'Views/';
            if (APP_DIR && is_file(MYPATH.'View/'.APP_DIR.'/'.$file)) {
                return MYPATH.'View/'.APP_DIR.'/'.$file;
            } elseif (!APP_DIR && is_file(MYPATH.'View/'.$file)) {
                return MYPATH.'View/'.$file;
            } elseif (is_file($adir.$file)) {
                return $adir.$file; // 调用当前后台的模板
            } elseif (is_file($this->_aroot.$file)) {
                return $this->_aroot.$file; // 当前项目目录模板不存在时调用主项目的
            } elseif ($dir != 'admin' && is_file(APPSPATH.ucfirst($dir).'/Views/'.$file)) {
                return APPSPATH.ucfirst($dir).'/Views/'.$file; // 指定模块时调用模块下的文件
            }
            $error = $adir.$file;
        } elseif (IS_MEMBER || $dir == 'member') {
            // 会员操作时，需要加载风格目录，如果文件不存在可以尝试调用主项目模板
            if ($dir === '/' && is_file($this->_root.$file)) {
                return $this->_root.$file;
            } elseif (is_file($this->_dir.$file)) {
                // 调用当前的会员模块目录
                return $this->_dir.$file;
            } elseif (is_file($this->_mroot.$file)) {
                // 调用默认的会员模块目录
                return $this->_mroot.$file;
            } elseif (is_file($this->_root.$file)) {
                // 调用网站主站模块目录
                return $this->_root.$file;
            }
            $error = $dir === '/' ? $this->_root.$file : $this->_dir.$file;
        } elseif ($file == 'goto_url') {
            // 转向字段模板
            return COREPATH.'Views/go.html';
        } else {
            if ($dir === '/' && is_file($this->_root.$file)) {
                // 强制主目录
                return $this->_root.$file;
            } else if (@is_file($this->_dir.$file)) {
                // 调用本目录
                return $this->_dir.$file;
            } else if (@is_file($this->_root.$file)) {
                // 再次调用主程序下的文件
                return $this->_root.$file;
            }
            $error = $dir === '/' ? $this->_root.$file : $this->_dir.$file;
        }

        /*
        // 如果移动端模板不存在就调用主网站风格
        if ($this->_is_mobile && is_file(str_replace('/mobile/', '/pc/', $error))) {
            return str_replace('/mobile/', '/pc/', $error);
        } elseif ($this->_is_mobile && is_file(str_replace('/mobile/', '/pc/', $this->_root.$file))) {
            return str_replace('/mobile/', '/pc/', $this->_root.$file);
        }*/
        if ($file == 'msg.html' && is_file(TPLPATH.'pc/default/home/msg.html')) {
            return TPLPATH.'pc/default/home/msg.html';
        }


        if (CI_DEBUG) {
            echo FC_NOW_URL.PHP_EOL.'<hr>';
            exit('模板文件 ('.$error.') 不存在');
        }

        exit('模板文件 ('.str_replace(TPLPATH, '/', $error).') 不存在');
    }


    /**
     * 设置模板变量
     */
    public function assign($key, $value = '') {

        if (!$key) {
            return FALSE;
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->_options[$k] = $v;
            }
        } else {
            $this->_options[$key] = $value;
        }
    }

    /**
     * 获取模板变量
     */
    public function get_value($key) {

        if (!$key) {
            return '';
        }

        return $this->_options[$key];
    }

    /**
     * 重新赋值模板变量
     */
    public function set_value($key, $value = '', $replace = '') {

        if (!$key) {
            return '';
        }

        $this->_options[$key] = $replace ? str_replace($replace, $value, $this->_options[$key]) : $value;
    }

    /**
     * 模板标签include/template
     *
     * @param	string	$name	模板文件
     * @param	string	$dir	应用、模块目录
     * @return  bool
     */
    public function _include($name, $dir = '') {

        $dir = $dir == 'MOD_DIR' ? MOD_DIR : $dir;
        $file = $this->get_file_name($name, $dir, TRUE);

        $fname = md5($file);
        isset($this->_include_file[$fname]) ? $this->_include_file[$fname] ++ : $this->_include_file[$fname] = 0;

        $this->_include_file[$fname] > 50 && exit('模板文件 ('.str_replace(TPLPATH, '/', $file).') 标签template引用文件目录结构错误');

        return $this->load_view_file($file);
    }

    /**
     * 模板标签load
     *
     * @param	string	$file	模板文件
     * @return  bool
     */
    public function _load($file) {

        $fname = md5($file);
        $this->_include_file[$fname] ++;

        $this->_include_file[$fname] > 50 && exit('模板文件 ('.str_replace(TPLPATH, '/', $file).') 标签load引用文件目录结构错误');

        return $this->load_view_file($file);
    }

    /**
     * 加载
     *
     * @param	string
     * @return  string
     */
    public function load_view_file($name) {

        $cache_file = $this->_cache.str_replace(array(WEBPATH, '/', '\\', DIRECTORY_SEPARATOR), array('', '_', '_', '_'), $name).($this->_is_mobile ? '.mobile.' : '').'.cache.php';

        // 当缓存文件不存在时或者缓存文件创建时间少于了模板文件时,再重新生成缓存文件
        if (!is_file($cache_file) || (is_file($cache_file) && is_file($name) && filemtime($cache_file) < filemtime($name))) {
            $content = $this->handle_view_file(file_get_contents($name));
            @file_put_contents($cache_file, $content, LOCK_EX) === FALSE && show_error('请将模板缓存目录（/cache/template/）权限设为777', 404, '无写入权限');
        }

        return $cache_file;
    }

    // 将模板代码转化为php
    public function code2php($code, $time = 0, $include = 1) {

        $file = md5($code).$time.'.code.php';
        !$include && $code = preg_replace([
            '#{template.*}#Uis',
            '#{load.*}#Uis'
        ], [
            '--',
            '--',
        ], $code);
        !is_file($this->_cache.$file) && @file_put_contents($this->_cache.$file, str_replace('$this->', '\Phpcmf\Service::V()->', $this->handle_view_file($code)));

        return $this->_cache.$file;
    }

    /**
     * 解析模板文件
     *
     * @param	string
     * @param	string
     * @return  string
     */
    public function handle_view_file($view_content) {

        if (!$view_content) {
            return '';
        }

        // 正则表达式匹配的模板标签
        $regex_array = [
            // 3维数组变量
            '#{\$(\w+?)\.(\w+?)\.(\w+?)\.(\w+?)}#i',
            // 2维数组变量
            '#{\$(\w+?)\.(\w+?)\.(\w+?)}#i',
            // 1维数组变量
            '#{\$(\w+?)\.(\w+?)}#i',
            // 3维数组变量
            '#\$(\w+?)\.(\w+?)\.(\w+?)\.(\w+?)#Ui',
            // 2维数组变量
            '#\$(\w+?)\.(\w+?)\.(\w+?)#Ui',
            // 1维数组变量
            '#\$(\w+?)\.(\w+?)#Ui',
            // PHP函数
            '#{([a-z_0-9]+)\((.*)\)}#Ui',
            // PHP常量
            '#{([A-Z_]+)}#',
            // PHP变量
            '#{\$(.+?)}#i',
            // 类库函数
            '#{([A-Za-z_]+)::(.+)\((.*)\)}#Ui',
            // 引入模板
            '#{\s*template\s+"([\$\-_\/\w\.]+)",\s*"(.+)"\s*}#Uis',
            '#{\s*template\s+"([\$\-_\/\w\.]+)",\s*MOD_DIR\s*}#Uis',
            '#{\s*template\s+"([\$\-_\/\w\.]+)"\s*}#Uis',
            '#{\s*template\s+([\$\-_\/\w\.]+)\s*}#Uis',
            // 加载指定文件到模板
            '#{\s*load\s+"([\$\-_\/\w\.]+)"\s*}#Uis',
            '#{\s*load\s+([\$\-_\/\w\.]+)\s*}#Uis',
            // php标签
            '#{php\s+(.+?)}#is',
            // list标签
            '#{list\s+(.+?)return=(.+?)\s?}#i',
            '#{list\s+(.+?)\s?}#i',
            '#{\s?\/list\s?}#i',
            // if判断语句
            '#{\s?if\s+(.+?)\s?}#i',
            '#{\s?else\sif\s+(.+?)\s?}#i',
            '#{\s?elseif\s+(.+?)\s?}#i',
            '#{\s?else\s?}#i',
            '#{\s?\/if\s?}#i',
            // 循环语句
            '#{\s?loop\s+\$(.+?)\s+\$(\w+?)\s?\$(\w+?)\s?}#i',
            '#{\s?loop\s+\$(.+?)\s+\$(\w+?)\s?}#i',
            '#{\s?loop\s+\$(.+?)\s+\$(\w+?)\s?=>\s?\$(\w+?)\s?}#i',
            '#{\s?\/loop\s?}#i',
            // for
            '#{for\s+(.+?)\s+(.+?)\s+(.+?)\s?}#i',
            '#{\s?\/for\s?}#i',
            // 结束标记
            '#{\s?php\s?}#i',
            '#{\s?\/php\s?}#i',
            '#\?\>\s*\<\?php\s#s',
        ];

        // 替换直接变量输出
        $replace_array = [
            "<?php echo \$\\1['\\2']['\\3']['\\4']; ?>",
            "<?php echo \$\\1['\\2']['\\3']; ?>",
            "<?php echo \$\\1['\\2']; ?>",
            "\$\\1['\\2']['\\3']['\\4']",
            "\$\\1['\\2']['\\3']",
            "\$\\1['\\2']",
            "<?php echo \\1(\\2); ?>",
            "<?php echo \\1; ?>",
            "<?php echo \$\\1; ?>",
            "<?php echo \\Phpcmf\\Service::L('\\1')->\\2(\\3); ?>",
            //"<?php echo \$this->library_method(\"\\1\",\"\\2\", \$this->_get_method(\\3));,
            "<?php if (\$fn_include = \$this->_include(\"\\1\", \"\\2\")) include(\$fn_include); ?>",
            "<?php if (\$fn_include = \$this->_include(\"\\1\", \"MOD_DIR\")) include(\$fn_include); ?>",
            "<?php if (\$fn_include = \$this->_include(\"\\1\")) include(\$fn_include); ?>",
            "<?php if (\$fn_include = \$this->_include(\"\\1\")) include(\$fn_include); ?>",
            "<?php if (\$fn_include = \$this->_load(\"\\1\")) include(\$fn_include); ?>",
            "<?php if (\$fn_include = \$this->_load(\"\\1\")) include(\$fn_include); ?>",
            "<?php \\1 ?>",
            "<?php \$return_\\2 = [];\$list_return_\\2 = \$this->list_tag(\"\\1 return=\\2\"); if (\$list_return_\\2) { extract(\$list_return_\\2); \$count_\\2=count(\$return_\\2);} if (is_array(\$return_\\2)) { foreach (\$return_\\2 as \$key_\\2=>\$\\2) {  \$is_first=\$key_\\2==0 ? 1 : 0;\$is_last=\$count_\\2==\$key_\\2+1 ? 1 : 0;  ?>",
            "<?php \$return = [];\$list_return = \$this->list_tag(\"\\1\"); if (\$list_return) { extract(\$list_return); \$count=count(\$return);} if (is_array(\$return)) { foreach (\$return as \$key=>\$t) { \$is_first=\$key==0 ? 1 : 0;\$is_last=\$count==\$key+1 ? 1 : 0; ?>",
            "<?php } } ?>",
            "<?php if (\\1) { ?>",
            "<?php } else if (\\1) { ?>",
            "<?php } else if (\\1) { ?>",
            "<?php } else { ?>",
            "<?php } ?>",
            "<?php if (is_array(\$\\1)) { \$count=count(\$\\1);foreach (\$\\1 as \$\\2=>\$\\3) { ?>",
            "<?php if (is_array(\$\\1)) { \$count=count(\$\\1);foreach (\$\\1 as \$\\2) { ?>",
            "<?php if (is_array(\$\\1)) { \$count=count(\$\\1);foreach (\$\\1 as \$\\2=>\$\\3) { ?>",
            "<?php } } ?>",
            "<?php for (\\1 ; \\2 ; \\3) { ?>",
            "<?php }  ?>",
            "<?php ",
            " ?>",
            " ",
        ];

        // list标签别名
        foreach ($this->action as $name) {
            // 正则表达式匹配的模板标签
            $regex_array[] = '#{'.$name.'\s+(.+?)return=(.+?)\s?}#i';
            $regex_array[] = '#{'.$name.'\s+(.+?)\s?}#i';
            $regex_array[] = '#{\s?\/'.$name.'\s?}#i';
            // 替换直接变量输出
            $replace_array[] = "<?php \$list_return_\\2 = \$this->list_tag(\"action=".$name." \\1 return=\\2\"); if (\$list_return_\\2) extract(\$list_return_\\2, EXTR_OVERWRITE); \$count_\\2=count(\$return_\\2); if (is_array(\$return_\\2)) { foreach (\$return_\\2 as \$key_\\2=>\$\\2) {  \$is_first=\$key_\\2==0 ? 1 : 0;\$is_last=\$count_\\2==\$key_\\2+1 ? 1 : 0; ?>";
            $replace_array[] = "<?php \$list_return = \$this->list_tag(\"action=".$name." \\1\"); if (\$list_return) extract(\$list_return, EXTR_OVERWRITE); \$count=count(\$return); if (is_array(\$return)) { foreach (\$return as \$key=>\$t) { \$is_first=\$key==0 ? 1 : 0;\$is_last=\$count==\$key+1 ? 1 : 0; ?>";
            $replace_array[] = "<?php } } ?>";
        }


        $view_content = preg_replace($regex_array, $replace_array, $view_content);

        $view_content = preg_replace_callback("/_get_var\('(.*)'\)/Ui", function ($match) {
            return "_get_var('".preg_replace('#\[\'(\w+)\'\]#Ui', '.\\1', $match[1])."')";
        }, $view_content);

        $view_content = preg_replace_callback("/list_tag\(\"(.*)\"\)/Ui", function ($match) {
            return "list_tag(\"".preg_replace('#\[\'(\w+)\'\]#Ui', '[\\1]', $match[1])."\")";
        }, $view_content);

        // 替换$ci  IS_PC   IS_MOBILE
        $view_content = str_replace([
            '$ci->',
            'IS_PC',
            'IS_MOBILE',
            'IS_MOBILE2',
        ], [
            '\Phpcmf\Service::C()->',
            '\Phpcmf\Service::IS_PC()',
            '\Phpcmf\Service::IS_MOBILE()',
            '\Phpcmf\Service::IS_MOBILE2()',
        ], $view_content);

        return $view_content;
    }

    // 方法类解析
    public function library_method($class, $method, $param) {

        return call_user_func_array(
            [\Phpcmf\Service::L($class), $method],
            $param
        );
    }

    // 替换方法变量
    public function _get_method(...$params) {

        if (!$params) {
            return [];
        }

        $value = [];
        foreach ($params as $var) {
            if (strpos($var, '$') === 0) {
                $value[] = preg_replace('/\[(.+)\]/U', '[\'\\1\']', $var);
            } else {
                $value[] = $var;
            }
        }

        return $value;
    }


    // list 标签解析
    public function list_tag($_params) {

        $system = [
            'db' => '', // 数据源
            'num' => '', // 显示数量
            'form' => '', // 表单
            'page' => '', // 是否分页
            'site' => '', // 站点id
            'flag' => '', // 推荐位id
            'more' => '', // 是否显示栏目模型表
            'catid' => '', // 栏目id，支持多id
            'field' => '', // 显示字段
            'order' => '', // 排序
            'space' => '', // 空间uid
            'table' => '', // 表名变量
            'total' => '', // 分页总数据
            'join' => '', // 关联表名
            'on' => '', // 关联表条件
            'cache' => SYS_CACHE ? (int)SYS_CACHE_LIST * 3600 : 0, // 默认缓存时间
            'action' => '', // 动作标识
            'return' => '', // 返回变量
            'sbpage' => '', // 不按默认分页
            'module' => MOD_DIR, // 模块名称
            'urlrule' => '', // 自定义分页规则
            'pagesize' => '', // 自定义分页数量
            'pagefile' => '', // 自定义分页配置文件
        ];

        $param = $where = [];

        // 过滤掉自定义where语句
        if (preg_match('/where=\'(.+)\'/sU', $_params, $match)) {
            $param['where'] = $match[1];
            $_params = str_replace($match[0], '', $_params);
        }

        $params = explode(' ', $_params);
        in_array($params[0], $this->action) &&  $params[0] = 'action='.$params[0];

        $sysadj = array('IN', 'BEWTEEN', 'BETWEEN', 'LIKE', 'NOTIN', 'NOT', 'BW');
        foreach ($params as $t) {
            $var = substr($t, 0, strpos($t, '='));
            $val = substr($t, strpos($t, '=') + 1);
            if (!$var) {
                continue;
            }
            $val = defined($val) ? constant($val) : $val;
            if ($var == 'fid' && !$val) {
                continue;
            }
            if (isset($system[$var])) { // 系统参数，只能出现一次，不能添加修饰符
                $system[$var] = dr_safe_replace($val);
            } else {
                if (preg_match('/^([A-Z_]+)(.+)/', $var, $match)) { // 筛选修饰符参数
                    $_pre = explode('_', $match[1]);
                    $_adj = '';
                    foreach ($_pre as $p) {
                        in_array($p, $sysadj) && $_adj = $p;
                    }
                    $where[$match[2]] = array(
                        'adj' => $_adj,
                        'name' => $match[2],
                        'value' => $val
                    );
                } else {
                    $where[$var] = array(
                        'adj' => '',
                        'name' => $var,
                        'value' => $val
                    );
                }
                $param[$var] = $val; // 用于特殊action
            }
        }

        // 替换order中的非法字符
        isset($system['order']) && $system['order'] && $system['order'] = str_ireplace(
            array('"', "'", ')', '(', ';', 'select', 'insert', '`'),
            '',
            $system['order']
        );

        $action = $system['action'];
        // 当hits动作时，定位到moule动作
        $system['action'] == 'hits' && $system['action'] = 'module';
        // 默认站点参数
        $system['site'] = !$system['site'] ? SITE_ID : $system['site'];
        // 默认模块参数
        $system['module'] = $dirname = $system['module'] ? $system['module'] : MOD_DIR;

        // action
        switch ($system['action']) {

            case 'cache': // 系统缓存数据

                if (!isset($param['name'])) {
                    return $this->_return($system['return'], 'name参数不存在');
                }

                $pos = strpos($param['name'], '.');
                if ($pos !== FALSE) {
                    $_name = substr($param['name'], 0, $pos);
                    $_param = substr($param['name'], $pos + 1);
                } else {
                    $_name = $param['name'];
                    $_param = NULL;
                }

                $cache = $this->_cache_var($_name, $system['site']);
                if (!$cache) {
                    return $this->_return($system['return'], "缓存({$_name})不存在，请在后台更新缓存");
                } elseif ($_name == 'module-content' && $system['more']) {
                    // 读取内容模块更多信息
                    foreach ($cache as $i => $t) {
                        $cache[$i] = \Phpcmf\Service::L('cache')->get('module-'.$system['site'].'-'.$t['dirname']);
                    }
                }

                if ($_param) {
                    $data = [];
                    @eval('$data=$cache'.$this->_get_var($_param).';');
                    if (!$data) {
                        return $this->_return($system['return'], "缓存({$_name})参数不存在!!");
                    }
                } else {
                    $data = $cache;
                }

                return $this->_return($system['return'], $data, '');
                break;

            case 'category': // 栏目

                if (!$dirname) {
                    return $this->_return($system['return'], 'module参数不能为空');
                }
                $module = \Phpcmf\Service::L('cache')->get('module-'.$system['site'].'-'.$dirname);
                if (!$module) {
                    return $this->_return($system['return'], "模块({$dirname})尚未安装");
                } elseif (!$module['category']) {
                    return $this->_return($system['return'], "模块({$dirname})没有栏目数据");
                }

                $i = 0;
                $show = isset($param['show']) ? 1 : 0; // 有show参数表示显示隐藏栏目
                $return = array();
                foreach ($module['category'] as $t) {
                    if ($system['num'] && $i >= $system['num']) {
                        break;
                    } elseif (!$t['show'] && !$show) {
                        continue;
                    } elseif (isset($param['pid']) && $t['pid'] != (int)$param['pid']) {
                        continue;
                    } elseif (isset($param['mid']) && $t['mid'] != $param['mid']) {
                        continue;
                    } elseif (isset($param['child']) && $t['child'] != (int)$param['child']) {
                        continue;
                    } elseif (isset($param['id']) && !in_array($t['id'], explode(',', $param['id']))) {
                        continue;
                    } elseif (isset($system['more']) && !$system['more']) {
                        unset($t['field'], $t['setting']);
                    }
                    $t['url'] = dr_url_prefix($t['url'], $dirname);
                    $return[] = $t;
                    $i ++;
                }

                if (!$return) {
                    return $this->_return($system['return'], '没有匹配到内容');
                }

                return $this->_return($system['return'], $return, '');
                break;

            case 'linkage': // 联动菜单

                $linkage = \Phpcmf\Service::L('cache')->get('linkage-'.$system['site'].'-'.$param['code']);
                if (!$linkage) {
                    return $this->_return($system['return'], "联动菜单{$param['code']}不存在，请在后台更新缓存");
                }

                // 通过别名找id
                $ids = @array_flip(\Phpcmf\Service::C()->get_cache('linkage-'.$system['site'].'-'.$param['code'].'-id'));
                if (isset($param['pid'])) {
                    if (is_numeric($param['pid'])) {
                        $pid = intval($param['pid']);
                    } elseif (!$param['pid']) {
                        $pid = 0;
                    } else {
                        $pid = isset($ids[$param['pid']]) ? $ids[$param['pid']] : 0;
                        !$pid && is_numeric($param['pid']) && \Phpcmf\Service::C()->get_cache('linkage-'.$system['site'].'-'.$param['code'].'-id', $param['pid']) && $pid = intval($param['pid']);
                    }
                }

                $i = 0;
                $return = array();
                foreach ($linkage as $t) {
                    if ($system['num'] && $i >= $system['num']) {
                        break;
                    } elseif (isset($param['pid']) && $t['pid'] != $pid) {
                        continue;
                    } elseif (isset($param['id']) && !in_array($t['id'], explode(',', $param['id']))) {
                        continue;
                    }
                    $return[] = $t;
                    $i ++;
                }

                if (!$return && isset($param['pid'])) {
                    $rpid = isset($param['fid']) ? (int)$ids[$param['fid']] : (int)$linkage[$param['pid']]['pid'];
                    foreach ($linkage as $t) {
                        if ($t['pid'] == $rpid) {
                            if ($system['num'] && $i >= $system['num']) {
                                break;
                            }
                            if (isset($param['id']) && !in_array($t['id'], explode(',', $param['id']))) {
                                continue;
                            }
                            $return[] = $t;
                            $i ++;
                        }
                    }
                    if (!$return) {
                        return $this->_return($system['return'], '没有匹配到内容');
                    }
                }

                return $this->_return($system['return'], isset($param['call']) && $param['call'] ? @array_reverse($return) : $return, '');
                break;

            case 'category_search_field': // 栏目搜索字段筛选

                $catid = $system['catid'];
                $module = \Phpcmf\Service::L('cache')->get('module-'.$system['site'].'-'.$dirname);
                if (!$module) {
                    return $this->_return($system['return'], "模块({$dirname})未安装");
                } elseif (dr_count($module['category'][$catid]['field']) == 0) {
                    return $this->_return($system['return'], '模块未安装或者此栏目无模型字段');
                }

                $return = [];
                foreach ($module['category'][$catid]['field'] as $t) {
                    $data = dr_format_option_array($t['setting']['option']['options']);
                    if ($t['issearch'] && $t['ismain'] && in_array($t['fieldtype'], ['Select', 'Radio']) && $data) {
                        $list = [];
                        foreach ($data as $value => $name) {
                            $name && !is_null($value) && $list[] = array(
                                'name' => trim($name),
                                'value' => trim($value)
                            );
                        }
                        $list && $return[] = array(
                            'name' => $t['name'],
                            'field' => $t['fieldname'],
                            'data' => $list,
                        );
                    }
                }

                return $this->_return($system['return'], $return, '');
                break;

            case 'navigator': // 网站导航

                $navigator = \Phpcmf\Service::C()->get_cache('navigator-'.$system['site']); // 导航缓存
                if (!$navigator) {
                    return $this->_return($system['return'], '导航数据为空');
                }

                $i = 0;
                $show = isset($param['show']) ? 1 : 0; // 有show参数表示显示隐藏栏目
                $data = $navigator[(int)$param['type']];
                if (!$data) {
                    // 没有查询到内容
                    return $this->_return($system['return'], '没有查询到内容');
                }

                $field = \Phpcmf\Service::L('cache')->get('navigator-'.$system['site'].'-field');
                $return = [];
                foreach ($data as $t) {
                    if ($system['num'] && $i >= $system['num']) {
                        break;
                    } elseif (isset($param['pid']) && $t['pid'] != (int)$param['pid']) {
                        continue;
                    } elseif (isset($param['id']) && $t['id'] != (int)$param['id']) {
                        continue;
                    } elseif (!$t['show'] && !$show) {
                        continue;
                    }
                    $return[] = \Phpcmf\Service::L('Field')->format_value($field, $t, 1);
                    $i ++;
                }

                if (!$return) {
                    return $this->_return($system['return'], '没有匹配到内容');
                }

                return $this->_return($system['return'], $return, '');
                break;

            case 'page': // 自定义页调用

                $data = \Phpcmf\Service::C()->get_cache('page-'.$system['site'], 'data'); // 单页缓存
                if (!$data) {
                    return $this->_return($system['return'], '没有查询到内容');
                }

                $i = 0;
                $show = isset($param['show']) ? 1 : 0; // 有show参数表示显示隐藏栏目
                $field = \Phpcmf\Service::L('cache')->get('page-'.$system['site'].'-field');
                $return = array();
                foreach ($data as $id => $t) {
                    if (!is_numeric($id)) {
                        continue;
                    } elseif ($system['num'] && $i >= $system['num']) {
                        break;
                    } elseif (!$t['show'] && !$show) {
                        continue;
                    } elseif (isset($param['pid']) && $t['pid'] != (int) $param['pid']) {
                        continue;
                    } elseif (isset($param['id']) && !in_array($t['id'], explode(',', $param['id']))) {
                        continue;
                    }
                    $t['setting'] = dr_string2array($t['setting']);
                    $return[] = \Phpcmf\Service::L('Field')->format_value($field, $t, 1);
                    $i ++;
                }

                if (!$return) {
                    return $this->_return($system['return'], '没有匹配到内容');
                }

                return $this->_return($system['return'], $return, '');
                break;

            case 'tag': // 调用tag


                // aninstall
                if (!dr_is_app('tag')) {
                    return $this->_return($system['return'], '没有安装Tag关键词库插件');
                }

                $system['order'] = dr_safe_replace($system['order'] ? ($system['order'] == 'rand' ? 'RAND()' : $system['order']) : 'displayorder asc');

                $table = \Phpcmf\Service::M()->dbprefix($system['site'].'_tag'); // tag表
                $where = '';
                if ($param['tag']) {
                    $in = $tag = $sql = [];
                    $array = explode(',', $param['tag']);
                    foreach ($array as $name) {
                        $name && $sql[] = '`name`="'.dr_safe_replace($name).'"';
                    }
                    $sql && $tag = $this->_query("SELECT code FROM {$table} WHERE ".implode(' OR ', $sql), $system['db'], $system['cache']);
                    if ($tag) {
                        $cache = \Phpcmf\Service::C()->get_cache('tag-'.$system['site']); // tag缓存
                        foreach ($tag as $t) {
                            if ($cache[$t['code']]['childids']) {
                                foreach ($cache[$t['code']]['childids'] as $i) {
                                    $in[] = $i;
                                }
                            }
                        }
                    }
                    $in && $where = 'WHERE id IN ('.implode(',', $in).')';
                }

                $sql = "SELECT * FROM {$table} {$where} ORDER BY ".$system['order']." LIMIT ".($system['num'] ? $system['num'] : 10);
                $data = $this->_query($sql, $system['db'], $system['cache']);

                // 没有查询到内容
                if (!$data) {
                    return $this->_return($system['return'], '没有查询到内容', $sql);
                }

                // 缓存查询结果
                $name = 'list-action-tag-'.md5($sql);
                $cache = \Phpcmf\Service::L('cache')->get_data($name);

                if (!$cache) {
                    foreach ($data as $i => $t) {
                        // 读缓存
                        $data[$i]['url'] = '/';
                        $file = WRITEPATH.'tags/'.md5(SITE_ID.'-'.$t['name']);
                        if ($file) {
                            $data[$i]['url'] = file_get_contents($file);
                        }
                    }
                    $cache = $system['cache'] ? \Phpcmf\Service::L('cache')->set_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql);
                break;

            case 'sql': // 直接sql查询

                if (preg_match('/sql=\'(.+)\'/sU', $_params, $sql)) {

                    // 替换前缀
                    $sql = str_replace(
                        array('@#S', '@#'),
                        array(\Phpcmf\Service::M()->dbprefix.$system['site'], \Phpcmf\Service::M()->dbprefix),
                        trim($sql[1])
                    );
                    stripos($sql, 'SELECT+') === 0 && $sql = urldecode($sql);
                    if (stripos($sql, 'SELECT') !== 0) {
                        return $this->_return($system['return'], 'SQL语句只能是SELECT查询语句');
                    } elseif (preg_match('/select(.*)into outfile(.*)/i', $sql)) {
                        return $this->_return($system['return'], 'SQL语句只能是SELECT查询语句');
                    } elseif (preg_match('/select(.*)into dumpfile(.*)/i', $sql)) {
                        return $this->_return($system['return'], 'SQL语句只能是SELECT查询语句');
                    }

                    $total = 0;
                    $pages = '';


                    // 如存在分页条件才进行分页查询
                    if ($system['page'] && $system['urlrule']) {
                        $page = max(1, (int)$_GET['page']);
                        $row = $this->_query(preg_replace('/select .* from /iUs', 'SELECT count(*) as c FROM ', $sql), $system['db'], $system['cache'], FALSE);
                        $total = (int)$row['c'];
                        $pagesize = $system['pagesize'] ? $system['pagesize'] : 10;
                        // 没有数据时返回空
                        if (!$total) {
                            return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                        }
                        $sql.= ' LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                        $pages = $this->_get_pagination($system['urlrule'], $pagesize, $total, $system['pagefile']);
                    }

                    $data = $this->_query($sql, $system['db'], $system['cache']);

                    return $this->_return($system['return'], $data, $sql, $total, $pages, $pagesize);
                } else {
                    return $this->_return($system['return'], '参数不正确，SQL语句必须用单引号包起来'); // 没有查询到内容
                }
                break;

            case 'table': // 表名查询

                if (!$system['table']) {
                    return $this->_return($system['return'], 'table参数不存在');
                }

                $tableinfo = \Phpcmf\Service::L('cache')->get_data('table-'.$system['table']);
                if (!$tableinfo) {
                    $tableinfo = \Phpcmf\Service::M('Table')->get_field($system['table']);
                    \Phpcmf\Service::L('cache')->set_data('table-'.$system['table'], $tableinfo, 36000);
                }
                if (!$tableinfo) {
                    return $this->_return($system['return'], '表('.$system['table'].')结构不存在');
                }


                // 是否操作自定义where
                if ($param['where']) {
                    $where[] = [
                        'adj' => 'SQL',
                        'value' => urldecode($param['where'])
                    ];
                    unset($param['where']);
                }

                $table = \Phpcmf\Service::M()->dbprefix($system['table']);
                $where = $this->_set_where_field_prefix($where, $tableinfo, $table); // 给条件字段加上表前缀
                $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo, $table); // 给显示字段加上表前缀
                $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo, $table); // 给排序字段加上表前缀

                $total = 0;
                $sql_from = $table; // sql的from子句

                // 关联表
                if ($system['join'] && $system['on']) {
                    $table2 = \Phpcmf\Service::M()->dbprefix($system['join']); // 关联表
                    $tableinfo2 = \Phpcmf\Service::L('cache')->get_data('table-'.$system['join']);
                    if (!$tableinfo2) {
                        \Phpcmf\Service::M('Table')->get_field($system['join']);
                        \Phpcmf\Service::L('cache')->set_data('table-'.$system['join'], $tableinfo, 36000);
                    }
                    if (!$tableinfo2) {
                        return $this->_return($system['return'], '关联数据表('.$system['join'].')结构不存在');
                    }
                    list($a, $b) = explode(',', $system['on']);
                    $b = $b ? $b : $a;
                    $sql_from.= ' LEFT JOIN '.$table2.' ON `'.$table.'`.`'.$a.'`=`'.$table2.'`.`'.$b.'`';
                }

                $sql_limit = $pages = '';
                $sql_where = $this->_get_where($where); // sql的where子句

                if ($system['page'] && $system['urlrule']) {
                    $page = max(1, (int)$_GET['page']);
                    $urlrule = $system['urlrule'];
                    $pagesize = (int) $system['pagesize'];
                    $pagesize = $pagesize ? $pagesize : 10;
                    $sql = "SELECT count(*) as c FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ORDER BY NULL";
                    $row = $this->_query($sql, $system['db'], $system['cache'], FALSE);
                    $total = (int)$row['c'];
                    // 没有数据时返回空
                    if (!$total) {
                        return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                    }
                    $sql_limit = 'LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                    $pages = $this->_get_pagination($urlrule, $pagesize, $total, $system['pagefile']);
                } elseif ($system['num']) {
                    $sql_limit = "LIMIT {$system['num']}";
                }

                $sql = "SELECT ".($system['field'] ? $system['field'] : "*")." FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ".($system['order'] ? "ORDER BY {$system['order']}" : "")." $sql_limit";
                $data = $this->_query($sql, $system['db'], $system['cache']);

                // 缓存查询结果
                $name = 'list-action-table-'.md5($sql);
                $cache = \Phpcmf\Service::L('cache')->get_data($name);
                if (!$cache && is_array($data)) {
                    $cache = $system['cache'] ? \Phpcmf\Service::L('cache')->set_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql, $total, $pages, $pagesize);
                break;


            case 'form': // 表单调用

                $mid = $system['form'];
                // 表单参数为数字时按id读取
                $form = \Phpcmf\Service::C()->get_cache('form-'.$system['site'], $mid);
                // 判断是否存在
                if (!$form) {
                    return $this->_return($system['return'], "表单($mid)不存在"); // 参数判断
                }

                // 表结构缓存
                $tableinfo = \Phpcmf\Service::L('cache')->get('table-'.$system['site']);
                if (!$tableinfo) {
                    // 没有表结构缓存时返回空
                    return $this->_return($system['return'], '表结构缓存不存在');
                }
                $table = \Phpcmf\Service::M()->dbprefix($system['site'].'_form_'.$form['table']); // 主表
                if (!isset($tableinfo[$table])) {
                    return $this->_return($system['return'], '表（'.$table.'）结构缓存不存在');
                }

                // 默认条件
                $where[] = array(
                    'adj' => '',
                    'name' => 'status',
                    'value' => 1
                );

                // 是否操作自定义where
                if ($param['where']) {
                    $where[] = [
                        'adj' => 'SQL',
                        'value' => urldecode($param['where'])
                    ];
                    unset($param['where']);
                }

                // 将catid作为普通字段
                if (isset($system['catid']) && $system['catid']) {
                    $where[] = array(
                        'adj' => '',
                        'name' => 'catid',
                        'value' => $system['catid']
                    );
                }

                $fields = $form['field'];
                $system['order'] = !$system['order'] ? 'inputtime_desc' : $system['order']; // 默认排序参数
                $where = $this->_set_where_field_prefix($where, $tableinfo[$table], $table, $fields); // 给条件字段加上表前缀
                $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table], $table); // 给显示字段加上表前缀
                $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$table], $table); // 给排序字段加上表前缀

                $total = 0;
                $sql_from = $table; // sql的from子句
                $sql_limit = $pages = '';
                $sql_where = $this->_get_where($where); // sql的where子句

                if ($system['page'] && $system['urlrule']) {
                    $page = max(1, (int)$_GET['page']);
                    $pagesize = (int) $system['pagesize'];
                    $pagesize = $pagesize ? $pagesize : 10;
                    $sql = "SELECT count(*) as c FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ORDER BY NULL";
                    $row = $this->_query($sql, $system['db'], $system['cache'], FALSE);
                    $total = (int)$row['c'];
                    // 没有数据时返回空
                    if (!$total) {
                        return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                    }
                    $sql_limit = 'LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                    $pages = $this->_get_pagination($system['urlrule'], $pagesize, $total, $system['pagefile']);
                } elseif ($system['num']) {
                    $sql_limit = "LIMIT {$system['num']}";
                }

                $sql = "SELECT ".($system['field'] ? $system['field'] : "*")." FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ".($system['order'] ? "ORDER BY {$system['order']}" : "")." $sql_limit";
                $data = $this->_query($sql, $system['db'], $system['cache']);

                // 缓存查询结果
                $name = 'list-action-form-'.md5($sql);
                $cache = \Phpcmf\Service::L('cache')->get_data($name);
                if (!$cache && is_array($data)) {
                    // 表的系统字段
                    $fields['inputtime'] = array('fieldtype' => 'Date');
                    $dfield = \Phpcmf\Service::L('Field')->app('form');
                    foreach ($data as $i => $t) {
                        $data[$i] = $dfield->format_value($fields, $t, 1, 'form');
                    }
                    $cache = $system['cache'] ? \Phpcmf\Service::L('cache')->set_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql, $total, $pages, $pagesize);
                break;

            case 'mform': // 模块表单调用

                $mid = $system['form'];
                $form = \Phpcmf\Service::L('cache')->get('module-'.$system['site'].'-'.$dirname, 'form', $mid);
                // 判断是否存在
                if (!$form) {
                    return $this->_return($system['return'], "模块{$dirname}表单({$mid})不存在"); // 参数判断
                }

                $tableinfo = \Phpcmf\Service::L('cache')->get('table-'.$system['site']);
                if (!$tableinfo) {
                    // 没有表结构缓存时返回空
                    return $this->_return($system['return'], '表结构缓存不存在');
                }

                $table = \Phpcmf\Service::M()->dbprefix($system['site'].'_'.$dirname.'_form_'.$form['table']); // 模块主表
                if (!isset($tableinfo[$table])) {
                    return $this->_return($system['return'], '表（'.$table.'）结构缓存不存在');
                }

                // 默认条件
                $where[] = array(
                    'adj' => '',
                    'name' => 'status',
                    'value' => 1
                );

                // 是否操作自定义where
                if ($param['where']) {
                    $where[] = [
                        'adj' => 'SQL',
                        'value' => urldecode($param['where'])
                    ];
                    unset($param['where']);
                }


                $fields = $form['fields'];
                $system['order'] = !$system['order'] ? 'inputtime_desc' : $system['order']; // 默认排序参数

                $where = $this->_set_where_field_prefix($where, $tableinfo[$table], $table, $fields); // 给条件字段加上表前缀
                $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table], $table); // 给显示字段加上表前缀
                $system['order'] = $this->_set_order_field_prefix($system['order'], $tableinfo[$table], $table); // 给排序字段加上表前缀

                $total = 0;
                $fields = $form['field']; // 主表的字段
                $sql_from = $table; // sql的from子句
                $sql_where = $this->_get_where($where); // sql的where子句
                $sql_limit = $pages = '';

                if ($system['page'] && $system['urlrule']) {
                    $page = max(1, (int)$_GET['page']);
                    $pagesize = (int) $system['pagesize'];
                    $pagesize = $pagesize ? $pagesize : 10;
                    $sql = "SELECT count(*) as c FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ORDER BY NULL";
                    $row = $this->_query($sql, $system['db'], $system['cache'], FALSE);
                    $total = (int)$row['c'];
                    // 没有数据时返回空
                    if (!$total) {
                        return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                    }
                    $sql_limit = 'LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                    $pages = $this->_get_pagination($system['urlrule'], $pagesize, $total, $system['pagefile']);
                } elseif ($system['num']) {
                    $sql_limit = "LIMIT {$system['num']}";
                }

                $sql = "SELECT ".($system['field'] ? $system['field'] : "*")." FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ".($system['order'] ? "ORDER BY {$system['order']}" : "")." $sql_limit";
                $data = $this->_query($sql, $system['db'], $system['cache']);

                // 缓存查询结果
                $name = 'list-action-mform-'.md5($sql);
                $cache = \Phpcmf\Service::L('cache')->get_data($name);
                if (!$cache && is_array($data)) {
                    // 表的系统字段
                    $fields['inputtime'] = array('fieldtype' => 'Date');
                    $dfield = \Phpcmf\Service::L('Field')->app($dirname);
                    foreach ($data as $i => $t) {
                        $data[$i] = $dfield->format_value($fields, $t, 1, 'form');
                    }
                    $cache = $system['cache'] ? \Phpcmf\Service::L('cache')->set_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql, $total, $pages, $pagesize);
                break;

            case 'member': // 会员信息

                $tableinfo = \Phpcmf\Service::L('cache')->get('table-'.$system['site']);
                if (!$tableinfo) {
                    // 没有表结构缓存时返回空
                    return $this->_return($system['return'], '表结构缓存不存在');
                }

                $table = \Phpcmf\Service::M()->dbprefix('member'); // 主表
                if (!isset($tableinfo[$table])) {
                    return $this->_return($system['return'], '表（'.$table.'）结构缓存不存在');
                }

                $fields = \Phpcmf\Service::C()->get_cache('member', 'field');


                // 是否操作自定义where
                if ($param['where']) {
                    $where[] = [
                        'adj' => 'SQL',
                        'value' => urldecode($param['where'])
                    ];
                    unset($param['where']);
                }

                // groupid查询
                if (isset($param['groupid']) && $param['groupid']) {

                    if (strpos($param['groupid'], ',') !== false) {
                        $gwhere = ' `'.$table.'`.`id` in (select uid from `'.$table.'_group_index` where `gid` = in ('.dr_safe_replace($param['groupid']).'))';
                    } elseif (strpos($param['groupid'], '-') !== false) {
                        $arr = explode('-', $param['groupid']);
                        $gwhere = [];
                        foreach ($arr as $t) {
                            $t = intval($t);
                            $t && $gwhere[] = ' `'.$table.'`.`id` in (select uid from `'.$table.'_group_index` where `gid` = '. $t.')';
                        }
                        $gwhere = $gwhere ? '('.implode(' AND ', $gwhere).')' : '';
                    } else {
                        $gwhere = ' `'.$table.'`.`id` in (select uid from `'.$table.'_group_index` where `gid` = '. intval($param['groupid']).')';
                    }

                    $gwhere && $where['id'] = [
                        'adj' => 'SQL',
                        'name' => 'id',
                        'value' => $gwhere
                    ];
                    unset($param['groupid']);
                }

                $system['order'] = !$system['order'] ? 'id' : $system['order']; // 默认排序参数

                $where = $this->_set_where_field_prefix($where, $tableinfo[$table], $table, $fields); // 给条件字段加上表前缀
                $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table], $table); // 给显示字段加上表前缀

                // 多表组合排序
                $_order = [];
                $_order[$table] = $tableinfo[$table];

                $sql_from = $table; // sql的from子句

                if ($system['more']) {
                    // 会员附表
                    $more = \Phpcmf\Service::M()->dbprefix('member_data'); // 附表
                    $where = $this->_set_where_field_prefix($where, $tableinfo[$more], $more, $fields); // 给条件字段加上表前缀
                    $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$more], $more); // 给显示字段加上表前缀
                    $_order[$more] = $tableinfo[$more];
                    $sql_from.= " LEFT JOIN $more ON `$table`.`id`=`$more`.`id`"; // sql的from子句
                }

                $total = 0;
                $sql_limit = '';
                $sql_where = $this->_get_where($where); // sql的where子句


                $system['order'] = $this->_set_orders_field_prefix($system['order'], $_order); // 给排序字段加上表前缀

                if ($system['page'] && $system['urlrule']) { // 如存在分页条件才进行分页查询
                    $page = max(1, (int)$_GET['page']);
                    $pagesize = (int) $system['pagesize'];
                    $pagesize = $pagesize ? $pagesize : 10;
                    $sql = "SELECT count(*) as c FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ORDER BY NULL";
                    $row = $this->_query($sql, $system['db'], $system['cache'], FALSE);
                    $total = (int)$row['c'];
                    if (!$total) {
                        // 没有数据时返回空
                        return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                    }
                    $sql_limit = ' LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                    $pages = $this->_get_pagination($system['urlrule'], $pagesize, $total, $system['pagefile']);
                } elseif ($system['num']) {
                    $sql_limit = "LIMIT {$system['num']}";
                }

                $sql = "SELECT ".($system['field'] ? $system['field'] : "*")." FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ".($system['order'] == "null" || !$system['order'] ? "" : " ORDER BY {$system['order']}")." $sql_limit";
                $data = $this->_query($sql, $system['db'], $system['cache']);

                // 缓存查询结果
                $name = 'list-action-member-'.md5($sql);
                $cache = \Phpcmf\Service::L('cache')->get_data($name);
                if (!$cache && is_array($data)) {
                    // 系统字段
                    $fields['regtime'] = array('fieldtype' => 'Date');
                    // 格式化显示自定义字段内容
                    $dfield = \Phpcmf\Service::L('Field')->app('member');
                    foreach ($data as $i => $t) {
                        $data[$i] = $dfield->format_value($fields, $t, 1, 'member');
                    }
                    $cache = $system['cache'] ? \Phpcmf\Service::L('cache')->set_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache, $sql, $total, $pages, $pagesize);
                break;


            case 'content': // 模块内容详细页面

                $module = \Phpcmf\Service::L('cache')->get('module-'.$system['site'].'-'.$dirname);
                if (!$module) {
                    return $this->_return($system['return'], "模块({$dirname})未安装");
                }

                $tableinfo = \Phpcmf\Service::L('cache')->get('table-'.$system['site']);
                if (!$tableinfo) {
                    // 没有表结构缓存时返回空
                    return $this->_return($system['return'], '表结构缓存不存在');
                }

                $table = \Phpcmf\Service::M()->dbprefix($system['site'].'_'.$module['dirname']); // 模块主表`
                if (!isset($tableinfo[$table])) {
                    return $this->_return($system['return'], '表（'.$table.'）结构缓存不存在');
                }

                // 初始化数据表
                $db = \Phpcmf\Service::M('Content', $dirname);
                $db->_init($dirname, $system['site']);
                $data = $db->get_data(intval($param['id']));

                // 缓存查询结果
                $name = 'list-action-content-'.md5($table.$param['id'].$this->_is_mobile);
                $cache = \Phpcmf\Service::L('cache')->get_data($name);
                if (!$cache && is_array($data)) {
                    // 模块表的系统字段
                    $fields = $module['field']; // 主表的字段
                    $fields['inputtime'] = array('fieldtype' => 'Date');
                    $fields['updatetime'] = array('fieldtype' => 'Date');
                    // 格式化显示自定义字段内容
                    $dfield = \Phpcmf\Service::L('Field')->app($module['dirname']);
                    $data['url'] = dr_url_prefix($data['url'], $dirname);
                    $data = $dfield->format_value($fields, $data, 1, $module['dirname']);
                    $cache = $system['cache'] ? \Phpcmf\Service::L('cache')->set_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], [$cache ? $cache : $data]);
                break;

            case 'related': // 模块的相关文章

                if (!$param['tag']) {
                    return $this->_return($system['return'], '没有查询到内容'); // 没有查询到内容
                }

                $table = \Phpcmf\Service::M()->dbprefix($system['site'].'_'.$dirname); // 模块主表

                $sql = [];
                $array = explode(',', $param['tag']);
                foreach ($array as $name) {
                    $name && $sql[] = '(`'.$table.'`.`title` LIKE "%'.dr_safe_replace($name).'%" OR `'.$table.'`.`keywords` LIKE "%'.dr_safe_replace($name).'%")';
                }
                $sql = $where[] = [
                    'adj' => 'SQL',
                    'value' => '('.implode(' OR ', $sql).')'
                ];
                unset($param['tag']);
                // 跳转到module方法
                goto module;
                break;

            case 'search': // 模块的搜索

                $total = (int)$system['total'];
                unset($system['total']);

                // 没有数据时返回空
                if (!$total) {
                    return $this->_return($system['return'], 'total参数为空', '', 0);
                } elseif (!$dirname) {
                    return $this->_return($system['return'], 'module参数不能为空');
                } elseif (!$param['id']) {
                    return $this->_return($system['return'], 'id参数为空', '', 0);
                }

                if ($where) {
                    foreach ($where as $i => $t) {
                        if ($t['name'] == 'id') {
                            unset($where[$i]);
                        }
                    }
                }

                $table = \Phpcmf\Service::M()->dbprefix($system['site'].'_'.$dirname); // 模块主表
                $index = $this->_query('SELECT `contentid` FROM `'.$table.'_search` WHERE `id`="'.$param['id'].'"', $system['db'], $system['cache'], 0);
                $where[] = [
                    'adj' => 'SQL',
                    'value' => '(`'.$table.'`.`id` IN('.($index ? $index['contentid'] : 0).'))'
                ];
                unset($param['id']);

                $system['sbpage'] = 1;

                // 跳转到module方法
                goto module;
                break;

            case 'module': // 模块数据

                module:

                $module = \Phpcmf\Service::L('cache')->get('module-'.$system['site'].'-'.$dirname);
                if (!$module) {
                    return $this->_return($system['return'], "模块({$dirname})未安装");
                }

                $tableinfo = \Phpcmf\Service::L('cache')->get('table-'.$system['site']);
                if (!$tableinfo) {
                    // 没有表结构缓存时返回空
                    return $this->_return($system['return'], '表结构缓存不存在');
                }

                $table = \Phpcmf\Service::M()->dbprefix($system['site'].'_'.$module['dirname']); // 模块主表`

                if (!isset($tableinfo[$table])) {
                    return $this->_return($system['return'], '表（'.$table.'）结构缓存不存在');
                }

                // 是否操作自定义where
                if ($param['where']) {
                    $where[] = [
                        'adj' => 'SQL',
                        'value' => urldecode($param['where'])
                    ];
                    unset($param['where']);
                }


                // 排序操作
                if (!$system['order'] && $where[0]['adj'] == 'IN' && $where[0]['name'] == 'id') {
                    // 按id序列来排序
                    $system['order'] = strlen($where[0]['value']) < 10000 && $where[0]['value'] ? 'instr("'.$where[0]['value'].'", `'.$table.'`.`id`)' : 'NULL';
                } else {
                    // 默认排序参数
                    !$system['order'] && ($system['order'] = $system['flag'] ? 'updatetime_desc' : ($action == 'hits' ? 'hits' : 'updatetime'));
                }

                // 栏目筛选
                if ($system['catid']) {
                    if (strpos($system['catid'], ',') !== FALSE) {
                        $temp = @explode(',', $system['catid']);
                        if ($temp) {
                            $catids = array();
                            foreach ($temp as $i) {
                                $catids = $module['category'][$i]['child'] ? array_merge($catids, $module['category'][$i]['catids']) : array_merge($catids, array($i));
                            }
                            $catids && $where[] = array(
                                'adj' => 'IN',
                                'name' => 'catid',
                                'value' => implode(',', $catids),
                            );
                            unset($catids);
                        }
                        unset($temp);
                    } elseif ($module['category'][$system['catid']]['child']) {
                        $where[] = array(
                            'adj' => 'IN',
                            'name' => 'catid',
                            'value' => $module['category'][$system['catid']]['childids']
                        );
                    } else {
                        $where[] = array(
                            'adj' => '',
                            'name' => 'catid',
                            'value' => (int)$system['catid']
                        );
                    }
                }

                $fields = $module['field']; // 主表的字段
                $where[] = array( 'adj' => '', 'name' => 'status', 'value' => 9);
                $where = $this->_set_where_field_prefix($where, $tableinfo[$table], $table, $fields); // 给条件字段加上表前缀
                $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table], $table); // 给显示字段加上表前缀


                // 多表组合排序
                $_order = [];
                $_order[$table] = $tableinfo[$table];

                // sql的from子句
                if ($action == 'hits') {
                    $sql_from = '`'.$table.'` LEFT JOIN `'.$table.'_hits` ON `'.$table.'`.`id`=`'.$table.'_hits`.`id`';
                    $table_more = $table.'_hits'; // hits表
                    $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table_more], $table_more); // 给显示字段加上表前缀
                    $_order[$table_more] = $tableinfo[$table_more];
                    if (!$system['field']) {
                        $system['field'] = '`'.$table.'`.*,`'.$table.'_hits`.`hits`,`'.$table.'_hits`.`day_hits`,`'.$table.'_hits`.`week_hits`,`'.$table.'_hits`.`month_hits`,`'.$table.'_hits`.`year_hits`';
                    }
                } else {
                    $sql_from = '`'.$table.'`';
                }

                // 关联栏目模型表
                if ($system['more']) {
                    $_catid = (int)$system['catid'];
                    if (isset($module['category'][$_catid]['field']) && $module['category'][$_catid]['field']) {
                        $fields = array_merge($fields, $module['category'][$_catid]['field']);
                        $table_more = $table.'_category_data'; // 栏目模型表
                        $where = $this->_set_where_field_prefix($where, $tableinfo[$table_more], $table_more, $fields); // 给条件字段加上表前缀
                        $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table_more], $table_more); // 给显示字段加上表前缀
                        $_order[$table_more] = $tableinfo[$table_more];
                        $sql_from.= " LEFT JOIN $table_more ON `$table_more`.`id`=`$table`.`id`"; // sql的from子句
                    }
                }

                // 关联表
                if ($system['join'] && $system['on']) {
                    $table_more = \Phpcmf\Service::M()->dbprefix($system['join']); // 关联表
                    if (!$tableinfo[$table_more]) {
                        return $this->_return($system['return'], '关联数据表（'.$table_more.'）不存在');
                    }
                    list($a, $b) = explode(',', $system['on']);
                    $b = $b ? $b : $a;
                    $where = $this->_set_where_field_prefix($where, $tableinfo[$table_more], $table_more); // 给条件字段加上表前缀
                    $system['field'] = $this->_set_select_field_prefix($system['field'], $tableinfo[$table_more], $table_more); // 给显示字段加上表前缀
                    $_order[$table_more] = $tableinfo[$table_more];
                    $sql_from.= ' LEFT JOIN `'.$table_more.'` ON `'.$table.'`.`'.$a.'`=`'.$table_more.'`.`'.$b.'`';
                }

                $sql_limit = $pages = '';
                $sql_where = $this->_get_where($where, $fields); // sql的where子句

                // 商品有效期
                // isset($fields['order_etime']) && ($system['oot'] ? $sql_where.= ' AND `order_etime` BETWEEN 1 AND '.SYS_TIME : $sql_where.= ' AND NOT (`order_etime` BETWEEN 1 AND '.SYS_TIME.')');

                // 推荐位调用
                if ($system['flag']) {
                    $in = array();
                    $flag = $this->_query("select id from {$table}_flag where ".(strpos($system['flag'], ',') ? '`flag` IN ('.$system['flag'].')' : '`flag`='.(int)$system['flag']), $system['db'], $system['cache']);
                    if ($flag) {
                        foreach ($flag as $t) {
                            $in[] = $t['id'];
                        }
                    }
                    // 没有查询到内容
                    if (!$in) {
                        return $this->_return($system['return'], '没有查询到推荐位内容');
                    }
                    $sql_where = ($sql_where ? $sql_where.' AND' : '')."`$table`.`id` IN (".implode(',', $in).")";
                    unset($flag, $in);
                }
                $first_url = '';
                if ($system['page']) {
                    $page = max(1, (int)$_GET['page']);
                    if ($system['catid'] && is_numeric($system['catid'])) {
                        !$system['urlrule'] && $system['urlrule'] = \Phpcmf\Service::L('router')->category_url($module, $module['category'][$system['catid']], '{page}');
                        if ($this->_is_mobile) {
                            $system['pagesize'] = (int)$module['category'][$system['catid']]['setting']['template']['mpagesize'];
                        } else {
                            $system['pagesize'] = (int)$module['category'][$system['catid']]['setting']['template']['pagesize'];
                        }
                        $first_url = \Phpcmf\Service::L('router')->category_url($module, $module['category'][$system['catid']]);
                    }
                    $pagesize = (int)$system['pagesize'];
                    !$pagesize && $pagesize = 10;
                    if (!$total) {
                        $sql = "SELECT count(*) as c FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "")." ORDER BY NULL";
                        $row = $this->_query($sql, $system['db'], $system['cache'], FALSE);
                        $total = (int)$row['c'];
                        // 没有数据时返回空
                        if (!$total) {
                            return $this->_return($system['return'], '没有查询到内容', $sql, 0);
                        }
                    }
                    $pages = $this->_get_pagination($system['urlrule'], $pagesize, $total, $system['pagefile'], $first_url);
                    $sql_limit = 'LIMIT '.$pagesize * ($page - 1).','.$pagesize;
                } elseif ($system['num']) {
                    $pages = '';
                    $sql_limit = "LIMIT {$system['num']}";
                }

                $system['order'] = $this->_set_orders_field_prefix($system['order'], $_order); // 给排序字段加上表前缀
                $sql = "SELECT ".$this->_get_select_field($system['field'] ? $system['field'] : '*')." FROM $sql_from ".($sql_where ? "WHERE $sql_where" : "").($system['order'] == "null" || !$system['order'] ? "" : " ORDER BY {$system['order']}")." $sql_limit";
                $data = $this->_query($sql, $system['db'], $system['cache']);

                // 缓存查询结果
                $name = 'list-action-module-'.md5($sql.$this->_is_mobile);
                $cache = \Phpcmf\Service::L('cache')->get_data($name);
                if (!$cache && is_array($data)) {
                    // 模块表的系统字段
                    $fields['inputtime'] = array('fieldtype' => 'Date');
                    $fields['updatetime'] = array('fieldtype' => 'Date');
                    // 格式化显示自定义字段内容
                    $dfield = \Phpcmf\Service::L('Field')->app($module['dirname']);
                    foreach ($data as $i => $t) {
                        $t['url'] = dr_url_prefix($t['url'], $dirname);
                        $data[$i] = $dfield->format_value($fields, $t, 1, $module['dirname']);
                    }
                    $cache = $system['cache'] ? \Phpcmf\Service::L('cache')->set_data($name, $data, $system['cache']) : $data;
                }

                return $this->_return($system['return'], $cache ? $cache : $data, $sql, $total, $pages, $pagesize);
                break;


            default :
                return $this->_return($system['return'], '无此标签('.$system['action'].')');
                break;
        }
    }

    /**
     * 查询缓存
     */
    public function _query($sql, $db, $cache, $all = TRUE) {

        // 缓存存在时读取缓存文件
        $cname = md5($db.$sql.dr_now_url());
        if (SYS_CACHE && $cache && $data = \Phpcmf\Service::L('cache')->get_data($cname)) {
            return $data;
        }

        $mysql = \Phpcmf\Service::M()->db;
        if ($db) {
            $mysql = \Config\Database::connect($db, false);
        }
        // 执行SQL
        $query = $mysql->query($sql);

        if (!$query) {
            return 'SQL查询解析不正确：'.$sql;
        }

        // 查询结果
        $data = $all ? $query->getResultArray() : $query->getRowArray();

        // 开启缓存时，重新存储缓存数据
        $cache && \Phpcmf\Service::L('cache')->set_data($cname, $data, $cache);

        return $data;
    }

    // 设置分页参数
    public function set_page_config($config) {
        $this->_page_config = $config;
    }

    /**
     * 分页
     */
    public function _get_pagination($url, $pagesize, $total, $name = 'page', $first_url = '') {

        // 这里要支持移动端分页条件
        !$name && $name = 'page';
        $file = 'config/page/'.($this->_is_mobile ? 'mobile' : 'pc').'/'.(dr_safe_filename($name)).'.php';
        if (is_file(WEBPATH.$file)) {
            $config = require WEBPATH.$file;
        } elseif (is_file(ROOTPATH.$file)) {
            $config = require ROOTPATH.$file;
        } else {
            exit('无法找到分页配置文件【'.$file.'】');
        }

        if ($this->_page_config) {
            $config = dr_array22array($config, $this->_page_config);
        }

        !$url && $url = '此标签没有设置urlrule参数';

        $config['base_url'] = str_replace(['[page]', '%7Bpage%7D', '%5Bpage%5D', '%7bpage%7d', '%5bpage%5d'], '{page}', $url);
        $config['first_url'] = $first_url;
        $config['per_page'] = $pagesize;
        $config['total_rows'] = $total;
        $config['use_page_numbers'] = TRUE;
        $config['query_string_segment'] = 'page';

        return \Phpcmf\Service::L('Page')->initialize($config)->create_links();
    }

    // 条件子句格式化
    public function _get_where($where) {

        if ($where) {
            $string = '';
            foreach ($where as $i => $t) {
                // 过滤字段
                if (isset($t['use']) && $t['use'] == 0 || !strlen($t['value'])) {
                    continue;
                }
                $join = $string ? ' AND' : '';
                switch ($t['adj']) {
                    case 'LIKE':
                        $string.= $join." {$t['name']} LIKE \"".dr_safe_replace($t['value'])."\"";
                        break;

                    case 'IN':
                        $string.= $join." {$t['name']} IN (".dr_safe_replace($t['value']).")";
                        break;

                    case 'NOTIN':
                        $string.= $join." {$t['name']} NOT IN (".dr_safe_replace($t['value']).")";
                        break;

                    case 'NOT':
                        $string.= $join.(is_numeric($t['value']) ? " {$t['name']} <> ".$t['value'] : " {$t['name']} <> \"".($t['value'] == "''" ? '' : dr_safe_replace($t['value']))."\"");
                        break;

                    case 'BETWEEN':
                        $string.= $join." {$t['name']} BETWEEN ".str_replace(',', ' AND ', $t['value'])."";
                        break;

                    case 'BEWTEEN':
                        $string.= $join." {$t['name']} BETWEEN ".str_replace(',', ' AND ', $t['value'])."";
                        break;

                    case 'BW':
                        $string.= $join." {$t['name']} BETWEEN ".str_replace(',', ' AND ', $t['value'])."";
                        break;

                    case 'SQL':
                        $string.= $join.' '.$t['value'];
                        break;

                    default:
                        if (strpos($t['name'], '`thumb`')) {
                            // 缩略图筛选
                            $t['value'] == 1 ? $string.= $join." {$t['name']}<>''" : $string.= $join." {$t['name']}=''";
                        } elseif (!$t['name'] && $t['value']) {

                            $string.= $join.' '.$t['value'];
                        } else {
                            $string.= $join.(is_numeric($t['value']) ? " {$t['name']} = ".$t['value'] : " {$t['name']} = \"".($t['value'] == "''" ? '' : dr_safe_replace($t['value']))."\"");
                        }
                        break;
                }
            }
            return trim($string);
        }

        return 1;
    }

    // 给条件字段加上表前缀
    public function _set_where_field_prefix($where, $field, $prefix, $myfield = []) {

        if (!$where) {
            return [];
        }

        foreach ($where as $i => $t) {
            if (in_array($t['name'], $field)) {
                $where[$i]['use'] = 1;
                $where[$i]['name'] = "`$prefix`.`{$t['name']}`";
                if ($myfield && $myfield[$t['name']]['fieldtype'] == 'Linkage') {
                    // 联动菜单
                    $data = dr_linkage($myfield[$t['name']]['setting']['option']['linkage'], $t['value']);
                    if ($data) {
                        if ($data['child']) {
                            $where[$i]['adj'] = 'IN';
                            $where[$i]['value'] = $data['childids'];
                        } else {
                            $where[$i]['value'] = intval($data['ii']);
                        }
                    }
                }
            } elseif (!$t['name'] && $t['value']) {
                // 标示只有where的条件查询
                $where[$i]['use'] = 1;
            } else {
                $where[$i]['use'] = $t['use'] ? 1 : 0;
            }
        }

        return $where;
    }

    // 给显示字段加上表前缀
    public function _set_select_field_prefix($select, $field, $prefix) {

        $select = str_replace('DISTINCT_', 'DISTINCT ', $select);

        if ($select) {
            $array = explode(',', $select);
            foreach ($array as $i => $t) {
                in_array($t, $field) && $array[$i] = "`$prefix`.`$t`";
            }
            return implode(',', $array);
        }

        return $select;
    }

    // 给排序字段加上多表前缀
    public function _set_orders_field_prefix($order, $fields) {

        if (!$order) {
            return NULL;
        } elseif (in_array(strtoupper($order), ['RAND()', 'RAND'])) {
            // 随机排序
            return 'RAND()';
        }
        // 字段排序
        $my = [];
        $array = explode(',', $order);
        foreach ($array as $i => $t) {
            if (strpos($t, '`') !== false) {
                $my[$i] = $t;
                continue;
            }
            $a = explode('_', $t);
            $b = end($a);
            if (in_array(strtolower($b), ['desc', 'asc'])) {
                $a = str_replace('_'.$b, '', $t);
            } else {
                $a = $t;
                $b = '';
            }
            $b = strtoupper($b);
            foreach ($fields as $prefix => $field) {
                if (in_array($a, $field)) {
                    $my[$i] = "`$prefix`.`$a` ".($b ? $b : "DESC");
                } elseif (in_array($a.'_lat', $field) && in_array($a.'_lng', $field)) {
                    if (\Phpcmf\Service::C()->my_position) {
                        $my[$i] = $a.' ASC';
                        $this->pos_order = $a;
                    } else {
                        \Phpcmf\Service::C()->msg('没有定位到您的坐标');
                    }
                }
            }

        }
        if ($my) {
            return implode(',', $my);
        }

        return NULL;
    }

    // 给排序字段加上表前缀
    public function _set_order_field_prefix($order, $field, $prefix) {

        if ($order) {
            if (in_array(strtoupper($order), ['RAND()', 'RAND'])) {
                // 随机排序
                return 'RAND()';
            } else {
                // 字段排序
                $my = [];
                $array = explode(',', $order);
                foreach ($array as $i => $t) {
                    if (strpos($t, '`') !== false) {
                        $my[$i] = $t;
                        continue;
                    }
                    $a = explode('_', $t);
                    $b = end($a);
                    if (in_array(strtolower($b), ['desc', 'asc'])) {
                        $a = str_replace('_'.$b, '', $t);
                    } else {
                        $a = $t;
                        $b = '';
                    }
                    $b = strtoupper($b);
                    if (in_array($a, $field)) {
                        $my[$i] = "`$prefix`.`$a` ".($b ? $b : "DESC");
                    } elseif (in_array($a.'_lat', $field) && in_array($a.'_lng', $field)) {
                        if (\Phpcmf\Service::C()->my_position) {
                            $my[$i] = $a.' ASC';
                            $this->pos_order = $a;
                        } else {
                            \Phpcmf\Service::C()->msg('没有定位到您的坐标');
                        }
                    }
                }
                if ($my) {
                    return implode(',', $my);
                }
            }
        }

        return NULL;
    }

    // 格式化查询参数
    private function _get_select_field($field) {

        $this->pos_order && (\Phpcmf\Service::C()->my_position ? $field.= ',ROUND(6378.138*2*ASIN(SQRT(POW(SIN(('.\Phpcmf\Service::C()->my_position['lat'].'*PI()/180-'.$this->pos_order.'_lat*PI()/180)/2),2)+COS('.\Phpcmf\Service::C()->my_position['lat'].'*PI()/180)*COS('.$this->pos_order.'_lat*PI()/180)*POW(SIN(('.\Phpcmf\Service::C()->my_position['lng'].'*PI()/180-'.$this->pos_order.'_lng*PI()/180)/2),2)))*1000) AS '.$this->pos_order : \Phpcmf\Service::C()->msg('没有定位到您的坐标'));

        return $field;
    }

    // list 返回
    public function _return($return, $data = [], $sql = '', $total = 0, $pages = '', $pagesize = 0) {

        $debug = $sql;
        if ($data && !is_array($data)) {
            $debug.= $data;
            $data = [];
        }

        $total = isset($total) ? $total : dr_count($data);
        $page = max(1, (int)$_GET['page']);
        $nums = $pagesize ? ceil($total/$pagesize) : 0;

        // 返回数据格式
        if ($return) {
            return [
                'nums_'.$return => $nums,
                'page_'.$return => $page,
                'pages_'.$return => $pages,
                'debug_'.$return => $debug,
                'total_'.$return => $total,
                'return_'.$return => $data,
                'pagesize_'.$return => $pagesize,
            ];
        } else {
            return [
                'nums' => $nums,
                'debug' => $debug,
                'page' => $page,
                'total' => $total,
                'pages' => $pages,
                'return' => $data,
                'pagesize' => $pagesize,
            ];
        }
    }

    // 替换变量
    public function _get_var($param) {

        $array = explode('.', $param);
        if (!$array) {
            return '';
        }

        $string = '';
        foreach ($array as $var) {
            $var = dr_safe_replace($var);
            $string.= '[';
            if (strpos($var, '$') === 0) {
                $string.= preg_replace('/\[(.+)\]/U', '[\'\\1\']', $var);
            } elseif (preg_match('/[A-Z_]+/', $var)) {
                $string.= ''.$var.'';
            } else {
                $string.= '\''.$var.'\'';
            }
            $string.= ']';
        }

        return $string;
    }

    // 公共变量参数
    public function _cache_var($name, $siteid = 0) {

        $data = null;
        $name = strtolower($name);

        switch ($name) {
            case 'member':
                $data = \Phpcmf\Service::C()->member_cache;
                break;
            case 'member_group':
                $data = \Phpcmf\Service::C()->member_cache['group'];
                break;
            case 'urlrule':
                $data = \Phpcmf\Service::L('cache')->get('urlrule');
                break;
            case 'module-content':
                $data = \Phpcmf\Service::L('cache')->get('module-'.$siteid.'-content');
                break;
            case 'category':
                $data = \Phpcmf\Service::L('cache')->get('module-'.$siteid.'-'.MOD_DIR, 'category');
                break;
            case 'page':
                $data = \Phpcmf\Service::L('cache')->get('page-'.$siteid, 'data');
                break;
            default:
                $data = \Phpcmf\Service::L('cache')->get($name.'-'.$siteid);
                break;
        }

        return $data;
    }

    // 模板中的全部变量
    public function get_data() {
        return $this->_options;
    }

    // 模板中的全部变量
    public function getData() {
        return $this->_options;
    }

    /**
     * 主要用于调试工具栏
     *
     * @return array
     */
    public function getPerformanceData(): array
    {
        return $this->performanceData;
    }

    /**
     * 记录模板的性能数据
     *
     * @param float  $start
     * @param float  $end
     * @param string $view
     */
    protected function logPerformance(float $start, float $end, string $view)
    {
        if (!CI_DEBUG)
            return;

        $this->performanceData[] = [
            'start'	 => $start,
            'end'	 => $end,
            'view'	 => $view
        ];
    }

}