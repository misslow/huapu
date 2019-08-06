<?php

/**
 * 菜单配置
 */


return [

    'admin' => [

        'app' => [

            'left' => [


                'app-httpapi' => [
                    'name' => 'API接口',
                    'icon' => 'fa fa-plug',
                    'link' => [
                        [
                            'name' => 'API接口密钥',
                            'icon' => 'fa fa-key',
                            'uri' => 'httpapi/auth/index',
                        ],
                        [
                            'name' => 'API接口数据',
                            'icon' => 'fa fa-plug',
                            'uri' => 'httpapi/http/index',
                        ],

                    ]
                ],
            ],



        ],

    ],
];