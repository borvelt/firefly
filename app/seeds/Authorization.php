<?php

return [
    'model' => 'Authorization',
    'dependencies' => [],
    'seeds' => function () {
        $seeds = [];

        $seeds[] = ['group'=> 'books', 'uri_pattern'=> '/books/by-link|GET',];
        $seeds[] = ['group'=> 'books', 'uri_pattern'=> '/books/by-link|POST',];
        $seeds[] = ['group'=> 'books', 'uri_pattern'=> '/books/search|POST',];
        $seeds[] = ['group'=> 'books', 'uri_pattern'=> '/books/report|POST',];

        $seeds[] = ['group'=> 'download', 'uri_pattern'=> '/download/:uid|GET',];

        $seeds[] = ['group'=> 'report', 'uri_pattern'=> '/books/downloaded|GET',];

        return $seeds;
    },
];
