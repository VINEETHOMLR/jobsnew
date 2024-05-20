<?php

return [
    'db' => [
        'dbname' => 'book',
        'host' => 'localhost',
        'user' => 'root',
        'pass' => ''
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
        'chinese' => 'chinese',
        'english' => 'english',
        'traditional' => 'traditional'
    ],
    'btc' => [
        "api_url" => "http://demotestivps.com/infinite_app/api/",
        "token" => "abcdefghijklmnopqr"
    ],
];
