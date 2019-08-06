<?php namespace Phpcmf\Controllers\Admin;

class Home extends \Phpcmf\Common
{

	public function __construct(...$params) {
		parent::__construct(...$params);
		\Phpcmf\Service::V()->assign('menu', \Phpcmf\Service::M('auth')->_admin_menu(
			[
				'微信公众号' => [APP_DIR.'/home/index', 'fa fa-area-chart'],
			]
		));

	}

	public function index() {

        $data = [];
        $fans = \Phpcmf\Service::M('Weixin', 'Weixin')->update_count_fans();
        if ($fans) {
            foreach ($fans as $d => $t) {
                $data[] = [
                    'country' => substr($t['date'], 0, 4).'-'.substr($t['date'], 4, 2).'-'.substr($t['date'], 6, 2),
                    'year2004' => $t['new'],
                    'year2005' => $t['cumulate'],
                ];
            }
        }

		\Phpcmf\Service::V()->assign([
		    'fans' => $fans,
            'today' => date('Ymd'),
            'yesterday' => date('Ymd', strtotime('-1 days')),
            'data_tj' => json_encode($data),
            'usercount' => \Phpcmf\Service::M()->table(weixin_wxtable('user'))->counts(),
            'msgcount' => \Phpcmf\Service::M()->table(weixin_wxtable('message'))->where('DATEDIFF(from_unixtime(inputtime),now())=0')->counts(),
		]);
		\Phpcmf\Service::V()->display('weixin_index.html');
	}

}
