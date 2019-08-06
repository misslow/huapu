<?php
/**
 * api 示例文件
 * 变量介绍
 * $return 表示标准返回变量
 */

$return = []; // 返回数据
// 查询全部会员，并返回username和email
// $data = \Phpcmf\Service::M()->table('member')->getAll();
$data = \Phpcmf\Service::M()->table('1_share_category')->where('tid!=0 and id!=10')->getAll();

if ($data) {
    foreach ($data as $r) {
        
        if ($r['pid']==0) {
        	
        	// $query = $db->query("SELECT id,name where pid="."$r['id']" ." FROM dr_1_share_category");
			$results = \Phpcmf\Service::M()->table('1_share_category')->where('pid='.$r['id'])->getAll();
			
			foreach ($results as $key => $value) {
				$res = \Phpcmf\Service::M()->table('1_news')->where('catid='.$value['id'])->getAll();
				$re = [];
				foreach ($res as $k => $v) {
					$re[$v['id']] = $v['title'];
				}
				$results[$key]['time'] = 123;
				
				$results[$key]['major'] = $re;
			}
			$return[] = [
	            'id' => $r['id'],
	            'name' => $r['name'],
	            'submajor' => $results
        	];
        }
    }
}