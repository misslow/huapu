<?php defined('SYSTEMPATH') || exit('No direct script access allowed'); ?>

CRITICAL - 2019-07-23 18:16:27 --> syntax error, unexpected end of file
#0 D:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\View.php(199): include()
#1 D:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Core\Controllers\Home.php(37): Phpcmf\View->display('index.html')
#2 D:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Core\Controllers\Home.php(45): Phpcmf\Controllers\Home->_index()
#3 D:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(838): Phpcmf\Controllers\Home->index()
#4 D:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(335): CodeIgniter\CodeIgniter->runController(Object(Phpcmf\Controllers\Home))
#5 D:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(245): CodeIgniter\CodeIgniter->handleRequest(NULL, Object(Config\Cache), false)
#6 D:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Init.php(305): CodeIgniter\CodeIgniter->run()
#7 D:\phpStudy\PHPTutorial\WWW\tpcmf\index.php(38): require('D:\\phpStudy\\PHP...')
#8 {main}
