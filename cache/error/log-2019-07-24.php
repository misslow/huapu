<?php defined('SYSTEMPATH') || exit('No direct script access allowed'); ?>

CRITICAL - 2019-07-24 14:16:06 --> syntax error, unexpected '<'
#0 D:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Control\Home\Module.php(506): Phpcmf\View->display('show_yy.html')
#1 D:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Core\Controllers\Show.php(40): Phpcmf\Home\Module->_Show(47, NULL, 1)
#2 D:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(838): Phpcmf\Controllers\Show->index()
#3 D:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(335): CodeIgniter\CodeIgniter->runController(Object(Phpcmf\Controllers\Show))
#4 D:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(245): CodeIgniter\CodeIgniter->handleRequest(NULL, Object(Config\Cache), false)
#5 D:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Init.php(305): CodeIgniter\CodeIgniter->run()
#6 D:\phpStudy\PHPTutorial\WWW\tpcmf\index.php(38): require('D:\\phpStudy\\PHP...')
#7 {main}
