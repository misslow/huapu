<?php namespace Phpcmf\Model;

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


// 支付类
// 支付状态 status 0未付款 1付款成功 2转账中 3转账被拒绝

class Pay extends \Phpcmf\Model
{

    protected $myfield;
    protected $payname;

    // 初始化
    public function __construct(...$params) {
        parent::__construct(...$params);
        $this->myfield = [
            'recharge' => [
                'fieldtype' => 'Pay',
                'fieldname' => 'pay',
                'setting' => [
                    'option' => [
                        'payfile' => 'pay.html', // 模板文件
                        'is_finecms' => 0, // 是否启用余额付款
                    ],
                ]
            ],
            'score' => [
                'fieldtype' => 'Pay',
                'fieldname' => 'pay',
                'setting' => [
                    'option' => [
                        'payfile' => 'score.html', // 模板文件
                        'is_finecms' => 1, // 是否启用余额付款
                    ],
                ]
            ],
            'donation' => [
                'fieldtype' => 'Pay',
                'fieldname' => 'pay',
                'setting' => [
                    'option' => [
                        'payfile' => 'donation.html', // 模板文件
                        'is_finecms' => 1, // 是否启用余额付款
                    ],
                ]
            ],
            'gathering' => [
                'fieldtype' => 'Pay',
                'fieldname' => 'price',
                'setting' => [
                    'option' => [
                        'payfile' => 'gathering.html', // 模板文件
                        'is_finecms' => 1, // 是否启用余额付款
                    ],
                ]
            ],
        ];
    }

    // 获取打赏模块内容
    public function get_module_row($dir, $id, $siteid) {

        $data = $this->table($siteid.'_'.$dir)->get($id);
        if (!$data) {
            return dr_return_data(0, dr_lang('打赏内容不存在'));
        }

        return dr_return_data(1, 'ok', $data);
    }

    // 获取支付价格和内容
    public function get_pay_info($id, $field, $num = 1, $sku = '') {

        list($table, $name, $username, $thumb, $url) = $this->_get_table_name(
            $field['relatedid'],
            $field['relatedname']
        );
        if (!$table) {
            return dr_return_data(0, dr_lang('支付关联表不存在'));
        }

        $rt = $this->table($table)->get($id);
        if (!$rt) {
            return dr_return_data(0, dr_lang('支付关联表[%s]内容(#%s)不存在', $table, $id));
        } elseif (!isset($rt[$field['fieldname']])) {
            return dr_return_data(0, dr_lang('支付关联表的支付字段[%s]不存在于主表', $field['fieldname']));
        } elseif (isset($rt[$thumb]) && $rt[$thumb] && !is_numeric($rt[$thumb])) {
            // 如果是多文件字段取第一个作为缩略图
            $image = dr_string2array($rt[$thumb]);
            isset($image['file'][0]) && $image['file'][0] && $rt[$thumb] = $image['file'][0];
        }

        $title = (string)$rt[$name];
        if ($field['fieldtype'] == 'Pays' && isset($rt[$field['fieldname'].'_sku']) && $rt[$field['fieldname'].'_sku']) {
            $rt[$field['fieldname'].'_sku'] = dr_string2array($rt[$field['fieldname'].'_sku']);
            if (!$sku && $rt[$field['fieldname'].'_sku']) {
                return dr_return_data(0, dr_lang('没有选择商品属性'));
            } elseif (!isset($rt[$field['fieldname'].'_sku']['value'][$sku]) || !$rt[$field['fieldname'].'_sku']['value'][$sku]) {
                #print_r($rt[$field['fieldname'].'_sku']['value']);
                return dr_return_data(0, dr_lang('商品(#'.$rt['id'].')属性（#'.$sku.'）无效'));
            }
            $sn = (string)$rt[$field['fieldname'].'_sku']['value'][$sku]['sn'];
            $price = (float)$rt[$field['fieldname'].'_sku']['value'][$sku]['price'];
            $quantity = (int)$rt[$field['fieldname'].'_sku']['value'][$sku]['quantity'];
            list($sku_name, $sku_string) = dr_sku_name($sku, $rt[$field['fieldname'].'_sku'], 1);
        } else {
            $sn = (string)$rt[$field['fieldname'].'_sn'];
            $price = (float)$rt[$field['fieldname']];
            $quantity = (int)$rt[$field['fieldname'].'_quantity'];
            $sku_name = '';
            $sku_string = '';
        }

        // 表名-主键id-字段id-数量-sku
        $mid = $table .'-'. $id .'-'. $field['id'] .'-'. max(1, (int)$num) .'-'. ($sku ? $sku : 'null');

        return [
            'mid' => $mid,
            'num' => $num,
            'price' => $price,
            'total' => $price * $num,
            'table' => $table,
            'sn' => $sn,
            'quantity' => $quantity,
            'sku_name' => $sku_name,
            'sku_string' => $sku_string,
            'sku_value' => $sku,
            'touid' => intval($rt['uid']),
            'tousername' => (string)$rt['username'],
            'title' => $title,
            'thumb' => (string)$rt[$thumb],
            'url' => $url ? $url.$id : '',
            'data' => $rt,
        ];
    }


    // 获取支付信息的表名
    public function _get_table_name($relatedid, $relatedname) {

        $data = \Phpcmf\Service::C()->get_cache('table-pay-'.SITE_ID, $relatedname.'-'.$relatedid);
        if (!$data) {
            return [];
        }

        // 表名 - 标题字段 - 卖家账号 - 缩略图 - 地址
        return [$data['table'], $data['name'], $data['username'], $data['thumb'], $data['url']];
    }

    // 支付状态
    public function paystatus($data) {

        switch ($data['status']) {

            case 0:
                return '<a href="javascript:dr_ajaxp_url(\''.ROOT_URL.'index.php?s=api&c=pay&m=ajax&id='.$data['id'].'\');" class="label label-danger"> '.dr_lang('未付款').' </a>';
                break;

            case 1:
                return '<span class="label label-success"> '.dr_lang('已付款').' </span>';
                break;

            case 2:
                return '<span class="label label-warning"> '.dr_lang('汇款中').' </span>';
                break;

            case 5:
                return '<span class="label label-warning"> '.dr_lang('上门付').' </span>';
                break;

            case 3:
                return '<span class="label label-danger"> '.dr_lang('被拒绝').' </span>';
                break;

        }

    }

    // 用途类型
    public function paytype($mark) {

        switch ($mark) {

            // 在线充值
            case 'recharge':
                return '<span class="label label-default"> '.dr_lang('充值').' </span>';
                break;

            // 在线充值
            case 'score':
                return '<span class="label label-success"> '.dr_lang(SITE_SCORE).' </span>';
                break;

            // 系统
            case 'system':
                return '<span class="label label-danger"> '.dr_lang('系统').' </span>';
                break;

            // 订单
            case 'order':
                return '<span class="label label-success"> '.dr_lang('订单').' </span>';
                break;

            // 订单
            case 'orders':
                return '<span class="label label-success"> '.dr_lang('订单').' </span>';
                break;

            // 提现
            case 'cash':
                return '<span class="label label-success"> '.dr_lang('提现').' </span>';
                break;

            // 其他来自自定义字段
            default:
                list($rname, $rid, $fid) = explode('-', $mark);
                switch ($rname) {

                    case 'order':
                        // 订单
                        return '<span class="label label-success"> '.dr_lang('订单').' </span>';
                        break;

                    // 订单
                    case 'orders':
                        return '<span class="label label-success"> '.dr_lang('订单').' </span>';
                        break;

                    case 'gathering':
                        // 收款插件
                        return '<span class="label label-danger"> '.dr_lang('收款').' </span>';
                        break;

                    case 'my':
                        // 二次开发
                        return $this->my_pay_obj($rid)->paytype();
                        break;

                    case 'donation':
                        // 打赏作者
                        return '<span class="label label-danger"> '.dr_lang('打赏').' </span>';
                        break;

                    default:
                        // 来自自定义字段
                        $field = \Phpcmf\Service::C()->get_cache('table-field', $fid);
                        if ($field['relatedname'] == 'module') {
                            // 模块
                            return '<span class="label label-success"> '.dr_lang('模块').' </span>';
                        }
                        return '<span class="label label-warning"> '.dr_lang('其他').' </span>';
                        break;
                }
                break;
        }
    }

    // 付款名称
    public function payname($name) {

        switch ($name) {

            case 'meet';
                return '上门';
                break;

            case 'remit';
                return '汇款';
                break;

            case 'system';
                return '系统';
                break;

            case 'admin';
                return '后台';
                break;

            case 'finecms';
                return '余额';
                break;

            case 'weixin';
                return '微信';
                break;

            case 'alipay';
                return '支付宝';
                break;

            default;

                if (!$this->payname[$name]) {
                    $this->payname[$name] = require WEBPATH.'api/pay/'.$name.'/config.php';
                }

                if (isset($this->payname[$name]['name']) && $this->payname[$name]['name']) {
                    return $this->payname[$name]['name'];
                }

                return $name;
                break;
        }
    }


    /**
     * 支付表单调用
     * mark     表名-主键id-字段id
     * value    支付金额
     * title    支付说明
     * */
    public function payform($mark, $value = 0, $title = '', $url = '', $remove_div = 0) {

        switch ($mark) {

            // 在线充值
            case 'recharge':
                $field = $this->myfield[$mark];
                break;

            // 金币充值
            case 'score':
                $field = $this->myfield[$mark];
                break;

            // 其他来自自定义字段
            default:
                list($rname, $rid, $fid, $num, $sku) = explode('-', $mark);
                switch ($rname) {

                    case 'gathering':
                        // 来自付款
                        $field = $this->myfield[$rname];
                        $value = ceil($value);
                        break;

                    case 'my':
                        // 来自二次开发
                        $obj = $this->my_pay_obj($rid);
                        $field = $obj->get_myfield();
                        if (method_exists($obj, 'pay_before')) {
                            $rt = $obj->pay_before($fid, $num, $sku, SITE_ID);
                            if ($rt) {
                                return $rt;
                            }
                        }
                        $value = $obj->get_price($fid, $num, $sku, SITE_ID);
                        break;

                    case 'donation':
                        // 打赏作者
                        $field = $this->myfield[$rname];
                        $value = ceil($value);
                        break;

                    default:
                        // 来自自定义字段
                        $field = \Phpcmf\Service::C()->get_cache('table-field', $fid);
                        if (!$field) {
                            return dr_lang('支付字段不存在');
                        }
                        break;
                }
                break;
        }

        $f = \Phpcmf\Service::L('Field')->get($field['fieldtype']);
        $f->remove_div = $remove_div;
        $html = $f->input($field, $value, [
            'mark' => $mark,
            'title' => $title,
            'url' => $url,
        ]);
        return $html;
    }

    // 增加流水记录
    public function add_paylog($data) {
        $data = [
            'site' => SITE_ID,
            'mid' => $data['mid'],
            'uid' => $data['uid'],
            'username' => $data['username'],
            'touid' => (int)$data['touid'],
            'tousername' => $data['tousername'] ? $data['tousername'] : '',
            'title' => $data['title'],
            'value' => $data['value'],
            'type' => $data['type'],
            'url' => !$data['url'] ? '' : dr_url_prefix($data['url']),
            'status' => (int)$data['status'],
            'result' => $data['result'] ? $data['result'] : '',
            'paytime' => $data['paytime'] ? $data['paytime'] : 0,
            'inputtime' => SYS_TIME,
        ];
        $rt = $this->table('member_paylog')->insert($data);
        $data['id'] = $rt['code'];
        $rt['data'] = $data;
        return $rt;
    }

    // 给账户充值money
    public function add_money($uid, $value) {
        return \Phpcmf\Service::M('member')->add_money($uid, $value);
    }

    // 充值成功的返回
    public function paysuccess($mark, $payid) {

        list($a, $id) = explode('-', $mark);
        $data = $this->table('member_paylog')->get($id);
        if (!$data) {
            return dr_return_data(0, dr_lang('支付记录不存在'));
        } elseif ($data['status'] == 1) {
            return dr_return_data(0, dr_lang('此账单已经支付'));
        } else {
            // 分析订单来源
            list($a, $b, $c, $d, $e, $f) = explode('-', $data['mid']);
            // 付款到账号余额中 // touid为空表示系统收款直接扣
            if ($data['touid']) {
                if ($data['touid'] == $data['uid']) {
                    // 表示为自己充值
                    $this->add_money($data['uid'], $data['value']);
                } else {
                    // 扣除自己的钱
                    $this->add_money($data['uid'], -abs($data['value']));
                }
            } else {
                // 扣除自己的钱 touid为空表示系统收款直接扣
                $this->add_money($data['uid'], -abs($data['value']));
            }

            // 支付成功
            $this->table('member_paylog')->update($id, [
                'status' => 1,
                'result' => $payid,
                'paytime' => SYS_TIME,
            ]);

            $data['status'] = 1;
            $data['result'] = $payid;
            $data['paytime'] = SYS_TIME;
            $data['url'] = \Phpcmf\Service::L('Router')->member_url('paylog/show', ['id' => $data['id']]);

            $user = \Phpcmf\Service::M('Member')->member_info($data['uid']);
            if ($user) {
                $data['phone'] = $user['phone'];
                $data['email'] = $user['email'];
                // 通知
                \Phpcmf\Service::L('Notice')->send_notice('pay_success', $data);
                // 挂钩
                \Phpcmf\Hooks::trigger('pay_success', $data);
            }

            switch ($a) {

                case 'recharge':
                    // 在线充值
                    if (dr_is_app('yaoqing')) {
                        \Phpcmf\Service::M('yq', 'yaoqing')->czfx($data);
                    }
                    break;

                case 'score':
                    // 虚拟币充值
                    $value = abs($data['value']) * \Phpcmf\Service::C()->member_cache['pay']['convert'];
                    \Phpcmf\Service::M('member')->add_score($data['uid'], $value, dr_lang('在线充值'), $data['url']);
                    break;
                case 'donation':
                    // 打赏记录
                    $cid = (int)$c;
                    $rt = $this->table($d.'_'.$b.'_donation')->insert([
                        'cid' => $cid,
                        'uid' => $data['uid'],
                        'value' => abs($data['value']),
                        'inputtime' => SYS_TIME
                    ]);
                    // 打款到收款人账户
                    $this->add_money($data['touid'], abs($data['value']));
                    // 收款人增加一条收入记录
                    $this->add_paylog([
                        'uid' => $data['touid'],
                        'username' => $data['tousername'],
                        'touid' => $data['uid'],
                        'tousername' => $data['username'],
                        'mid' => $data['mid'],
                        'title' => $data['title'],
                        'value' => abs($data['value']),
                        'type' => $data['type'],
                        'status' => 1,
                        'result' => $payid,
                        'paytime' => SYS_TIME,
                        'inputtime' => $data['inputtime'],
                    ]);
                    // 更新主表
                    if ($rt['code']) {
                        $sum = $this->db->table($d.'_'.$b.'_donation')->selectSum('value')->where('cid', $cid)->get()->getRowArray();
                        $this->db->table($d.'_'.$b)->where('id', $cid)->set('donation', $sum['value'])->update();
                        \Phpcmf\Service::L('cache')->clear('module_'.$b.'_show_id_'.$cid);
                        $data['cid'] = $cid;
                        $data['mid'] = $b;

                        // 初始化数据表
                        $content_model = \Phpcmf\Service::M('Content', $b);
                        $content_model->_init($b, $d);
                        $content_model->_content_donation_after($cid, $data);

                        \Phpcmf\Hooks::trigger('donation_success', $data);
                    } else {
                        log_message('error', '打赏付款(#'.$id.')回调失败：'.$rt['msg']);
                    }
                    break;

                case 'order':
                    // 订单交易
                    \Phpcmf\Service::M('order', 'order')->pay($c, $id);
                    break;

                case 'orders':
                    // 订单交易
                    $oids = explode(',', $c);
                    foreach ($oids as $oi) {
                        \Phpcmf\Service::M('order', 'order')->pay($oi, $id);
                    }
                    break;

                case 'gathering':
                    // 来自收款
                    $row = $this->table($c)->get($b);
                    if (!$row) {
                        log_message('error', '收款(#'.$id.')回调失败：主题#'.$c.'不存在');
                    } else {
                        // 更新表
                        $row['nums'] = $row['nums'] + 1;
                        $row['total'] = $row['total'] + abs($data['value']);
                        $this->table($c)->update($b, [
                            'total' => $row['total'],
                            'nums' => $row['nums'],
                            'updatetime' => SYS_TIME
                        ]);
                        // 收款开发钩子
                        \Phpcmf\Hooks::trigger('gathering_'.$c.'_success', $row, $data);
                    }
                case 'my':
                    // 来自二次开发
                    $obj = $this->my_pay_obj($b);
                    if (method_exists($obj, 'success')) {
                        $obj->success($c, $data, $d, $e);
                    }


            }

            return dr_return_data(1, dr_lang('支付成功'));
        }
    }

    // 支付成功的回调url
    public function paycall_url($data) {

        // 默认地址
        $url = dr_url_prefix('index.php?s=member&c=paylog&m=show&id='.$data['id'], '', $data['site']);

        list($rname, $rid, $fid, $num, $sku) = explode('-', $data['mid']);

        switch ($rname) {

            case 'my':
                // 来自二次开发
                $row = $this->my_pay_obj($rid)->call_url($fid, $data);
                if ($row) {
                    $url = $row;
                }
                break;

            case 'order':
                // 来自单用户商城订单系统
                $url = dr_url_prefix('index.php?s=member&app=order&c=home&m=show&id='.$fid, '', $data['site']);
                break;

            case 'orders':
                // 来自组合订单商城订单系统
                $url = dr_url_prefix('index.php?s=member&app=order&c=home&m=index', '', $data['site']);
                break;
        }

        return $url;
    }

    /**
     * 支付提交
     * */
    public function post($post) {

        if (strlen($post['money']) > 8) {
            return dr_return_data(0, dr_lang('付款金额[%s]不规范', $post['money']));
        }

        $post['uid'] = intval($post['uid']);
        $post['username'] = (string)$post['username'];
        $post['money'] = floatval($post['money']);
        if ($post['money'] <= 0) {
            return dr_return_data(0, dr_lang('付款金额[%s]不规范', $post['money']));
        } elseif ((string)$post['money'] == 'INF') {
            return dr_return_data(0, dr_lang('付款金额[%s]不规范', $post['money']));
        } elseif (!$post['type']) {
            return dr_return_data(0, dr_lang('未知支付接口'));
        }

        // 初始化数据
        $touid = $money = $fid = 0;
        $tousername = $title = '';

        switch ($post['mark']) {

            // 在线充值
            case 'recharge':
                if (!$post['uid']) {
                    return dr_return_data(0, dr_lang('付款账号不存在'));
                }
                $title = dr_lang('用户（%s）充值', $post['username']);
                $money = $post['money'];
                if (\Phpcmf\Service::C()->member_cache['pay']['min'] && $money < \Phpcmf\Service::C()->member_cache['pay']['min']) {
                    return dr_return_data(0, dr_lang('系统最小充值金额为%s元', \Phpcmf\Service::C()->member_cache['pay']['min']));
                }
                $touid = $post['uid']; // 收款方为自己
                $tousername = $post['username']; // 收款方为自己
                if ($post['type'] == 'finecms') {
                    return dr_return_data(0, dr_lang('充值不能使用余额支付'));
                }
                break;

            // 金币充值
            case 'score':
                if (!$post['uid']) {
                    return dr_return_data(0, dr_lang('付款账号不存在'));
                }
                $money = (int)$post['money'];
                $title = dr_lang('用户（%s）充值%s：%s', $post['username'], SITE_SCORE, $money);
                $touid = $post['uid']; // 收款方为自己
                $tousername = $post['username']; // 收款方为自己
                $money = - $money / \Phpcmf\Service::C()->member_cache['pay']['convert'];
                break;

            // 其他来自自定义字段
            default:
                list($rname, $rid, $fid, $num, $sku) = explode('-', $post['mark']);

                switch ($rname) {

                    case 'gathering':
                        // 来自收款
                        $field = $this->myfield[$rname];
                        $row = $this->table($fid)->get($rid);
                        if (!$row) {
                            return dr_return_data(0, dr_lang('收款主题不存在'));
                        }
                        $money = floatval($row['price'] > 0 ? $row['price'] : $post['money']);
                        if ($money <= 0) {
                            return dr_return_data(0, dr_lang('金额不规范'));
                        }
                        $title = $row['title'];
                        $money = -$money;
                        $touid = 0; // 收款方为统系
                        $tousername = ''; // 收款方为统系
                        break;

                    case 'my':
                        // 来自二次开发
                        $row = $this->my_pay_obj($rid)->get_row($fid, $num, $sku, SITE_ID);
                        if (!$row) {
                            return dr_return_data(0, dr_lang('主题不存在'));
                        }
                        $money = floatval($row['price']);
                        if ($money <= 0) {
                            return dr_return_data(0, dr_lang('金额不规范'));
                        }
                        $title = $row['title'];
                        $money = -$money;
                        $touid = (int)$row['sell_uid']; // 收款方
                        $tousername = (string)$row['sell_username']; // 收款方
                        break;

                    case 'donation':
                        // 打赏作者
                        $field = $this->myfield[$rname];
                        $money = (float)$post['money'];
                        if ($money <= 0) {
                            return dr_return_data(0, dr_lang('金额不规范'));
                        }
                        // 获取文章信息
                        $rt = $this->get_module_row($rid, $fid, $num);
                        if (!$rt['code']) {
                            return dr_return_data(0, $rt['msg']);
                        }
                        $min = floatval(\Phpcmf\Service::C()->member_cache['pay']['smin']);
                        if ($min > 0 && $money < $min) {
                            return dr_return_data(0, dr_lang('打赏金额不能低于%s元', $min));
                        }
                        $touid = $rt['data']['uid'];
                        $tousername = $rt['data']['author'];
                        $title = $post['title'];
                        $money = -$money;
                        if ($this->uid == $touid) {
                            return dr_return_data(0, dr_lang('不能对自己打赏'));
                        }
                        break;

                    case 'order':
                        // 来自单用户商城订单系统
                        $title = dr_lang('订单编号：%s', $post['sn']);
                        $money = -(float)$post['money'];
                        $touid = 0; // 收款方为统系
                        $tousername = ''; // 收款方为统系
                        break;

                    case 'orders':
                        // 来自多用户商城订单系统
                        $title = dr_lang('订单编号：%s', $post['sn']);
                        $money = -(float)$post['money'];
                        $touid = 0; // 收款方为统系
                        $tousername = ''; // 收款方为统系
                        break;

                    default:
                        // 来自自定义字段
                        $field = \Phpcmf\Service::C()->get_cache('table-field', $fid);
                        if (!$field) {
                            return dr_return_data(0, dr_lang('支付字段不存在'));
                        }
                        // 获取付款价格
                        $rt = $this->get_pay_info($rid, $field, $num, $sku);
                        if ($rt['total'] <= 0) {
                            return dr_return_data(0, dr_lang('金额不规范'));
                        } elseif ($rt['mid'] != $post['mark']) {
                            return dr_return_data(0, dr_lang('支付信息验证失败'));
                        }
                        $touid = $rt['touid'];
                        $tousername = $rt['tousername'];
                        $title = $rt['title'];
                        $money = -(float)$rt['total'];
                        if ($this->uid == $touid) {
                            return dr_return_data(0, dr_lang('不能对自己付款'));
                        }
                        break;
                }

                break;
        }

        // 判断接口
        $apifile = ROOTPATH.'api/pay/'.$post['type'].'/pay.php';
        if (!is_file($apifile)) {
            return dr_return_data(0, dr_lang('支付接口文件（%s）不存在', $post['type']));
        }

        // 增加流水记录
        return $this->add_paylog([
            'uid' => $post['uid'],
            'username' => $post['username'],
            'touid' => $touid,
            'tousername' => $tousername,
            'mid' => $post['mark'],
            'title' => $title,
            'value' => $money,
            'type' => $post['type'],
            'status' => 0,
            'url' => $post['url'],
            'result' => '',
            'paytime' => 0,
            'inputtime' => SYS_TIME,
        ]);
    }

    // 调用支付接口
    public function dopay($apifile, $data) {

        $id = $data['id']; // 支付记录的id
        // 生成唯一支付id 接口使用
        $sn = $pid = \Phpcmf\Service::C()->member_cache['pay']['prefix'].date('YmdHis', $data['inputtime']).'-'.$id;

        // 接口配置参数
        $config = \Phpcmf\Service::C()->member_cache['payapi'][$data['type']];

        $return = [];
        $data['value'] = abs($data['value']);
        require $apifile;

        return $return;
    }

    // 缓存可用的支付字段
    public function cache() {

        $cache = [];
        \Phpcmf\Service::L('cache')->set_file('pay', $cache);
        return;
    }

    // 提现手续费比例
    public function cash_price($member) {

        if (!$member) {
            return 0;
        }

        $price = [];

        if (\Phpcmf\Service::C()->member_cache['pay']['cash']['group_price'] && is_array(\Phpcmf\Service::C()->member_cache['pay']['cash']['group_price'])) {
            foreach (\Phpcmf\Service::C()->member_cache['pay']['cash']['group_price'] as $gid => $value) {
                if (in_array($gid, $member['groupid'])) {
                    $price[] = $value;
                }
            }
            if ($price) {
                return intval(min($price));
            }
        }

        return 0;
    }

    /// 提现申请
    public function add_cash($post) {

        if (!$this->uid || !$post) {
            return dr_return_data(0, dr_lang('参数错误'));
        } elseif (strlen($post['value']) > 8) {
            return dr_return_data(0, dr_lang('金额不规范'));
        }

        // 手续费
        $price = $this->cash_price($this->member);

        $rt = $this->table('member_cashlog')->insert([
            'uid' => $this->uid,
            'username' => $this->member['username'],
            'content' => dr_safe_replace($post['content']),
            'value' => $post['value'],
            'money' => $price ? $post['value'] - $post['value'] * ($price/100) : $post['value'],
            'status' => 0,
            'result' => '',
            'paytime' => 0,
            'inputtime' => SYS_TIME,
        ]);
        if (!$rt['code']) {
            return $rt;
        }

        // 扣钱
        $rt2 = \Phpcmf\Service::M('member')->add_freeze($this->uid, $post['value']);
        if (!$rt2['code']) {
            // 删除刚刚添加的数据
            $this->table('member_cashlog')->delete($rt['code']);
        }

        return $rt;
    }

    // 提现申请处理拒绝
    public function cash_fail($id, $post, $old) {

        $this->table('member_cashlog')->update($id, [
            'result' => $post['result'],
            'status' => 1, // 拒绝
        ]);
        // 取消冻结资金
        return \Phpcmf\Service::M('member')->cancel_freeze($old['uid'], $old['value']);
    }

    // 提现申请处理成功
    public function cash_successs($id, $post, $old) {

        // 增加消费量 并移除冻结资金
        $rt = \Phpcmf\Service::M('member')->use_freeze($old['uid'], $old['value']);
        if (!$rt['code']) {
            return $rt;
        }

        // 标识申请成功
        $this->table('member_cashlog')->update($id, [
            'result' => $post['result'],
            'status' => 2, // 成功
        ]);

        // 增加消费记录
        return $this->add_paylog([
            'uid' => $old['uid'],
            'username' => $old['username'],
            'touid' => $old['uid'],
            'tousername' => $old['username'],
            'mid' => 'cash',
            'title' => dr_lang('现金提现'),
            'value' => -$old['value'],
            'type' => 'finecms',
            'status' => 1,
            'url' => '',
            'result' => $post['result'],
            'paytime' => SYS_TIME,
            'inputtime' => SYS_TIME,
        ]);
    }


    // 获取二次开发对象
    public function my_pay_obj($name) {
        list($app, $class) = explode('_', $name);
        return \Phpcmf\Service::M($class, $app);
    }

    // 清除3天未付款的流水
    public function clear_paylog() {
        $this->db->table('member_paylog')->where('status', 0)->where('inputtime <'.(SYS_TIME - 3600*24*3))->delete();
    }


}