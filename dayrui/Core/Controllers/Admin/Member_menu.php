<?php namespace Phpcmf\Controllers\Admin;

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



class Member_menu extends \Phpcmf\Common
{
	private $form; // 表单验证配置

	public function __construct(...$params) {
		parent::__construct(...$params);
		\Phpcmf\Service::V()->assign('menu', \Phpcmf\Service::M('auth')->_admin_menu(
			[
				'用户中心菜单' => ['member_menu/index', 'fa fa-list-alt'],
				'初始化菜单' => ['ajax:member_menu/init', 'fa fa-refresh'],
                'help' => [381],
			]
		));
		// 表单验证配置
		$this->form = [
			'name' => [
				'name' => '菜单名称',
				'rule' => [
					'empty' => dr_lang('菜单名称不能为空')
				],
				'filter' => [],
				'length' => '200'
			],
			'icon' => [
				'name' => '菜单图标',
				'rule' => [
					'empty' => dr_lang('菜单图标不能为空')
				],
				'filter' => [],
				'length' => '30'
			],
			'uri' => [
				'name' => '系统路径',
				'rule' => [
					'empty' => dr_lang('系统路径不能为空')
				],
				'filter' => [],
				'length' => '200'
			],
			'url' => [
				'name' => '实际地址',
				'rule' => [
					'empty' => dr_lang('实际地址不能为空')
				],
				'filter' => [
					'url'
				],
				'length' => '200'
			],
		];
	}

	public function index() {

		// 用户组
		$group = $color = [];
		$data = \Phpcmf\Service::M()->db->table('member_group')->orderBy('displayorder ASC,id ASC')->get()->getResultArray();
		if ($data) {
			foreach ($data as $i => $t) {
				$group[$t['id']] = $t;
			}
		}

		\Phpcmf\Service::V()->assign([
			'data' => \Phpcmf\Service::M('Menu')->gets('member'),
			'group' => $group,
			'color' => ['blue', 'red', 'green', 'dark', 'yellow'],
		]);
		\Phpcmf\Service::V()->display('member_menu_list.html');
	}

	public function group_add() {

		$ids = \Phpcmf\Service::L('Input')->get_post_ids();
		!$ids && $this->_json(0, dr_lang('你还没有选择呢'));

		$gid = (int)\Phpcmf\Service::L('Input')->post('groupid');
		!$gid && $this->_json(0, dr_lang('你还没有选择用户组'));

		$data = \Phpcmf\Service::M()->db->table('member_menu')->whereIN('id', $ids)->get()->getResultArray();
		!$data && $this->_json(0, dr_lang('无可用菜单'));

		foreach ($data as $t) {
			$value = dr_string2array($t['group']);
			$value[$gid] = $gid;
			\Phpcmf\Service::M()->db->table('member_menu')->where('id', $t['id'])->update([
				'group' => dr_array2string($value)
			]);
		}

        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
		$this->_json(1, dr_lang('划分成功'));
	}

	public function group_del() {

		$id = (int)\Phpcmf\Service::L('Input')->get('id');
		$data = \Phpcmf\Service::M('Menu')->getRowData('member', $id);
		!$data && $this->_json(0, dr_lang('菜单不存在'));

		$gid = (int)\Phpcmf\Service::L('Input')->get('gid');
		!$gid && $this->_json(0, dr_lang('用户组id不存在'));

		$value = dr_string2array($data['group']);
		unset($value[$gid]);

		\Phpcmf\Service::M()->db->table('member_menu')->where('id', $id)->update([
			'group' => dr_array2string($value)
		]);

        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
		$this->_json(1, dr_lang('删除成功'));
	}

	public function site_add() {

		$ids = \Phpcmf\Service::L('Input')->get_post_ids();
		!$ids && $this->_json(0, dr_lang('你还没有选择呢'));

		$sid = (int)\Phpcmf\Service::L('Input')->post('siteid');
		!$sid && $this->_json(0, dr_lang('你还没有选择站点'));

		$data = \Phpcmf\Service::M()->db->table('member_menu')->whereIN('id', $ids)->get()->getResultArray();
		!$data && $this->_json(0, dr_lang('无可用菜单'));

		foreach ($data as $t) {
			$value = dr_string2array($t['site']);
			$value[$sid] = $sid;
			\Phpcmf\Service::M()->db->table('member_menu')->where('id', $t['id'])->update([
				'site' => dr_array2string($value)
			]);
		}

        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
		$this->_json(1, dr_lang('划分成功'));
	}

	public function site_del() {

		$id = (int)\Phpcmf\Service::L('Input')->get('id');
		$data = \Phpcmf\Service::M('Menu')->getRowData('member', $id);
		!$data && $this->_json(0, dr_lang('菜单不存在'));

        $sid = (int)\Phpcmf\Service::L('Input')->get('sid');
		!$sid && $this->_json(0, dr_lang('站点id不存在'));

		$value = dr_string2array($data['site']);
		unset($value[$sid]);

		\Phpcmf\Service::M()->db->table('member_menu')->where('id', $id)->update([
			'site' => dr_array2string($value)
		]);

        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
		$this->_json(1, dr_lang('删除成功'));
	}


	public function add() {

		$pid = intval(\Phpcmf\Service::L('Input')->get('pid'));
		$top = \Phpcmf\Service::M('Menu')->get_top('member');

		if (IS_AJAX_POST) {
			$data = \Phpcmf\Service::L('Input')->post('data');
			$this->_validation($data);
			\Phpcmf\Service::L('Input')->system_log('添加用户中心菜单: '.$data['name']);
            if (\Phpcmf\Service::M('Menu')->_add('member', $pid, $data)) {
                \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
                $this->_json(1, dr_lang('操作成功'));
            } else {
                $this->_json(0, dr_lang('操作失败'));
            }
		}

		\Phpcmf\Service::V()->assign([
			'top' => $top,
			'type' => $pid,
			'form' => dr_form_hidden()
		]);
		\Phpcmf\Service::V()->display('member_menu_add.html');
		exit;
	}

	public function edit() {

		$id = intval(\Phpcmf\Service::L('Input')->get('id'));
		$data = \Phpcmf\Service::M('Menu')->getRowData('member', $id);
		!$data && $this->_json(0, dr_lang('数据#%s不存在', $id));

		$top = \Phpcmf\Service::M('Menu')->get_top('member');

		if (IS_AJAX_POST) {
			$data = \Phpcmf\Service::L('Input')->post('data');
			$this->_validation($data);
			\Phpcmf\Service::M('Menu')->_update('member', $id, $data);
			\Phpcmf\Service::L('Input')->system_log('修改用户中心菜单: '.$data['name']);

            \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
			exit($this->_json(1, dr_lang('操作成功')));
		}

		\Phpcmf\Service::V()->assign([
			'top' => $top,
			'type' => $data['pid'],
			'data' => $data,
			'form' => dr_form_hidden(),
		]);
		\Phpcmf\Service::V()->display('member_menu_add.html');
		exit;
	}

	public function del() {

		$ids = \Phpcmf\Service::L('Input')->get_post_ids();
		!$ids && exit($this->_json(0, dr_lang('你还没有选择呢')));

		\Phpcmf\Service::M('Menu')->_delete('member', $ids);
        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
		\Phpcmf\Service::L('Input')->system_log('批量删除用户中心菜单: '. @implode(',', $ids));
		exit($this->_json(1, dr_lang('操作成功'), ['ids' => $ids]));
	}


	// 初始化
	public function init() {

		\Phpcmf\Service::M('Menu')->init('member');
		\Phpcmf\Service::L('Input')->system_log('初始化用户中心菜单');
		exit($this->_json(1, dr_lang('初始化菜单成功，请按F5刷新整个页面')));
	}

	// 隐藏或者启用
	public function use_edit() {

		$i = intval(\Phpcmf\Service::L('Input')->get('id'));
		$v = \Phpcmf\Service::M('Menu')->_uesd('member', $i);
		$v == -1 && exit($this->_json(0, dr_lang('数据#%s不存在', $i), ['value' => $v]));
		\Phpcmf\Service::L('Input')->system_log('修改用户中心菜单状态: '. $i);
        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
		exit($this->_json(1, dr_lang($v ? '此菜单已被隐藏' : '此菜单已被启用'), ['value' => $v]));

	}

	// 保存数据
	public function save_edit() {

		$i = intval(\Phpcmf\Service::L('Input')->get('id'));
		\Phpcmf\Service::M('Menu')->_save(
			'member',
			$i,
			dr_safe_replace(\Phpcmf\Service::L('Input')->get('name')),
			dr_safe_replace(\Phpcmf\Service::L('Input')->get('value'))
		);

        \Phpcmf\Service::M('cache')->sync_cache(''); // 自动更新缓存
		\Phpcmf\Service::L('Input')->system_log('修改用户中心菜单信息: '. $i);
		exit($this->_json(1, dr_lang('更改成功')));
	}


	// 验证数据
	private function _validation($data) {

		if ($data['type'] != 3) {
			// 非链接菜单时不录入url和uri
			unset($this->form['url'], $this->form['uri']);
		} else {
			// url和uri 只验证一个
			if ($data['url']) unset($this->form['uri']);
			if ($data['uri']) unset($this->form['url']);
		}
		list($data, $return) = \Phpcmf\Service::L('form')->validation($data, $this->form);
		$return && exit($this->_json(0, $return['error'], ['field' => $return['name']]));

	}

}
