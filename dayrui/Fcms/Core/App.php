<?php namespace Phpcmf;

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




// 插件公共继承类
class App extends \Phpcmf\Common
{

    public function __construct(...$params)
    {
        parent::__construct($params);
        if (!dr_is_app(APP_DIR)) {
            if (is_file(APPPATH.'Config/App.php')) {
                $cfg = require APPPATH.'Config/App.php';
                $this->_msg(0, dr_lang('插件[%s]未安装', $cfg['name']));
            } else {
                $this->_msg(0, dr_lang('插件[%s]未安装', APP_DIR));
            }
        }
    }

}
