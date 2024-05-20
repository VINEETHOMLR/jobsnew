<?php


return [
    'db' => [
        'dbname' => '19_v2',
        'host' => '192.168.88.200',
        'user' => 'root',
        'pass' => 'RtGHSrv45#6'
    ],
    'logsdb' => [
        'dbname' => '19_v2_logs',
        'host' => '192.168.88.200',
        'user' => 'root',
        'pass' => 'RtGHSrv45#6'
    ],
    'mvc' => [
        'defaults' => [
            'action' => 'index',
            'controller' => 'index',
            'error' => 'error'
        ]
    ],
    'route' => [
        'prettyURL' => true
    ],
    'languagesPacksAvailable' => [
        'chinese' => 'cn',
        'english' => 'english',
        'traditional' => 'traditional'
    ]
];
