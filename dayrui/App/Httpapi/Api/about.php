<?php
/**
 * api 示例文件
 * 变量介绍
 * $return 表示标准返回变量
 */

$return = []; // 返回数据
// 查询全部会员，并返回username和email
// $data = \Phpcmf\Service::M()->table('member')->getAll();
$data = \Phpcmf\Service::M()->table('1_share_category')->where('id=11')->getAll();

if ($data) {
    $return = $data[0];
}