<?php defined('SYSTEMPATH') || exit('No direct script access allowed'); ?>

CRITICAL - 2019-06-14 09:50:09 --> Access denied for user '****'@'localhost' (using password: YES)
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
#10 {main}
CRITICAL - 2019-06-14 09:56:14 --> Table 'tpcmf.dr_1_block' doesn't exist
#0 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\MySQLi\Connection.php(329): mysqli->query('SELECT count(*)...')
#1 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(709): CodeIgniter\Database\MySQLi\Connection->execute('SELECT count(*)...')
#2 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(637): CodeIgniter\Database\BaseConnection->simpleQuery('SELECT count(*)...')
#3 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseBuilder.php(1476): CodeIgniter\Database\BaseConnection->query('SELECT count(*)...', Array, false)
#4 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\Model.php(580): CodeIgniter\Database\BaseBuilder->get()
#5 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\Table.php(523): Phpcmf\Model->limit_page(10)
#6 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\App\Block\Controllers\Admin\Home.php(151): Phpcmf\Table->_List()
#7 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(838): Phpcmf\Controllers\Admin\Home->index()
#8 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(335): CodeIgniter\CodeIgniter->runController(Object(Phpcmf\Controllers\Admin\Home))
#9 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(245): CodeIgniter\CodeIgniter->handleRequest(NULL, Object(Config\Cache), false)
#10 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Init.php(305): CodeIgniter\CodeIgniter->run()
#11 C:\phpStudy\PHPTutorial\WWW\tpcmf\index.php(38): require('C:\\phpStudy\\PHP...')
#12 C:\phpStudy\PHPTutorial\WWW\tpcmf\admin.php(9): require('C:\\phpStudy\\PHP...')
#13 {main}
CRITICAL - 2019-06-14 09:56:31 --> Table 'tpcmf.dr_1_block' doesn't exist
#0 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\MySQLi\Connection.php(329): mysqli->query('SELECT count(*)...')
#1 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(709): CodeIgniter\Database\MySQLi\Connection->execute('SELECT count(*)...')
#2 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(637): CodeIgniter\Database\BaseConnection->simpleQuery('SELECT count(*)...')
#3 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseBuilder.php(1476): CodeIgniter\Database\BaseConnection->query('SELECT count(*)...', Array, false)
#4 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\Model.php(580): CodeIgniter\Database\BaseBuilder->get()
#5 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\Table.php(523): Phpcmf\Model->limit_page(10)
#6 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\App\Block\Controllers\Admin\Home.php(151): Phpcmf\Table->_List()
#7 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(838): Phpcmf\Controllers\Admin\Home->index()
#8 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(335): CodeIgniter\CodeIgniter->runController(Object(Phpcmf\Controllers\Admin\Home))
#9 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(245): CodeIgniter\CodeIgniter->handleRequest(NULL, Object(Config\Cache), false)
#10 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Init.php(305): CodeIgniter\CodeIgniter->run()
#11 C:\phpStudy\PHPTutorial\WWW\tpcmf\index.php(38): require('C:\\phpStudy\\PHP...')
#12 C:\phpStudy\PHPTutorial\WWW\tpcmf\admin.php(9): require('C:\\phpStudy\\PHP...')
#13 {main}
CRITICAL - 2019-06-14 09:57:36 --> Table 'tpcmf.dr_1_block' doesn't exist
#0 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\MySQLi\Connection.php(329): mysqli->query('SELECT count(*)...')
#1 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(709): CodeIgniter\Database\MySQLi\Connection->execute('SELECT count(*)...')
#2 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(637): CodeIgniter\Database\BaseConnection->simpleQuery('SELECT count(*)...')
#3 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseBuilder.php(1476): CodeIgniter\Database\BaseConnection->query('SELECT count(*)...', Array, false)
#4 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\Model.php(580): CodeIgniter\Database\BaseBuilder->get()
#5 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\Table.php(523): Phpcmf\Model->limit_page(10)
#6 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\App\Block\Controllers\Admin\Home.php(151): Phpcmf\Table->_List()
#7 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(838): Phpcmf\Controllers\Admin\Home->index()
#8 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(335): CodeIgniter\CodeIgniter->runController(Object(Phpcmf\Controllers\Admin\Home))
#9 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(245): CodeIgniter\CodeIgniter->handleRequest(NULL, Object(Config\Cache), false)
#10 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Init.php(305): CodeIgniter\CodeIgniter->run()
#11 C:\phpStudy\PHPTutorial\WWW\tpcmf\index.php(38): require('C:\\phpStudy\\PHP...')
#12 C:\phpStudy\PHPTutorial\WWW\tpcmf\admin.php(9): require('C:\\phpStudy\\PHP...')
#13 {main}
CRITICAL - 2019-06-14 09:58:31 --> Table 'tpcmf.dr_1_block' doesn't exist
#0 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\MySQLi\Connection.php(329): mysqli->query('SELECT count(*)...')
#1 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(709): CodeIgniter\Database\MySQLi\Connection->execute('SELECT count(*)...')
#2 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(637): CodeIgniter\Database\BaseConnection->simpleQuery('SELECT count(*)...')
#3 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseBuilder.php(1476): CodeIgniter\Database\BaseConnection->query('SELECT count(*)...', Array, false)
#4 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\Model.php(580): CodeIgniter\Database\BaseBuilder->get()
#5 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\Table.php(523): Phpcmf\Model->limit_page(10)
#6 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\App\Block\Controllers\Admin\Home.php(151): Phpcmf\Table->_List()
#7 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(838): Phpcmf\Controllers\Admin\Home->index()
#8 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(335): CodeIgniter\CodeIgniter->runController(Object(Phpcmf\Controllers\Admin\Home))
#9 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(245): CodeIgniter\CodeIgniter->handleRequest(NULL, Object(Config\Cache), false)
#10 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Init.php(305): CodeIgniter\CodeIgniter->run()
#11 C:\phpStudy\PHPTutorial\WWW\tpcmf\index.php(38): require('C:\\phpStudy\\PHP...')
#12 C:\phpStudy\PHPTutorial\WWW\tpcmf\admin.php(9): require('C:\\phpStudy\\PHP...')
#13 {main}
CRITICAL - 2019-06-14 09:59:44 --> Table 'tpcmf.dr_1_block' doesn't exist
#0 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\MySQLi\Connection.php(329): mysqli->query('SELECT count(*)...')
#1 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(709): CodeIgniter\Database\MySQLi\Connection->execute('SELECT count(*)...')
#2 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseConnection.php(637): CodeIgniter\Database\BaseConnection->simpleQuery('SELECT count(*)...')
#3 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\Database\BaseBuilder.php(1476): CodeIgniter\Database\BaseConnection->query('SELECT count(*)...', Array, false)
#4 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\Model.php(580): CodeIgniter\Database\BaseBuilder->get()
#5 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Core\Table.php(523): Phpcmf\Model->limit_page(10)
#6 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\App\Block\Controllers\Admin\Home.php(151): Phpcmf\Table->_List()
#7 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(838): Phpcmf\Controllers\Admin\Home->index()
#8 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(335): CodeIgniter\CodeIgniter->runController(Object(Phpcmf\Controllers\Admin\Home))
#9 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\System\CodeIgniter.php(245): CodeIgniter\CodeIgniter->handleRequest(NULL, Object(Config\Cache), false)
#10 C:\phpStudy\PHPTutorial\WWW\tpcmf\dayrui\Fcms\Init.php(305): CodeIgniter\CodeIgniter->run()
#11 C:\phpStudy\PHPTutorial\WWW\tpcmf\index.php(38): require('C:\\phpStudy\\PHP...')
#12 C:\phpStudy\PHPTutorial\WWW\tpcmf\admin.php(9): require('C:\\phpStudy\\PHP...')
#13 {main}
