<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


2019-07-26 09:01:26 --> mysqli_sql_exception
文件: D:\phpStudy\PHPTutorial\WWW\tpcmf_xcx\dayrui\System\Database\MySQLi\Connection.php
行号: 329
错误: Table 'tpcmf_xcx.dr_categoey' doesn't exist
{"html":"<pre><code><span class=\"line\"><span class=\"number\">322<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$this<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">connID<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">next_result<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">323<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if&nbsp;(<\/span><span style=\"color: #c7c7c7\">$res&nbsp;<\/span><span style=\"color: #f1ce61;\">=&nbsp;<\/span><span style=\"color: #c7c7c7\">$this<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">connID<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">store_result<\/span><span style=\"color: #f1ce61;\">())\n<span class=\"line\"><span class=\"number\">324<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{\n<span class=\"line\"><span class=\"number\">325<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$res<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">free<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">326<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">327<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">328<\/span> \n<span class='line highlight'><span class='number'>329<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;return&nbsp;$this-&gt;connID-&gt;query($this-&gt;prepQuery($sql));\n<\/span><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><span class=\"line\"><span class=\"number\">330<\/span> &nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">331<\/span> \n<span class=\"line\"><span class=\"number\">332<\/span> &nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #767a7e; font-style: italic\">\/\/--------------------------------------------------------------------\n<span class=\"line\"><span class=\"number\">333<\/span> \n<span class=\"line\"><span class=\"number\">334<\/span> &nbsp;&nbsp;&nbsp;&nbsp;\/**\n<span class=\"line\"><span class=\"number\">335<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;Prep&nbsp;the&nbsp;query\n<span class=\"line\"><span class=\"number\">336<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*\n<\/span><\/code><\/pre>"}
地址: http://www.xcx.com/admin.php?s=httpapi&c=http&m=test_index&id=4&is_ajax=1
来源: http://www.xcx.com/admin.php?s=httpapi&c=http&m=index&total=4&order=id+desc


2019-07-26 09:01:47 --> mysqli_sql_exception
文件: D:\phpStudy\PHPTutorial\WWW\tpcmf_xcx\dayrui\System\Database\MySQLi\Connection.php
行号: 329
错误: Table 'tpcmf_xcx.dr_share_categoey' doesn't exist
{"html":"<pre><code><span class=\"line\"><span class=\"number\">322<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$this<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">connID<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">next_result<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">323<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if&nbsp;(<\/span><span style=\"color: #c7c7c7\">$res&nbsp;<\/span><span style=\"color: #f1ce61;\">=&nbsp;<\/span><span style=\"color: #c7c7c7\">$this<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">connID<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">store_result<\/span><span style=\"color: #f1ce61;\">())\n<span class=\"line\"><span class=\"number\">324<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{\n<span class=\"line\"><span class=\"number\">325<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$res<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">free<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">326<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">327<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">328<\/span> \n<span class='line highlight'><span class='number'>329<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;return&nbsp;$this-&gt;connID-&gt;query($this-&gt;prepQuery($sql));\n<\/span><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><span class=\"line\"><span class=\"number\">330<\/span> &nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">331<\/span> \n<span class=\"line\"><span class=\"number\">332<\/span> &nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #767a7e; font-style: italic\">\/\/--------------------------------------------------------------------\n<span class=\"line\"><span class=\"number\">333<\/span> \n<span class=\"line\"><span class=\"number\">334<\/span> &nbsp;&nbsp;&nbsp;&nbsp;\/**\n<span class=\"line\"><span class=\"number\">335<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;Prep&nbsp;the&nbsp;query\n<span class=\"line\"><span class=\"number\">336<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*\n<\/span><\/code><\/pre>"}
地址: http://www.xcx.com/admin.php?s=httpapi&c=http&m=test_index&id=4&is_ajax=1
来源: http://www.xcx.com/admin.php?s=httpapi&c=http&m=index&total=4&order=id+desc


2019-07-26 09:02:11 --> mysqli_sql_exception
文件: D:\phpStudy\PHPTutorial\WWW\tpcmf_xcx\dayrui\System\Database\MySQLi\Connection.php
行号: 329
错误: Table 'tpcmf_xcx.dr_1_share_categoey' doesn't exist
{"html":"<pre><code><span class=\"line\"><span class=\"number\">322<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$this<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">connID<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">next_result<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">323<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if&nbsp;(<\/span><span style=\"color: #c7c7c7\">$res&nbsp;<\/span><span style=\"color: #f1ce61;\">=&nbsp;<\/span><span style=\"color: #c7c7c7\">$this<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">connID<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">store_result<\/span><span style=\"color: #f1ce61;\">())\n<span class=\"line\"><span class=\"number\">324<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{\n<span class=\"line\"><span class=\"number\">325<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$res<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">free<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">326<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">327<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">328<\/span> \n<span class='line highlight'><span class='number'>329<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;return&nbsp;$this-&gt;connID-&gt;query($this-&gt;prepQuery($sql));\n<\/span><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><span class=\"line\"><span class=\"number\">330<\/span> &nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">331<\/span> \n<span class=\"line\"><span class=\"number\">332<\/span> &nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #767a7e; font-style: italic\">\/\/--------------------------------------------------------------------\n<span class=\"line\"><span class=\"number\">333<\/span> \n<span class=\"line\"><span class=\"number\">334<\/span> &nbsp;&nbsp;&nbsp;&nbsp;\/**\n<span class=\"line\"><span class=\"number\">335<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;Prep&nbsp;the&nbsp;query\n<span class=\"line\"><span class=\"number\">336<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*\n<\/span><\/code><\/pre>"}
地址: http://www.xcx.com/admin.php?s=httpapi&c=http&m=test_index&id=4&is_ajax=1
来源: http://www.xcx.com/admin.php?s=httpapi&c=http&m=index&total=4&order=id+desc


2019-07-26 09:02:17 --> mysqli_sql_exception
文件: D:\phpStudy\PHPTutorial\WWW\tpcmf_xcx\dayrui\System\Database\MySQLi\Connection.php
行号: 329
错误: Table 'tpcmf_xcx.dr_1_share_categoey' doesn't exist
{"html":"<pre><code><span class=\"line\"><span class=\"number\">322<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$this<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">connID<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">next_result<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">323<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if&nbsp;(<\/span><span style=\"color: #c7c7c7\">$res&nbsp;<\/span><span style=\"color: #f1ce61;\">=&nbsp;<\/span><span style=\"color: #c7c7c7\">$this<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">connID<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">store_result<\/span><span style=\"color: #f1ce61;\">())\n<span class=\"line\"><span class=\"number\">324<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{\n<span class=\"line\"><span class=\"number\">325<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$res<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">free<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">326<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">327<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">328<\/span> \n<span class='line highlight'><span class='number'>329<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;return&nbsp;$this-&gt;connID-&gt;query($this-&gt;prepQuery($sql));\n<\/span><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><span class=\"line\"><span class=\"number\">330<\/span> &nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">331<\/span> \n<span class=\"line\"><span class=\"number\">332<\/span> &nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #767a7e; font-style: italic\">\/\/--------------------------------------------------------------------\n<span class=\"line\"><span class=\"number\">333<\/span> \n<span class=\"line\"><span class=\"number\">334<\/span> &nbsp;&nbsp;&nbsp;&nbsp;\/**\n<span class=\"line\"><span class=\"number\">335<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;Prep&nbsp;the&nbsp;query\n<span class=\"line\"><span class=\"number\">336<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*\n<\/span><\/code><\/pre>"}
地址: http://www.xcx.com/admin.php?s=httpapi&c=http&m=test_index&id=4&is_ajax=1
来源: http://www.xcx.com/admin.php?s=httpapi&c=http&m=index&total=4&order=id+desc


2019-07-26 09:02:20 --> mysqli_sql_exception
文件: D:\phpStudy\PHPTutorial\WWW\tpcmf_xcx\dayrui\System\Database\MySQLi\Connection.php
行号: 329
错误: Table 'tpcmf_xcx.dr_1_share_categoey' doesn't exist
{"html":"<pre><code><span class=\"line\"><span class=\"number\">322<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$this<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">connID<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">next_result<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">323<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if&nbsp;(<\/span><span style=\"color: #c7c7c7\">$res&nbsp;<\/span><span style=\"color: #f1ce61;\">=&nbsp;<\/span><span style=\"color: #c7c7c7\">$this<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">connID<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">store_result<\/span><span style=\"color: #f1ce61;\">())\n<span class=\"line\"><span class=\"number\">324<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{\n<span class=\"line\"><span class=\"number\">325<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$res<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">free<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">326<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">327<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">328<\/span> \n<span class='line highlight'><span class='number'>329<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;return&nbsp;$this-&gt;connID-&gt;query($this-&gt;prepQuery($sql));\n<\/span><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><span class=\"line\"><span class=\"number\">330<\/span> &nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">331<\/span> \n<span class=\"line\"><span class=\"number\">332<\/span> &nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #767a7e; font-style: italic\">\/\/--------------------------------------------------------------------\n<span class=\"line\"><span class=\"number\">333<\/span> \n<span class=\"line\"><span class=\"number\">334<\/span> &nbsp;&nbsp;&nbsp;&nbsp;\/**\n<span class=\"line\"><span class=\"number\">335<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;Prep&nbsp;the&nbsp;query\n<span class=\"line\"><span class=\"number\">336<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*\n<\/span><\/code><\/pre>"}
地址: http://www.xcx.com/admin.php?s=httpapi&c=http&m=test_index&id=4&is_ajax=1
来源: http://www.xcx.com/admin.php?s=httpapi&c=http&m=index&total=4&order=id+desc


2019-07-26 09:11:37 --> ParseError
文件: D:\phpStudy\PHPTutorial\WWW\tpcmf_xcx\dayrui\App\Httpapi\Api\cate.php
行号: 18
错误: syntax error, unexpected '' (T_ENCAPSED_AND_WHITESPACE), expecting '-' or identifier (T_STRING) or variable (T_VARIABLE) or number (T_NUM_STRING)
{"html":"<pre><code><span class=\"line\"><span class=\"number\">11<\/span> <\/span><span style=\"color: #c7c7c7\">$data&nbsp;<\/span><span style=\"color: #f1ce61;\">=&nbsp;\\<\/span><span style=\"color: #c7c7c7\">Phpcmf<\/span><span style=\"color: #f1ce61;\">\\<\/span><span style=\"color: #c7c7c7\">Service<\/span><span style=\"color: #f1ce61;\">::<\/span><span style=\"color: #c7c7c7\">M<\/span><span style=\"color: #f1ce61;\">()-&gt;<\/span><span style=\"color: #c7c7c7\">table<\/span><span style=\"color: #f1ce61;\">(<\/span><span style=\"color: #869d6a\">'1_share_category'<\/span><span style=\"color: #f1ce61;\">)-&gt;<\/span><span style=\"color: #c7c7c7\">getAll<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">12<\/span> \n<span class=\"line\"><span class=\"number\">13<\/span> if&nbsp;(<\/span><span style=\"color: #c7c7c7\">$data<\/span><span style=\"color: #f1ce61;\">)&nbsp;{\n<span class=\"line\"><span class=\"number\">14<\/span> &nbsp;&nbsp;&nbsp;&nbsp;foreach&nbsp;(<\/span><span style=\"color: #c7c7c7\">$data&nbsp;<\/span><span style=\"color: #f1ce61;\">as&nbsp;<\/span><span style=\"color: #c7c7c7\">$r<\/span><span style=\"color: #f1ce61;\">)&nbsp;{\n<span class=\"line\"><span class=\"number\">15<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n<span class=\"line\"><span class=\"number\">16<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if&nbsp;(<\/span><span style=\"color: #c7c7c7\">$r<\/span><span style=\"color: #f1ce61;\">[<\/span><span style=\"color: #869d6a\">'pid'<\/span><span style=\"color: #f1ce61;\">]==<\/span><span style=\"color: #c7c7c7\">0<\/span><span style=\"color: #f1ce61;\">)&nbsp;{\n<span class=\"line\"><span class=\"number\">17<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n<span class='line highlight'><span class='number'>18<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$query&nbsp;=&nbsp;$db-&gt;query(\"SELECT&nbsp;id,name&nbsp;where&nbsp;pid=$r['id']&nbsp;FROM&nbsp;dr_1_share_category\");\n<\/span><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #869d6a\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #869d6a\"><\/span><span style=\"color: #f1ce61;\"><span class=\"line\"><span class=\"number\">19<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$results&nbsp;<\/span><span style=\"color: #f1ce61;\">=&nbsp;<\/span><span style=\"color: #c7c7c7\">$query<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">getResult<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">20<\/span> \n<span class=\"line\"><span class=\"number\">21<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$return<\/span><span style=\"color: #f1ce61;\">[]&nbsp;=&nbsp;[\n<span class=\"line\"><span class=\"number\">22<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #869d6a\">'id'&nbsp;<\/span><span style=\"color: #f1ce61;\">=&gt;&nbsp;<\/span><span style=\"color: #c7c7c7\">$r<\/span><span style=\"color: #f1ce61;\">[<\/span><span style=\"color: #869d6a\">'id'<\/span><span style=\"color: #f1ce61;\">],\n<span class=\"line\"><span class=\"number\">23<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #869d6a\">'catename'&nbsp;<\/span><span style=\"color: #f1ce61;\">=&gt;&nbsp;<\/span><span style=\"color: #c7c7c7\">$r<\/span><span style=\"color: #f1ce61;\">[<\/span><span style=\"color: #869d6a\">'name'<\/span><span style=\"color: #f1ce61;\">],\n<span class=\"line\"><span class=\"number\">24<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #869d6a\">'item'&nbsp;<\/span><span style=\"color: #f1ce61;\">=&gt;&nbsp;<\/span><span style=\"color: #c7c7c7\">$results\n<span class=\"line\"><span class=\"number\">25<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #f1ce61;\">];\n<\/span><\/code><\/pre>"}
地址: http://www.xcx.com/admin.php?s=httpapi&c=http&m=test_index&id=4&is_ajax=1
来源: http://www.xcx.com/admin.php?s=httpapi&c=http&m=index


2019-07-26 09:12:38 --> ParseError
文件: D:\phpStudy\PHPTutorial\WWW\tpcmf_xcx\dayrui\App\Httpapi\Api\cate.php
行号: 18
错误: syntax error, unexpected '' (T_ENCAPSED_AND_WHITESPACE), expecting '-' or identifier (T_STRING) or variable (T_VARIABLE) or number (T_NUM_STRING)
{"html":"<pre><code><span class=\"line\"><span class=\"number\">11<\/span> <\/span><span style=\"color: #c7c7c7\">$data&nbsp;<\/span><span style=\"color: #f1ce61;\">=&nbsp;\\<\/span><span style=\"color: #c7c7c7\">Phpcmf<\/span><span style=\"color: #f1ce61;\">\\<\/span><span style=\"color: #c7c7c7\">Service<\/span><span style=\"color: #f1ce61;\">::<\/span><span style=\"color: #c7c7c7\">M<\/span><span style=\"color: #f1ce61;\">()-&gt;<\/span><span style=\"color: #c7c7c7\">table<\/span><span style=\"color: #f1ce61;\">(<\/span><span style=\"color: #869d6a\">'1_share_category'<\/span><span style=\"color: #f1ce61;\">)-&gt;<\/span><span style=\"color: #c7c7c7\">getAll<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">12<\/span> \n<span class=\"line\"><span class=\"number\">13<\/span> if&nbsp;(<\/span><span style=\"color: #c7c7c7\">$data<\/span><span style=\"color: #f1ce61;\">)&nbsp;{\n<span class=\"line\"><span class=\"number\">14<\/span> &nbsp;&nbsp;&nbsp;&nbsp;foreach&nbsp;(<\/span><span style=\"color: #c7c7c7\">$data&nbsp;<\/span><span style=\"color: #f1ce61;\">as&nbsp;<\/span><span style=\"color: #c7c7c7\">$r<\/span><span style=\"color: #f1ce61;\">)&nbsp;{\n<span class=\"line\"><span class=\"number\">15<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n<span class=\"line\"><span class=\"number\">16<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if&nbsp;(<\/span><span style=\"color: #c7c7c7\">$r<\/span><span style=\"color: #f1ce61;\">[<\/span><span style=\"color: #869d6a\">'pid'<\/span><span style=\"color: #f1ce61;\">]==<\/span><span style=\"color: #c7c7c7\">0<\/span><span style=\"color: #f1ce61;\">)&nbsp;{\n<span class=\"line\"><span class=\"number\">17<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n<span class='line highlight'><span class='number'>18<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$query&nbsp;=&nbsp;$db-&gt;query(\"SELECT&nbsp;id,name&nbsp;where&nbsp;pid=\".\"$r['id']\"&nbsp;.\"FROM&nbsp;dr_1_share_category\");\n<\/span><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #869d6a\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #869d6a\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #869d6a\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #869d6a\"><\/span><span style=\"color: #f1ce61;\"><span class=\"line\"><span class=\"number\">19<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$results&nbsp;<\/span><span style=\"color: #f1ce61;\">=&nbsp;<\/span><span style=\"color: #c7c7c7\">$query<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">getResult<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">20<\/span> \n<span class=\"line\"><span class=\"number\">21<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$return<\/span><span style=\"color: #f1ce61;\">[]&nbsp;=&nbsp;[\n<span class=\"line\"><span class=\"number\">22<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #869d6a\">'id'&nbsp;<\/span><span style=\"color: #f1ce61;\">=&gt;&nbsp;<\/span><span style=\"color: #c7c7c7\">$r<\/span><span style=\"color: #f1ce61;\">[<\/span><span style=\"color: #869d6a\">'id'<\/span><span style=\"color: #f1ce61;\">],\n<span class=\"line\"><span class=\"number\">23<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #869d6a\">'catename'&nbsp;<\/span><span style=\"color: #f1ce61;\">=&gt;&nbsp;<\/span><span style=\"color: #c7c7c7\">$r<\/span><span style=\"color: #f1ce61;\">[<\/span><span style=\"color: #869d6a\">'name'<\/span><span style=\"color: #f1ce61;\">],\n<span class=\"line\"><span class=\"number\">24<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #869d6a\">'item'&nbsp;<\/span><span style=\"color: #f1ce61;\">=&gt;&nbsp;<\/span><span style=\"color: #c7c7c7\">$results\n<span class=\"line\"><span class=\"number\">25<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #f1ce61;\">];\n<\/span><\/code><\/pre>"}
地址: http://www.xcx.com/admin.php?s=httpapi&c=http&m=test_index&id=4&is_ajax=1
来源: http://www.xcx.com/admin.php?s=httpapi&c=http&m=index


2019-07-26 09:15:19 --> ParseError
文件: D:\phpStudy\PHPTutorial\WWW\tpcmf_xcx\dayrui\App\Httpapi\Api\cate.php
行号: 18
错误: syntax error, unexpected '' (T_ENCAPSED_AND_WHITESPACE), expecting '-' or identifier (T_STRING) or variable (T_VARIABLE) or number (T_NUM_STRING)
{"html":"<pre><code><span class=\"line\"><span class=\"number\">11<\/span> <\/span><span style=\"color: #c7c7c7\">$data&nbsp;<\/span><span style=\"color: #f1ce61;\">=&nbsp;\\<\/span><span style=\"color: #c7c7c7\">Phpcmf<\/span><span style=\"color: #f1ce61;\">\\<\/span><span style=\"color: #c7c7c7\">Service<\/span><span style=\"color: #f1ce61;\">::<\/span><span style=\"color: #c7c7c7\">M<\/span><span style=\"color: #f1ce61;\">()-&gt;<\/span><span style=\"color: #c7c7c7\">table<\/span><span style=\"color: #f1ce61;\">(<\/span><span style=\"color: #869d6a\">'1_share_category'<\/span><span style=\"color: #f1ce61;\">)-&gt;<\/span><span style=\"color: #c7c7c7\">getAll<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">12<\/span> \n<span class=\"line\"><span class=\"number\">13<\/span> if&nbsp;(<\/span><span style=\"color: #c7c7c7\">$data<\/span><span style=\"color: #f1ce61;\">)&nbsp;{\n<span class=\"line\"><span class=\"number\">14<\/span> &nbsp;&nbsp;&nbsp;&nbsp;foreach&nbsp;(<\/span><span style=\"color: #c7c7c7\">$data&nbsp;<\/span><span style=\"color: #f1ce61;\">as&nbsp;<\/span><span style=\"color: #c7c7c7\">$r<\/span><span style=\"color: #f1ce61;\">)&nbsp;{\n<span class=\"line\"><span class=\"number\">15<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n<span class=\"line\"><span class=\"number\">16<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if&nbsp;(<\/span><span style=\"color: #c7c7c7\">$r<\/span><span style=\"color: #f1ce61;\">[<\/span><span style=\"color: #869d6a\">'pid'<\/span><span style=\"color: #f1ce61;\">]==<\/span><span style=\"color: #c7c7c7\">0<\/span><span style=\"color: #f1ce61;\">)&nbsp;{\n<span class=\"line\"><span class=\"number\">17<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n<span class='line highlight'><span class='number'>18<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$query&nbsp;=&nbsp;$db-&gt;query(\"SELECT&nbsp;id,name&nbsp;where&nbsp;pid=\".\"$r['id']\"&nbsp;.\"&nbsp;FROM&nbsp;dr_1_share_category\");\n<\/span><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #869d6a\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #869d6a\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #869d6a\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #869d6a\"><\/span><span style=\"color: #f1ce61;\"><span class=\"line\"><span class=\"number\">19<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$results&nbsp;<\/span><span style=\"color: #f1ce61;\">=&nbsp;<\/span><span style=\"color: #c7c7c7\">$query<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">getResult<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">20<\/span> \n<span class=\"line\"><span class=\"number\">21<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$return<\/span><span style=\"color: #f1ce61;\">[]&nbsp;=&nbsp;[\n<span class=\"line\"><span class=\"number\">22<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #869d6a\">'id'&nbsp;<\/span><span style=\"color: #f1ce61;\">=&gt;&nbsp;<\/span><span style=\"color: #c7c7c7\">$r<\/span><span style=\"color: #f1ce61;\">[<\/span><span style=\"color: #869d6a\">'id'<\/span><span style=\"color: #f1ce61;\">],\n<span class=\"line\"><span class=\"number\">23<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #869d6a\">'catename'&nbsp;<\/span><span style=\"color: #f1ce61;\">=&gt;&nbsp;<\/span><span style=\"color: #c7c7c7\">$r<\/span><span style=\"color: #f1ce61;\">[<\/span><span style=\"color: #869d6a\">'name'<\/span><span style=\"color: #f1ce61;\">],\n<span class=\"line\"><span class=\"number\">24<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #869d6a\">'item'&nbsp;<\/span><span style=\"color: #f1ce61;\">=&gt;&nbsp;<\/span><span style=\"color: #c7c7c7\">$results\n<span class=\"line\"><span class=\"number\">25<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #f1ce61;\">];\n<\/span><\/code><\/pre>"}
地址: http://www.xcx.com/admin.php?s=httpapi&c=http&m=test_index&id=4&is_ajax=1
来源: http://www.xcx.com/admin.php?s=httpapi&c=http&m=index


2019-07-26 09:16:20 --> mysqli_sql_exception
文件: D:\phpStudy\PHPTutorial\WWW\tpcmf_xcx\dayrui\System\Database\MySQLi\Connection.php
行号: 329
错误: Unknown column 'ArrayArray' in 'where clause'
{"html":"<pre><code><span class=\"line\"><span class=\"number\">322<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$this<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">connID<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">next_result<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">323<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if&nbsp;(<\/span><span style=\"color: #c7c7c7\">$res&nbsp;<\/span><span style=\"color: #f1ce61;\">=&nbsp;<\/span><span style=\"color: #c7c7c7\">$this<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">connID<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">store_result<\/span><span style=\"color: #f1ce61;\">())\n<span class=\"line\"><span class=\"number\">324<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{\n<span class=\"line\"><span class=\"number\">325<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #c7c7c7\">$res<\/span><span style=\"color: #f1ce61;\">-&gt;<\/span><span style=\"color: #c7c7c7\">free<\/span><span style=\"color: #f1ce61;\">();\n<span class=\"line\"><span class=\"number\">326<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">327<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">328<\/span> \n<span class='line highlight'><span class='number'>329<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;return&nbsp;$this-&gt;connID-&gt;query($this-&gt;prepQuery($sql));\n<\/span><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><\/span><span style=\"color: #c7c7c7\"><\/span><span style=\"color: #f1ce61;\"><span class=\"line\"><span class=\"number\">330<\/span> &nbsp;&nbsp;&nbsp;&nbsp;}\n<span class=\"line\"><span class=\"number\">331<\/span> \n<span class=\"line\"><span class=\"number\">332<\/span> &nbsp;&nbsp;&nbsp;&nbsp;<\/span><span style=\"color: #767a7e; font-style: italic\">\/\/--------------------------------------------------------------------\n<span class=\"line\"><span class=\"number\">333<\/span> \n<span class=\"line\"><span class=\"number\">334<\/span> &nbsp;&nbsp;&nbsp;&nbsp;\/**\n<span class=\"line\"><span class=\"number\">335<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;Prep&nbsp;the&nbsp;query\n<span class=\"line\"><span class=\"number\">336<\/span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*\n<\/span><\/code><\/pre>"}
地址: http://www.xcx.com/admin.php?s=httpapi&c=http&m=test_index&id=4&is_ajax=1
来源: http://www.xcx.com/admin.php?s=httpapi&c=http&m=index


