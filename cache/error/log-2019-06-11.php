<?php defined('SYSTEMPATH') || exit('No direct script access allowed'); ?>

CRITICAL - 2019-06-11 09:47:37 --> Access denied for user '****'@'localhost' (using password: YES)
#0 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(376): CodeIgniter\Database\MySQLi\Connection->connect(false)
#1 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(614): CodeIgniter\Database\BaseConnection->initialize()
#2 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseBuilder.php(1476): CodeIgniter\Database\BaseConnection->query('SELECT *\nFROM `...', Array, false)
#3 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Model\Member.php(236): CodeIgniter\Database\BaseBuilder->get()
#4 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\Phpcmf.php(260): Phpcmf\Model\Member->get_member(1)
#5 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(813): Phpcmf\Common->__construct()
#6 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(330): CodeIgniter\CodeIgniter->createController()
#7 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(245): CodeIgniter\CodeIgniter->handleRequest(NULL, Object(Config\Cache), false)
#8 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Init.php(305): CodeIgniter\CodeIgniter->run()
#9 C:\phpStudy\PHPTutorial\WWW\tpcmf\index.php(38): require('C:\\phpStudy\\PHP...')
#10 C:\phpStudy\PHPTutorial\WWW\tpcmf\admin.php(9): require('C:\\phpStudy\\PHP...')
#11 {main}
CRITICAL - 2019-06-11 09:48:00 --> Access denied for user '****'@'localhost' (using password: NO)
#0 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(376): CodeIgniter\Database\MySQLi\Connection->connect(false)
#1 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(614): CodeIgniter\Database\BaseConnection->initialize()
#2 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseBuilder.php(1476): CodeIgniter\Database\BaseConnection->query('SELECT *\nFROM `...', Array, false)
#3 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Model\Member.php(236): CodeIgniter\Database\BaseBuilder->get()
#4 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\Phpcmf.php(260): Phpcmf\Model\Member->get_member(1)
#5 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(813): Phpcmf\Common->__construct()
#6 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(330): CodeIgniter\CodeIgniter->createController()
#7 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(245): CodeIgniter\CodeIgniter->handleRequest(NULL, Object(Config\Cache), false)
#8 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Init.php(305): CodeIgniter\CodeIgniter->run()
#9 C:\phpStudy\PHPTutorial\WWW\tpcmf\index.php(38): require('C:\\phpStudy\\PHP...')
#10 C:\phpStudy\PHPTutorial\WWW\tpcmf\admin.php(9): require('C:\\phpStudy\\PHP...')
#11 {main}
CRITICAL - 2019-06-11 09:48:06 --> Access denied for user '****'@'localhost' (using password: NO)
#0 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(376): CodeIgniter\Database\MySQLi\Connection->connect(false)
#1 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(614): CodeIgniter\Database\BaseConnection->initialize()
#2 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseBuilder.php(1476): CodeIgniter\Database\BaseConnection->query('SELECT *\nFROM `...', Array, false)
#3 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Model\Member.php(236): CodeIgniter\Database\BaseBuilder->get()
#4 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\Phpcmf.php(260): Phpcmf\Model\Member->get_member(1)
#5 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(813): Phpcmf\Common->__construct()
#6 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(330): CodeIgniter\CodeIgniter->createController()
#7 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(245): CodeIgniter\CodeIgniter->handleRequest(NULL, Object(Config\Cache), false)
#8 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Init.php(305): CodeIgniter\CodeIgniter->run()
#9 C:\phpStudy\PHPTutorial\WWW\tpcmf\index.php(38): require('C:\\phpStudy\\PHP...')
#10 C:\phpStudy\PHPTutorial\WWW\tpcmf\admin.php(9): require('C:\\phpStudy\\PHP...')
#11 {main}