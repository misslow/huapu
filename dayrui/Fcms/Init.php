<?php

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


// 当前URL
$pageURL = 'http';
((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
    || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443')) && $pageURL.= 's';
$pageURL.= '://';
if (strpos($_SERVER['HTTP_HOST'], ':') !== FALSE) {
    $url = explode(':', $_SERVER['HTTP_HOST']);
    $url[0] ? $pageURL.= $_SERVER['HTTP_HOST'] : $pageURL.= $url[0];
} else {
    $pageURL.= $_SERVER['HTTP_HOST'];
}

define('FC_NOW_URL', $pageURL.($_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']));
define('FC_NOW_HOST', $pageURL.'/');

//率先读取静态文件
//is_file(WRITEPATH.'html/'.md5(FC_NOW_URL).'.html') && exit(file_get_contents(WRITEPATH.'html/'.md5(FC_NOW_URL).'.html'));

// 用于显示debug
$startMemory = memory_get_usage();
$startTime = microtime(true);

// CI框架目录
define('BASEPATH', FCPATH.'System/');
define('SYSTEMPATH', BASEPATH);
// CMS公共程序目录
define('CMSPATH', FCPATH.'Fcms/');
// 核心程序目录
define('COREPATH', FCPATH.'Core/');
// App程序目录
!defined('APPSPATH') && define('APPSPATH', FCPATH.'App/');
// 程序初始化
!defined('MYPATH') && define('MYPATH', FCPATH.'My/');
// 定义模板目录
!defined('TPLPATH') && define('TPLPATH', ROOTPATH.'template/');

// 是否来自ajax提交
define('IS_AJAX', (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'));
// 是否来自post提交
define('IS_POST', isset($_POST) && count($_POST) ? TRUE : FALSE);

define('IS_AJAX_POST', IS_POST);
// 当前系统时间戳
define('SYS_TIME', $_SERVER['REQUEST_TIME'] ? $_SERVER['REQUEST_TIME'] : time());

// 系统变量
$system = is_file(WRITEPATH.'config/system.php') ? require WRITEPATH.'config/system.php' : [
    'SYS_DEBUG'                     => '0', //调试器开关
    'SYS_ADMIN_CODE'                => '0', //后台登录验证码开关
    'SYS_ADMIN_LOG'                 => '0', //后台操作日志开关
    'SYS_AUTO_FORM'                 => '0', //自动存储表单数据
    'SYS_ADMIN_PAGESIZE'            => '10', //后台数据分页显示数量
    'SYS_CAT_RNAME'                 => '1', //栏目目录允许重复
    'SYS_PAGE_RNAME'                => '0', //单页目录允许重复
    'SYS_KEY'                       => '', //安全密匙
    'SYS_HTTPS'                     => '0', //https模式
    'SYS_ATTACHMENT_DB'             => '', //附件归属开启模式
    'SYS_ATTACHMENT_PATH'           => '', //附件上传路径
    'SYS_ATTACHMENT_URL'            => '', //附件访问地址
    'SYS_ATTACHMENT_URL'            => '', //附件访问地址
];
foreach ($system as $var => $value) {
    !defined($var) && define($var, $value);
}

// 缓存变量
$cache = [];
if (is_file(WRITEPATH.'config/cache.php')) {
    $cache = require WRITEPATH.'config/cache.php';
}
foreach ([
             'SYS_CACHE',
             'SYS_CACHE_SHOW',
             'SYS_CACHE_PAGE',
             'SYS_CACHE_ATTACH',
             'SYS_CACHE_LIST',
             'SYS_CACHE_SEARCH',
         ] as $name) {
    define($name, (int)$cache[$name]);
}

// 当前域名
define('DOMAIN_NAME', strtolower($_SERVER['HTTP_HOST']));

// 伪静态字符串
$uu = isset($_SERVER['HTTP_X_REWRITE_URL']) || trim($_SERVER['REQUEST_URI'], '/') == SELF ? trim($_SERVER['HTTP_X_REWRITE_URL'], '/') : ($_SERVER['REQUEST_URI'] ? trim($_SERVER['REQUEST_URI'], '/') : NULL);

// 以index.php或者?开头的uri不做处理
$uri = strpos($uu, SELF) === 0 || strpos($uu, '?') === 0 ? '' : $uu;

// 当前URI
define('CMSURI', $uri);

// 根据自定义URL规则来识别路由
if (!IS_ADMIN && $uri && !defined('IS_API')) {
	// 自定义URL解析规则
	$routes = is_file(WEBPATH.'config/rewrite.php') ? require WEBPATH.'config/rewrite.php' : [];
    $routes['rewrite-test.html'] = 'index.php?s=api&c=rewrite&m=test'; // 测试规则
    $routes['sitemap.xml'] = 'index.php?s=api&c=rewrite&m=sitemap'; // 地图规则
	// 正则匹配路由规则
	$is_404 = 1;
	foreach ($routes as $key => $val) {
		$rewrite = $match = [];
		if ($key == $uri || preg_match('/^'.$key.'$/U', $uri, $match)) {
			unset($match[0]);
			// 开始匹配
			$is_404 = 0;
			// 开始解析路由 URL参数模式
            $_GET = [];
            $queryParts = explode('&', str_replace('index.php?', '', $val));
            if ($queryParts) {
                foreach ($queryParts as $param) {
                    $item = explode('=', $param);
                    $_GET[$item[0]] = $item[1];
                    if (strpos($item[1], '$') !== FALSE) {
                        $id = (int)substr($item[1], 1);
                        $_GET[$item[0]] = isset($match[$id]) ? $match[$id] : $item[1];
                    }
                }
            }
            !$_GET['c'] && $_GET['c'] = 'home';
            !$_GET['m'] && $_GET['m'] = 'index';
			// 结束匹配
			break;
		}
	}
	// 自定义路由模式
    if ($is_404 && is_file(WEBPATH.'config/router.php')) {
	    require WEBPATH.'config/router.php';
    }
	// 说明是404
	if ($is_404) {
		$_GET['s'] = '';
		$_GET['c'] = 'home';
		$_GET['m'] = 's404';
		$_GET['uri'] = $uri;
	}
}

// API接口项目标识 放到后面是为了识别api 的伪静态
!defined('IS_API') && define('IS_API', isset($_GET['s']) && $_GET['s'] == 'api');

// 解析自定义域名
if (!IS_API && $_GET['s'] != 'api' && is_file(WRITEPATH.'config/domain_app.php')){
    $domain = require WRITEPATH.'config/domain_app.php';
    // 强制定义为模块
    if (isset($domain[DOMAIN_NAME]) && $domain[DOMAIN_NAME] && is_dir(APPSPATH.ucfirst($domain[DOMAIN_NAME]))) {
        $_GET['s'] = $domain[DOMAIN_NAME];
    }
    unset($domain);
}

// 自定义开发目录分布
if (is_file(MYPATH.'Dev.php')) {
    require MYPATH.'Dev.php';
} else {
    // 判断是否是app目录
    function dr_is_app_dir($name) {
        return is_dir(APPSPATH.ucfirst($name));
    }
    function dr_get_app_dir($name) {
        return APPSPATH.ucfirst($name).'/';
    }
    function dr_get_app_tpl($name = '') {
        return TPLPATH;
    }
    function dr_get_app_list() {
        return APPSPATH;
    }
    function dr_get_app_css($name) {
        return THEME_PATH.$name.'/';
    }
}

// 判断s参数,“应用程序”文件夹目录
if (!IS_API && isset($_GET['s']) && preg_match('/^[a-z]+$/i', $_GET['s'])) {
	// 判断会员模块,排除后台调用
	$dir = ucfirst($_GET['s']);
	if (!IS_ADMIN && $dir == 'Member') {
		// 会员
		if ($_GET['app'] && dr_is_app_dir($_GET['app'])) {
			// 模块应用
			define('APPPATH', dr_get_app_dir($_GET['app']));
			define('APP_DIR', strtolower($_GET['app'])); // 应用目录名称
		} else {
			// 表示会员模块
			define('APPPATH', COREPATH);
			define('APP_DIR', ''); // 模块目录名称
		}
		define('IS_MEMBER', TRUE);
	} elseif (dr_is_app_dir($dir)) {
		// 模块应用
		define('APPPATH', dr_get_app_dir($dir));
		define('APP_DIR', strtolower($dir)); // 应用目录名称
		define('IS_MEMBER', FALSE);
	} else {
		// 不存在的应用
        exit('APP目录不存在: APPSPATH/'.$dir.'/');
	}
} else {
	// 系统主目录
	define('APPPATH', COREPATH);
	define('APP_DIR', '');
	define('IS_MEMBER', FALSE);
}

define('CI_DEBUG', IS_DEV ? 1 : IS_ADMIN && SYS_DEBUG);
define('TESTPATH', WRITEPATH.'tests/');

// 显示错误提示
if (CI_DEBUG) {
   ini_set('display_errors', 1);
} else {
   ini_set('display_errors', 0);
}

/*
 * 重写config函数，防止modules被加载
 */
function config ($name, $getShared = true) {

    if ($name == 'Modules') {
        $name = 'Config\Modules';
    }

    return \CodeIgniter\Config\Config::get($name, $getShared);
}

/******* CodeIgniter Bootstrap *******/

// 定义常量
require COREPATH.'Config/Constants.php';

require BASEPATH.'Common.php';


// 自动加载机制
require COREPATH . 'Config/Modules.php';

require BASEPATH.'Autoloader/Autoloader.php';
require COREPATH .'Config/Autoload.php';

require BASEPATH .'Config/BaseService.php';
require COREPATH .'Config/Services.php';


// Use Config\Services as CodeIgniter\Services
class_alias('Config\Services', 'CodeIgniter\Services');

$loader = CodeIgniter\Services::autoloader();
//$loader->initialize(new Config\Autoload());
$loader->initialize(new Config\Autoload(), new Config\Modules());
$loader->register();    // Register the loader with the SPL autoloader stack.


// Load environment settings from .env files
// into $_SERVER and $_ENV
require BASEPATH . 'Config/DotEnv.php';

$env = new \CodeIgniter\Config\DotEnv(COREPATH);
$env->load();

// Always load the URL helper -
// it should be used in 90% of apps.
helper('url');

/*
 * ---------------------------------------------------------------
 * GRAB OUR CODEIGNITER INSTANCE
 * ---------------------------------------------------------------
 *
 * The CodeIgniter class contains the core functionality to make
 * the application run, and does all of the dirty work to get
 * the pieces all working together.
 */


$app = new \Phpcmf\Extend\CodeIgniter(config(\Config\App::class));
$app->initialize();
$app->run();