<?php

return [
    'model' => 'Authorization',
    'dependencies' => [],
    'seeds' => function () {
        $seeds = [];

        $seeds[] = ['group' => 'books', 'uri_pattern' => '/books/search|GET'];
        $seeds[] = ['group' => 'books', 'uri_pattern' => '/books/report|POST'];

        return $seeds;
    },
];
