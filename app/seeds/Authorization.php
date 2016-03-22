<?php

return [
    'model' => 'Authorization',
    'dependencies' => [],
    'seeds' => function () {
        $seeds = [];
        $seeds[] = ['group'=> 'getByLink', 'uri_pattern'=> '/books/by-link|GET',];
        $seeds[] = ['group'=> 'getByLinkNeedCaptcha', 'uri_pattern'=> '/books/by-link|POST',];
        $seeds[] = ['group'=> 'downloadBook', 'uri_pattern'=> '/download/:uid|GET',];
        return $seeds;
    },
];
