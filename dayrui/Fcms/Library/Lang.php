<?php namespace Phpcmf\Library;

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


/**
 * 语言包
 */

class Lang {

    public $lang;

    /**
     * 加载自定义语言
     */
    public function __construct(...$params) {
        if (is_file(ROOTPATH.'config/language/'.SITE_LANGUAGE.'/lang.php')) {
            $this->lang = require ROOTPATH.'config/language/'.SITE_LANGUAGE.'/lang.php';
        } else {
            $this->lang = [];
        }
    }

    /**
     * 输出最终语言
     */
    public function text($text) {
        return isset($this->lang[$text]) ? $this->lang[$text] : $text;
    }

}