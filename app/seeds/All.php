<?php
return [
    'model' => null,
    'dependencies' => ['User','Authorization'],
    'seeds' => function() {
        User::find(1)->authorize()->attach(range(1,10));
        return [];
    },
];
