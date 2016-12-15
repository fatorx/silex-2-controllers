<?php

namespace Reports;

return [
    'db.options_test' => [
        'driver' => 'pdo_sqlite',
        'path' =>  __DIR__.'/../app/db/app.db',
    ],
    'db.options' => [
        'driver'    => 'pdo_mysql',
        'host'      => 'localhost',
        'dbname'    => 'enter_game',
        'user'      => 'root',
        'password'  => '',
        'charset'   => 'utf8',
    ],
    'orm.proxies_dir' => __DIR__ . '/../cache/doctrine/proxies',
    'orm.em.options' => [
        'default_cache' => 'array',
        'mappings' => [
            [
                'type' => 'annotation',
                'use_simple_annotation_reader' => false, // not document
                'namespace' => __NAMESPACE__ . '\Entities',
                'path' => __DIR__ . '/../src/' . __NAMESPACE__ . '/Entities',
            ],
        ],
    ],
];