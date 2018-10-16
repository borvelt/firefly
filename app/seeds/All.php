<?php
return [
    'model' => null,
    'dependencies' => ['User', 'Authorization'],
    'seeds' => function () {
        User::find(1)->authorize()->attach(1);
        return [];
    },
];
