<?php

return [
    'boots'    => [
        'tools' => \Ahlife\Tool::class,
    ],

    // 授权中心配置
    'ocenter'  => [
        'server_uri'  => 'xxxxx',
        'session_key' => 'xxxxx',
        'ts_salt'     => 'xxxxx',
        'app'         => 'xxxxx',
    ],

    // 腾讯防水墙配置
    'tcverify' => [
        'appid'  => "xxxxx",
        'secret' => "xxxxx",
        'apiurl' => 'xxxxx',
    ],

];
