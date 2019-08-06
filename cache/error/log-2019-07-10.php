<?php defined('SYSTEMPATH') || exit('No direct script access allowed'); ?>

CRITICAL - 2019-07-10 14:27:24 --> Table 'tpcmf.dr_attachment' doesn't exist
#0 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\MySQLi\Connection.php(329): mysqli->query('SELECT *\nFROM `...')
#1 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(709): CodeIgniter\Database\MySQLi\Connection->execute('SELECT *\nFROM `...')
#2 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(637): CodeIgniter\Database\BaseConnection->simpleQuery('SELECT *\nFROM `...')
#3 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseBuilder.php(1476): CodeIgniter\Database\BaseConnection->query('SELECT *\nFROM `...', Array, false)
#4 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\Phpcmf.php(582): CodeIgniter\Database\BaseBuilder->get()
#5 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\Helper.php(838): Phpcmf\Common->get_attachment(9)
#6 C:\phpStudy\PHPTutorial\WWW\tpcmf\cache\template\template_pc_guangxun_home_index.html.cache.php(30): dr_get_file('9')
#7 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\View.php(199): include('C:\\phpStudy\\PHP...')
#8 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Core\Controllers\Home.php(37): Phpcmf\View->display('index.html')
#9 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Core\Controllers\Home.php(80): Phpcmf\Controllers\Home->_index()
#10 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(838): Phpcmf\Controllers\Home->index()
#11 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(335): CodeIgniter\CodeIgniter->runController(Object(Phpcmf\Controllers\Home))
#12 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(245): CodeIgniter\CodeIgniter->handleRequest(NULL, Object(Config\Cache), false)
#13 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Init.php(305): CodeIgniter\CodeIgniter->run()
#14 C:\phpStudy\PHPTutorial\WWW\tpcmf\index.php(38): require('C:\\phpStudy\\PHP...')
#15 {main}
CRITICAL - 2019-07-10 14:28:08 --> Table 'tpcmf.dr_member' doesn't exist
#0 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\MySQLi\Connection.php(329): mysqli->query('SELECT `passwor...')
#1 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(709): CodeIgniter\Database\MySQLi\Connection->execute('SELECT `passwor...')
#2 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(637): CodeIgniter\Database\BaseConnection->simpleQuery('SELECT `passwor...')
#3 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseBuilder.php(1476): CodeIgniter\Database\BaseConnection->query('SELECT `passwor...', Array, false)
#4 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Model\Auth.php(68): CodeIgniter\Database\BaseBuilder->get()
#5 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Core\Controllers\Admin\Login.php(42): Phpcmf\Model\Auth->login('admin', 'admin')
#6 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(838): Phpcmf\Controllers\Admin\Login->index()
#7 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(335): CodeIgniter\CodeIgniter->runController(Object(Phpcmf\Controllers\Admin\Login))
#8 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(245): CodeIgniter\CodeIgniter->handleRequest(NULL, Object(Config\Cache), false)
#9 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Init.php(305): CodeIgniter\CodeIgniter->run()
#10 C:\phpStudy\PHPTutorial\WWW\tpcmf\index.php(38): require('C:\\phpStudy\\PHP...')
#11 C:\phpStudy\PHPTutorial\WWW\tpcmf\admin.php(9): require('C:\\phpStudy\\PHP...')
#12 {main}
