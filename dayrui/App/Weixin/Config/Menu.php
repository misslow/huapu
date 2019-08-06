<?php

/**
 * 菜单配置
 */

return [

    'admin' => [

        'app-weixin' => [
            'name' => '微信',
            'icon' => 'fa fa-weixin',
            'left' => [
                'app-weixin-home' => [
                    'name' => '系统',
                    'icon' => 'fa fa-home',
                    'link' => [
                        [
                            'name' => '概况',
                            'icon' => 'fa fa-area-chart',
                            'uri' => 'weixin/home/index',
                        ],
                        [
                            'name' => '公众号',
                            'icon' => 'fa fa-weixin',
                            'uri' => 'weixin/account/index',
                        ],
                        [
                            'name' => '小程序',
                            'icon' => 'fa fa-link',
                            'uri' => 'weixin/xcx/index',
                        ],
                        [
                            'name' => '系统配置',
                            'icon' => 'fa fa-cog',
                            'uri' => 'weixin/config/index',
                        ],
                        [
                            'name' => '公众号菜单',
                            'icon' => 'fa fa-list',
                            'uri' => 'weixin/menu/index',
                        ],
                    ],
                ],
                'app-weixin-content' =>[
                    'name' => '公众号内容',
                    'icon' => 'fa fa-th-large',
                    'link' => [
                        [
                            'name' => '素材管理',
                            'icon' => 'fa fa-th-large',
                            'uri' => 'weixin/content/index',
                        ],
                        [
                            'name' => '自动回复',
                            'icon' => 'fa fa-comments',
                            'uri' => 'weixin/reply/index',
                        ],
                    ]
                ],
                'app-weixin-fans' =>[
                    'name' => '公众号粉丝',
                    'icon' => 'fa fa-user',
                    'link' => [
                        [
                            'name' => '粉丝管理',
                            'icon' => 'fa fa-user',
                            'uri' => 'weixin/user/index',
                        ],
                        [
                            'name' => '用户标签组',
                            'icon' => 'fa fa-users',
                            'uri' => 'weixin/group/index',
                        ],
                    ]
                ],
                'app-weixin-message' =>[
                    'name' => '公众号消息',
                    'icon' => 'fa fa-envelope',
                    'link' => [
                        [
                            'name' => '消息记录',
                            'icon' => 'fa fa-envelope',
                            'uri' => 'weixin/message/index',
                        ],
                        [
                            'name' => '客服群发',
                            'icon' => 'fa fa-envelope-o',
                            'uri' => 'weixin/send/index',
                        ],
                        [
                            'name' => '高级群发',
                            'icon' => 'fa fa-volume-up',
                            'uri' => 'weixin/sendall/index',
                        ],
                    ]
                ],

            ],
        ],


    ],

];