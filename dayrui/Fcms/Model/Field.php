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


// 字段操作表
class Field extends \Phpcmf\Model
{

    public $data;
    public $func;
    public $relatedid;
    public $relatedname;

    // 全部字段
    public function get_all() {
        
        if (!$this->relatedname) {
            return null;
        }

        $data = $this->db->table('field')
                    ->where('relatedid', $this->relatedid)
                    ->where('relatedname', $this->relatedname)
                    ->orderBy('disabled ASC,displayorder ASC,id ASC')
                    ->get()
                    ->getResultArray();
        if (!$data) {
            return null;
        }

        $rt = [];
        foreach ($data as $i => $t) {
            $t['spacer'] = '';
            $t['setting'] = dr_string2array($t['setting']);
            $rt[$t['id']] = $t;
        }

        return $rt;
    }

    // 获取任意表的自定义字段
    public function get_mytable_field($table, $siteid = 0) {

        $name = 'table-'.$table;
        $value = \Phpcmf\Service::L('cache')->init()->get($name);
        if (!$value) {
            $field = $this->db->table('field')
                        ->where('disabled', 0)
                        ->where('relatedid', $siteid)
                        ->where('relatedname', $name)
                        ->orderBy('displayorder ASC,id ASC')
                        ->get()
                        ->getResultArray();
            if ($field) {
                foreach ($field as $t) {
                    $t['setting'] = dr_string2array($t['setting']);
                    $value[$t['fieldname']] = $t;
                }
            }
            \Phpcmf\Service::L('cache')->init()->save($name, $value);
        }

        return $value;
    }

    // 删除字段
    public function delete_field($ids) {

        foreach ($ids as $id) {
            $id = intval($id);
            $data = $this->table('field')->get($id);
            if (!$data) {
                return dr_return_data(0, dr_lang('字段不存在(id:%s)', $id));
            } elseif ($data['issystem']) {
                return dr_return_data(0, dr_lang('系统字段不允许删除(id:%s)', $id));
            }
            $rt = $this->table('field')->delete($id);
            if (!$rt['code']) {
                return dr_return_data(0, $rt['msg']);
            }
            // 删除表中数据
            $field = \Phpcmf\Service::L('field')->get($data['fieldtype']);
            if ($field) {
                // 非系统字段才支持删除
                $sql = $field->drop_sql($data['fieldname']);
                // 需要分别更新各站点
                $sql && $this->update_table($sql, $data['ismain']);
            }
        }

        return dr_return_data(1, '');
    }

    /**
     * 添加字段
     *
     * @param	array	$data
     * @param	object	$field
     * @return	void
     */
    public function add($data, $field) {

        // 先读取sql语句
        $sql = $field->create_sql($data['fieldname'], $data['setting']['option'], $data['name']);

        // 当为编辑器类型时，关闭xss过滤
        $data['fieldtype'] == 'Ueditor' && $data['setting']['validate']['xss'] = 1;

        $data['ismain'] = (int)$data['ismain'];
        $data['setting'] = dr_array2string($data['setting']);
        $data['issystem'] = 0;
        $data['issearch'] = (int)$data['issearch'];
        $data['ismember'] = (int)$data['ismember'];
        $data['disabled'] = (int)$data['disabled'];
        $data['relatedid'] = $this->relatedid;
        $data['relatedname'] = $this->relatedname;
        $data['displayorder'] = (int)$data['displayorder'];

        // 入库字段表
        $rt = $this->table('field')->insert($data);

        // 执行数据库语句
        if ($rt['code'] && $sql) {
            $this->_table_field = [];
            $this->update_table($sql, $data['ismain']);
            // 验证字段是否上传成功
            $this->db->resetDataCache();// 清除缓存，影响字段存在的重复
            if ($this->_table_field && $yz = $field->test_sql($this->_table_field, $data['fieldname'])) {
                // 删除本字段
                $this->table('field')->delete($rt['code']);
                return dr_return_data(0, dr_lang('字段创建失败: %s', $yz));
            }
        }
        
        return $rt;
    }

    /**
     * 修改字段
     *
     * @param	array	$_data	旧数据
     * @param	array	$data	新数据
     * @param	string	$sql	执行该操作的sql语句
     * @return	string
     */
    public function edit($_data, $data, $sql) {

        if (!$_data || !$data) {
            return dr_return_data(0, dr_lang('参数不完整'));
        }

        // 如果字段类型、长度变化时，分别更新各站点
        ($data['setting']['option']['fieldtype'] != $_data['setting']['option']['fieldtype']
            || $data['setting']['option']['fieldlength'] != $_data['setting']['option']['fieldlength'])
        && $this->update_table($sql, $_data['ismain']);

        $data['setting'] = dr_array2string($data['setting']);
        $data['issearch'] = (int)$data['issearch'];
        $data['ismember'] = (int)$data['ismember'];
        $data['disabled'] = (int)$data['disabled'];

        // 更新字段表
        return $this->table('field')->update($_data['id'], $data);

    }

    //--------------------------------------------------------------------
    
    /**
     * 分别更新各站点的表结构
     *
     * @param	string	$sql		执行该操作的sql语句
     * @param	intval	$ismain		是否主表
     * @return	void
     */
    public function update_table($sql, $ismain) {

        if (!$sql || !$this->func) {
            return null;
        }

        return call_user_func_array(array($this, '_sql_'.$this->func), array($sql, $ismain));
        
    }
    
    /**
     * 判断同表字段否存在
     *
     * @param	string	$name	字段名称
     * @param	intval	$int	字段id
     * @return	int
     */
    public function exitsts($name) {

        if (!$name)	{
            return 1;
        }

        return call_user_func_array(array($this, '_field_'.$this->func), array($name));
    }

    /**
     * 判断表字段否存在
     */
    private function _field_exitsts($id, $name, $table, $siteid = 0) {

        if (!$table)	{
            return 0;
        }

        return $this->db->fieldExists($name, $table);
    }

    //--------------------------------------------------------------------

    // 栏目模型字段
    private function _sql_category_data($sql, $ismain) {
        $table = $this->dbprefix(SITE_ID.'_'.$this->data['dirname'].'_category_data'); // 主表名称
        if (!$this->db->tableExists($table)) {
            return;
        }
        if ($ismain) {
            // 更新主表 格式: 站点id_名称
            $this->db->simpleQuery(str_replace('{tablename}', $table, $sql));
            $this->_table_field[] = $table;
        } else {
            for ($i = 0; $i < 200; $i ++) {
                if (!$this->db->query("SHOW TABLES LIKE '".$table.'_'.$i."'")->getRowArray()) {
                    break;
                }
                $this->db->simpleQuery(str_replace('{tablename}', $table.'_'.$i, $sql)); //执行更新语句
                $this->_table_field[] = $table.'_'.$i;
            }
        }
    }
    // 字段是否存在
    private function _field_category_data($name) {
        // 主表
        $table = $this->dbprefix(SITE_ID.'_'.$this->data['dirname'].'_category_data');
        $rt = $this->_field_exitsts('id', $name, $table, SITE_ID);
        if ($rt) {
            return 1;
        }
        // 附表
        $rt = $this->_field_exitsts('id', $name, $table.'_0', SITE_ID);
        if ($rt) {
            return 1;
        }
        return 0;
    }

    //--------------------------------------------------------------------


    //--------------------------------------------------------------------

    // 评论自定义字段
    private function _sql_comment($sql, $ismain) {
        // 更新站点模块
        foreach (\Phpcmf\Service::C()->site_info as $sid => $v) {
            $table = $this->dbprefix($sid.'_'.$this->data.'_comment');
            if (!$this->db->tableExists($table)) {
                return;
            }
            $this->db->simpleQuery(str_replace('{tablename}', $table, $sql));
            $this->_table_field[] = $table;
        }
    }
    // 字段是否存在
    private function _field_comment($name) {
        // 主表
        $table = $this->dbprefix(SITE_ID.'_'.$this->data.'_comment');
        $rt = $this->_field_exitsts('id', $name, $table, SITE_ID);
        if ($rt) {
            return 1;
        }
        return 0;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------

    // 栏目字段
    private function _sql_category($sql, $ismain) {
        // 更新站点模块
        foreach (\Phpcmf\Service::C()->site_info as $sid => $v) {
            $table = $this->dbprefix($sid.'_'.$this->data.'_category');
            if (!$this->db->tableExists($table)) {
                return;
            }
            $this->db->simpleQuery(str_replace('{tablename}', $table, $sql));
            $this->_table_field[] = $table;
        }
    }
    // 字段是否存在
    private function _field_category($name) {
        // 主表
        $table = $this->dbprefix(SITE_ID.'_'.$this->data.'_category');
        $rt = $this->_field_exitsts('id', $name, $table, SITE_ID);
        if ($rt) {
            return 1;
        }
        return 0;
    }

    //--------------------------------------------------------------------
    //--------------------------------------------------------------------

    // 会员字段
    private function _sql_member($sql, $ismain) {
        $this->db->simpleQuery(str_replace('{tablename}', $this->dbprefix('member_data'), $sql));
        $this->_table_field[] = $this->dbprefix('member_data');
    }
    // 字段是否存在
    private function _field_member($name) {
        // 主表
        $table = $this->dbprefix('member_data');
        $rt = $this->_field_exitsts('id', $name, $table, SITE_ID);
        if ($rt) {
            return 1;
        }
        return 0;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------

    // 网站表单字段
    private function _sql_form($sql, $ismain) {
        $table = $this->dbprefix(SITE_ID.'_form_'.$this->data['table']); // 主表名称
        if (!$this->db->tableExists($table)) {
            return;
        }
        if ($ismain) {
            // 更新主表 格式: 站点id_名称
            $this->db->simpleQuery(str_replace('{tablename}', $table, $sql));
            $this->_table_field[] = $table;
        } else {
            for ($i = 0; $i < 200; $i ++) {
                if (!$this->db->query("SHOW TABLES LIKE '".$table.'_data_'.$i."'")->getRowArray()) {
                    break;
                }
                $this->db->simpleQuery(str_replace('{tablename}', $table.'_data_'.$i, $sql)); //执行更新语句
                $this->_table_field[] = $table.'_data_'.$i;
            }
        }
    }
    // 字段是否存在
    private function _field_form($name) {
        // 主表
        $table = $this->dbprefix(SITE_ID.'_form_'.$this->data['table']);
        $rt = $this->_field_exitsts('id', $name, $table, SITE_ID);
        if ($rt) {
            return 1;
        }
        // 附表
        $rt = $this->_field_exitsts('id', $name, $table.'_data_0', SITE_ID);
        if ($rt) {
            return 1;
        }
        return 0;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------

    // 联动字段
    private function _sql_linkage($sql, $ismain) {
        $table = $this->dbprefix('linkage_data_'.$this->relatedid);
        if (!$this->db->tableExists($table)) {
            return;
        }
        $this->db->simpleQuery(str_replace('{tablename}', $table, $sql));
        $this->_table_field[] = $table;
    }
    // 字段是否存在
    private function _field_linkage($name) {
        // 主表
        $table = $this->dbprefix('linkage_data_'.$this->relatedid);
        $rt = $this->_field_exitsts('id', $name, $table, $this->relatedid);
        if ($rt) {
            return 1;
        }
        return 0;
    }

    //--------------------------------------------------------------------

    // Tag字段
    private function _sql_tag($sql, $ismain) {
        $table = $this->dbprefix($this->relatedid.'_tag');
        if (!$this->db->tableExists($table)) {
            return;
        }
        $this->db->simpleQuery(str_replace('{tablename}', $table, $sql));
        $this->_table_field[] = $table;
    }
    // 字段是否存在
    private function _field_tag($name) {
        // 主表
        $table = $this->dbprefix($this->relatedid.'_tag');
        $rt = $this->_field_exitsts('id', $name, $table, $this->relatedid);
        if ($rt) {
            return 1;
        }
        return 0;
    }

    //--------------------------------------------------------------------//--------------------------------------------------------------------

    // navigator字段
    private function _sql_navigator($sql, $ismain) {
        $table = $this->dbprefix($this->relatedid.'_navigator');
        if (!$this->db->tableExists($table)) {
            return;
        }
        $this->db->simpleQuery(str_replace('{tablename}', $table, $sql));
        $this->_table_field[] = $table;
    }
    // 字段是否存在
    private function _field_navigator($name) {
        // 主表
        $table = $this->dbprefix($this->relatedid.'_navigator');
        $rt = $this->_field_exitsts('id', $name, $table, $this->relatedid);
        if ($rt) {
            return 1;
        }
        return 0;
    }

    //--------------------------------------------------------------------

    // 订单插件字段
    private function _sql_order($sql, $ismain) {
        $table = $this->dbprefix($this->relatedid.'_order');
        if (!$this->db->tableExists($table)) {
            return;
        }
        $this->db->simpleQuery(str_replace('{tablename}', $table, $sql));
        $this->_table_field[] = $table;
    }
    // 字段是否存在
    private function _field_order($name) {
        // 主表
        $table = $this->dbprefix($this->relatedid.'_order');
        $rt = $this->_field_exitsts('id', $name, $table, $this->relatedid);
        if ($rt) {
            return 1;
        }
        return 0;
    }


    //--------------------------------------------------------------------

    // 单页字段
    private function _sql_page($sql, $ismain) {
        $table = $this->dbprefix($this->relatedid.'_page');
        if (!$this->db->tableExists($table)) {
            return;
        }
        $this->db->simpleQuery(str_replace('{tablename}', $table, $sql));
        $this->_table_field[] = $table;
    }
    // 字段是否存在
    private function _field_page($name) {
        // 主表
        $table = $this->dbprefix($this->relatedid.'_page');
        $rt = $this->_field_exitsts('id', $name, $table, $this->relatedid);
        if ($rt) {
            return 1;
        }
        return 0;
    }

    //--------------------------------------------------------------------



    //--------------------------------------------------------------------

    // 模块字段
    private function _sql_module($sql, $ismain) {
        // 更新站点模块
        foreach (\Phpcmf\Service::C()->site_info as $sid => $v) {
            $table = $this->dbprefix($sid.'_'.$this->data['dirname']); // 主表名称
            if (!$this->db->tableExists($table)) {
                continue;
            }
            if ($ismain) {
                // 更新主表 格式: 站点id_名称
                $this->db->simpleQuery(str_replace('{tablename}', $table, $sql));
                $this->_table_field[] = $table;
            } else {
                // 更新副表 格式: 名称_站点id_data_副表id
                for ($i = 0; $i < 200; $i ++) {
                    if (!$this->db->query("SHOW TABLES LIKE '".$table.'_data_'.$i."'")->getRowArray()) {
                        break;
                    }
                    $this->db->simpleQuery(str_replace('{tablename}', $table.'_data_'.$i, $sql)); //执行更新语句
                    $this->_table_field[] = $table.'_data_'.$i;
                }
            }
        }
    }
    // 字段是否存在
    private function _field_module($name) {
        // 主表
        $table = $this->dbprefix(SITE_ID.'_'.$this->data['dirname']);
        $rt = $this->_field_exitsts('id', $name, $table, SITE_ID);
        if ($rt) {
            return 1;
        }
        // 附表
        $rt = $this->_field_exitsts('id', $name, $table.'_data_0', SITE_ID);
        if ($rt) {
            return 1;
        }
        return 0;
    }

    //--------------------------------------------------------------------


    //--------------------------------------------------------------------

    // 模块表单字段
    private function _sql_mform($sql, $ismain) {

        // 更新站点模块
        foreach (\Phpcmf\Service::C()->site_info as $sid => $v) {
            $table = $this->dbprefix($sid.'_'.$this->data['module'].'_form_'.$this->data['table']); // 主表名称
            if (!$this->db->tableExists($table)) {
                continue;
            }
            if ($ismain) {
                // 更新主表 格式: 站点id_名称
                $this->db->simpleQuery(str_replace('{tablename}', $table, $sql));
                $this->_table_field[] = $table;
            } else {
                // 更新副表 格式: 名称_站点id_data_副表id
                for ($i = 0; $i < 200; $i ++) {
                    if (!$this->db->query("SHOW TABLES LIKE '".$table.'_data_'.$i."'")->getRowArray()) {
                        break;
                    }
                    $this->db->simpleQuery(str_replace('{tablename}', $table.'_data_'.$i, $sql)); //执行更新语句
                    $this->_table_field[] = $table.'_data_'.$i;
                }
            }
        }

    }
    // 字段是否存在
    private function _field_mform($name) {
        // 主表
        $table = $this->dbprefix(dr_module_table_prefix($this->data['module']).'_form_'.$this->data['table']);
        $rt = $this->_field_exitsts('id', $name, $table, $this->data['module'] == 'space' ? 0 : SITE_ID);
        if ($rt) {
            return 1;
        }
        // 附表
        $rt = $this->_field_exitsts('id', $name, $table.'_data_0', $this->data['module'] == 'space' ? 0 : SITE_ID);
        if ($rt) {
            return 1;
        }
        return 0;
    }

    //--------------------------------------------------------------------


    //--------------------------------------------------------------------

    // 任意表
    private function _sql_table($sql, $ismain) {
        $table = $this->dbprefix($this->data);
        if (!$this->db->tableExists($table)) {
            return;
        }
        $this->db->simpleQuery(str_replace('{tablename}', $table, $sql));
        $this->_table_field[] = $table;
    }
    // 字段是否存在
    private function _field_table($name) {
        // 主表
        $table = $this->dbprefix($this->data);
        $rt = $this->_field_exitsts('id', $name, $table, $this->relatedid);
        if ($rt) {
            return 1;
        }
        return 0;
    }

    //--------------------------------------------------------------------

}